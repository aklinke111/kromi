<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";

$sql = "Select id, name from tl_sortlyTemplatesIVM";
$result = $db->query($sql);

    while ($item = $result->fetch_assoc()) {
        $id = $item['id']; 
        echo "<b>".$item['name']."</b><br>"; 

         $sql1 = "Select distinct
                    tl_sortlyTemplatesIVM.name,
                    tl_bom.sortlyId,
                    sortly.name As item,
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
        echo "<p>";

           while ($item1 = $result1->fetch_assoc()) {
               $calculate = $item1['calculate'];
               if($calculate){
                    echo $item1['sortlyId']." --- "; 
                    echo "<span style='color: blue;'>".$item1['bomQuantity']." pc.    </span>";                    
                    echo $item1['item']."<br>";
  
               } else{
                    echo "<span style='color: red;'>";
                    echo $item1['sortlyId']." "; 
                    echo $item1['item']."</span><br>";
               }
           }
        echo "<p>";
    }
