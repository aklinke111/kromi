<?php

// Needed to return pretty JSON
header('Content-Type: application/json');

// Get data from TCWeb by API-URL
function getSortlyJSON($apiUrl) {

        // Bearer token for authentication
        $token = 'sk_sortly_oCDxewcXoQSyWNxNohQ_';

//        // Set up HTTP headers with Authorization header containing the bearer token
//        $context = [
//            'http' => [
//                'header' => "Authorization: Bearer $token"
//            ]
//        ];

    // Create HTTP headers with basic authentication
     $context = stream_context_create([
            'http' => [
                'header' => "Authorization: Bearer $token"
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
//            return "fuck";
        }
    }
}
