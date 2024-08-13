<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/_includes.php";
include_once '_includes.php';


function totalNeededQuantity($db, $newRetro){
    
    $quantityName = "quantityTotal".$newRetro;
    
    if($newRetro == 'New'){
        $inClause = "1,2,3,4,5,6,7,8,9,10,11,12,13";
        $expression = "new implementations";
    } else {
        $inClause = "16,17,18";
        $expression = "for facelifts";
    }
    
    $msg = "<b>Calculate needed quantities for $expression </b><p>";
    
    // 1. Split regions
    $regions = regionsToArray($db);
    foreach ($regions as $regionId) {
        
        // Loop through IVM-IDs
        $sql = "Select id FROM tl_sortlyTemplatesIVM Where id in($inClause) order by id";
        $result = $db->query($sql);

        while($item = $result->fetch_assoc()){
             $id = $item['id'];
             
            //Insert quantity for this ID and region
                         
            $msg .= insertQuantitiesByRegions($db, $id, $regionId, $quantityName, $expression);
            
            //Insert working hours for this ID and region

            $msg .= insertHoursByRegions($db, $id, $quantityName, $regionId);
            
        }
    }
    return $msg;
}


function insertQuantitiesByRegions($db, $id, $regionId, $quantityName, $expression){
    
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
    Order By 
        regionId, 
        id_ivm         
        ";
    $result = $db->query($sql);

    while($item = $result->fetch_assoc()){
        $quantity = $item['quantity'];
        $regionName = $item['regionName'];
        $exclude = 1;
        $note = "Total needed quantity $expression $regionName [$exclude]";

        $msg .= insertQuantity($db, $id, $quantityName, $quantity, $note, $exclude, $regionId);
        $msg .= "<br>";
    }
    return $msg; 
}


function insertHoursByRegions($db, $id, $quantityName, $regionId){
    
    $msg = "";
    $quantity = 0;
  
    $quantityIVM = lookupTotalNeededQuantity($db, $id, $quantityName, $regionId);
    
    $sql = "Select
        Sum(tl_bom.bomQuantity) As quantityHours,
        tl_sortlyTemplatesIVM.id,
        tl_sortlyTemplatesIVM.name
    From
        tl_sortlyTemplatesIVM Inner Join
        tl_bom On tl_bom.pid = tl_sortlyTemplatesIVM.id Inner Join
        sortly On tl_bom.sortlyId = sortly.sortlyId
    Where
        tl_sortlyTemplatesIVM.id = 17 And
        tl_bom.hr = 1
    Group By
        tl_sortlyTemplatesIVM.id,
        tl_sortlyTemplatesIVM.name     
        ";
    $result = $db->query($sql);

    while($item = $result->fetch_assoc()){
        $quantityHours = $item['quantityHours'];
        $quantity = $quantityIVM * $quantityHours;
        
        $exclude = 1;
        $quantityName = "quantityTotalHours";
        
        $regionName = lookupNameRegion($db, $regionId);
        $note = "Total working hours on machine in $regionName [$exclude]";

        $msg .= insertQuantity($db, $id, $quantityName, $quantity, $note, $exclude, $regionId);
        $msg .= "<br>";
    }
    return $msg; 
}


function lookupNameRegion($db, $regionId){
    
    $sql = "Select name from tl_region WHERE id = $regionId";
    $result = $db->query($sql);
    
    while($item = $result->fetch_assoc()){ 
        return $item['name'];
    }
}


function lookupTotalNeededQuantity($db, $id, $quantityName,$regionId){
    
    $sql = "Select 
        quantity 
    From 
        kr_quantityIVM 
    Where 
        id_ivm = $id and
        quantityName like '$quantityName' and
        regionId = $regionId ";
    $result = $db->query($sql);

    while($item = $result->fetch_assoc()){
        return $item['quantity'];
    }
}


function updateTotalQuantity($db, $id){
      
}