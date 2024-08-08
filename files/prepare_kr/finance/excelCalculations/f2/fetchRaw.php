<?php

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";

function fetchRawIVM($db){
    $excelContent = "";
    $group = "G2_";
    // open sheet
    $sheetName = "IVM Raw";  
    $excelContent .= '<Worksheet ss:Name="'.$group.$sheetName.'">';
    $excelContent .= '<Table>';
    
    // Write header row
    $excelContent .= '<Row>';
    $excelContent .= '<Cell><Data ss:Type="String">Type</Data></Cell>';
    $excelContent .= '<Cell><Data ss:Type="String">Model</Data></Cell>';
    $excelContent .= '<Cell><Data ss:Type="String">Location</Data></Cell>';
    $excelContent .= '<Cell><Data ss:Type="String">SortlyId</Data></Cell>';    
    $excelContent .= '<Cell><Data ss:Type="String">Price €</Data></Cell>';
    $excelContent .= '</Row>';  
    
    // query items
    $sql = "Select
        tl_sortlyTemplatesIVM.note as type,
        tl_sortlyTemplatesIVM.name As model,
        sortly_ktc.name As location,
        sortly.sortlyId,
        sortly.inventoryNo,
        sortly.IVM,
        sortly.raw
    From
        sortly Inner Join
        sortly_ktc On sortly.pid = sortly_ktc.sid Inner Join
        tl_sortlyTemplatesIVM On sortly.name = tl_sortlyTemplatesIVM.name
    Where
        sortly_ktc.name Not Like 'SCRAP' And
        tl_sortlyTemplatesIVM.note Like 'Helix%' And
        sortly.raw = 1
    Order By
        type,
        location";

    $result = $db->query($sql);
    
    while ($item = $result->fetch_assoc()) {
        // lookup for price
        if ($item['model'] == 'KTC-HX/S'){
            $sortlyIdLookup = "SD0M4T2603";
        }
        elseif ($item['model'] == 'KTC-HX/M'){
          $sortlyIdLookup = "SD0M4T2604";  
        }
        $sql = "Select price from sortly where sortlyId like '$sortlyIdLookup'";
         $resultPrice = $db->query($sql);
         
         while ($rowPrice = $resultPrice->fetch_assoc()) {
             $price = round($rowPrice['price'],2);
         }
        // write content
       $excelContent .= '<Row>';
       $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['type']) . '</Data></Cell>';       
       $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['model']) . '</Data></Cell>';
       $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['location']) . '</Data></Cell>';
       $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['sortlyId']) . '</Data></Cell>';
       $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($price) . '</Data></Cell>';             
       $excelContent .= '</Row>';   
    }
    // Close tags sheet
    $excelContent .= '</Table>';
    $excelContent .= '</Worksheet>';
    
    return $excelContent;   
}