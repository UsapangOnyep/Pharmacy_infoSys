<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "checkShiftStatus") {
    try {
        include __DIR__ . '/../Database/connection.php';

        $userId = $_POST['user_id'] ?? null;
        if (!$userId) {
            throw new Exception("User not logged in.");
        }

        // Check if the user already has an active shift
        $checkQuery = " SELECT * 
                        FROM shifts
                        WHERE accountid = ?
                        AND `EndTime` IS NULL
                        AND `StartTime` IS NOT NULL
                        AND `StartTime` <= NOW()
                        AND `status` = 'active'
                        AND ShiftNumber = (
                            SELECT ShiftNumber 
                            FROM lShift 
                            WHERE (
                            (ShiftTimeIn < ShiftTimeOut AND CURTIME() BETWEEN ShiftTimeIn AND ShiftTimeOut)
                            OR
                            (ShiftTimeIn > ShiftTimeOut AND (CURTIME() >= ShiftTimeIn OR CURTIME() <= ShiftTimeOut))
                            )
                            LIMIT 1
                        )
                        AND DATEDIFF(CURDATE(), DATE(StartTime)) = 0 
                        ORDER BY ID DESC
                        LIMIT 1;";
        
        $checkStmt = $conn->prepare($checkQuery);
        if (!$checkStmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        $checkStmt->bind_param("i", $userId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $shift = $checkResult->fetch_assoc();
            echo json_encode([
                "success" => false,
                "message" => "User already has an active shift.",
                "redirectUrl" => "../Seller/pos.php",
                "shiftData" => [
                    "id" => $shift['ShiftID'] ?? null,
                    "accountId" => $shift['AccountID'] ?? null,
                    "startTime" => $shift['StartTime'] ?? null,
                    "status" => $shift['Status'] ?? null,
                    "shiftNumber" => $shift['ShiftNumber'] ?? null
                ]
            ]);
            exit;
        } else {
            echo json_encode([
                "success" => true,
                "message" => "No active shift found for this user."
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "An error occurred: " . $e->getMessage()
        ]);
    }
}
?>
