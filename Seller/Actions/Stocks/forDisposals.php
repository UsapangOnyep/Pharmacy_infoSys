<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

$response = array();

$sql = "SELECT 
            s.ID, 
            i.ItemName, 
            s.QTY
        FROM items i
        INNER JOIN stocks s ON s.ItemID = i.ID
        WHERE expiryDate < CURDATE() AND s.QTY > 0";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $items = array();
    while ($row = $result->fetch_assoc()) {
        $items[] = array(
            'ID' => $row['ID'],
            'ItemName' => $row['ItemName'],
            'QTY' => $row['QTY']
        );
    }

    $response['status'] = 'success';
    $response['items'] = $items;
} else {
    $response['status'] = 'error';
    $response['message'] = 'No expired items found or no stock available for disposal.';
}

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>
