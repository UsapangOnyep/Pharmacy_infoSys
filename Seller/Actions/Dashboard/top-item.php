<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

$sql = "SELECT 
            get_itemName_via_stockID(t1.StockID) as ItemName,
            SUM(QTY) AS QTYSold
        FROM
            invtransaction t1
        WHERE
                MONTH(t1.datetimecreated) = MONTH(CURDATE() - INTERVAL 1 MONTH)
            AND YEAR(t1.datetimecreated) = YEAR(CURDATE() - INTERVAL 1 MONTH)
        GROUP BY t1.StockID
        ORDER BY QTYSold DESC
        LIMIT 1;";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$data = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode([
    'ItemName' => $data['ItemName'] ?? 'No Data',
    'QTYSold' => $data['QTYSold'] ?? 0
]);

$conn->close();
?>
