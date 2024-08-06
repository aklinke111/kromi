<?php

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";


function abcAnalysesSupplier($db){
   
    $msg = "";
    
    $msg .= truncateTableAbcAnalyses($db);
    
    $sql = "Select
            bomCalculations.supplierName,
            Round(Sum(bomCalculations.quantityNeeded * sortly.price),0) As totalPrice
        From
            bomCalculations Inner Join
            sortly On sortly.sortlyId = bomCalculations.sortlyId
        Where
            sortly.pid = '58670984'
        Group By
            bomCalculations.supplierName
        Order by totalPrice desc
        ";
        $result = $db->query($sql);
        
        while($item = $result->fetch_assoc()){ 
        
        $supplierName = $item['supplierName'];
        $totalPrice = $item['totalPrice'];
        
        // calculate percent value
        $totalAllSuppliers = totalAllSuppliers($db);
        $percentValue = $totalPrice / $totalAllSuppliers * 100;
        
        
        insertDataAbc($db, $supplierName, $totalPrice, $percentValue);
        
        }
    
}



function insertDataAbc($db, $supplierName, $totalPrice, $percentValue) {
    // Prepare the SQL statement
    $sql = "INSERT INTO kr_abcAnalyses(
                tstamp,
                supplierName,       
                totalPrice,
                percentValue
                ) 
            VALUES
                (
                ?, ?, ?, ?
                )";

    $stmt = $db->prepare($sql);
    $parameterTypes = "isdd";
    $stmt->bind_param($parameterTypes,
            time(),
            $supplierName,
            $totalPrice,
            $percentValue
    );
    // Execute the statement
    $stmt->execute();
}



function truncateTableAbcAnalyses($db){
    // truncate table 'kr_abcAnalyses'
    $sql = "TRUNCATE TABLE kr_abcAnalyses";
    $result = $db->query($sql);
}



function totalAllSuppliers($db){
    $sql = "Select
                Round(Sum(bomCalculations.quantityNeeded * sortly.price),0) As totalPrice
            From
                bomCalculations Inner Join
                sortly On sortly.sortlyId = bomCalculations.sortlyId
            Where
                sortly.pid = '58670984'";
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        return $item['totalPrice'];
    }
}