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
$Name = isset($_POST['CategoryName']) ? trim($_POST['CategoryName']) : '';
$CreatedBy = isset($_POST['CreatedBy']) ? trim($_POST['CreatedBy']) : '';
$Status = 1; 

// Validate required fields
if (empty($Name)) {
    echo json_encode(['status' => 'error', 'message' => 'Category is required.']);
    exit;
}

try {
    $checkUsernameQuery = "SELECT COUNT(ID) FROM lCategory WHERE `Description` = ?";
    $stmt = $conn->prepare($checkUsernameQuery);
    $stmt->bind_param("s", $Name);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Category already exists.']);
        exit;
    }

    $query = "INSERT INTO lCategory (`Description`, CreatedBy, DateCreated, `Status`) 
              VALUES (?, ?, NOW(), ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $Name, $CreatedBy, $Status); // Add $CreatedBy

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Category added successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add category.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    $stmt->close(); 
    $conn->close();
}
?>
