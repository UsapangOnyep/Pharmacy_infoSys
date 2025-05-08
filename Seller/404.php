<?php
// Start the session to access session variables
session_start();

// Determine the URL to redirect to based on the user's account status
$redirectUrl = 'index.php'; // Default URL for users who are not logged in

if (isset($_SESSION['user_role'])) {
    switch ($_SESSION['user_role']) {
        case 'user':
            $redirectUrl = '/user/index.php';
            break;
        case 'seller':
            $redirectUrl = 'index.php';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <link rel="stylesheet" href="Assets/css/main.css">
    <style>
        .error-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(calc(-50% + 125px), -50%);
            text-align: center;
        }

        .error-code {
            font-size: 10rem;
            font-weight: bold;
            color: var(--btn-error);
            margin: 0;
        }

        .error-message {
            font-size: 1.5rem;
            color: var(--btn-error);
            margin-bottom: 20px;
        }

    </style>
</head>

<body>
    <div class="error-container">
        <h1 class="error-code">404</h1>
        <p class="error-message">Oops! The page you are looking for does not exist.</p>
        <a class="btn-default" href="<?php echo htmlspecialchars($redirectUrl); ?>">Go Back</a>
    </div>
</body>

</html>