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
$Name = isset($_POST['supplierName']) ? trim($_POST['supplierName']) : '';
$Address = isset($_POST['supplierAddress']) ? trim($_POST['supplierAddress']) : '';
$TIN = isset($_POST['supplierTIN']) ? trim($_POST['supplierTIN']) : '';
$CreatedBy = isset($_POST['CreatedBy']) ? trim($_POST['CreatedBy']) : '';
$Status = 1; 

// Validate required fields
if (empty($Name) || empty($Address) || empty($TIN)) {
    echo json_encode(['status' => 'error', 'message' => 'All feilds are required.']);
    exit;
}

try {
    $checkUsernameQuery = "SELECT COUNT(ID) FROM lSuppliers WHERE `Name` = ? AND `Address` = ? AND `TIN` = ?";
    $stmt = $conn->prepare($checkUsernameQuery);
    $stmt->bind_param("sss", $Name, $Address, $TIN);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Supplier already exists.']);
        exit;
    }

    $query = "INSERT INTO lSuppliers (`Name`, `Address`, `TIN`, CreatedBy, DateCreated, `Status`) 
              VALUES (?, ?, ?, ?, NOW(), ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $Name, $Address, $TIN, $CreatedBy, $Status); // Add $CreatedBy

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Supplier added successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add supplier.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    $stmt->close(); 
    $conn->close();
}
?>
