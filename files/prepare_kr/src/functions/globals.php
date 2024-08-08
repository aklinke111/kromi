<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";


function globalVal($db, $var){
    
    // lookup for value from var tl_Globals
    
    $sql_val = "Select val from tl_globals where var like '$var'";
    $result_val = $db->query($sql_val);
    
    while ($row_val = $result_val->fetch_assoc()) {
        return $row_val['val'];
    }
}


function globals($db){
    $msg = "";
    
    // lookup ForecastPeriod
    $ForecastPeriod = globalVal($db, 'ForecastPeriod');
    $msg .= "Forecast period: $ForecastPeriod months<br>";
    
       // lookup DGUV3 period
    $DGUV3_Period = globalVal($db, 'DGUV3_Period');
    $msg .= "Period between DGUV3 checks: ".$DGUV3_Period."<br>";
    
    // 
    // lookup for pice per each IVM checked e.g. => 80.00€
    $DGUV3_PricePerIVM = globalVal($db, 'DGUV3_PricePerIVM');
    $msg .= "Price per checked IVM DGUV3: ".$DGUV3_PricePerIVM."<br>";
    
    // lookup for pice per each approach e.g. => 79.00€
    $DGUV3_PricePerApproach = globalVal($db, 'DGUV3_PricePerApproach');
    $msg .= "DGUV3 price per approach: ".$DGUV3_PricePerApproach."<br>";
    
    // lookup period of passed IVM installations 
    $HistoryPeriod = globalVal($db, 'HistoryPeriod');
    $msg .= "Period looking back in history to receive statistical data: ".$HistoryPeriod." months<br>"; 
    $msg .=  "<p>";
    
    return $msg;
}