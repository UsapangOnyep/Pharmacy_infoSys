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
$ID = isset($_POST['editID']) ? trim($_POST['editID']) : '';
$Name = isset($_POST['editsupplierName']) ? trim($_POST['editsupplierName']) : '';
$Address = isset($_POST['editsupplierAddress']) ? trim($_POST['editsupplierAddress']) : '';
$TIN = isset($_POST['editsupplierTIN']) ? trim($_POST['editsupplierTIN']) : '';
$UpdatedBy = isset($_POST['CreatedBy']) ? trim($_POST['CreatedBy']) : '';

// Validate required fields
if (empty($Name) || empty($Address) || empty($TIN)) {
    echo json_encode(['status' => 'error', 'message' => 'All feilds are required.']);
    exit;
}

try {
    $query = "UPDATE lSuppliers 
              SET  `Name` = ?, `Address` = ?, `TIN` = ?, UpdatedBy = ?, DateUpdated = NOW() 
              WHERE ID = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $Name, $Address, $TIN, $UpdatedBy, $ID); 
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Supplier updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update supplier.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    $stmt->close();
    $conn->close();
}
?>
