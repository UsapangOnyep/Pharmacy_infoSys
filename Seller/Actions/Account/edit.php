<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $accountID = $_POST['accountID'];
        $email = $_POST['accountEmail'];
        $position = $_POST['accountPosition'];
        $accountType = $_POST['accountType'];
        $accountStatus = $_POST['accountStatus'];

        // Validate input
        if (empty($accountID) || empty($email) || empty($position) || empty($accountType) || !isset($accountStatus)) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
            exit();
        }

        // Prepare update statement
        $sql = "UPDATE user_account SET Email = ?, Position = ?, UserType = ?, Status = ? WHERE ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssii", $email, $position, $accountType, $accountStatus, $accountID);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Account updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update account.']);
        }

        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'An error occurred.']);
        error_log($e->getMessage());
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
