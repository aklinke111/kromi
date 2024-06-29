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

//$dbHost     = "127.0.0.1:3307";
//$dbUsername = "xm3xbj34_c1";
//$dbPassword = "Kromi2000!";
//$dbName     = "xm3xbj34_c1";

//connect to mysql db
$host = "127.0.0.1:3307";
$username = "xm3xbj34_c1";
$password = "Kromi2000!";
$database = "xm3xbj34_c1";

// open mysql connection
$con = mysqli_connect($host, $username, $password, $database) or die('Error in Connecting: ' . mysqli_error($con));

//Drop table if exists
$table_name = "tl_hel_toolcenters";
$sql = "TRUNCATE TABLE $table_name";

if ($con->query($sql) === TRUE) {
    echo "Table $table_name truncated successfully";
} else {
    echo "Error truncating table: " . $con->error;
}

$sql= 'INSERT INTO '. $table_name.' (tstamp, ktcId, subsidiaryId, customerId, active, dateOfImplementation, costcenter) VALUES (?, ?, ?, ?, ?, ?, ?)';

//$sql= 'INSERT INTO '. $table_name.' (tstamp,ktcId) VALUES (?,?)';
//echo($sql);

// use prepare statement for insert query
$st = mysqli_prepare($con, $sql);

// bind variables to insert query params
mysqli_stmt_bind_param($st, 'sssssss', $tstamp, $ktcId, $subsidiaryId, $customerId, $active, $dateOfImplementation, $costcenter);
//mysqli_stmt_bind_param($st, 'ss', $tstamp, $ktcId);

// read json file
$json = file_get_contents($url, false, $context);
//var_dump($json);

//convert json object to php associative array
  $data = json_decode($json, true);

  
// loop through the array
    foreach ($data as $row) {
        $tstamp = time();
        $ktcId = $row['id'];
        $subsidiaryId = $row['niederlassungId'];
        $customerId = $row['geschaeftspartnerId'];
        $active = $row['active'];
        $costcenter = $row['kostenstelleKostenstelle'];
        $dateOfImplementation = $row['inbetriebnahmedatum'];  
//print($ktcId.'<br>');

         //execute insert query
        //echo($st);
        
        mysqli_stmt_execute($st);
    }

//close connection
mysqli_close($con);

