<?php
// App\EventListener\DataContainer\SortlyFunctions.php 
namespace App\EventListener\DataContainer;

use Contao\DataContainer;
use Contao\Database;
use Contao\DC_Table;
use Contao\System;
use Contao\Backend;
use Contao\Input;

class UpdateSortly 
{
    public function updatePrice(DataContainer $dc){
        
        $text = "";   
        $db = Database::getInstance();
        $sortlyUrlPrefix = 'https://api.sortly.co/api/v1/items/'; 
        $sortlyUrlAppendix = '/?&include=custom_attributes%2Cphotos%2Coptions';

        // Perform actions or modifications on form submission
        if ($dc->activeRecord !== null) {

            // only update price after delivery
            if($dc->activeRecord->delivered){

                $price = $dc->activeRecord->price;
                $packageUnit = $dc->activeRecord->packageUnit;
                // Divide price by package unit
                $price /= $packageUnit;
                $sortlyId = $dc->activeRecord->sortlyId;
    
                $sql = "Update sortly set price = $price WHERE sortlyId LIKE '$sortlyId'";
                $db->prepare($sql)->execute();
    
                //log
                $text.= "Update Sortly API with SQL: ".$sql."<p>";
                
                //Run specified update
                $newValue = $price;
                
                $sql = "Select sid from sortly where sortlyId like '$sortlyId'";
                $result = $db->prepare($sql)->execute();
                    while($result->next())
                    {
                        $sid = $result->sid;
        //                $text.= $sid." -> ".$newValue."<br>";
                        $text.= $this->update_sortlyPrice($newValue, $sid, $db, $sortlyUrlPrefix, $sortlyUrlAppendix);
                    }
                // log
                $category = "SORTLY API";
                MyFunctions::log($text, $category, __METHOD__);
            }
        }
    }
    
    
    public function update_sortlyPrice($newValue, $sid, $db, $sortlyUrlPrefix, $sortlyUrlAppendix) {
        
        // prepare the update payload
        $payload = $this->updatePayload($newValue);

        // Run the update
        $this->sortlyItemUpdate($sortlyUrlPrefix, $sid, $payload);

        // Output updated item
        return $this->getSortlyJSON($sortlyUrlPrefix, $sid, $sortlyUrlAppendix)."<p>";
    }
    
    
        // Update price
        function updatePayload($newValue){

            // payload array
            $array = [
                'price' => $newValue,
            ];
    
            return $payload = json_encode($array,JSON_PRETTY_PRINT);
        }
    
    
//    public function updatePayloadCustomfield($newValue,){
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
//    }
    
    
    public function sortlyItemUpdate($sortlyUrlPrefix, $sid, $payload){
    
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
    public function getSortlyJSON($sortlyUrlPrefix, $sid, $sortlyUrlAppendix) {
        
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
        $response = file_get_contents($sortlyUrlPrefix.$sid.$sortlyUrlAppendix, false, $context);
        
        // Returns pretty JSON 
        $data = json_encode(json_decode($response), JSON_PRETTY_PRINT);
        return $data;
    }    
  
    
    
}



// Adjust your payload with these sample attributes
function updatePayloadSample($item,$row,$sortlyPictureName,$sortlyPictureUrl){
    
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
    //$returnedArray = sortlyItemGenerate($payload);
    
}


function testPayload(){
    // JSON payload for testing
    return $payload = json_encode([
        'name'      => 'Germany888',
        'notes'     => 'test777',
    ],JSON_PRETTY_PRINT);
}


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

