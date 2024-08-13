<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/_includes.php";
include_once '_includes.php';


function quantityOnStockIVM($db, $activeInactive){

//    if($activeInactive == 1){
//        $expression = "active and availabe";
//    } else {
//        $expression = "for overhaul";
//    }
    $expression = "active and availabe";
    $msg = "<b>IVMs active and $expression on KROMI stock:</b><p>";
    
    // Calculating all devices on stock   
    
// $sql = 
//        "Select
//        tl_sortlyTemplatesIVM.name As model,
//        Sum(sortly.IVM) As quantity,
//        tl_sortlyTemplatesIVM.id As id,
//        tl_region.name As regionName,
//        tl_region.id As regionId
//    From
//        sortly Inner Join
//        tl_sortlyTemplatesIVM On sortly.name = tl_sortlyTemplatesIVM.name Inner Join
//        sortly_ktc On sortly.pid = sortly_ktc.sid Inner Join
//        sortly_customer On sortly_ktc.pid = sortly_customer.sid Inner Join
//        sortly_subsidiary On sortly_customer.pid = sortly_subsidiary.sid Inner Join
//        sortly_country On sortly_subsidiary.pid = sortly_country.sid Inner Join
//        tl_country2Region On sortly_country.id = tl_country2Region.countryId Inner Join
//        tl_region On tl_region.id = tl_country2Region.regionId
//    Where
//        sortly_ktc.name Not Like 'KTC-%' And
//        sortly_ktc.name Not Like 'SCRAP' And
//        sortly.IVM = 1 And
//        sortly.raw = 0 And
//        sortly.available = $activeInactive And
//        sortly.active = $activeInactive
//    Group By
//        tl_sortlyTemplatesIVM.name,
//        tl_sortlyTemplatesIVM.id,
//        tl_region.name,
//        tl_region.id
//    Order By
//        id,
//        regionName";
// 
 
 
 
    $sql = 
    "Select
        tl_sortlyTemplatesIVM.name As model,
        tl_sortlyTemplatesIVM.note,
        Count(sortly.IVM) As quantity,
        tl_sortlyTemplatesIVM.id As id,
        tl_region.name As regionName,
        tl_region.id As regionId    
    From
        sortly Inner Join
        tl_sortlyTemplatesIVM On sortly.name = tl_sortlyTemplatesIVM.name Inner Join
        sortly_ktc On sortly.pid = sortly_ktc.sid Inner Join
        sortly_customer On sortly_ktc.pid = sortly_customer.sid Inner Join
        sortly_subsidiary On sortly_customer.pid = sortly_subsidiary.sid Inner Join
        sortly_country On sortly_subsidiary.pid = sortly_country.sid Inner Join
        tl_country2Region On sortly_country.id = tl_country2Region.countryId Inner Join
        tl_region On tl_region.id = tl_country2Region.regionId
    Where
        sortly_ktc.name Not LIKE 'KTC-%' And
        sortly_ktc.name Not LIKE 'SCRAP' And 
        sortly.IVM = 1 And
        sortly.raw = 0 And        
        sortly.available = 1 And
        sortly.active = 1 
    Group By
        tl_sortlyTemplatesIVM.name,
        tl_region.name
    Order By
    regionId,
    tl_sortlyTemplatesIVM.id";
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        
        $id = $item['id'];
        $regionId = $item['regionId']; 
        $regionName = $item['regionName'];           
        $quantity = $item['quantity'];

        // in Sortly is made no difference between regular Helix or facelifted Helix. We need to adjust this according flag 'overhaul'
        // if we have en entry there, it is a facelifted device and assigned like that. Change id from 5 to 15 for master and from 2 to 16 for slaves

        // first update the regular devices with quantity minus quantity overhauled. We can subtract because in case there are no overhauled IVMs, $quantityOverhaul is 0 
//        $quantity -= $quantityOverhaul;
        $quantity *= -1;

        //overwrite  Helix Master (5 to 17) and Slave (2 to 16) to assign Facelift ids - all used for Brazil and not calculated
//        if($id == 5 or $id == 2){$id = 17;}
        
//        if($quantityOverhaul > 0){
//
//            switch ($id) {
//                case 5:
//                    $id = 17;
//                break;
//                case 2:
//                    $id = 16;
//                break;
//            }
//        }


//        if($activeInactive == 1){
//            $title = "Availabe";
//            $exclude = 0; 
//        } else {
//            $title = "Overhaul";
//        $exclude = 1;             
//        }
        $title = "Availabe";
        $quantityName = "quantity".$title."OnStock";
        $exclude = 0;
        
        $note = "IVMs $expression on KROMI stock $regionName [$exclude]";
        $msg .= insertQuantity($db, $id, $quantityName, $quantity, $note, $exclude, $regionId);
        $msg .= "<br>";

    } 
    return $msg;
}



function calculateRawIvmOnStock($db){
    $msg = "<b>IVMs on Kromi stock raw for build new:</b><p>";

// Calculating all devices on stock   
    $sql = 
    "Select
        tl_sortlyTemplatesIVM.name As model,
        Count(sortly.IVM) As quantity,
        tl_sortlyTemplatesIVM.id As id,
        sortly.sid
    From
        sortly Inner Join
        sortly_ktc On sortly.pid = sortly_ktc.sid Inner Join
        tl_sortlyTemplatesIVM On sortly.name = tl_sortlyTemplatesIVM.name
    Where
        sortly.IVM = 1 And
        sortly_ktc.name Not Like 'SCRAP' And
        sortly_ktc.name Not Like 'KTC-%' And
        sortly.active = 0 and
        sortly.raw = 1
    Group By
        tl_sortlyTemplatesIVM.name
    ";
    
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        
        $model = $item['model'];
        $sid = $item['sid'];          
        $quantity = $item['quantity'];
        $id = $item['id'];
        
        $regionId = 1;
        $regionName = "Europe";
        
        $quantityName = "quantityRaw";
        $exclude = 1;        
        $note = "IVMs on Kromi stock raw for build new in $regionName [$exclude]";
        $msg .= insertQuantity($db, $id, $quantityName, $quantity, $note, $exclude, $regionId);
        $msg .= "<br>";
        
        // assign ,model
        if($model == "KTC-HX/S"){
           $sid = 73933218;
        } elseif ($model == "KTC-HX/M"){
            $sid = 73933243;
        }
        
        $msg.= "$model (ID $id) updated in Sortly with quantity $quantity <p>";
        
        // update quantity in Sortly
        $newValue = $quantity;
        
        $msg.= singleUpdateQuantity($db, $sid, $newValue);
    } 
    return $msg;
}   