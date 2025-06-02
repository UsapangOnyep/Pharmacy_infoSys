<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

$sql = "SELECT 
            t1.ItemName, t1.ReorderLevel, SUM(t2.QTY) AS CurStock
        FROM
            items t1
                INNER JOIN
            Stocks t2 ON t2.ItemID = t1.ID
        GROUP BY
            t1.ItemName,
            t1.ReorderLevel
        HAVING
            CurStock < t1.ReorderLevel
        ORDER BY
            CurStock ASC
        LIMIT 5;";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

header('Content-Type: application/json');
echo json_encode($rows);

$conn->close();
?>
