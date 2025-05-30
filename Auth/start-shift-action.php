<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "startShift") {
    try {
        include __DIR__ . '/../Database/connection.php';

        $conn->begin_transaction();

        $userId = $_POST['user_id'] ?? null;
        if (!$userId) {
            throw new Exception("User not logged in.");
        }

        // Get current shift ID based on current time
        $shiftQuery = "
            SELECT ShiftNumber as ID 
            FROM lShift 
            WHERE (
                ShiftTimeIn < ShiftTimeOut AND CURTIME() BETWEEN ShiftTimeIn AND ShiftTimeOut
            ) OR (
                ShiftTimeIn > ShiftTimeOut AND (
                    CURTIME() >= ShiftTimeIn OR CURTIME() <= ShiftTimeOut
                )
            )
            LIMIT 1";

        $shiftResult = $conn->query($shiftQuery);
        if (!$shiftResult || $shiftResult->num_rows === 0) {
            throw new Exception("Unable to determine current shift number.");
        }

        $shiftRow = $shiftResult->fetch_assoc();
        $shiftNumber = $shiftRow['ID'];

        // Insert the new shift record
        $query = "INSERT INTO shifts (`AccountID`, `StartTime`, `EndTime`, `Status`, `ShiftNumber`) VALUES (?, NOW(), NULL, 'active', ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        $stmt->bind_param("ii", $userId, $shiftNumber);
        if (!$stmt->execute()) {
            throw new Exception("Failed to start shift: " . $stmt->error);
        }

        $shiftId = $stmt->insert_id;

        $stmt->close();
        $conn->commit();
        $conn->close();

        echo json_encode([
            "success" => true,
            "message" => "Shift started successfully.",
            "redirectUrl" => "../Seller/pos.php",
            "shiftData" => [
                "id" => $shiftId,
                "accountId" => $userId,
                "startTime" => date("Y-m-d H:i:s"),
                "status" => "active",
                "shiftNumber" => $shiftNumber
            ]
        ]);
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            "success" => false,
            "message" => $e->getMessage()
        ]);
        exit;
    }
}
?>
