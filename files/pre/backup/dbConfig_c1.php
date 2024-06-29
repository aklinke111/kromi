<?php
// Database configuration
$dbHost     = "127.0.0.1:3307";
$dbUsername = "xm3xbj34_c1";
$dbPassword = "Kromi2000!";
$dbName     = "xm3xbj34_c1";

// Create database connection
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

