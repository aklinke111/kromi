<?php

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/_includes.php";



function personnelNewIVM($db, $id, $forecastDate, $regionId){

    $ForecastPeriod = globalVal($db, 'ForecastPeriod');
    $HistoryPeriod = globalVal($db, 'HistoryPeriod');
    
    $msg = "";
    $totalPerMonth = 0;
    
    $sql = "Select
    Sum(kr_quantityIVM.quantity * tl_sortlyTemplatesIVM.priceHr) As totalPrice,
    tl_region.name As regionName
    From
        kr_quantityIVM Inner Join
        tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.id = kr_quantityIVM.id_ivm Inner Join
        tl_region On kr_quantityIVM.regionId = tl_region.id
    Where
        kr_quantityIVM.quantityName Like 'quantityProjects_install_new' And
        kr_quantityIVM.regionId = $regionId
    ";
    $result = $db->query($sql);
    
    while ($row = $result->fetch_assoc()) {
        $totalPrice = $row['totalPrice'];
        $regionName = $row['regionName'];
        
//        $totalPendingOrders = totalPendingOrders($db, $ForecastPeriod, 'external');
        $totalPendingOrders = 0;     

        
        $totalPerMonth = Round((($totalPrice - $totalPendingOrders) / $HistoryPeriod),2);
        // Insert
        $sql = "INSERT INTO kr_forecastEngineering (tstamp, forecastDate, categoryId, regionId, cost) VALUES (".time().", '$forecastDate', $id, $regionId, $totalPerMonth)";
        if($db->query($sql)){
        $msg .= "Monthly cost for peronell producing new IVM in region '$regionName' of $totalPerMonth € for forecast date $forecastDate and forecast period of $ForecastPeriod months inserted successfully in table 'kr_forecastEngineering'<p>";
        }
    }
    
    return $msg;
}
