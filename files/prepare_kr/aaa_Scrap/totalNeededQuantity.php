<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/_includes.php";
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
            $msg .= insertPersonnalHoursByRegions($db, $id, $quantityName, $regionId);
            $msg .= "<br>";
            
        }
    }
    return $msg;
}


function insertQuantitiesByRegions($db, $id, $regionId, $quantityName, $expression){
    
    $msg = "";
    
    $sql = 
    "Select
        tl_sortlyTemplatesIVM.name As modelName,
        tl_sortlyTemplatesIVM.id As id,
        tl_region.name as regionName,
        tl_sortlyTemplatesIVM.sortlyId,
        Count(tl_toolcenterProjectComponents.id) As quantity
    From
        tl_toolcenterProjectCategory Inner Join
        tl_toolcenterProjects On tl_toolcenterProjectCategory.id = tl_toolcenterProjects.projectCategory Inner Join
        tl_toolcenterProjectStatus On tl_toolcenterProjectStatus.id = tl_toolcenterProjects.projectStatus Inner Join
        tl_toolcenterProjectComponents On tl_toolcenterProjects.id = tl_toolcenterProjectComponents.pid Inner Join
        tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.id = tl_toolcenterProjectComponents.componentModel Inner Join
        sortly_ktc On tl_toolcenterProjects.ktcId = sortly_ktc.name Inner Join
        sortly_customer On sortly_customer.sid = sortly_ktc.pid Inner Join
        sortly_subsidiary On sortly_subsidiary.sid = sortly_customer.pid Inner Join
        sortly_country On sortly_country.sid = sortly_subsidiary.pid Inner Join
        tl_country2Region On sortly_country.id = tl_country2Region.countryId Inner Join
        tl_region On tl_region.id = tl_country2Region.regionId
    Where
        tl_toolcenterProjectStatus.status Like 'planned' And
        tl_toolcenterProjectComponents.`usage` Like 'install' And
        tl_toolcenterProjectCategory.category Like 'facelift' And
        tl_region.id = $regionId And
        tl_sortlyTemplatesIVM.id = $id
    Group By
        tl_sortlyTemplatesIVM.name,
        tl_sortlyTemplatesIVM.id,
        tl_region.name,
        tl_sortlyTemplatesIVM.sortlyId,
        tl_toolcenterProjectCategory.category
    Order By
        tl_toolcenterProjects.ktcId       
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


function insertQuantitiesByRegions_old($db, $id, $regionId, $quantityName, $expression){
    
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
       $quantityName = "quantityProjects_".$removeInstall."_".$faceliftNew;

        $msg .= insertQuantity($db, $id, $quantityName, $quantity, $note, $exclude, $regionId);
        $msg .= "<br>";
        
    }
    return $msg; 
}