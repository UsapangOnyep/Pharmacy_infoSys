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
$ID = isset($_POST['employeeID']) ? trim($_POST['employeeID']) : '';
$Fname = isset($_POST['employeeFName']) ? trim($_POST['employeeFName']) : '';
$MName = isset($_POST['employeeMName']) ? trim($_POST['employeeMName']) : '';
$LName = isset($_POST['employeeLName']) ? trim($_POST['employeeLName']) : '';
$Suffix = isset($_POST['employeeSuffix']) ? trim($_POST['employeeSuffix']) : '';
$UpdatedBy = isset($_POST['CreatedBy']) ? trim($_POST['CreatedBy']) : '';

// Validate required fields
if (empty($Fname) || empty($LName)) {
    echo json_encode(['status' => 'error', 'message' => 'First name and last name are required.']);
    exit;
}

try {
    $query = "UPDATE lemployee 
              SET Fname = ?, MName = ?, LName = ?, Suffix = ?, UpdatedBy = ?, DateUpdated = NOW() 
              WHERE ID = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", $Fname, $MName, $LName, $Suffix, $UpdatedBy, $ID);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Employee updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update employee.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    $stmt->close();
    $conn->close();
}
?>
