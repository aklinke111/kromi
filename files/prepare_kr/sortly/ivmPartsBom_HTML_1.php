<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";

//echo ivmBomDisplayAndUpdate($db);

function ivmBomDisplayAndUpdate($db){
    $msg = "";
    // empty column price
    $sql = "Update tl_sortlyTemplatesIVM set price = 0";
        if($resultUpdate = $db->query($sql)){
            $msg.= "Successfully updated column 'tl_sortlyTemplatesIVM.price' - set all to 0<p>"; 
        }  
    
    $sql = "Select id, name from tl_sortlyTemplatesIVM";
    $result = $db->query($sql);
    
    while ($item = $result->fetch_assoc()) {
        $id = $item['id'];
        $name = $item['name'];
        $priceTotal = 0;
        
        $msg.= "<b>".$name."</b><br>"; 

         $sql1 = "Select distinct
                    tl_sortlyTemplatesIVM.name,
                    tl_bom.sortlyId,
                    sortly.name As item,
                    sortly.price,
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
                   $priceTotal += $item1['price']*$item1['bomQuantity'];
                    $msg.=  $item1['sortlyId']." --- "; 
                    $msg.=  "<span style='color: blue;'>".$item1['bomQuantity']." pc.    </span>"; 
                    $msg.=  "<span style='color: red;'>".$item1['price']." €    </span>";                     
                    $msg.=  $name."<br>";
  
               } else{
                    $msg.=  "<span style='color: red;'>";
                    $msg.=  $item1['sortlyId']." "; 
                    $msg.=  $name."</span><br>";
               }
           }
//        echo "<br>";   
        $msg.=  "<b><span style='color: green;'>Total price: ".round($priceTotal,2)." €</span></b>";    
        $msg.=  "<p>";
        
    // update price
    $sql = "Update tl_sortlyTemplatesIVM set price = round($priceTotal,2) where id = $id";
        if($resultUpdate = $db->query($sql)){
            $msg.= "Successfully updated 'tl_sortlyTemplatesIVM.price' for $name<p>"; 
        }  
    }
    return $msg;
}



