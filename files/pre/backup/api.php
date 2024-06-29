<?php

$url = 'https://tcweb.heliotronic.de/api/v1/kromi/toolcenters';

$access_token = "YW5kcmVhcy5rbGlua2VAa3JvbWkuZGU6S3JvbWkyMDIwIQ==";
//$data = "";
$options = [
    'http' => [
        'method' => 'GET',
        
        "header" => ["Authorization: Basic " . $access_token,
        "Content-Type: application/json"]
        
        //"content" => $data
    ]];

$context = stream_context_create($options);

//connect to mysql db
$host = "localhost";
$username = "web1645";
$password = "xxxxxx";
$database = "usr_web1645_4";

// open mysql connection
$con = mysqli_connect($host, $username, $password, $database) or die('Error in Connecting: ' . mysqli_error($con));

// use prepare statement for insert query
$st = mysqli_prepare($con, 'INSERT INTO tl_toolcenters(ktcId, niederlassungId, geschaeftspartnerId, active, kostenstelleKostenstelle) VALUES (?, ?, ?, ?, ?)');

// bind variables to insert query params
mysqli_stmt_bind_param($st, 'sssss', $ktcId, $niederlassungId, $geschaeftspartnerId, $active, $kostenstelleKostenstelle);

// read json file
$json = file_get_contents($url, false, $context);

//convert json object to php associative array
  $data = json_decode($json, true);

// loop through the array
    foreach ($data as $row) {
        $ktcId = $row['id'];
        $niederlassungId = $row['niederlassungId'];
        $geschaeftspartnerId = $row['geschaeftspartnerId'];
        $active = $row['active'];
        $kostenstelleKostenstelle = $row['kostenstelleKostenstelle'];  

         //execute insert query
        mysqli_stmt_execute($st);
    }

//close connection
mysqli_close($con);