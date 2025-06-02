<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

$sql = "SELECT 
            get_itemname_via_stockID(itemid) as ItemName,
            DATE_FORMAT(ExpiryDate, '%b %d %Y') as ExpiryDate,
            QTY as Quantity
        FROM
            stocks
        WHERE
            DATEDIFF(CURDATE(), ExpiryDate) > 0 
            AND QTY > 0
        ORDER BY ExpiryDate DESC
        LIMIT 10;";

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
