<?php

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/_includes.php";



function totalCostFacelift($db, $id, $forecastDate, $regionId){

    $msg = "";
    $totalPerMonth = 0;
    
    $sql = "Select
    Sum(kr_quantityIVM.quantity * tl_sortlyTemplatesIVM.price) As totalPrice,
    tl_region.name As regionName
    From
        kr_quantityIVM Inner Join
        tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.id = kr_quantityIVM.id_ivm Inner Join
        tl_region On kr_quantityIVM.regionId = tl_region.id
    Where
        kr_quantityIVM.quantityName Like 'quantityTotalFacelift' And
        kr_quantityIVM.regionId = $regionId
    ";
    $result = $db->query($sql);
    
    while ($row = $result->fetch_assoc()) {
        $totalPrice = $row['totalPrice'];
        $regionName = $row['regionName'];

        $globalVar = "EndOfFaceliftMeasures ".$regionName;
        
        $dateEndOfFacelift = globalVal($db, $globalVar);
        $countMonthEndOfFaceliftMeasures = getMonthsToDate($dateEndOfFacelift) + 1;
               
        $date = date_create($dateEndOfFacelift);
        $date_1 = date_format($date,"Y-m");
        $date_2 = $forecastDate;

//         echo " ############################### ".$totalPrice. " //".$date_1."//". " $countMonthEndOfFaceliftMeasures ############################################";

         
        // Dieser Betrag wird nur über die Laufzeit in Monaten hinzugefügt, danach wird er zu 0
        $answer = compareMonthYear($date_1, $date_2);

        if($answer == "higher date" or $answer == 'same date'){
            $totalPerMonth = Round(($totalPrice / $countMonthEndOfFaceliftMeasures),2);
        } 
        
        // category id
        if($answer == "higher date" or $answer == 'same date'){
            $totalPerMonth = Round(($totalPrice / $countMonthEndOfFaceliftMeasures),2);
        }         

        // Insert
        $sql = "INSERT INTO kr_forecastEngineering (tstamp, forecastDate, categoryId, regionId, cost) VALUES (".time().", '$forecastDate', $id, $regionId, $totalPerMonth)";
        if($db->query($sql)){
        $msg .= "Monthly cost for KTC-facelifts in region '$regionName' of $totalPerMonth € for forecast date $forecastDate and forecast period of $countMonthEndOfFaceliftMeasures months inserted successfully in table 'kr_forecastEngineering'<p>";
        }
    }
    
    return $msg;
}



//function totalCostFacelift($db, $id, $forecastDate, $ForecastPeriod){
//
//    $msg = "";
//    $totalPerMonth = 0;
//
//    $modelIds = "16, 17, 18"; // Facelift models
//    $totalAllDevices = calculationCostModels($db, $modelIds);
//    
//    $dateEndOfFacelift = globalVal($db, 'EndOfFaceliftMeasures');
//    $countMonthEndOfFaceliftMeasures = getMonthsToDate($dateEndOfFacelift);
//    
//
//    $date = date_create($dateEndOfFacelift);
//    $date_1 = date_format($date,"Y-m");
//    $date_2 = $forecastDate;
//    
//    // Dieser Betrag wird nur über die Laufzeit in Monaten hinzugefügt, danach wird er zu 0
//    $answer = compareMonthYear($date_1, $date_2);
//    
//    if($answer == "higher date"){
//        $totalPerMonth = Round(($totalAllDevices / $countMonthEndOfFaceliftMeasures),2);
//    } 
//
//    // Insert
//    $sql = "INSERT INTO kr_forecastEngineering (tstamp, forecastDate, categoryId, cost) VALUES (".time().", '$forecastDate', $id, $totalPerMonth)";
//    if($result = $db->query($sql)){
//    $msg .= "Monthly cost for KTC-facelifts of $totalPerMonth € for forecast date $forecastDate and forecast period of $countMonthEndOfFaceliftMeasures months inserted successfully in table 'kr_forecastEngineering'<p>";
//    }
//    
//    return $msg;
//}