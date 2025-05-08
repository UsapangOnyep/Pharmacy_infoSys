<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        require_once $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);

        $query = "SELECT * FROM user_account WHERE Username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            echo json_encode([
                'success' => false,
                'message' => 'Incorrect username.'
            ]);
            exit();
        }
        
        $status = $user['Status'];
        if (!$status) {
            echo json_encode([
                'success' => false,
                'message' => 'The account access has been removed by the admin.'
            ]);
            exit();
        }

        if (password_verify($password, $user['Password'])) {

            $employee = null;
            $query = "SELECT * FROM lemployee WHERE ID = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $user["EmployeeID"]);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $employee = $result->fetch_assoc();
            }
            
            session_start();
            $_SESSION['LoggedinUser'] = $employee['LName'] . ', ' . $employee['Fname'];

            echo json_encode([
                'success' => true,
                'redirectUrl' => $user['usertype'] === 'admin' ? '../Seller/' : '../Seller/pos.php',
                'user' => $user,
                'employee' => $employee,
                'usertype' => $user['usertype']
            ]);

        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Incorrect password.'
            ]);
        }

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred. Please try again later.'
        ]);
        error_log($e->getMessage()); 
    }
}
?>
