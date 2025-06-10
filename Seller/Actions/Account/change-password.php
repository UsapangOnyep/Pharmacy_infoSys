<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Read JSON input
$input = json_decode(file_get_contents("php://input"), true);

// Extract input fields
$ID = isset($input['UID']) ? trim($input['UID']) : '';
$currentPassword = isset($input['currentPassword']) ? trim($input['currentPassword']) : '';
$newPassword = isset($input['newPassword']) ? trim($input['newPassword']) : '';

if (empty($ID) || empty($currentPassword) || empty($newPassword)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
    exit;
}

try {
    // Get current hashed password from DB
    $stmt = $conn->prepare("SELECT `Password` FROM user_account WHERE ID = ?");
    $stmt->bind_param("i", $ID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        exit;
    }

    $stmt->bind_result($storedHashedPassword);
    $stmt->fetch();

    if (!password_verify($currentPassword, $storedHashedPassword)) {
        echo json_encode(['status' => 'error', 'message' => 'Incorrect current password.']);
        exit;
    }

    $stmt->close();

    // Hash new password and update
    $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE user_account SET `Password` = ?, UpdatedBy = ?, DateUpdated = NOW() WHERE ID = ?");
    $stmt->bind_param("ssi", $hashedNewPassword, $ID, $ID);

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
