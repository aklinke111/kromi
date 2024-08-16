<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";


function lookupTotalCountIVM($db){
        
    $sql = "Select
        sum(kr_quantityIVM.quantity) as totalCount
    From
        kr_quantityIVM Left Join
        tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.id = kr_quantityIVM.id_ivm
    Where
        kr_quantityIVM.quantityName Like 'quantityOverAll' And
        kr_quantityIVM.id_ivm <> 6 And
        tl_sortlyTemplatesIVM.exclude = 0";
    $result = $db->query($sql);
    
    while($item = $result->fetch_assoc()){ 
        return $item['totalCount'];
    }
}