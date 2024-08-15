<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/_includes.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/_includes.php";
include_once '_includes.php';


function totalNeededIVM($db){

    $msg = "<b>Calculate total needed IVM quantities</b><p>";

        $sql = "Select
        tl_sortlyTemplatesIVM.name,
        kr_quantityIVM.id_ivm,
        Sum(kr_quantityIVM.quantity) As quantity,
        kr_quantityIVM.regionId,
        tl_region.name As regionName
    From
        kr_quantityIVM Inner Join
        tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.id = kr_quantityIVM.id_ivm Inner Join
        tl_region On kr_quantityIVM.regionId = tl_region.id
    Where
        kr_quantityIVM.exclude = 0
    Group By
        tl_sortlyTemplatesIVM.name,
        kr_quantityIVM.id_ivm,
        kr_quantityIVM.regionId,
        kr_quantityIVM.exclude,
        tl_region.name
    Order By
        regionName,
        tl_sortlyTemplatesIVM.name";
        $result = $db->query($sql);

        while($item = $result->fetch_assoc()){
             $id = $item['id_ivm'];
             $quantity = $item['quantity'];
             $regionName = $item['regionName'];
             $regionId = $item['regionId'];
              
        $exclude = 1;    
        $quantityName = "quantityNeeded";
        $note = " Total needed quantity of IVM needed for $regionName [$exclude]";

        $msg .= insertQuantity($db, $id, $quantityName, $quantity, $note, $exclude, $regionId);
        $msg .= "<br>";
        }
    return $msg;
}