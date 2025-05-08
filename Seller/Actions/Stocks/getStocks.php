<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

$sql = "SELECT 
    s.Barcode,
    i.ItemName, 
    i.ItemDesc, 
    s.ID, 
    s.QTY, 
    s.PriceCurrent, 
    CASE 
        WHEN s.ExpiryDate IS NULL THEN 'No Expiry' 
        ELSE s.ExpiryDate 
    END AS ExpiryDate
FROM items i
INNER JOIN stocks s ON s.ItemID = i.ID
WHERE s.QTY > 0";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$stocks = [];
while ($row = $result->fetch_assoc()) {
    $stocks[] = $row;
}

header('Content-Type: application/json');
echo json_encode([
    'stocks' => $stocks  // Ensure the response is wrapped in the 'stocks' key
]);

$conn->close();
?>
