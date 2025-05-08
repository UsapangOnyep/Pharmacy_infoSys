<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

header('Content-Type: application/json'); 

// Ensure POST data is set
$ID = isset($_POST['ID']) ? trim($_POST['ID']) : '';

if (empty($ID)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid employee ID.']);
    exit;
}

try {
    $query = "SELECT ID, Fname, MName, Lname, Suffix FROM lemployee WHERE ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $ID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
        echo json_encode(['status' => 'success', 'employee' => $employee]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Employee not found.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    $stmt->close();
    $conn->close();
}
?>
