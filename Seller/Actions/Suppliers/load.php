<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

$page = isset($_GET['page']) ? intval($_GET['page']) : 1; 
$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%'; 

$limit = 15; 
$offset = ($page - 1) * $limit; 

$sql = "SELECT ID, `Name`, `Address`, `TIN`, 
            CASE WHEN `Status` = 1 THEN 'Active' ELSE 'Inactive' END AS `Status` 
        FROM lSuppliers
        WHERE `Name` LIKE ? OR `Address` LIKE ? OR `TIN` LIKE ?
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('sssii',  $search, $search,$search,$limit, $offset);
$stmt->execute();
$result = $stmt->get_result(); 

$suppliers = [];
while ($row = $result->fetch_assoc()) {
    $suppliers[] = $row;
}

$sqlCount = "SELECT COUNT(ID) AS total FROM lSuppliers WHERE `Name` LIKE ? OR `Address` LIKE ? OR `TIN` LIKE ? ";
$stmtCount = $conn->prepare($sqlCount);
$stmtCount->bind_param('sss', $search, $search, $search);

$stmtCount->execute();
$countResult = $stmtCount->get_result();
$totalRows = $countResult->fetch_assoc()['total'];

$totalPages = ceil($totalRows / $limit);

header('Content-Type: application/json');
echo json_encode([
    'suppliers' => $suppliers,
    'totalPages' => $totalPages,
    'currentPage' => $page,
    'totalRows' => $totalRows
]);

$conn->close();
?>
