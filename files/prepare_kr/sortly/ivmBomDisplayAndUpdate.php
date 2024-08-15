<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/sortly/ivmBomDisplayAndUpdate.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/_includes.php";

if (isset($_GET['webhookFunction'])) {

    $function = $_GET['webhookFunction'];
    
    if($function == "ivmBomDisplayAndUpdate"){
        
        echo ivmBomDisplayAndUpdate($db);
//        echo personnelCostIVMs($db);

    }
}
     


function ivmBomDisplayAndUpdate($db){
    $msg = "";
    
    // empty column price (material)
    $sql = "Update tl_sortlyTemplatesIVM set price = 0";
    if($db->query($sql)){
        $msg.= "Successfully updated column 'tl_sortlyTemplatesIVM.price' - set all to 0<p>"; 
    }  
    
    
    $sql = "Select id, name from tl_sortlyTemplatesIVM";
    $result = $db->query($sql);
    
    while ($item = $result->fetch_assoc()) {
        $id = $item['id'];
        $name = $item['name'];
        
        $priceTotalMaterial = 0;
        $costTotalPersonnal = 0;        
        
        $msg.= "<b>".$name."</b><br>"; 

         $sql1 = "Select distinct
                    tl_sortlyTemplatesIVM.name,
                    tl_bom.sortlyId,
                    sortly.name As item,
                    round(sortly.price,2) as price,
                    tl_bom.bomQuantity,
                    tl_bom.calculate
                From
                    tl_sortlyTemplatesIVM Inner Join
                    tl_bom On tl_sortlyTemplatesIVM.id = tl_bom.pid Inner Join
                    sortly On sortly.sortlyId = tl_bom.sortlyId
                Where tl_bom.pid = $id 
                Order By
                    sortly.name";
         
        $result1 = $db->query($sql1);
        $msg.=  "<p>";

           while ($item1 = $result1->fetch_assoc()) {
               $calculate = $item1['calculate'];
               if($calculate){
                   $priceTotalMaterial += $item1['price']*$item1['bomQuantity'];
                    $msg.=  $item1['sortlyId']." --- "; 
                    $msg.=  "<span style='color: blue;'>".$item1['bomQuantity']." pc.    </span>"; 
                    $msg.=  "<span style='color: red;'>".$item1['price']." €    </span>";                     
                    $msg.=  $item1['item']."<br>";
  
               } else{
                    $costTotalPersonnal += $item1['price']*$item1['bomQuantity'];                   
                    $msg.=  "<span style='color: red;'>";
                    $msg.=  $item1['sortlyId']." "; 
                    $msg.=  $item1['item']."</span><br>";
               }
           }
//        echo "<br>";   
        $msg.=  "<b><span style='color: green;'>Total price: ".round($priceTotalMaterial,2)." €</span></b>";    
        $msg.=  "<p>";
        
        $materialPrice = round($priceTotalMaterial,2); 
        
    // update price for material
    $sql = "Update tl_sortlyTemplatesIVM set price = $materialPrice where id = $id";
        if($db->query($sql)){
            $msg.= "Successfully updated 'tl_sortlyTemplatesIVM.price' with value of $materialPrice for $name<p>"; 
        } 
    }
    return $msg;
}



