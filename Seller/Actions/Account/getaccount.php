<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$accountID = isset($_POST['ID']) ? intval($_POST['ID']) : 0;
if ($accountID <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid account ID.']);
    exit;
}

$sql = "SELECT ID, FMLS_GetFullNameViaID(EmployeeID) as FullName, Username, UserType, Email, Position, Status 
        FROM user_account 
        WHERE ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $accountID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $account = $result->fetch_assoc();
    echo json_encode(['status' => 'success', 'account' => $account]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Account not found.']);
}

$conn->close();
?>