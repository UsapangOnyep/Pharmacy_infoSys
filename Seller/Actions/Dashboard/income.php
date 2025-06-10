<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

$sql = "SELECT 
    SUM(CASE WHEN DATE(datetimecreated) = CURDATE() THEN Amount ELSE 0 END) AS totalSalesToday,
    SUM(CASE WHEN DATE(datetimecreated) BETWEEN CURDATE() - INTERVAL 6 DAY AND CURDATE() THEN Amount ELSE 0 END) AS totalSalesSevenDays,
    SUM(CASE WHEN MONTH(datetimecreated) = MONTH(CURDATE()) AND YEAR(datetimecreated) = YEAR(CURDATE()) THEN Amount ELSE 0 END) AS totalSalesThisMonth
FROM salestransaction
WHERE `STATUS` = 1;";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$data = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode([
    'totalSalesToday' => $data['totalSalesToday'] ?? 0,
    'totalSalesSevenDays' => $data['totalSalesSevenDays'] ?? 0,
    'totalSalesThisMonth' => $data['totalSalesThisMonth'] ?? 0
]);

$conn->close();
?>
