<?php

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";


function totalPriceFromBOM($db){
    // Summe der benötigten Teile laut BOM

    $sql = "Select Distinct
        Round(Sum((sortly.price * (bomCalculations.quantityNeeded - bomCalculations.quantityStock -
        bomCalculations.quantityOrdered))),2) As purchasePrice
    From
        bomCalculations Left Join
        sortly On bomCalculations.sortlyId = sortly.sortlyId
    where
        (sortly.price * (bomCalculations.quantityNeeded - bomCalculations.quantityStock -
        bomCalculations.quantityOrdered)) > 0";
    $result = $db->query($sql);
    
    while ($row = $result->fetch_assoc()) {
        return $row['purchasePrice'];
    }
}


function calculationCostModels($db, $modelIds){
    
    $totalAllDevices = 0;
        
    $sql = "Select
        tl_sortlyTemplatesIVM.name As model,
        tl_sortlyTemplatesIVM.id As id,
        tl_sortlyTemplatesIVM.price,
        tl_sortlyTemplatesIVM.quantity,
        (tl_sortlyTemplatesIVM.price * tl_sortlyTemplatesIVM.quantity) AS totalPerModel
    FROM 
        tl_sortlyTemplatesIVM
    Where
        tl_sortlyTemplatesIVM.id IN ($modelIds)";
    $result = $db->query($sql);
    
    // iterate price and quantity needed for each IVM-model
    while ($row = $result->fetch_assoc()) {
        $totalPerModel = $row['totalPerModel'];
        $totalAllDevices += $totalPerModel;
    }   
    return $totalAllDevices;
}