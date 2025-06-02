<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

$sql = "SELECT 
            get_itemName_via_stockID(t1.StockID) as ItemName,
            SUM(QTY) AS QTYSold
        FROM
            invtransaction t1
        WHERE
                MONTH(t1.datetimecreated) = MONTH(CURDATE())
            AND YEAR(t1.datetimecreated) = YEAR(CURDATE())
        GROUP BY t1.StockID
        ORDER BY QTYSold DESC
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
