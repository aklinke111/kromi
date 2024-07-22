<?php


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