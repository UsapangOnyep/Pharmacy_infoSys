<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

// Fetch items with status = 1 and who are not already in the user_account table
$sql = "SELECT ID, ItemName, ItemDesc, Category, Brand, Model FROM items WHERE `Status` = 1;";

$result = $conn->query($sql);

// Initialize an array to store items
$items = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'ID' => $row['ID'],
            'ItemName' => $row['ItemName'],
            'ItemDesc' => $row['ItemDesc'],
            'Category' => $row['Category'],
            'Brand' => $row['Brand'],
            'Model' => $row['Model']
        ];
    }
}

// Return employee data as a JSON response
header('Content-Type: application/json');
echo json_encode(['items' => $items]);

$conn->close();
?>