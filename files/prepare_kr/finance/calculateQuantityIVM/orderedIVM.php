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
        tl_sortlyTemplatesIVM.id
    From
        tl_orders Inner Join
        tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.sortlyId = tl_orders.sortlyId
    Where
        Not tl_orders.delivered And
        tl_orders.calculated And
        tl_orders.estimatedDeliveryDate Between CurDate() And CurDate() + Interval $ForecastPeriod Month
    Group By
        tl_orders.internalExternal,
        tl_sortlyTemplatesIVM.name,
        tl_sortlyTemplatesIVM.id";
    
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        $quantityOrdered = $item['quantityOrdered'];
        $internalExternal = $item['internalExternal'];
        $id = $item['id'];
       
        $regionId = 0;
        $quantityName = "quantityOrdered".$internalExternal;
        $exclude = 0;        
        $note = $internalExternal." order with forecast period of ".$ForecastPeriod." months [$exclude]";

        $msg .= insertQuantity($db, $id, $quantityName, $quantityOrdered, $note, $exclude, $regionId);
        $msg .= "<br>";
    }
    return $msg;
}