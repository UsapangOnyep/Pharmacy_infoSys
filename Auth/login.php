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
        <img src="../Logo/logo-dark.png" alt="Logo">
        <h2>Hello, Welcome Back!</h2>

        <form class="login-form">
            <div class="form-group">
                <img src="img/user.png" alt="user" class="left">
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>
            <div class="form-group">
                <img src="img/key.png" alt="key" class="left">
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <img src="img/eye.png" alt="eye" class="right" id="togglePassword">
            </div>
            <span id="error" class="error-message" style="color:red;"></span>

            <button type="submit">Login</button>
        </form>

        <p>&copy; <?php echo date("Y"); ?> All rights reserved.</p>
    </div>
<script src="js/login.js"></script>
</body>

</html>