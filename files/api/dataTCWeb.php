<?php

// Needed to return pretty JSON
//header('Content-Type: application/json');

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/pre/db/dbConfig.php";

//Start migration
//migrateKomponentenFromTcwebToSortly($db);


// Get data from TCWeb by API-URL
function getTCWebJSON($apiUrl) {
    
    echo $apiUrl." ---> ";
        
    // API credentials
    $username = 'andreas.klinke@kromi.de';
    $password = 'Kromi2020!';
    


    // Create HTTP headers with basic authentication
    $auth = base64_encode("$username:$password");
    $context = stream_context_create([
        'http' => [
            'header' => "Authorization: Basic $auth"
        ]
    ]);
    // Make the HTTP request and fetch data
    $response = file_get_contents($apiUrl, false, $context); 
    
    // Check for errors
    if ($response === false) {
        echo "Error fetching data from API";
    } else {
        // Decode JSON response
        $data = json_decode($response, true);

        // Check if decoding was successful
        if ($data === null) {
            echo "Error decoding JSON: " . json_last_error_msg();
        } else {
            // JSON data is now in $data, you can use it as needed
//            var_dump($data);
            return $data;
        }
    }
}


// Get data from TCWeb API table "Komponenten" and generate the devices in Sortly
function migrateKomponentenFromTcwebToSortly($db){
    
    // API endpoint URL
    $apiUrl = 'https://tcweb.heliotronic.de/api/v1/kromi/komponenten';
    
    $data = getTCWebJSON($apiUrl);
    print_r($data);
    
    // Iterate through all KTC-Komponenten
    foreach ($data as $item) {
        echo $ktcId = $item['id']."br>";
        echo $name = $item['bezeichnung']."br>";
    }
}
