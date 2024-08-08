<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/_includes.php";
include_once '_includes.php';


function totalNeededQuantity($db, $newRetro){
    
    if($newRetro == 'New'){
        $expression = "new implementations";
    } else {
        $expression = "for facelifts";
    }
    
    $msg = "<b>Calculate needed quantities for $expression </b><p>";
    
    // 1. Split regions
    $regions = regionsToArray($db);
    foreach ($regions as $regionId) {
        
        // Loop through IVM-IDs
        $sql = "Select id FROM tl_sortlyTemplatesIVM";
        $result = $db->query($sql);

        while($item = $result->fetch_assoc()){
             $id = $item['id'];
             
            //Insert quantity for this ID and region
            $msg .= insertQuantitiesByRegions($db, $id, $regionId, $newRetro, $expression);
        }
    }
    return $msg;
}


function insertQuantitiesByRegions($db, $id, $regionId, $newRetro, $expression){
    
    $msg = "";
    
    $sql = "Select
        Sum(kr_quantityIVM.quantity) As quantity,
        tl_region.name as regionName
    From
        kr_quantityIVM Inner Join
        tl_region On kr_quantityIVM.regionId = tl_region.id
    Where
        kr_quantityIVM.id_ivm = $id And
        kr_quantityIVM.exclude = 0 And
        kr_quantityIVM.regionId = $regionId
    Group By
        tl_region.name
        ";
    $result = $db->query($sql);

    while($item = $result->fetch_assoc()){
        $quantity = $item['quantity'];
        $quantityName = "quantityTotal".$newRetro;
        $regionName = $item['regionName'];
        $exclude = 1;
        $note = "Total needed quantity $expression $regionName [$exclude]";

        $msg .= insertQuantity($db, $id, $quantityName, $quantity, $note, $exclude, $regionId);
        $msg .= "<br>";
    }
    return $msg; 
}