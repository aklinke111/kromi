<?php
session_start();

//// Check if user is authenticated
//if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
//    header("Location: login.php");
//    exit;
//}


$message = "";

// Receive parameters
if (isset($_GET['webhookFunction'])) {
    
     // build header    
    $urPathPre = "Location: /files/prepare_kr/sortly/";
    
    // functionName = filename
    $functionName = $_GET['webhookFunction'];
    $file = $functionName.".php";
    $filepath = $urPathPre.$file;
    
    $parameterKey = "?webhookFunction=";
    $parameterValue = htmlspecialchars($_GET['webhookFunction']);

    $header = $filepath.$parameterKey.$parameterValue;
    
    // call function
    header($header);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Submission Result</title>
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
        .result-container {
            width: 360px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
        }
        h2 {
            color: #007bff; /* Blue color for headings */
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .icon {
            font-size: 48px; /* Icon size */
            margin-bottom: 20px;
        }
        .icon-success {
            color: #28a745; /* Green color for success */
        }
        .icon-error {
            color: #dc3545; /* Red color for error */
        }
        .back-button {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 10px;
            border: none;
            color: #333; /* Blue color for button text */
            font-size: 16px;
            cursor: pointer;
            transition: font-size 0.3s ease; /* Only transition for font size change */
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .back-button i {
            margin-right: 5px;
        }
        .back-button:hover {
            font-size: 18px; /* Increase font size on hover */
        }
    </style>
</head>
<body>
    <div class="result-container">
        <a class="back-button" href="/files/prepare_kr/index.php">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <div class="icon <?php echo $message ? ($iconClass === "fas fa-check-circle" ? 'icon-success' : 'icon-error') : ''; ?>">
            <i class="<?php echo $iconClass; ?>"></i>
        </div>
        <h2>Form Submission Result</h2>
        <p><?php echo $message; ?></p>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>
