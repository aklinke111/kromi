<?php
// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/db/dbConfig.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $message = mysqli_real_escape_string($db, $_POST['message']);
    
    // Insert data into the database
    $sql = "INSERT INTO submissions (name, email, message) VALUES ('$name', '$email', '$message')";
    
    if (mysqli_query($db, $sql)) {
        // Redirect back to the form page
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($db);
    }
}
?>
