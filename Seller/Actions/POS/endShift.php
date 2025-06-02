<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Database/connection.php'; 

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$oneThousand = isset($data['txt1k']) ? (int) $data['txt1k'] : 0;
$fiveHundred = isset($data['txt5H']) ? (int) $data['txt5H'] : 0;
$twoHundred = isset($data['txt2H']) ? (int) $data['txt2H'] : 0;
$oneHundred = isset($data['txt1H']) ? (int) $data['txt1H'] : 0;
$fifty = isset($data['txt50']) ? (int) $data['txt50'] : 0;  
$twenty = isset($data['txt20']) ? (int) $data['txt20'] : 0;
$ten = isset($data['txt10']) ? (int) $data['txt10'] : 0;
$five = isset($data['txt5']) ? (int) $data['txt5'] : 0;
$one = isset($data['txt1']) ? (int) $data['txt1'] : 0;
$twentyFiveCents = isset($data['txt25c']) ? (int) $data['txt25c'] : 0;

$CreatedBy = isset($data['CreatedBy']) ? trim($data['CreatedBy']) : '';

$ShiftNumber = isset($data['ShiftNumber']) ? (int) $data['ShiftNumber'] : 0; 
// $ShiftDate = date("Y-m-d"); 

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("
        INSERT INTO endshiftreport (AccountID, `1000`, `500`, `200`, `100`, `50`, `20`, `10`, `5`, `1`, `25c`, ShiftNumber, ShiftDate, DateTimeCreated) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");

    $stmt->bind_param("iiiiiiiiiiii", 
        $CreatedBy, $oneThousand, $fiveHundred, $twoHundred, $oneHundred, 
        $fifty, $twenty, $ten, $five, $one, $twentyFiveCents, $ShiftNumber //, $ShiftDate
    );

    $stmt->execute();

    // update the shifts table to set the end time and status
    $updateStmt = $conn->prepare("
        UPDATE shifts 
        SET EndTime = NOW(), Status = 'completed' 
        WHERE AccountID = ? AND ShiftNumber = ? AND EndTime IS NULL");
    $updateStmt->bind_param("ii", $CreatedBy, $ShiftNumber);
    $updateStmt->execute();
    if ($updateStmt->affected_rows === 0) {
        throw new Exception("No active shift found for the given account and shift number.");
    }
    $updateStmt->close();
    $stmt->close();

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'End of shift report saved successfully.']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Transaction failed.', 'error' => $e->getMessage()]);
}

$conn->close();
?>
