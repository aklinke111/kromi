<?php

//https://scoutapm.com/blog/php-json_encode-serialize-php-objects-to-json

//https://stackoverflow.com/questions/10165234/nested-php-arrays-to-json

// Parse from JSON
// https://angrystudio.com/how-to-parse-multidimensional-json-arrays-in-php/

//Documentation API Sortly
//https://sortlyapi.docs.apiary.io/#

// Needed to return pretty JSON
header('Content-Type: application/json');

// Load the database configuration file
include_once '../db/dbConfig.php';

//Start migration
migrateKomponentenFromTcwebToSortly($db);


// Get data from TCWeb by API-URL
function getTCWebJSON($apiUrl) {
    

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



// Generate the item with payload collected in main-function 
function sortlyItemGenerate($payload){
    
    //Basic URL
    $sortlyUrl = 'https://api.sortly.co/api/v1/items/';

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
    return $dataArray = json_decode($response, true);
    
//    return json_encode(json_decode($response), JSON_PRETTY_PRINT);
}


// Get data from TCWeb API table "Komponenten" and generate the devices in Sortly
function migrateKomponentenFromTcwebToSortly($db){
    
    // API endpoint URL
    $apiUrl = 'https://tcweb.heliotronic.de/api/v1/kromi/komponenten';
    
    $data = getTCWebJSON($apiUrl);
    
    // Iterate through all KTC-Komponenten
        foreach ($data as $item) {
            
        // Identify KTC-ID og row
        $ktcId = $item['id'];
        $name = $item['bezeichnung'];
        $sortlyPictureName = '';
        $sortlyPictureUrl = '';
        
        // Identify photos
        $sql = "select sortlyPictureName, sortlyPictureUrl FROM tl_kr_componentsBasics WHERE model = '$name'";
        $result = $db->query($sql);
            while($row = $result->fetch_assoc()){ 
                $sortlyPictureName = $row[sortlyPictureName];
                $sortlyPictureUrl = $row[sortlyPictureUrl];            
            }  
        
            // Limit evaluation on a certain KTC
            if($ktcId == '018'){

            // identify Sortly sid for this KTC-ID in order to find the right node for creating item/s
            $sql = "SELECT sid, name FROM sortly where name = 'KTC-$ktcId'";
            $result = $db->query($sql);
                while($row = $result->fetch_assoc()){        
    //                echo "KTC: ".$row[name]."<br>";
    //                echo "Inventarnummer: " . $item['seriennummer']. "<br>";
    //                //Id parent folder, where item or folder is generated
    //                echo "sid: ".$row[sid]."<p>";


                $array = [
                    'name' => $item['bezeichnung'],
                    'type' => 'item',               
                    'parent_id' => $row[sid],  
                    'custom_attribute_values' => [
                        [
                            'value' => $item['seriennummer'],
                            'custom_attribute_id' => '287983',
                            'custom_attribute_name' => 'inventoryNo'                       
                        ],
                        [
                            'value' => true,
                            'custom_attribute_id' => '320035',
                            'custom_attribute_name' => 'available'                       
                        ],
                        [
                            'value' => $item['sn'],
                            'custom_attribute_id' => '287982',
                            'custom_attribute_name' => 'serialNo'                       
                        ],  
                        [
                            'value' => $item['erstelldatum'],
                            'custom_attribute_id' => '320034',
                            'custom_attribute_name' => 'overhaul'                       
                        ], 
                        [
                            'value' => $item['baujahr'].'-01-01T00:00:00.000Z',
                            'custom_attribute_id' => '320033',
                            'custom_attribute_name' => 'built'                       
                        ], 
                        [
                            'value' => $item['reserviert'],
                            'custom_attribute_id' => '316079',
                            'custom_attribute_name' => 'reserved'                       
                        ], 
                    ],
                    'photos' => [
                        [
                            'id' => 41705700,
                            'name' => '2024-04-18/399774bc07c2/photos/5f9b310c-b3c5-471e-8f53-ea2081491989.large.jpg',
                            'url' => 'https://lnk.sortly.co/v2/downloads/photo/v2_l2ag5iM04MGDvxVlQu-8ULCusfMeaDLfjacTwfSwPdy0tNk2wlqAmQ=='                       
                        ],
                    ] 
                ];

                echo $payload = json_encode($array,JSON_PRETTY_PRINT);     

                //Generate item and receive return array (needed for SID)
                $returnedArray = sortlyItemGenerate($payload);

    //            echo "SID: ".$returnedArray[data][sid]."<p>"; 

                    //Insert inventorNo and SortlyId into matching table 
                    $sql_1 = "INSERT INTO tl_matchSortlyTcweb (inventoryNoTcweb,sortlyId) VALUES ('".$item['seriennummer']."', '".$returnedArray[data][sid]."')";
                    $result = $db->query($sql_1);

    //            echo "ID: ".$returnedArray[data][custom_attribute_values][custom_attribute_name][id]."<p>";      

                  echo "<p>".$data = json_encode($returnedArray, JSON_PRETTY_PRINT);
    //            print_r($returnedArray);

                }
            exit(); // only for testing one item generated
        }
    
    }
}


// Fetch item for checking after running an update
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
