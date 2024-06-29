<?php
// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/db/dbConfig.php";

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($db, $_GET['id']);
    
    // Delete the record from the database
    $sql = "DELETE FROM submissions WHERE id = $id";
    
    if (mysqli_query($db, $sql)) {
        // Redirect back to the form page
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($db);
    }
}
?>
