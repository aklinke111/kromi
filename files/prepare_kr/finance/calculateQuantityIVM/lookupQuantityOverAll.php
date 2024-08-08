<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/_includes.php";
include_once '_includes.php';


function lookupQuantityOverAll($db){

    $msg = "<b>Lookup quantity of all active IVMs</b><p>";
    
    $sql = "Select
        Count(sortly.name) As quantityOverAll,
        sortly.name,
        tl_sortlyTemplatesIVM.id,
        tl_region.id As regionId,
        tl_region.name As regionName
    From
        sortly Inner Join
        tl_sortlyTemplatesIVM On sortly.name = tl_sortlyTemplatesIVM.name Inner Join
        sortly_ktc On sortly_ktc.sid = sortly.pid Inner Join
        sortly_customer On sortly_customer.sid = sortly_ktc.pid Inner Join
        sortly_subsidiary On sortly_subsidiary.sid = sortly_customer.pid Inner Join
        sortly_country On sortly_country.sid = sortly_subsidiary.pid Inner Join
        tl_country2Region On sortly_country.id = tl_country2Region.countryId Inner Join
        tl_region On tl_region.id = tl_country2Region.regionId
    Where
        sortly.name Not Like 'SCRAP' And
        sortly.IVM = 1 And
        sortly.active = 1 And
        sortly.available = 1
    Group By
        sortly.name,
        tl_sortlyTemplatesIVM.id,
        tl_region.name    
    ";
    $result = $db->query($sql);
    
    while($item = $result->fetch_assoc()){ 
        $id = $item['id']; 
        $quantityOverAll = $item['quantityOverAll'];
        $regionName = $item['regionName'];
        $regionId = $item['regionId'];
        $quantityName = "quantityOverAll";
        $exclude = 1;        
        $note = "All active, not scrapped IVMs in $regionName [$exclude]";
        $msg .= insertQuantity($db, $id, $quantityName, $quantityOverAll, $note, $exclude, $regionId);
        $msg .= "<br>";

    }
    return $msg;
}