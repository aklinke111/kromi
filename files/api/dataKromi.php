<?php

//https://confluence-kromilogistikag.msappproxy.net/display/KTCPROD/API+Connections

// Needed to return pretty JSON
//header('Content-Type: application/json');

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/pre/db/dbConfig.php";

//Start migration
//getKromiUsers($db);


// Get data from KROMI by API-URL
function getKromiJSON($apiUrl) {

    echo $apiUrl." ---> ";
    
    // API credentials
    $username = "WtpaEK2zw8k4ECDq6JuVXqyah33Mx3wB";
    $password = "2MFHRyXtwsn9tWNsGQPMxCtRUyj7Sfc9";
    
    // Create HTTP headers with basic authentication
    $auth = base64_encode("$username:$password"); // V3RwYUVLMnp3OGs0RUNEcTZKdVZYcXlhaDMzTXgzd0I6Mk1GSFJ5WHR3c245dFdOc0dRUE14Q3RSVXlqN1NmYzk=
        
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
function getKromiUsers($db){
    
    // API endpoint URL
    $apiUrl = 'https://tcweb-users.kromi.de/users';
    
    $data = getKromiJSON($apiUrl);
    
    // Iterate through all KTC-Komponenten
    foreach ($data as $item) {
        $lastName = $item['lastName'];
        $firstName = $item['firstName'];
        $email = $item['email'];
        $id = $item['pager'];
        
        echo $sql = "INSERT INTO tl_member 
                    (tstamp, lastname, firstname, email, fax, dateAdded, username) 
                    VALUES 
                    (UNIX_TIMESTAMP(), '$lastName', '$firstName', '$email', '$id',UNIX_TIMESTAMP(), CONCAT('$firstName','$lastName'))";
        // Execute the query
        $result = $db->query($sql);

        if ($result) {
            //Count inserted datasets
            $msg.= "$count Records inserted successfully in table 'tl_members";
        } else {
            $msg.= "Error: " . $sql . "<br>" . $db->error;
        }
    }
    echo $msg;
}
