<?php

//header('Content-Type: application/json');

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/api/dataKromi.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/api/dataTCWeb.php";
//include_once $_SERVER['DOCUMENT_ROOT']."/files/api/dataSortly.php";

//TCWeb tables
$TCWebTable0 = "komponentenstatus";
$TCWebTable1 = "komponenten";
$TCWebTable2 = "schrankattribute";
$TCWebTable3 = "schrankattributoptionen2komponente";
$TCWebTable4 = "schrankattributoptionen";
$TCWebTable5 = "toolcenters";
$TCWebTable6 = "kostenstellen";

$TCWebTables = array($TCWebTable0,$TCWebTable1,$TCWebTable2,$TCWebTable3,$TCWebTable5,$TCWebTable6);


//Kromi tables
$KromiTable1 = "users";

$KromiTables = array($KromiTable1);

//Sortly tables !!!! Doesnt work in nested arrays  !!!!!
//$SortlyTable1 = "items";
//$SortlyTables = array($SortlyTable1);
//// generate tables from SORTLY API
//$apiUrl = 'https://api.sortly.co/api/v1/';
//$api = "sortly";
//generateTempTables($db, $SortlyTables, $apiUrl, $api);

if (isset($_GET['webhookFunction'])) {

    $function = $_GET['webhookFunction'];
    if($function == "createMySqlTable"){
        
        // generate tables from TCWeb API
        $apiUrl = 'https://tcweb.heliotronic.de/api/v1/kromi/';
        $api = "tcweb";
        generateTempTables($db, $TCWebTables, $apiUrl, $api);
        
        // generate tables from Kromi API
        $apiUrl = 'https://tcweb-users.kromi.de/';
        $api = "kromi";
        generateTempTables($db, $KromiTables, $apiUrl, $api);
    }
}


function generateTempTables($db, $ArrayOfTables, $apiUrl, $api){
    foreach ($ArrayOfTables as $table) {
        createTable($db, $table, $apiUrl, $api);
    } 
}


function createTable($db, $table, $apiUrl, $api){
    
    // Create table name
    $tableName = "tmp_".$table;

    // API endpoint URL added table
    $apiUrl.= $table;
    
        switch ($api) {
            case "tcweb":
                $data = getTCWebJSON($apiUrl);
            break;  
            case "kromi":
                $data = getKromiJSON($apiUrl);
            break;
            case "sortly":
                $data = getSortlyJSON($apiUrl);
            break;  
        }

    // Check if data is not empty and is an array
    if (is_array($data) && !empty($data)) {
        
//        var_dump($data); // For checking
                
        // Dynamically create table based on JSON keys
        $columns = array_keys($data[0]);
        $columnDefinitions = [];
        foreach ($columns as $column) {
            // You can adjust the data types based on your needs
             $columnDefinitions[] = "`$column` VARCHAR(255)";
        }
        $columnDefinitionsString = implode(", ", $columnDefinitions);

         $createTableSQL = "CREATE TABLE IF NOT EXISTS `$tableName` (
            idTest INT AUTO_INCREMENT PRIMARY KEY, 
            $columnDefinitionsString
        )";

        // Execute create table query
        if ($db->query($createTableSQL) === TRUE) {
            echo 'Table <span style="color: blue"><b>'.$tableName.'</b></span> created successfully.<br>';
        } else {
            echo "Error creating table: " . $db->error . "<br>";
        }
        
        // empty table
        $sql = "TRUNCATE TABLE $tableName";
        $result = $db->query($sql);

        // Insert data into table
        foreach ($data as $row) {
            
            $columnsString = implode(", ", array_keys($row));
            $valuesString = implode("', '", array_map([$db, 'real_escape_string'], array_values($row)));
            
            if(!is_array($columnsString) OR !is_array($valuesString)){
                $insertSQL = "INSERT INTO `$tableName` ($columnsString) VALUES ('$valuesString')";
                if ($db->query($insertSQL) === TRUE) {
//                    echo "New record created successfully.<br>";
                } else {
                    echo "Error inserting record: " . $db->error . "<br>";
                }
            }
        }

    } else {
        echo "No data available to insert.";
    }
}

