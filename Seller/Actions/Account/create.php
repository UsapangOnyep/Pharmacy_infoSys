<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Database/connection.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$EmpID = isset($_POST['accountName']) ? trim($_POST['accountName']) : '';  
$Username = isset($_POST['accountUsername']) ? trim($_POST['accountUsername']) : '';  
$Email = isset($_POST['accountEmail']) ? trim($_POST['accountEmail']) : '';  
$Position = isset($_POST['accountPosition']) ? trim($_POST['accountPosition']) : '';  
$Password = isset($_POST['accountPassword']) ? trim($_POST['accountPassword']) : '';  
$ConfirmPassword = isset($_POST['accountConfirmPassword']) ? trim($_POST['accountConfirmPassword']) : '';  
$AccountType = isset($_POST['accountType']) ? trim($_POST['accountType']) : '';  

session_start();
$CreatedBy = $_SESSION['user_id'] ?? 1;  
$Status = 1;

if (empty($EmpID) || empty($Username) || empty($Email) || empty($Position) || empty($Password) || empty($ConfirmPassword) || empty($AccountType)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

if ($Password !== $ConfirmPassword) {
    echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
    exit;
}

try {
    $checkUsernameQuery = "SELECT COUNT(*) FROM user_account WHERE Username = ?";
    $stmt = $conn->prepare($checkUsernameQuery);
    $stmt->bind_param("s", $Username);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username already exists.']);
        exit;
    }

    $hashedPassword = password_hash($Password, PASSWORD_BCRYPT);

    $query = "INSERT INTO user_account (EmployeeID, Username, `Password`, Email, Position, UserType, CreatedBy, DateCreated, `Status`) 
              VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("issssssi", $EmpID, $Username, $hashedPassword, $Email, $Position, $AccountType, $CreatedBy, $Status);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Account created successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create account.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    $stmt->close(); 
    $conn->close();
}
?>
