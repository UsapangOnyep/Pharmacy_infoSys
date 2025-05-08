<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

$limit = 15;
$offset = ($page - 1) * $limit;

$sql = "SELECT 
    IFNULL(s.Barcode, '') AS Barcode,
    i.Category, 
    i.Brand, 
    i.Model, 
    i.ItemName, 
    i.ItemDesc, 
    i.ReorderLevel, 
    i.ItemPath, 
    s.ID, 
    s.QTY, 
    s.PriceCurrent, 
    CASE 
        WHEN s.ExpiryDate IS NULL THEN 'No Expiry' 
        ELSE s.ExpiryDate 
    END AS ExpiryDate
FROM items i
INNER JOIN stocks s ON s.ItemID = i.ID
        WHERE s.Barcode LIKE ? OR Category LIKE ? OR Brand LIKE ? OR Model LIKE ? OR ItemDesc LIKE ? OR ItemName LIKE ? 
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ssssssii', $search,  $search, $search, $search, $search, $search, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$stocks = [];
while ($row = $result->fetch_assoc()) {
    $stocks[] = $row;
}

$sqlCount = "SELECT COUNT(s.ID) AS total FROM items i INNER JOIN stocks s ON s.ItemID = i.ID WHERE s.Barcode LIKE ? OR i.Category LIKE ? OR i.Brand LIKE ? OR i.Model LIKE ? OR i.ItemDesc LIKE ? OR i.ItemName LIKE ?";
$stmtCount = $conn->prepare($sqlCount);
$stmtCount->bind_param('ssssss', $search, $search, $search, $search, $search, $search);
$stmtCount->execute();
$countResult = $stmtCount->get_result();
$totalRows = $countResult->fetch_assoc()['total'];

$totalPages = ceil($totalRows / $limit);

header('Content-Type: application/json');
echo json_encode([
    'stocks' => $stocks,
    'totalPages' => $totalPages,
    'currentPage' => $page,
    'totalRows' => $totalRows
]);

$conn->close();
?>