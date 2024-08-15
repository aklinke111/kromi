<?php

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/_includes.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/calculateQuantityIVM/_includes.php";


function costPersonnelIVM($db, $id, $forecastDate, $regionId){

    $msg = "";
    $totalPerMonth = 0;
    
    $personnelCostTotalIVM = quantitiesAndCostsIVM($db, $regionId);
//    die();

    $regionName = lookupNameRegion($db, $regionId);
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
        $totalPerMonth = Round(($personnelCostTotalIVM / $countMonthEndOfFaceliftMeasures),2);
    } 


    // Insert
    $sql = "INSERT INTO kr_forecastEngineering (tstamp, forecastDate, categoryId, regionId, cost) VALUES (".time().", '$forecastDate', $id, $regionId, $totalPerMonth)";
    if($db->query($sql)){
        $msg .= "Personnal costs for producing IVM in region '$regionName' of $totalPerMonth € for forecast date $forecastDate and forecast period of $countMonthEndOfFaceliftMeasures months inserted successfully in table 'kr_forecastEngineering'<p>";
    }
    return $msg;
}


function quantitiesAndCostsIVM($db, $regionId){
    
$personnelCostPerIVM = 0;

    $sql = "
    Select
        tl_sortlyTemplatesIVM.name,
        kr_quantityIVM.id_ivm,
        kr_quantityIVM.quantity As quantity,
        kr_quantityIVM.regionId,
        tl_region.name As regionName,
        kr_quantityIVM.quantityName
    From
        kr_quantityIVM Inner Join
        tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.id = kr_quantityIVM.id_ivm Inner Join
        tl_region On kr_quantityIVM.regionId = tl_region.id
    Where
        kr_quantityIVM.quantityName Like 'quantityNeeded' And
        tl_region.id = $regionId
    Group By
        tl_sortlyTemplatesIVM.name,
        kr_quantityIVM.id_ivm,
        kr_quantityIVM.quantity,
        kr_quantityIVM.regionId,
        kr_quantityIVM.exclude,
        tl_region.name,
        kr_quantityIVM.quantityName
    Order By
        regionName,
        tl_sortlyTemplatesIVM.name";
    $result = $db->query($sql);
    
    while ($row = $result->fetch_assoc()) {
        $id_ivm = $row['id_ivm'];  
        $quantity = $row['quantity'];  
                
        $personnelCostPerIVM += $quantity * personnelCostPerIVM($db, $id_ivm);
//        echo $personnelCostPerIVM."<br>";
    }
    return $personnelCostPerIVM;
}



function personnelCostPerIVM($db, $id_ivm){
    
    $sql = "
        Select
        Sum(tl_bom.bomQuantity * sortly.price) As quantityHoursCost,
        tl_sortlyTemplatesIVM.id,
        tl_sortlyTemplatesIVM.name
    From
        tl_sortlyTemplatesIVM Inner Join
        tl_bom On tl_bom.pid = tl_sortlyTemplatesIVM.id Inner Join
        sortly On tl_bom.sortlyId = sortly.sortlyId
    Where
        tl_sortlyTemplatesIVM.id = $id_ivm And
        tl_bom.hr = 1
    Group By
        tl_sortlyTemplatesIVM.name
        ";
    $result = $db->query($sql);
    
    while ($row = $result->fetch_assoc()) {
        return $quantityHoursCost = $row['quantityHoursCost'];
    }
}