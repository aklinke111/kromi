<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/_includes.php";
include_once '_includes.php';


function lookupQuantityValue($db, $column){
    
    $msg = "<b>Read $column values</b><p>";
        
    $sql = "Select id, $column FROM tl_sortlyTemplatesIVM";
    $result = $db->query($sql);
    
    while($item = $result->fetch_assoc()){
        $id = $item['id'];
        $quantity = $item[$column];
        $quantityName = $column;

        $regionId = 1;
        $regionName = "all regions";
        $exclude = 0;
        $note = "Read $column values for $regionName [$exclude]";
        $msg .= insertQuantity($db, $id, $quantityName, $quantity, $note, $exclude, $regionId);
        $msg .= "<br>";
    }
    return $msg;
}