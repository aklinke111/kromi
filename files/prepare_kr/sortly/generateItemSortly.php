<?php
//Ducumentation API Sortly
//https://sortlyapi.docs.apiary.io/#

// Needed to return pretty JSON
header('Content-Type: application/json');

//Id parent folder, where item or folder is generated
$sortlyPid = 58670979;

$payload = '{
    "name": "itemNameXXX",
    "notes": "newNotes",
    "price": 10,
    "quantity": 1,
    "min_quantity": 1,
    "type": "folder",
    "parent_id": '.$sortlyPid.'
}
';

//Basic URL
$sortlyUrl = 'https://api.sortly.co/api/v1/items/';

// Run the update
echo sortlyItemGenerate($sortlyUrl, $payload);

// Fetch item for checking
     function getSortlyJSON($sortlyUrlGET,$sortlyItemId) {
        
        // Bearer token for authentication
        $token = 'sk_sortly_oCDxewcXoQSyWNxNohQ_';

        // Set up HTTP headers with Authorization header containing the bearer token
        $options = [
            'http' => [
                'header' => "Authorization: Bearer $token\r\n"
            ]
        ];

        // Create a stream context
        $context = stream_context_create($options);

        // Use file_get_contents with the created context to fetch data from the URL
        $response = file_get_contents($sortlyUrlGET.$sortlyItemId, false, $context);
        
        // Returns Array
//        $data = json_decode($response, true);
        
        // Returns raw JSON
//        $data = $response;

        // Returns pretty JSON 
        $data = json_encode(json_decode($response), JSON_PRETTY_PRINT);
        return $data;
    }
    
function sortlyItemGenerate($sortlyUrl, $payload){
    
    // Authorization token (replace 'your_secret_token' with your actual token)
    $authToken = 'sk_sortly_oCDxewcXoQSyWNxNohQ_';

    // Create HTTP headers
    $headers = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n" .
                        "Authorization: $authToken\r\n",
            'content' => $payload
        ]
    ];
    
        // Create stream context
    $context = stream_context_create($headers);

    // Send HTTP request and capture API response

    $response = file_get_contents($sortlyUrl, false, $context);
    return json_encode(json_decode($response), JSON_PRETTY_PRINT);
}

   //// Process API response
// $responseData = json_decode($response, true);
//if ($responseData !== null) {
//    echo 'Response from server: ';
//    print_r($responseData);
//} else {
//    echo 'Invalid JSON received from server.';
//}
