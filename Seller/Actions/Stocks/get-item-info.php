<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

if (isset($_GET['id'])) {
    $itemID = $_GET['id'];

    $sql = "SELECT 
                i.Category, 
                i.Brand, 
                i.Model, 
                i.ItemDesc,
                s.ExpiryDate 
            FROM items i
            INNER JOIN stocks s ON s.ItemID = i.ID
            WHERE s.ID = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $itemID);
        $stmt->execute();
        $stmt->bind_result($category, $brand, $model, $itemDesc, $expiryDate);

        if ($stmt->fetch()) {
            echo json_encode([
                'ItemDesc' => $itemDesc,
                'Category' => $category,
                'Brand' => $brand,
                'Model' => $model,
                'ExpiryDate' => $expiryDate
            ]);
        } else {
            echo json_encode(['error' => 'Item not found']);
        }

        $stmt->close();
    } else {
        echo json_encode(['error' => 'Failed to prepare the query']);
    }
} else {
    echo json_encode(['error' => 'Item ID is missing']);
}

$conn->close();
?>
