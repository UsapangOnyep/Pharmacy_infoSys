<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$orderItems = isset($data['orderItems']) ? $data['orderItems'] : [];
$discount = isset($data['discount']) ? (float) $data['discount'] : 0.00;
$CashTendered = isset($data['payment']) ? (float) $data['payment'] : 0.00;
$Change = isset($data['Change']) ? (float) $data['Change'] : 0.00;   
$orderTotal = isset($data['orderTotal']) ? (float) $data['orderTotal'] : 0.00;
$CreatedBy = isset($data['CreatedBy']) ? trim($data['CreatedBy']) : '';
$Status = isset($data['orderStatus']) ? $data['orderStatus'] : '1';

if ($Status != "0" && (empty($orderItems) || $CashTendered <= 0 || $orderTotal <= 0)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid order data.']);
    exit;
}

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("SELECT LPAD(IFNULL(MAX(TransactionNo), 0) + 1, 12, '0') AS TransactionNo FROM SalesTransaction");
    $stmt->execute();
    $TransactionNo = $stmt->get_result()->fetch_assoc()['TransactionNo'];

    $SINo = ($Status == 1) ? $conn->query("SELECT LPAD(IFNULL(MAX(SINo), 0) + 1, 12, '0') AS SINo FROM SalesTransaction")->fetch_assoc()['SINo'] : '';

    $stmt = $conn->prepare("INSERT INTO SalesTransaction (TransactionNo, SINo, Amount, Discount, CashTendered, CashChange, DateTimeCreated, CreatedBy, `Status`) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?)");
    $stmt->bind_param("ssdddsis", $TransactionNo, $SINo, $orderTotal, $discount, $CashTendered, $Change, $CreatedBy, $Status);
    $stmt->execute();

    foreach ($orderItems as $item) {
        $stmt = $conn->prepare("INSERT INTO SalesTransactionItems (TransactionNo, StockID, Qty, Price, Discount) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("siidd", $TransactionNo, $item['StockID'], $item['Qty'], $item['Price'], $item['Discount']);
        $stmt->execute();

        if ($Status == "1") {
            $stmt = $conn->prepare("UPDATE stocks SET QTY = (QTY - ?), UpdatedBy = ?, DateTimeUpdated = NOW() WHERE ID = ?");
            $stmt->bind_param("isi",  $item['Qty'], $CreatedBy, $item['StockID']);
            $stmt->execute();

            $stmt = $conn->prepare("INSERT INTO invtransaction (ReferenceCode, StockID, Qty, ActionType, ActionTaken, CreatedBy, DatetimeCreated) VALUES (?, ?, ?, 'OUT', 'Dispense', ?, NOW())");
            $stmt->bind_param("siis", $TransactionNo, $item['StockID'], $item['Qty'], $CreatedBy);
            $stmt->execute();
        }
    }

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Order processed successfully.', 'TransactionNo' => $TransactionNo]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Transaction failed.']);
}
$conn->close();
?>