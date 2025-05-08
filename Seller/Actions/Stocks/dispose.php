<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the item IDs and quantities to dispose
    $itemIDs = $_POST['itemID'] ?? [];
    $qtyToDispose = $_POST['qtyToDispose'] ?? [];

    // Check if the necessary data is provided
    if (empty($itemIDs) || empty($qtyToDispose)) {
        echo json_encode(['status' => 'error', 'message' => 'No data to process.']);
        exit;
    }

    $conn->begin_transaction();

    try {
        $reason = $_POST['Reason'] ?? '';
        $CreatedBy = $_POST['CreatedBy'] ?? 'System'; 
        if (empty($reason)) {
            throw new Exception("Disposal reason is required.");
        }

        $LogDisposal = "INSERT INTO tbl_disposed (Reason, DisposedBy, DTDisposed) 
        VALUES (?, ?, NOW())";
        $LogDisposalStmt = $conn->prepare($LogDisposal);
        $LogDisposalStmt->bind_param('ss', $reason, $CreatedBy);
        $LogDisposalStmt->execute();

        if ($LogDisposalStmt->affected_rows === 0) {
            throw new Exception("Failed to log disposal reason.");
        }
        
        // Get the last inserted ID for the disposal log
        $disposalID = $conn->insert_id;

        foreach ($itemIDs as $index => $itemID) {
            $quantity = $qtyToDispose[$index];

            $itemNameQuery = "SELECT i.itemName FROM stocks s INNER JOIN items i ON i.ID = s.ItemID WHERE s.ID = ?";
            $itemNameStmt = $conn->prepare($itemNameQuery);
            $itemNameStmt->bind_param('i', $itemID);
            $itemNameStmt->execute();
            $itemNameStmt->store_result();
            $itemNameStmt->bind_result($itemName);
            $itemNameStmt->fetch();

            if ($itemNameStmt->num_rows === 0) {
                // If no item found for the given itemID, return an error
                throw new Exception("Item not found for item ID: $itemID");
            }

            // Fetch the current stock quantity from the stocks table
            $query = "SELECT QTY FROM stocks WHERE ID = ? AND QTY >= ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ii', $itemID, $quantity);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($currentQty);
            $stmt->fetch();

            if ($stmt->num_rows === 0) {
                // If no sufficient stock is available, return an error
                throw new Exception("Insufficient stock for item $itemName (ID: $itemID)");
            }

            // Update the stock quantity in the stocks table
            $updateQuery = "UPDATE stocks SET QTY = QTY - ? WHERE ID = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param('ii', $quantity, $itemID);
            $updateStmt->execute();

            if ($updateStmt->affected_rows === 0) {
                throw new Exception("Failed to update stock for item $itemName (ID: $itemID)");
            }

            // Insert a record into the invtransaction table to log the disposal action
            $actionQuery = "INSERT INTO invtransaction (StockID, ActionType, ActionTaken, QTY, DatetimeCreated, CreatedBy, ReferenceCode) 
                            VALUES (?, 'OUT', 'Dispose', ?, NOW(), ?, ?)";
            $actionStmt = $conn->prepare($actionQuery);
            $actionStmt->bind_param('iisi', $itemID, $quantity, $CreatedBy, $disposalID);
            $actionStmt->execute();

            if ($actionStmt->affected_rows === 0) {
                throw new Exception("Failed to record transaction for item $itemName (ID: $itemID) or row number " . ($index + 1));
            }


            // Insert a record into the tbl_disposed_items table to log the disposed items
            $logQuery = "INSERT INTO tbl_disposeditems (disposedID, stockID, QTY) VALUES (?, ?, ?)";
            $logStmt = $conn->prepare($logQuery);
            $logStmt->bind_param('iii', $disposalID, $itemID, $quantity);
            $logStmt->execute();

            if ($logStmt->affected_rows === 0) {
                throw new Exception("Failed to log disposed item $itemName (ID: $itemID)");
            }
        }

        // Commit the transaction
        $conn->commit();

        // Respond with success message
        echo json_encode(['status' => 'success', 'message' => 'Stock disposed successfully!']);

    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $conn->rollback();
        
        // Respond with an error message
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

    // Close the database connection
    $conn->close();
}
?>
