<?php

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/_includes.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/calculateQuantityIVM/_includes.php";


// working hours for producing vending machines
function personnelVM($db){
    
    $msg = "<b>IVMs personnel costs:</b><p>";
    
    $sql = "Select
        tl_bom.bomQuantity As quantityHours,
        tl_sortlyTemplatesIVM.id,
        tl_sortlyTemplatesIVM.name,
        sortly.name As department
    From
        tl_sortlyTemplatesIVM Inner Join
        tl_bom On tl_bom.pid = tl_sortlyTemplatesIVM.id Inner Join
        sortly On tl_bom.sortlyId = sortly.sortlyId
    Where
        tl_bom.hr = 1
    Group By
        tl_sortlyTemplatesIVM.id,
        tl_sortlyTemplatesIVM.name,
        sortly.name
        ";
    $result = $db->query($sql);

    while($item = $result->fetch_assoc()){
        $id = $item['id'];
        $quantityHours = $item['quantityHours'];
        $department = $item['department'];        
        
        $exclude = 1;
        $quantityName = "personnalCost_".$department;
        $note = "Total $department personnal hours producing IVM [$exclude]";
        
        $msg .= insertQuantity($db, $id, $quantityName, $quantityHours, $note, $exclude, 0);
        $msg .= "<br>";
    }
    return $msg; 
}


//function personnalCostPerIVM($db, $id){
//    
//    
////    $quantityIVM = lookupTotalNeededQuantity($db, $id, $quantityName, $regionId);
////    $totalCostIVM = personnalCostPerIVM($db, $id) * $quantityIVM;
////    $regionName = lookupNameRegion($db, $regionId);
//            
//            
//    $sql = "
//    Select
//        Sum(tl_bom.bomQuantity * sortly.price) As quantityHoursCost,
//        tl_sortlyTemplatesIVM.id
//    From
//        tl_sortlyTemplatesIVM Inner Join
//        tl_bom On tl_bom.pid = tl_sortlyTemplatesIVM.id Inner Join
//        sortly On tl_bom.sortlyId = sortly.sortlyId
//    Where
//        tl_sortlyTemplatesIVM.id = $id And
//        tl_bom.hr = 1
//        ";
//    $result = $db->query($sql);
//    
//    while ($row = $result->fetch_assoc()) {
//        return $quantityHoursCost = $row['quantityHoursCost'];
//    }
//}