<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

// Get daily sales for the last 7 days (including today)
$sql = "SELECT 
    DATE(datetimecreated) AS sale_date,
    SUM(Amount) AS total
FROM salestransaction
WHERE `STATUS` = 1
  AND DATE(datetimecreated) BETWEEN CURDATE() - INTERVAL 6 DAY AND CURDATE()
GROUP BY DATE(datetimecreated)
ORDER BY DATE(datetimecreated);";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$salesData = [];

// Initialize the last 7 days with 0
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $salesData[$date] = 0;
}

// Fill in actual totals
while ($row = $result->fetch_assoc()) {
    $salesData[$row['sale_date']] = (float)$row['total'];
}

header('Content-Type: application/json');
echo json_encode($salesData);

$conn->close();
?>
