<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="login-container">
        <h2>Start Shift</h2>
        <form class="login-form">
            <div class="form-group">
                <label for="openingBalance" class="left">₱</label>
                <input type="number" id="openingBalance" name="openingBalance" required placeholder="Enter opening balance">
            </div>
            <button type="submit" id="startShiftButton">Start Shift</button>
        </form>
    </div>
</body>

<script src="js/startshift.js"></script>
<script src="../Seller/Assets/js/SweetAlert/sweetalert2.js"></script>

</html>