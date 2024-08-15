<?php

function personnelCostIVMs($db){
    // loops IVMs to select the total price for all department working hours on IVMs
    $msg = "";
    
    // empty column price (personnel cost)
    $sql = "Update tl_sortlyTemplatesIVM set priceHr = 0";
    if($db->query($sql)){
        $msg.= "Successfully updated column 'tl_sortlyTemplatesIVM.price' - set all to 0<p>"; 
    }  
    
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
        tl_bom.hr = 1
    Group By
        tl_sortlyTemplatesIVM.name
        ";
    $result = $db->query($sql);
    
    while ($item = $result->fetch_assoc()) {
        $id = $item['id'];
        $quantityHoursCost = $item['quantityHoursCost'];
        
        // update
        $msg.= personnelCostIVMUpdate($db, $id, $quantityHoursCost, $msg)."<br>";;
    }
}

function personnelCostIVMUpdate($db, $id, $quantityHoursCost, $msg){
    
    // update price for personnel costs
    $sql = "Update tl_sortlyTemplatesIVM set priceHr = round($quantityHoursCost,2) where id = $id";
//    die();
    if($db->query($sql)){
        $nameIVM = lookupNameIVM($db, $id);
        $msg.= "Successfully updated 'tl_sortlyTemplatesIVM.priceHr' for $nameIVM <p>"; 
    }  
    return $msg;
}


function lookupNameIVM($db, $id_ivm){
    
    $sql = "Select name from tl_sortlyTemplatesIVM WHERE id = $id_ivm";
    $result = $db->query($sql);
    
    while($item = $result->fetch_assoc()){ 
        return $item['name'];
    }
}