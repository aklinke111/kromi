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
    tl_region.id
Order By
    regionId,
    tl_sortlyTemplatesIVM.id";
//    die();
    $result = $db->query($sql);

    while($item = $result->fetch_assoc()){ 
        $regionName = $item['regionName'];
        $regionId = $item['regionId'];        
        $id = $item['id'];
        $quantityFromHistory = $item['quantity'];
        
        // compare quantities from lookback in history with quantity in projects waiting for contract
        $quantity = lookupForecast($db, $id, $quantityFromHistory, $regionId);
        
        if($removeInstall == 'Remove'){
            $quantity *= -1;
        }

        // factor
//        $factor = $ForecastPeriod / $HistoryPeriod;
//        $quantity_24 = $quantity * $factor;
        $exclude = 0;
        $quantityName = "quantityForecastInstallations".$removeInstall;
        $note = "period of $HistoryPeriod months in $regionName [$exclude]";

        $msg .= insertQuantity($db, $id, $quantityName, $quantity, $note, $exclude, $regionId);
        $msg .= "<br>";
    } 
    return $msg;
}


function lookupForecast($db, $idMain, $quantityFromHistory, $regionId){
    
    $sql = "Select
        tl_sortlyTemplatesIVM.name As model,
        tl_sortlyTemplatesIVM.id As id,
        Count(tl_toolcenterProjectComponents.id) As quantity,
        tl_region.name As regionName,
        tl_region.id As regionId
    From
        tl_toolcenterProjectCategory Inner Join
        tl_toolcenterProjects On tl_toolcenterProjectCategory.id = tl_toolcenterProjects.projectCategory Inner Join
        tl_toolcenterProjectStatus On tl_toolcenterProjectStatus.id = tl_toolcenterProjects.projectStatus Inner Join
        tl_toolcenterProjectComponents On tl_toolcenterProjects.id = tl_toolcenterProjectComponents.pid Inner Join
        tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.id = tl_toolcenterProjectComponents.componentModel Inner Join
        tl_country On tl_toolcenterProjects.countryId = tl_country.id Inner Join
        sortly_country On sortly_country.sid = tl_country.sid Inner Join
        tl_country2Region On sortly_country.id = tl_country2Region.countryId Inner Join
        tl_region On tl_country2Region.regionId = tl_region.id
    Where
        tl_toolcenterProjectComponents.`usage` Like 'install' And
        tl_toolcenterProjects.projectDateFinished Between CurDate() And CurDate() + Interval 12 Month And
        tl_toolcenterProjectStatus.status Like 'waiting%' And
        tl_toolcenterProjectCategory.category Like 'implementation' and
        tl_sortlyTemplatesIVM.id = $idMain and
        tl_region.id = $regionId   
    Group By
        tl_sortlyTemplatesIVM.name,
        tl_region.id
    order by
        tl_region.id";
   $result = $db->query($sql);
    
    if($result->num_rows >0){ 
        
        while($item = $result->fetch_assoc()){ 
              $quantity = $item['quantity'];
              // if quantity from contracts > than statistical qunatity...
            if($quantity > $quantityFromHistory){ 
                return $quantityFromHistory + $quantity; // add it to 12 momths historic quantity
            } else {
               return $quantity + $quantity; // double up the quntities to reach 24 months
            }
        }
    }else{
        return $quantityFromHistory + $quantityFromHistory;
    }
        
    

}


