<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";



function insertQuantity($db, $id_ivm, $quantityName, $quantity, $note, $exclude, $regionId) {
    
    $nameIVM = lookupNameIVM($db, $id_ivm);
   
    $sql = "INSERT INTO kr_quantityIVM(
        tstamp,
        id_ivm,
        quantityName,
        quantity,
        regionId,        
        note,
        exclude
        ) 
    VALUES
        (
        ?, ?, ?, ?, ?, ?, ?
        )";

   $stmt = $db->prepare($sql);
   $parameterTypes = "iisdisi";
   $stmt->bind_param($parameterTypes,
        time(),
        $id_ivm,
        $quantityName,                        
        $quantity,
        $regionId,          
        $note,
        $exclude
   );
    // Execute the statement
    if($stmt->execute()){
        return "Succesfully inserted 'kr_quantityIVM.$quantityName' for IVM-ID $id_ivm [$nameIVM] with quantity of <b> $quantity </b>$note";
    } else {
        return "Error inserting 'kr_quantityIVM.$quantityName' for IVM-ID $id_ivm [$nameIVM] with quantity of $quantity ";
    }
}