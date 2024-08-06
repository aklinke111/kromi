<?php

//Documentation API Sortly
//https://sortlyapi.docs.apiary.io/#

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";


// Needed to return pretty JSON
//header('Content-Type: application/json');
//Basic URL to GET sortly items
$sortlyUrlPrefix = 'https://api.sortly.co/api/v1/items/';
$sortlyUrlAppendix = '/?&include=custom_attributes%2Cphotos%2Coptions';

//Run specified update
//bulkUpdate_fieldbus($db,$sortlyUrlPrefix,$sortlyUrlAppendix);

//main function for updating Sortly API with $newValue
// !!! payload needs to be adjusted with "custom_attribute_id" and "custom_attribute_name" !!!!
function bulkUpdate_fieldbus($db,$sortlyUrlPrefix,$sortlyUrlAppendix){
    
    // variable to switch from test- to life mode
    $updateApi = false;
    
    $msg = "";
    
    $sql = "Select
    sortly_country.name As country,
    sortly_subsidiary.name As subsidiary,
    sortly_customer.name As customer,
    sortly_ktc.name,
    sortly.name As model,
    tl_sortlyTemplatesIVM.note As description,
    sortly.inventoryNo As inventoryNo,
    sortly.sortlyId,
    sortly.sid,
    sortly.fieldbus,
    sortly_ktc.tags
From
    sortly_ktc Inner Join
    sortly_customer On sortly_customer.sid = sortly_ktc.pid Inner Join
    sortly_subsidiary On sortly_subsidiary.sid = sortly_customer.pid Inner Join
    sortly_country On sortly_country.sid = sortly_subsidiary.pid Inner Join
    tl_customer On sortly_customer.sid = tl_customer.sid Inner Join
    sortly On sortly_ktc.sid = sortly.pid Left Join
    tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.name = sortly.name
Where
    sortly.name Like 'KTC-ES/%'";
    $result = $db->query($sql);
    
    while($item = $result->fetch_assoc()){ 
     
        $sid = $item['sid'];
        $name = $item['name'];
        $sortlyId = $item['sortlyId'];
        
        $newValue = "Bedrunka Websocket";
                
            echo $sid.' -----'.$name.'---'.$newValue.'-----'.$sortlyId.'<br>';
                   
                if($updateApi){
                        echo "Update";
                    // prepare the update payload
                    $payload = updatePayload($newValue);

                    // Run the update
                    sortlyItemUpdate($sortlyUrlPrefix, $sid, $payload);

                    // Output updated item
                    $msg.= getSortlyJSON($sortlyUrlPrefix, $sid,$sortlyUrlAppendix)."<p>";
                }
            // write log
            writeLog('function: bulkUpdate_fieldbus from sortly/updateSortly.php', 'UPDATE', $msg, $db);
            }

        //echo $msg; 
        }    


////Update active
//function updatePayload($newValue,){
//    
//    // payload array
//    $array = [
//        'custom_attribute_values' => [
//            [
//                'value' => $newValue,
//                'custom_attribute_id' => '322300',
//                'custom_attribute_name' => 'fieldbus'                       
//            ],  
//        ],
//    ];
//
//    return $payload = json_encode($array,JSON_PRETTY_PRINT);
//}
        
        
        
//bulkUpdate_active($db,$sortlyUrlPrefix,$sortlyUrlAppendix);
//
//function bulkUpdate_active($db,$sortlyUrlPrefix,$sortlyUrlAppendix){
//
//    $msg = "";
//    
//    $sql = "SELECT sid,name,sortlyId FROM sortly WHERE name LIKE 'KTC-%' AND Type LIKE 'folder' ORDER BY name";
//    $result = $db->query($sql);
//    while($item = $result->fetch_assoc()){ 
//     
//        $sid = $item['sid'];
//        $name = $item['name'];
//        $sortlyId = $item['sortlyId'];
//        
//        $newValue = 1;
//
////        echo $sid.' -----'.$name.'---'.$newValue.'-----'.$sortlyId.'<br>';
//        
//            // Limit evaluation on no KTCs
////            if($sortlyId == 'SD0M4R0236'){
//                
//            echo $sid.' -----'.$name.'---'.$newValue.'-----'.$sortlyId.'<br>';
//                    
//            // prepare the update payload
//            $payload = updatePayload($newValue);
//
//            // Run the update
//            sortlyItemUpdate($sortlyUrlPrefix, $sid, $payload);
//
//            // Output updated item
//            $msg.= getSortlyJSON($sortlyUrlPrefix, $sid,$sortlyUrlAppendix)."<p>";
//       
////            }
//        }        
////    echo $msg;
//}
//
//// Update active
//function updatePayload($newValue,){
//    
//    // payload array
//    $array = [
//        'custom_attribute_values' => [
//            [
//                'value' => $newValue,
//                'custom_attribute_id' => '317639',
//                'custom_attribute_name' => 'active'                       
//            ],  
//        ],
//    ];
//
//    return $payload = json_encode($array,JSON_PRETTY_PRINT);
//}


        
        
        
        
//bulkUpdate_name($db,$sortlyUrlPrefix,$sortlyUrlAppendix);
////
//function bulkUpdate_name($db,$sortlyUrlPrefix,$sortlyUrlAppendix){
//
//    $msg = "";
//    
//    $sql = "SELECT sid,name,sortlyId FROM sortly WHERE name LIKE 'dreck111'";
//    $result = $db->query($sql);
//    while($item = $result->fetch_assoc()){ 
//     
//        $sid = $item['sid'];
//        $name = $item['name'];
//        $sortlyId = $item['sortlyId'];
//        
//        $newValue = "dreck222";
//
//        
//        echo $sid.' -----'.$name.'---'.$newValue.'-----'.$sortlyId.'<br>';
//        
//            // Limit evaluation on no KTCs
////            if($sortlyId == 'SD0M4T1129'){
//                    
//            // prepare the update payload
//            $payload = updatePayload($newValue);
//
//            // Run the update
//            sortlyItemUpdate($sortlyUrlPrefix, $sid, $payload);
//
//            // Output updated item
//            $msg.= getSortlyJSON($sortlyUrlPrefix, $sid,$sortlyUrlAppendix)."<p>";
//       
////            }
//        }        
//    echo $msg;
//}
//


//bulkUpdate_name($db,$sortlyUrlPrefix,$sortlyUrlAppendix);
//
//function bulkUpdate_Built($db,$sortlyUrlPrefix,$sortlyUrlAppendix){
//    
//    // API endpoint URL
//    $apiUrl = 'https://tcweb.heliotronic.de/api/v1/kromi/komponenten';
//    
//    $data = getTCWebJSON($apiUrl);
////    print_r($data);
//    $msg = "";
//    
//    // Iterate through all KTC-Komponenten
//        foreach ($data as $item) {
//            
//        // Identify KTC-ID og row
//        $inventoryNo = $item['seriennummer'];
//        $built = $item['erstelldatum'];
//        $id = $item['id'];
//        
//            $sql = "SELECT sid FROM sortly WHERE inventoryNo LIKE '$inventoryNo'";
//            $result = $db->query($sql);
//            while($row = $result->fetch_assoc()){ 
//                $sortlyItemId = $row[sid];
//            } 
//       
//            // Limit evaluation on no KTCs
//            if(!is_numeric($item['id'])){
////            if($inventoryNo == '100203'){
////                echo $id.' -----'.$inventoryNo.'---'.$built.'---'.$sortlyItemId.'<br>';
//                    
//            // prepare the update payload
//            $payload = updatePayload($built.'T00:00:00');
//
//            // Run the update
//            sortlyItemUpdate($sortlyUrlPrefix, $sortlyItemId, $payload);
//
//            // Output updated item
//            $msg.= getSortlyJSON($sortlyUrlPrefix, $sortlyItemId,$sortlyUrlAppendix)."<p>";
//       
//            }
//        }
//
////    echo $msg;
//}


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


//bulkUpdateDGUV3($db,$sortlyUrlPrefix,$sortlyUrlAppendix);
//function bulkUpdateDGUV3($db,$sortlyUrlPrefix,$sortlyUrlAppendix){
//
//    $msg = "";
//    $sql=   "SELECT sid as sortlyItemId, machineNo AS DGUV3No, dateOfDguv3 AS DGUV3, SERIAL AS inventoryNo 
//            FROM tl_hel_componentsPlus 
//            JOIN sortly ON sortly.inventoryNo = tl_hel_componentsPlus.SERIAL
//            WHERE machineNo NOT LIKE ''";
////            AND SERIAL LIKE '101375'";
//    $result = $db->query($sql);
//    
//      while($row = $result->fetch_assoc()){  
//        
//        echo "<p>";  
//        $sortlyItemId = $row[sortlyItemId];
//        echo "sid: ".$sortlyItemId."<br>";   
//        $inventoryNo = $row[inventoryNo];
//        echo "inventoryNo: ". $inventoryNo."<br>";  
//        $DGUV3 = $row[DGUV3];
//        echo "DGUV3: ". $DGUV3."<br>";
//        $DGUV3No = $row[DGUV3No];
//        echo "DGUV3No: ". $DGUV3No."<p>";
//        
//        // prepare the update payload
//        $payload = updatePayload($DGUV3.'T00:00:00',$DGUV3No);
//        
//        // Run the update
//        sortlyItemUpdate($sortlyUrlPrefix, $sortlyItemId, $payload);
//        
//        // Output updated item
//        $msg.= (getSortlyJSON($sortlyUrlPrefix, $sortlyItemId,$sortlyUrlAppendix))."<p>";
//      }
//    echo $msg;
//}


//// Update DGUV
//function updatePayload($DGUV3,$DGUV3No){
//    
//    // payload array
//    $array = [
//        'custom_attribute_values' => [
//            [
//                'value' => $DGUV3,
//                'custom_attribute_id' => '316329',
//                'custom_attribute_name' => 'DGUV3'                       
//            ],  
//            [
//                'value' => $DGUV3No,
//                'custom_attribute_id' => '322428',
//                'custom_attribute_name' => 'DGUV3No'                       
//            ],  
//        ],
//    ];
//
//    return $payload = json_encode($array,JSON_PRETTY_PRINT);
//}



function sortlyItemUpdate($sortlyUrlPrefix, $sid, $payload){
    
    // URL with sortly item
    $apiUrl = $sortlyUrlPrefix.$sid;
    
    // Authorization token (replace 'your_secret_token' with your actual token)
    $authToken = 'sk_sortly_oCDxewcXoQSyWNxNohQ_';

    // Create HTTP headers
    $headers = [
        'http' => [
            'method' => 'PUT',
            'header' => "Content-Type: application/json\r\n" .
                        "Authorization: $authToken\r\n",
            'content' => $payload
        ]
    ];
    
        // Create stream context
    $context = stream_context_create($headers);

    // Send HTTP request and capture API response
    return $response = file_get_contents($apiUrl, false, $context);
}


// Fetch item for checking
     function getSortlyJSON($sortlyUrlPrefix,$sortlyItemId, $sortlyUrlAppendix) {
        
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
        $response = file_get_contents($sortlyUrlPrefix.$sortlyItemId.$sortlyUrlAppendix, false, $context);
        
//        // Returns Array
//        $data = json_decode($response, true);
//        
//        // Returns raw JSON
//        $data = $response;
//
//        // Returns pretty JSON 
        $data = json_encode(json_decode($response), JSON_PRETTY_PRINT);
        
        return $data;
    }
 

// Adjust your payload with these sample attributes
function updatePayloadSample(){
    
    // payload array
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
                'custom_attribute_id' => '322311',
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
                'value' => $item['DGUV3'],
                'custom_attribute_id' => '316329',
                'custom_attribute_name' => 'DGUV3'                       
            ],  
            [
                'value' => $item['DGUV3No'],
                'custom_attribute_id' => '322428',
                'custom_attribute_name' => 'DGUV3No'                       
            ], 
            [
                'value' => $item['reserviert'],
                'custom_attribute_id' => '316079',
                'custom_attribute_name' => 'reserved'                       
            ],  
            [
                'value' => 1,
                'custom_attribute_id' => '322290',
                'custom_attribute_name' => 'IVM'                       
            ], 
        ],
        'photos' => [
            [
                'id' => 41705700,
                'name' => $sortlyPictureName,
                'url' => $sortlyPictureUrl                     
            ],
        ] 
    ];


    $payload = json_encode($array,JSON_PRETTY_PRINT);     
    echo $payload;

    //Generate item and receive return array (needed for SID)
    $returnedArray = sortlyItemGenerate($payload);
    
}


// write log entry in tl_myLogs
function writeLog($text,$category,$method,$db)
{
    // insert new log entry
    $text = str_replace("'", "\'", $text);
    $sql = "INSERT INTO tl_myLogs (tstamp,text,category,method) VALUES ('".time()."','$text','$category','$method')";
    $db->prepare($sql)
        ->execute();
}



//Payload for update
//$payload = testPayload();

function testPayload(){
    // JSON payload for testing
    return $payload = json_encode([
        'name'      => 'Germany888',
        'notes'     => 'test777',
    ],JSON_PRETTY_PRINT);
}

   //// Process API response
// $responseData = json_decode($response, true);
//if ($responseData !== null) {
//    echo 'Response from server: ';
//    print_r($responseData);
//} else {
//    echo 'Invalid JSON received from server.';
//}


//// Update name
//function updatePayload($newValue){
//    
//    // payload array
//    $array = [
//        'name' => $newValue,
//    ];
//
//    return $payload = json_encode($array,JSON_PRETTY_PRINT);
//}
