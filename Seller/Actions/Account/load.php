<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

$page = isset($_GET['page']) ? intval($_GET['page']) : 1; 
$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%'; 

$limit = 15; 
$offset = ($page - 1) * $limit; 

$sql = "SELECT ID, FMLS_GetFullNameViaID(EmployeeID) as Fullname, Username, UserType, Email, Position, 
            CASE WHEN `Status` = 1 THEN 'Active' ELSE 'Inactive' END AS `Status` 
        FROM user_account
        WHERE FMLS_GetFullNameViaID(EmployeeID) LIKE ? OR Username LIKE ? OR usertype LIKE ? OR Email LIKE ? OR Position LIKE ? 
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ssssssi', $search, $search, $search, $search, $search, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$accounts = [];
while ($row = $result->fetch_assoc()) {
    $accounts[] = $row;
}

$sqlCount = "SELECT COUNT(ID) AS total FROM user_account WHERE FMLS_GetFullNameViaID(EmployeeID) LIKE ? OR Username LIKE ? OR usertype LIKE ? OR Email LIKE ? OR Position LIKE ?";
$stmtCount = $conn->prepare($sqlCount);
$stmtCount->bind_param('sssss', $search, $search, $search, $search, $search);
$stmtCount->execute();
$countResult = $stmtCount->get_result();
$totalRows = $countResult->fetch_assoc()['total'];

$totalPages = ceil($totalRows / $limit);

header('Content-Type: application/json');
echo json_encode([
    'accounts' => $accounts,
    'totalPages' => $totalPages,
    'currentPage' => $page,
    'totalRows' => $totalRows
]);

$conn->close();
?>
