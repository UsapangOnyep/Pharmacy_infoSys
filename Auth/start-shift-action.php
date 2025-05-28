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

        $query = "INSERT INTO shifts (`AccountID`, `StartTime`, `EndTime`, `Status`, `ShiftNumber`) VALUES (?, NOW(), NULL, 'active', NULL)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            throw new Exception("Failed to start shift: " . $stmt->error);
        }

        //Get inserted Data
        $shiftId = $stmt->insert_id;


        $stmt->close();
        $conn->commit();
        $conn->close();

        echo json_encode([
            "success" => true,
            "message" => "Shift started successfully.",
            "redirectUrl" => "../Seller/pos.php", // Optional: define redirect target,
            "shiftData" => [
                "id" => $shiftId,
                "accountId" => $userId,
                "startTime" => date("Y-m-d H:i:s"),
                "status" => "active"
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
