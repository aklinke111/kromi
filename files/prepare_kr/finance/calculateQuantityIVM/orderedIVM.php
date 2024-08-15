<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/_includes.php";
include_once '_includes.php';


 function orderedIVM($db){
    // lookup ordered quantity

    $msg = "<b>IVMs ordered:</b><p>";
    
    $ForecastPeriod = globalVal($db, 'ForecastPeriod');
    
    $sql = "Select
        Sum(tl_orders.orderQuantity) As quantityOrdered,
        tl_orders.internalExternal,
        tl_sortlyTemplatesIVM.name,
        tl_sortlyTemplatesIVM.id,
        tl_region.name As regionName,
        tl_region.id As regionId
    From
        tl_orders Inner Join
        tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.sortlyId = tl_orders.sortlyId Inner Join
        tl_customer On tl_customer.id = tl_orders.customerId Inner Join
        sortly_customer On sortly_customer.sid = tl_customer.sid Inner Join
        sortly_subsidiary On sortly_subsidiary.sid = sortly_customer.pid Inner Join
        sortly_country On sortly_country.sid = sortly_subsidiary.pid Inner Join
        tl_country2Region On sortly_country.id = tl_country2Region.countryId Inner Join
        tl_region On tl_region.id = tl_country2Region.regionId
    Where
        Not tl_orders.delivered And
        tl_orders.calculated And
        tl_orders.estimatedDeliveryDate Between CurDate() And CurDate() + Interval $ForecastPeriod Month
    Group By
        tl_orders.internalExternal,
        tl_sortlyTemplatesIVM.name,
        tl_sortlyTemplatesIVM.id,
        tl_region.name,
        tl_region.id
    Order By
        regionId,
        tl_sortlyTemplatesIVM.id";
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        $quantityOrdered = $item['quantityOrdered'];
        $internalExternal = $item['internalExternal'];
        $id = $item['id'];
        $regionName = $item['regionName'];      
        $regionId = $item['regionId'];   

        $quantityName = "quantityOrdered".$internalExternal;
         
//        // Internal orders are considered, external not
//        if($internalExternal == 'internal'){
//            $exclude = 0;   
//        } else{
//            $exclude = 1; 
//        }
        
        $exclude = 1; 

        $note = $internalExternal." order with forecast period of ".$ForecastPeriod." months $regionName [$exclude]";
        
        // overwrite  Helix Master (5 to 17) and Slave (2 to 16) to assign Facelift ids - DELETE after facelift measures!
        if($id == 5){$id = 17;}
        if($id == 2){$id = 16;}

        $msg .= insertQuantity($db, $id, $quantityName, $quantityOrdered, $note, $exclude, $regionId);
        $msg .= "<br>";
    }
    return $msg;
}