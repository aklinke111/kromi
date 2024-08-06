<?php
session_start();
//print_r($_SESSION); 
//die();

//var_dump($_SESSION['authenticated']);

                 
//// Check if user is authenticated
//if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
//    header("Location: login.php");
//    exit;
//}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    
    // Validate form data (basic validation for demonstration)
    if (!empty($name) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Process form data (e.g., save to database)
        $success_message = "Form submitted successfully!";
    } else {
        $error_message = "Please fill out all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KROMI Plus +</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
        .form-container {
            width: 360px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .form-container:hover {
            transform: translateY(-5px);
        }
        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .button-container {
            display: flex;
            flex-direction: column;
            margin-top: 20px;
        }
        button {
            padding: 15px;
            border: none;
            border-radius: 8px;
            background-color: #28a745; /* Green background color */
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-bottom: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        button:hover {
            background-color: #218838; /* Darker green on hover */
            transform: scale(1.05);
        }
        button:focus {
            outline: none;
        }
        .button-icon {
            margin-right: 10px;
        }
        .back-button {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 10px;
            border: none;
            background-color: transparent;
            color: #333;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s ease; /* Only transition for font size change */
        }
        .back-button:hover {
            font-size: 18px; /* Increase font size on hover */
            background-color: transparent; /* Remove background color on hover */
        }
    </style>
</head>
<body>
    <div class="form-container">
<!--        <button class="back-button" onclick="goBack()">
            <i class="fas fa-arrow-left"></i> Back
        </button>-->
        <h2>Choose an Option</h2>
        <div class="button-container">
            <button onclick="submitForm('createMySqlTable')">
                <i class="fas fa-exclamation-circle button-icon"></i>
                <span>Create MySQL tmp_table from TCWeb API</span>
            </button>
            <button onclick="submitForm('mySqlDump')">
                <i class="fas fa-exclamation-circle button-icon"></i>
                <span>MySQL dump of contao database</span>
            </button>
            <button onclick="submitForm('webhook')">
                <i class="fas fa-exclamation-circle button-icon"></i>
                <span>Trigger Sortly webhook</span>
            </button>
            <button onclick="submitForm('updateSortly')">
                <i class="fas fa-exclamation-circle button-icon"></i>
                <span>Update via Sortly API</span>
            </button>  
            <button onclick="submitForm('functionsCalculate')">
                <i class="fas fa-exclamation-circle button-icon"></i>
                <span>Calculate BOM</span>
            </button>
            <button onclick="submitForm('controllingReport')">
                <i class="fas fa-exclamation-circle button-icon"></i>
                <span>Controlling Report KTC for JEDOX import</span>
            </button>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script>
        function submitForm(action) {
            const form = document.createElement('form');
            form.method = 'post';
            form.action = 'submit.php';

            const inputAction = document.createElement('input');
            inputAction.type = 'hidden';
            inputAction.name = 'action';
            inputAction.value = action;

            const inputButton = document.createElement('input');
            inputButton.type = 'hidden';
            inputButton.name = 'button';
            inputButton.value = action;

            form.appendChild(inputAction);
            form.appendChild(inputButton);
            document.body.appendChild(form);
            form.submit();
        }

        function goBack() {
            window.history.back(); // Go back to the previous page
        }
    </script>
</body>
</html>