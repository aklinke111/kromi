<?php

//session_set_cookie_params([
//    'domain' => '.import.kromiag.de',
//    'secure' => true,
//    'httponly' => true,
//    'samesite' => 'Lax' // or 'Strict'
//]);


session_start();

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/db/dbConfig.php";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $input_username = $_POST['username'];
     $input_password = $_POST['password'];

    $sql = "SELECT * FROM ak_users WHERE username = '$input_username'";
//    die();
    $result = $db->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()){
            $user = $row['username'];
            $password = $row['password'];

            if ($input_password === $password) {
                $_SESSION['authenticated'] = true;
//                print_r($_SESSION);  
//                die();
                header("Location: form.php");
                exit; 
            } else {
                $error_message = "Invalid username or password";
            }
        }
    } else {
        $error_message = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to KROMI+</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Favicon -->
    <link rel="icon" href="favicon.png" type="image/x-icon">
    <link rel="shortcut icon" href="favicon.png" type="image/x-icon">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            width: 360px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .login-container:hover {
            transform: translateY(-5px);
        }
        .login-heading {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            color: #333;
            display: flex;
            align-items: center;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #007bff;
        }
        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background-color: #28a745;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #218838;
        }
        button:focus {
            outline: none;
        }
        .bottom-links {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        .bottom-links a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .bottom-links a:hover {
            color: #0056b3;
        }
        .input-icon {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="login-heading">Welcome to KROMI+</h2>
        <form method="post" action="#">
            <div class="form-group">
                <label for="username"><i class="fas fa-user input-icon"></i> Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock input-icon"></i> Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit"><i class="fas fa-sign-in-alt input-icon"></i> Login</button>
        </form>
        <div class="bottom-links">
            <a href="#">Forgot password?</a> | <a href="#">Create account</a>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>   
</body>
</html>

