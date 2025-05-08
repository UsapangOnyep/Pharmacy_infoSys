<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$stockID = isset($_POST['editID']) ? $_POST['editID'] : null;
$Price = isset($_POST['editNewPrice']) ? $_POST['editNewPrice'] : null;
$UpdatedBy = isset($_POST['CreatedBy']) ? trim($_POST['CreatedBy']) : '';
$Status = 1;

if (empty($Price)) {
    echo json_encode(['status' => 'error', 'message' => 'New Price is required fields.']);
    exit;
}

try {
    $conn->autocommit(FALSE);
    $conn->begin_transaction();


    $query = "UPDATE stocks 
    SET PriceCurrent = ?, PriceOld = PriceCurrent, UpdatedBy = ?, DateTimeUpdated = NOW()
    WHERE ID = ?;";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $Price, $UpdatedBy, $stockID);

    $stmt->execute();

    // Commit the transaction
    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Price updated successfully.',
        'id' => $stockID,
        'newPrice' => $Price
    ]);
    
} catch (Exception $e) {
    $conn->rollback(); // Rollback the transaction in case of error
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
} finally { 
    $stmt->close();
    $conn->close();
}
?>