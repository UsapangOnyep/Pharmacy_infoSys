<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); 

// Early return if not POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Ensure POST data is set
$ID = isset($_POST['ID']) ? trim($_POST['ID']) : '';
$newPassword = 'a123bc'; // Default password for restoration
$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
$UpdatedBy = 'Admin'; 

// Validate required fields
if (empty($ID)) {
    echo json_encode(['status' => 'error', 'message' => 'Account ID is required.']);
    exit;
}

try {
    $query = "UPDATE user_account SET UpdatedBy = ?, DateUpdated = NOW(), `Password` = ? WHERE ID = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $UpdatedBy, $hashedPassword, $ID);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Account password reset successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to reset account password.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    $stmt->close(); 
    $conn->close();
}
?>
