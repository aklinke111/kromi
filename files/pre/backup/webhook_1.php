<?php

// Serialize $_POST data into a string
$postData = print_r($_POST, true);

// Define the file path for logging
$logFile = 'post_data.log';

// Append the serialized $_POST data to the log file
file_put_contents($logFile, $postData . PHP_EOL, FILE_APPEND);

// Respond with a success message (optional)
echo "POST data logged successfully.";
