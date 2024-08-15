<?php

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/_includes.php";

function personellFaceliftIVM($db, $id, $forecastDate, $regionId){

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
        kr_quantityIVM.quantityName Like 'quantityProjects_install_facelift' And
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
        $msg .= "Monthly personnel cost for producing facelifted IVMs in region '$regionName' of $totalPerMonth € for forecast date $forecastDate and forecast period of $countMonthEndOfFaceliftMeasures months inserted successfully in table 'kr_forecastEngineering'<p>";
        }
    }
    
    return $msg;
}