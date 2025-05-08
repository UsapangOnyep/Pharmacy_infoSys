<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <link rel="stylesheet" href="Assets/css/main.css">
    <style>
        /* Keep the original body background color */
        body {
            background-color: rgba(89, 115, 153);
            /* Original background color */
            font-family: 'Helvetica', sans-serif;
            color: white;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        /* Container for the error message */
        .error-container {
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        .error-code {
            font-size: 10rem;
            font-weight: bold;
            color: #ecf0f1;
            margin: 0;
        }

        .error-message {
            font-size: 1.5rem;
            color: #ecf0f1;
            margin-bottom: 20px;
        }

        a {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 12px 20px;
            border-radius: 5px;
            text-decoration: none;
            color: rgba(89, 115, 153);
            font-size: 1rem;
            transition: background-color 0.3s;
        }

    </style>
</head>

<body>
    <div class="error-container">
        <h1 class="error-code">404</h1>
        <p class="error-message">Oops! The page you are looking for does not exist.</p>
        <a href="#" id="GoBack">Go Back</a>
    </div>

    <script>
        document.getElementById("GoBack").addEventListener("click", function (event) {
            event.preventDefault();

            const userType = localStorage.getItem("usertype");

            if (userType === "admin") {
                window.location.href = "/pharmacy_infosys/seller/";
            } else if (userType === "user") {
                window.location.href = "/pharmacy_infosys/seller/pos";
            } else {
                window.location.href = "/pharmacy_infosys/auth/";
            }
        });
    </script>

</body>

</html>