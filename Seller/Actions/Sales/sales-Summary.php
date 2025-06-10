<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

// Get monthly sales for the current year
$sql = "SELECT 
    MONTH(datetimecreated) AS month,
    SUM(Amount) AS total
FROM salestransaction
WHERE `STATUS` = 1
AND YEAR(datetimecreated) = YEAR(CURDATE())
GROUP BY MONTH(datetimecreated)
ORDER BY MONTH(datetimecreated);";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$salesData = array_fill(1, 12, 0); // Initialize all months with 0

while ($row = $result->fetch_assoc()) {
    $salesData[(int)$row['month']] = (float)$row['total'];
}

header('Content-Type: application/json');
echo json_encode($salesData);

$conn->close();
?>
