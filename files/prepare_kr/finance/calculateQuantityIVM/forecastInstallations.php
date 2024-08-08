<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/_includes.php";
include_once '_includes.php';

function forecastInstallations($db, $removeInstall){
    
    $whereClause = "";
    if($removeInstall == 'Remove'){
        $whereClause = "And tl_toolcenterProjectCategory.category In ('discontinued', 'change configuration')";
    } else {
        $whereClause = "And tl_toolcenterProjectCategory.category In ('implementation', 'change configuration')";
    }
   
                
    // lookup period of passed IVM deinstallations 
    $HistoryPeriod = globalVal($db, 'HistoryPeriod');
    $ForecastPeriod = globalVal($db, 'ForecastPeriod');
    
    $msg = "<b>IVMs forecast based on devices '$removeInstall' according installations and changes in projects for a period of $HistoryPeriod months </b><p>";

    $sql = "Select
    tl_sortlyTemplatesIVM.name As model,
    tl_sortlyTemplatesIVM.id,
    Count(tl_sortlyTemplatesIVM.id) As quantity,
    tl_region.name as regionName,
    tl_region.id As regionId
From
    tl_toolcenterProjectCategory Inner Join
    tl_toolcenterProjects On tl_toolcenterProjectCategory.id = tl_toolcenterProjects.projectCategory Inner Join
    tl_toolcenterProjectStatus On tl_toolcenterProjectStatus.id = tl_toolcenterProjects.projectStatus Inner Join
    tl_toolcenterProjectComponents On tl_toolcenterProjects.id = tl_toolcenterProjectComponents.pid Inner Join
    tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.id = tl_toolcenterProjectComponents.componentModel Inner Join
    sortly_ktc On sortly_ktc.name = tl_toolcenterProjects.ktcId Inner Join
    sortly_customer On sortly_customer.sid = sortly_ktc.pid Inner Join
    sortly_subsidiary On sortly_subsidiary.sid = sortly_customer.pid Inner Join
    sortly_country On sortly_country.sid = sortly_subsidiary.pid Inner Join
    tl_country2Region On sortly_country.id = tl_country2Region.countryId Inner Join
    tl_region On tl_region.id = tl_country2Region.regionId
Where
    tl_toolcenterProjectComponents.`usage` Like '$removeInstall' And
    tl_toolcenterProjects.projectDateFinished Between CurDate() - Interval $HistoryPeriod Month And CurDate() And
    tl_toolcenterProjectStatus.status Like 'done' 
    $whereClause
Group By
    tl_sortlyTemplatesIVM.name,
    tl_region.id";
//    die();
    $result = $db->query($sql);

    while($item = $result->fetch_assoc()){ 
        $regionName = $item['regionName'];
        $regionId = $item['regionId'];        
        $id = $item['id'];
        $quantity = $item['quantity'];
        
        if($removeInstall == 'Remove'){
            $quantity *= -1;
        }

        // factor
        $factor = $ForecastPeriod / $HistoryPeriod;
        $quantity *= $factor;
        $exclude = 0;
        $quantityName = "quantityForecastInstallations".$removeInstall;
        $note = "period of $factor * $HistoryPeriod months in $regionName [$exclude]";

        $msg .= insertQuantity($db, $id, $quantityName, $quantity, $note, $exclude, $regionId);
        $msg .= "<br>";
    } 
    return $msg;
}


