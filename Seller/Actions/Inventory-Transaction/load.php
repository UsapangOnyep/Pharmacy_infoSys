<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

$page = isset($_GET['page']) ? intval($_GET['page']) : 1; 
$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%'; 

$limit = 15; 
$offset = ($page - 1) * $limit; 

$sql = "SELECT 
            t1.ID,
            t1.DateTimeCreated AS TransactionDate,
            t1.ActionTaken,
            t1.QTY,
            t3.ItemName,
            t3.ItemDesc,
            t3.Category,
            t3.Brand,
            t3.Model,
            t2.ExpiryDate
        FROM
            invtransaction t1
            INNER JOIN stocks t2 ON t1.StockID = t2.ID
            INNER JOIN items t3 ON t2.ItemID = t3.ID
        WHERE t3.Category LIKE ? OR t3.Brand LIKE ? OR t3.Model LIKE ? OR t3.ItemDesc LIKE ? OR t3.ItemName LIKE ? OR t1.ActionTaken LIKE ?          
        ORDER BY t1.id DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('sssssssi', $search, $search, $search, $search, $search, $search, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

$sqlCount = "SELECT COUNT(t1.ID) AS total FROM invtransaction t1
                INNER JOIN stocks t2 ON t1.StockID = t2.ID
                INNER JOIN items t3 ON t2.ItemID = t3.ID
            WHERE t3.Category LIKE ? OR t3.Brand LIKE ? OR t3.Model LIKE ? OR t3.ItemDesc LIKE ? OR t3.ItemName LIKE ? OR t1.ActionTaken LIKE ?";
$stmtCount = $conn->prepare($sqlCount);
$stmtCount->bind_param('ssssss', $search, $search, $search, $search, $search, $search);
$stmtCount->execute();
$countResult = $stmtCount->get_result();
$totalRows = $countResult->fetch_assoc()['total'];

$totalPages = ceil($totalRows / $limit);

header('Content-Type: application/json');
echo json_encode([
    'transactions' => $transactions,
    'totalPages' => $totalPages,
    'currentPage' => $page,
    'totalRows' => $totalRows
]);

$conn->close();
?>
