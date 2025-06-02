<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$itemID = isset($_POST['ItemName']) ? $_POST['ItemName'] : null;
$Brand = isset($_POST['ItemBrand']) ? $_POST['ItemBrand'] : null;
$Model = isset($_POST['ItemModel']) ? $_POST['ItemModel'] : null;
$QTY = isset($_POST['QTY']) ? $_POST['QTY'] : null;
$Price = isset($_POST['Price']) ? $_POST['Price'] : null;
$noExpiration = isset($_POST['noExpiration']) ? $_POST['noExpiration'] : false;
$ExpiryDate = isset($_POST['ExpiryDate']) ? $_POST['ExpiryDate'] : null;
$Barcode = isset($_POST['Barcode']) ? $_POST['Barcode'] : null;
$UpdatedBy = isset($_POST['CreatedBy']) ? trim($_POST['CreatedBy']) : '';
$Status = 1;

if (empty($itemID) || empty($QTY) || empty($Price)) {
    echo json_encode(['status' => 'error', 'message' => 'Item ID, Quantity, and Price are required fields.']);
    exit;
}

if ($noExpiration) {
    $ExpiryDate = NULL;
}

try {
    $conn->autocommit(FALSE); 
    $conn->begin_transaction();

    if ($ExpiryDate === NULL) {
        $query = "SELECT s.ID FROM stocks s
                  INNER JOIN items i ON s.ItemID = i.ID
                  WHERE s.ItemID = ? AND s.ExpiryDate IS NULL AND i.Brand = ? AND i.Model = ?";
        $stmt = $conn->prepare($query);

        $stmt->bind_param("iss", $itemID, $Brand, $Model); 

    } else {    
        $query = "SELECT s.ID FROM stocks s
                  INNER JOIN items i ON s.ItemID = i.ID
                  WHERE s.ItemID = ? AND s.ExpiryDate = ? AND i.Brand = ? AND i.Model = ?";
        $stmt = $conn->prepare($query);

        $stmt->bind_param("isss", $itemID, $ExpiryDate, $Brand, $Model); 
    }

    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($stockID);

    if ($stmt->num_rows > 0) {
        // Item exists, update quantity and prices
        if ($ExpiryDate === NULL) {
            $query = "UPDATE stocks 
                      SET QTY = QTY + ?, PriceCurrent = ?, PriceOld = ?, UpdatedBy = ?, DateTimeUpdated = NOW()
                      WHERE ItemID = ? AND ExpiryDate IS NULL";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("idsss", $QTY, $Price, $Price, $UpdatedBy, $itemID);
        } else {
            $query = "UPDATE stocks 
                      SET QTY = QTY + ?, PriceCurrent = ?, PriceOld = ?, UpdatedBy = ?, DateTimeUpdated = NOW()
                      WHERE ItemID = ? AND ExpiryDate = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("idssss", $QTY, $Price, $Price, $UpdatedBy, $itemID, $ExpiryDate);
        }

        $stmt->execute();
    } else {
        // Insert a new stock item
        $query = "INSERT INTO stocks (ItemID, QTY, PriceCurrent, PriceOld, ExpiryDate, UpdatedBy, DateTimeUpdated, Barcode)
                  VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiiisss", $itemID, $QTY, $Price, $Price, $ExpiryDate, $UpdatedBy, $Barcode);
        $stmt->execute();

        $stockID = $stmt->insert_id; 
    }

    // Insert the transaction into invtransaction table
    $transactionQuery = "INSERT INTO invtransaction (StockID, ActionType, ActionTaken, QTY, DatetimeCreated, CreatedBy)
                         VALUES (?, 'IN', 'Add Stocks', ?, NOW(), ?)";
    $stmt = $conn->prepare($transactionQuery);
    $stmt->bind_param("iis", $stockID, $QTY, $UpdatedBy);
    $stmt->execute();

    $conn->commit();

    echo json_encode(['status' => 'success', 'message' => 'Stock added/updated successfully and transaction recorded.']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    $stmt->close();
    $conn->close();
}
?>
