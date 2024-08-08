<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/_includes.php";
include_once '_includes.php';


function calculateQuantityIvmProjectsReturn($db){

    $msg = "<b>IVMs returned from planned projects:</b><p>";
    
// Calculating all devices from planned projects & components exclude Brazil--->  SQL in FlySQL  query 'projectComponents'    
    $sql = 
    "Select
        tl_sortlyTemplatesIVM.name As model,
        Count(tl_sortlyTemplatesIVM.id ) As quantity,
        tl_sortlyTemplatesIVM.id As id,
        tl_sortlyTemplatesIVM.note
    From
        tl_toolcenterProjectCategory Inner Join
        tl_toolcenterProjects On tl_toolcenterProjectCategory.id = tl_toolcenterProjects.projectCategory Inner Join
        tl_toolcenterProjectStatus On tl_toolcenterProjectStatus.id = tl_toolcenterProjects.projectStatus Inner Join
        tl_toolcenterProjectComponents On tl_toolcenterProjects.id = tl_toolcenterProjectComponents.pid Inner Join
        tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.id = tl_toolcenterProjectComponents.componentModel
    Where
        tl_toolcenterProjectStatus.status Like 'planned' And
        tl_toolcenterProjectComponents.`usage` Like 'remove' and
        tl_toolcenterProjects.ktcId not like 'KTC-3%'
    Group By
        tl_sortlyTemplatesIVM.name
    ";
    
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        $model = $item['model'];
        $quantity = $item['quantity'];
        $id = $item['id'];
        
//        // overwrite  Helix Master (5 to 17) and Slave (2 to 16) to assign Facelift ids
//        if($id == 5){$id = 17;}
//        if($id == 2){$id = 16;}
        
        $quantityName = "quantityReturn";
        $note = "IVMs returned from planned projects";
        $exclude = 1;
        $msg .= insertQuantity($db, $id, $quantityName, $quantity, $note, $exclude);
        $msg .= "<br>";
    } 
    return $msg;
}



function calculateQuantityIvmProjects($db, $removeInstall){

    $msg = "<b>IVMs to $removeInstall for planned projects:</b><p>";

// Calculating all devices from planned projects & components exclude Brazil--->  SQL in FlySQL  query 'projectComponents'    
    $sql = 
    "Select
    tl_sortlyTemplatesIVM.name As model,
    Count(tl_toolcenterProjects.ktcId) As quantity,
    tl_sortlyTemplatesIVM.id As id,
    tl_sortlyTemplatesIVM.note,
    tl_region.id as regionId,    
    tl_region.name as regionName
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
    tl_toolcenterProjectComponents.`usage` Like '$removeInstall'
Group By
    tl_sortlyTemplatesIVM.name,
    tl_region.name,
    tl_region.id
    ";
    
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        $regionId = $item['regionId'];
        $regionName = $item['regionName'];
        $quantity = $item['quantity'];
        $id = $item['id'];
        
        if($removeInstall == 'remove'){
            $exclude = 1; 
        } else {
            $exclude = 0; 
        }
       
        $quantityName = "quantityProjects".$removeInstall;
        $note = "IVMs for projects '$removeInstall' in $regionName [$exclude]";

        $msg .= insertQuantity($db, $id, $quantityName, $quantity, $note, $exclude, $regionId);
        $msg .= "<br>";
    } 
    return $msg;
}
