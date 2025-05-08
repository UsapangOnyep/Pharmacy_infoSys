<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

$page = isset($_GET['page']) ? intval($_GET['page']) : 1; 
$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%'; 

$limit = 15; 
$offset = ($page - 1) * $limit; 

$sql = "SELECT ID, `Description`, 
            CASE WHEN `Status` = 1 THEN 'Active' ELSE 'Inactive' END AS `Status` 
        FROM lcategory
        WHERE `Description` LIKE ?
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('sii',  $search, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result(); 

$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

$sqlCount = "SELECT COUNT(ID) AS total FROM lcategory WHERE `Description` LIKE ? ";
$stmtCount = $conn->prepare($sqlCount);
$stmtCount->bind_param('s', $search);
$stmtCount->execute();
$countResult = $stmtCount->get_result();
$totalRows = $countResult->fetch_assoc()['total'];

$totalPages = ceil($totalRows / $limit);

header('Content-Type: application/json');
echo json_encode([
    'categories' => $categories,
    'totalPages' => $totalPages,
    'currentPage' => $page,
    'totalRows' => $totalRows
]);

$conn->close();
?>
