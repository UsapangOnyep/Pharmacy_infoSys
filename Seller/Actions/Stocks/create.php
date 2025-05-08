<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$itemIDs = $_POST['ItemName'] ?? [];
$Barcodes = $_POST['Barcode'] ?? [];
$QTYs = $_POST['QTY'] ?? [];
$Prices = $_POST['Price'] ?? [];
$ExpiryDates = $_POST['ExpiryDate'] ?? [];
$noExpirationChecks = $_POST['noExpiration'] ?? [];
$UpdatedBy = $_POST['CreatedBy'] ?? '';
$Status = 1;

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$conn->autocommit(FALSE);
$conn->begin_transaction();

try {
    foreach ($itemIDs as $index => $itemID) {
        $itemID = intval($itemID);
        $QTY = intval($QTYs[$index] ?? 0);
        $Price = floatval($Prices[$index] ?? 0);
        $ExpiryDate = $ExpiryDates[$index] ?? null;
        $Barcode = trim($Barcodes[$index] ?? '');
        $noExpiration = isset($noExpirationChecks[$index]);

        if (!$itemID || $QTY < 1 || $Price < 0 || !$Barcode) {
            throw new Exception("Invalid Quantity, Item, Price, or Barcode.");
        }

        if ($noExpiration) {
            $ExpiryDate = null;
        }

        // ✅ Check if barcode exists and is associated with a DIFFERENT item or expiry date
        $barcodeCheckQuery = "SELECT ID, ItemID, ExpiryDate FROM stocks WHERE Barcode = ?";
        $barcodeStmt = $conn->prepare($barcodeCheckQuery);
        $barcodeStmt->bind_param("s", $Barcode);
        $barcodeStmt->execute();
        $barcodeStmt->store_result();

        if ($barcodeStmt->num_rows > 0) {
            $barcodeStmt->bind_result($existingID, $existingItemID, $existingExpiryDate);
            $barcodeStmt->fetch();

            $expiryMismatch = false;

            if ($ExpiryDate === null && $existingExpiryDate !== null) {
                $expiryMismatch = true;
            } elseif ($ExpiryDate !== null && $existingExpiryDate !== null && $ExpiryDate !== $existingExpiryDate) {
                $expiryMismatch = true;
            }

            if ($itemID !== $existingItemID || $expiryMismatch) {
                throw new Exception("Barcode '{$Barcode}' already exists but with different item or expiry date.");
            }
        }

        // ✅ Now safe to proceed (insert/update)
        if ($ExpiryDate === null) {
            $query = "SELECT ID FROM stocks WHERE ItemID = ? AND Barcode = ? AND ExpiryDate IS NULL";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("is", $itemID, $Barcode);
        } else {
            $query = "SELECT ID FROM stocks WHERE ItemID = ? AND Barcode = ? AND ExpiryDate = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iss", $itemID, $Barcode, $ExpiryDate);
        }

        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($stockID);
            $stmt->fetch();

            $updateQuery = "UPDATE stocks 
                            SET QTY = QTY + ?, PriceCurrent = ?, PriceOld = ?, UpdatedBy = ?, DateTimeUpdated = NOW()
                            WHERE ID = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("idssi", $QTY, $Price, $Price, $UpdatedBy, $stockID);
            $updateStmt->execute();
        } else {
            $insertQuery = "INSERT INTO stocks (ItemID, QTY, PriceCurrent, PriceOld, ExpiryDate, UpdatedBy, DateTimeUpdated, Barcode)
                            VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("iiiisss", $itemID, $QTY, $Price, $Price, $ExpiryDate, $UpdatedBy, $Barcode);
            $insertStmt->execute();
            $stockID = $insertStmt->insert_id;
        }

        $transactionQuery = "INSERT INTO invtransaction (StockID, ActionType, ActionTaken, QTY, DatetimeCreated, CreatedBy)
                             VALUES (?, 'IN', 'Add Stocks', ?, NOW(), ?)";
        $transactionStmt = $conn->prepare($transactionQuery);
        $transactionStmt->bind_param("iis", $stockID, $QTY, $UpdatedBy);
        $transactionStmt->execute();
    }

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Stock added/updated successfully.']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($barcodeStmt)) $barcodeStmt->close();
    $conn->close();
}
?>
