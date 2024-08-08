<?php

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";

function fetchProducedIVM($db, $dateStart, $dateEnd ){
    $excelContent = "";
    //Group for better use of sheets
    $group = "G3_";
    // open sheet
    $sheetName = "IVM released";  
    $excelContent .= '<Worksheet ss:Name="'.$group.$sheetName.'">';
    $excelContent .= '<Table>';
    
    // Write header row
    $excelContent .= '<Row>';
    $excelContent .= '<Cell><Data ss:Type="String">Type</Data></Cell>';
    $excelContent .= '<Cell><Data ss:Type="String">Model</Data></Cell>';
    $excelContent .= '<Cell><Data ss:Type="String">Location</Data></Cell>';
    $excelContent .= '<Cell><Data ss:Type="String">SortlyId</Data></Cell>';    
    $excelContent .= '<Cell><Data ss:Type="String">Date Built</Data></Cell>';    
    $excelContent .= '<Cell><Data ss:Type="String">Date Overhaul €</Data></Cell>';
    $excelContent .= '<Cell><Data ss:Type="String">Price €</Data></Cell>';
    $excelContent .= '<Cell><Data ss:Type="String">State</Data></Cell>';    
    $excelContent .= '</Row>';  
    
   
    // query items - DateAdd because of time delay America-Germany, regional format from Sortly
    $sql = "Select
        tl_sortlyTemplatesIVM.note,
        tl_sortlyTemplatesIVM.name As model,
        sortly_ktc.name As location,
        Date_Format(DATE_ADD(sortly.built, INTERVAL +1 DAY), '%Y-%m-%d') As built,
        Date_Format(DATE_ADD(sortly.overhaul, INTERVAL +1 DAY), '%Y-%m-%d') As overhaul,
        sortly.sortlyId,
        sortly.inventoryNo,
        sortly.serialNo,
        sortly.IVM,
        sortly.active,
        sortly.raw
    From
        sortly Inner Join
        sortly_ktc On sortly.pid = sortly_ktc.sid Inner Join
        tl_sortlyTemplatesIVM On sortly.name = tl_sortlyTemplatesIVM.name
    Where
        sortly.IVM = 1 And
        sortly_ktc.name Not Like 'SCRAP' And
        (sortly.overhaul > DATE_ADD('$dateStart', INTERVAL -1 DAY) Or sortly.built > DATE_ADD('$dateStart', INTERVAL -1 DAY)) And
        (sortly.overhaul < DATE_ADD('$dateEnd', INTERVAL -1 DAY) And sortly.built < DATE_ADD('$dateEnd', INTERVAL -1 DAY)) OR
        (sortly.built > DATE_ADD('$dateStart', INTERVAL -1 DAY))
    Order By
        note,
        overhaul,
        built";
    $result = $db->query($sql);
    
    while ($item = $result->fetch_assoc()) {
        $model = $item['model'];
        $addFacelift = "";
        // lookup for state
        if (empty($item['overhaul'])){
            $state = "new";
        }
        else{
            $state = "refurbed"; 
            $addFacelift = " Facelift"; // for identifying facelift prices
        }
        
        // concat model with optional facelift attribute
        $name = $model.$addFacelift;
        
        // lookup for price
        $sql = "Select price from tl_sortlyTemplatesIVM where name like '$name'";
        $resultPrice = $db->query($sql);
        while ($rowPrice = $resultPrice->fetch_assoc()) {
             $price = round($rowPrice['price'],2);
         }
       
        // write content
        $excelContent .= '<Row>';
        $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['note']) . '</Data></Cell>';       
        $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['model']) . '</Data></Cell>';
        $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['location']) . '</Data></Cell>';
        $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['sortlyId']) . '</Data></Cell>';
        $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['built']) . '</Data></Cell>';
        $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($item['overhaul']) . '</Data></Cell>';
        $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($price) . '</Data></Cell>';    
        $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($state) . '</Data></Cell>';   
        $excelContent .= '</Row>';   
    }
    // Close tags sheet
    $excelContent .= '</Table>';
    $excelContent .= '</Worksheet>';
    
    return $excelContent;   
}