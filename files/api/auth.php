<?php

// Load the database configuration file
//include_once $_SERVER['DOCUMENT_ROOT']."/db/dbConfig.php";

// Sample credentials for demonstration purposes
define('API_USERNAME', 'aklinke');
define('API_PASSWORD', 'Kromi2000!');

// Function to send a 401 Unauthorized response
function sendUnauthorizedResponse() {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="My API"');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Check if the Authorization header is set
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    sendUnauthorizedResponse();
} else {
    // Get the username and password from the headers
    $username = $_SERVER['PHP_AUTH_USER'];
    $password = $_SERVER['PHP_AUTH_PW'];

    // Validate the credentials
    if ($username === API_USERNAME && $password === API_PASSWORD) {
        // Credentials are valid, proceed with the API response
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Welcome to the API!']);
    } else {
        // Invalid credentials, send 401 Unauthorized response
        sendUnauthorizedResponse();
    }
}
