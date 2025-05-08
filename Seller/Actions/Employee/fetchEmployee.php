<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

// Fetch employees with status = 1 and who are not already in the user_account table
$sql = "SELECT ID, FMLS_GetFullNameViaID(ID) AS FullName
        FROM lEmployee
        WHERE `Status` = 1
          AND NOT EXISTS (SELECT 1 FROM user_account WHERE user_account.EmployeeID = lEmployee.ID);";

$result = $conn->query($sql);

// Initialize an array to store employees
$employees = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = [
            'ID' => $row['ID'],
            'FullName' => $row['FullName']
        ];
    }
}

// Return employee data as a JSON response
header('Content-Type: application/json');
echo json_encode(['employees' => $employees]);

$conn->close();
?>