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
$UpdatedBy = 'Admin'; 

// Validate required fields
if (empty($ID)) {
    echo json_encode(['status' => 'error', 'message' => 'Employee ID is required.']);
    exit;
}

try {
    $query = "UPDATE lemployee SET UpdatedBy = ?, DateUpdated = NOW(), `Status` = 0 WHERE ID = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $UpdatedBy, $ID); 

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Employee removed successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to remove employee.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    $stmt->close(); 
    $conn->close();
}
?>
