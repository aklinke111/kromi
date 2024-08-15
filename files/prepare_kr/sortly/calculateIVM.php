<?php

// Needed to return pretty JSON
//header('Content-Type: application/json');
//
// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/_includes.php";

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/calculateQuantityIVM/_includes.php";

// main function for calculating quantites of IVM and update table tl_sortlyTemplatesIVM
if (isset($_GET['webhookFunction'])) {

    $function = $_GET['webhookFunction'];
    
    if($function == "calculateIVM"){
        
        $sql = "TRUNCATE TABLE kr_quantityIVM";
        $result = $db->query($sql);
        
        echo globals($db);
        echo "<p>";  

        echo orderedIVM($db);
        echo "<p>";            

//        echo forecastInstallations($db, 'Remove');
//        echo "<p>";  
//        echo forecastInstallations($db, 'Install');
//        echo "<p>";  
//        
        echo calculateQuantityIvmProjects($db, 'remove', 'all');
        echo "<p>";   
        echo calculateQuantityIvmProjects($db, 'install', 'facelift');
        echo "<p>"; 
        echo calculateQuantityIvmProjects($db, 'install', 'new');
        echo "<p>";          
            
        echo quantityOnStockIVM($db, 1);
        echo "<p>"; 
        echo quantityOnStockIVM($db, 2);
        echo "<p>";         
        echo calculateRawIvmOnStock($db);
        echo "<p>";  
        
        echo lookupQuantityValue($db, 'quantityMinimum');
        echo "<p>"; 
//        echo lookupQuantityValue($db, 'quantityExtra');
//        echo "<p>"; 

        echo totalNeededIVM($db);
        echo "<p>";         
        
        echo personnelVM($db);
        echo "<p>";         

        echo lookupQuantityOverAll($db);
        echo "<p>"; 
    }
}  