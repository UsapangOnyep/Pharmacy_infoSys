<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

$page = isset($_GET['page']) ? intval($_GET['page']) : 1; 
$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%'; 

$limit = 15; 
$offset = ($page - 1) * $limit; 

$sql = "SELECT id, Category, Brand, Model, ItemName, ItemDesc, ReorderLevel, ItemPath, CASE WHEN `Status` = 1 THEN 'Active' ELSE 'Inactive' END AS `Status`  
        FROM items
        WHERE Category LIKE ? OR Brand LIKE ? OR Model LIKE ? OR ItemDesc LIKE ? OR ItemName LIKE ? 
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ssssssi', $search, $search, $search, $search, $search, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

$sqlCount = "SELECT COUNT(ID) AS total FROM items WHERE Category LIKE ? OR Brand LIKE ? OR Model LIKE ? OR ItemDesc LIKE ? OR ItemName LIKE ?";
$stmtCount = $conn->prepare($sqlCount);
$stmtCount->bind_param('sssss', $search, $search, $search, $search, $search);
$stmtCount->execute();
$countResult = $stmtCount->get_result();
$totalRows = $countResult->fetch_assoc()['total'];

$totalPages = ceil($totalRows / $limit);

header('Content-Type: application/json');
echo json_encode([
    'items' => $items,
    'totalPages' => $totalPages,
    'currentPage' => $page,
    'totalRows' => $totalRows
]);

$conn->close();
?>
