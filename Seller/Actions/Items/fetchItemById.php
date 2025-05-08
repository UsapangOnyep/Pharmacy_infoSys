<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

$id = $_GET['id'];

// Fetch item by ID
$sql = "SELECT ItemDesc, Category, Brand, Model FROM items WHERE ID = ? AND Status = 1;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

$item = [];

if ($result->num_rows > 0) {
    $item = $result->fetch_assoc();
}

// Return item data as a JSON response
header('Content-Type: application/json');
echo json_encode($item);

$conn->close();
?>