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