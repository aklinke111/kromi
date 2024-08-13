<?php

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";

function fetchBOM($db){
    
    $excelContent = "";
    $group = "G1_";
    
    // loop templates for finance relevant devices
    $sql = "Select * from tl_sortlyTemplatesIVM WHERE financeReport = 1 order by name";
    $result = $db->query($sql);
    
    while ($item = $result->fetch_assoc()) {
        $id = $item['id']; 
        $note = $item['note'];
        $priceTotal = 0;
        $hrTotal = 0;        
        // replacde characters which doesn't work for excel sheet names
        $sheetName = str_replace("/", "_", $item['name']);   
    
        // sheet '$sheetName'
        $excelContent .= '<Worksheet ss:Name="'.$group.$sheetName.'">';
        $excelContent .= '<Table>';

        // Write title
        $excelContent .= '<Row>';
        $excelContent .= '<Cell ss:StyleID="sBold"><Data ss:Type="String">'.$note.'</Data></Cell>';
        $excelContent .= '</Row>'; 

        // empty row
        $excelContent .= '<Row></Row>';
        
        // subtitle
        $note = "--- material cost ---";
        $excelContent .= '<Row>';
        $excelContent .= '<Cell><Data ss:Type="String">'.$note.'</Data></Cell>';
        $excelContent .= '</Row>'; 

        // Write header row
        $excelContent .= '<Row>';
        $excelContent .= '<Cell><Data ss:Type="String">SortlyID</Data></Cell>';
        $excelContent .= '<Cell><Data ss:Type="String">Item</Data></Cell>';
        $excelContent .= '<Cell><Data ss:Type="String">Quantity</Data></Cell>';
        $excelContent .= '<Cell><Data ss:Type="String">Price €</Data></Cell>';
        $excelContent .= '</Row>';    

        // Write content rows
        $sql1 = "Select distinct
                    tl_sortlyTemplatesIVM.name,
                    tl_bom.sortlyId,
                    sortly.name As item,
                    sortly.price,
                    tl_bom.bomQuantity,
                    sortly.price,
                    tl_bom.calculate
                From
                    tl_sortlyTemplatesIVM Inner Join
                    tl_bom On tl_sortlyTemplatesIVM.id = tl_bom.pid Inner Join
                    sortly On sortly.sortlyId = tl_bom.sortlyId
                Where 
                tl_bom.pid = $id And
                tl_bom.calculate = 1    
                Order By
                    sortly.name";
        $result1 = $db->query($sql1);

        while ($row = $result1->fetch_assoc()) {
            // sum price    
            $priceTotal += $row['price']*$row['bomQuantity'];   
            // write content
            $excelContent .= '<Row>';
            $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($row['sortlyId']) . '</Data></Cell>';
            $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($row['item']) . '</Data></Cell>';
            $excelContent .= '<Cell><Data ss:Type="Number">' . htmlspecialchars($row['bomQuantity']) . '</Data></Cell>';
            $excelContent .= '<Cell><Data ss:Type="Number">' . htmlspecialchars(round($row['price'],2)) . '</Data></Cell>';             
            $excelContent .= '</Row>';
        }
    // Write total    
        $excelContent .= '<Row>';
        $excelContent .= '<Cell></Cell>';
        $excelContent .= '<Cell></Cell>';
        $excelContent .= '<Cell></Cell>';
        $excelContent .= '<Cell ss:StyleID="sBoldRed"><Data ss:Type="Number">' . htmlspecialchars(round($priceTotal,2)) . '</Data></Cell>';             
        $excelContent .= '</Row>';
        
        // empty row
        $excelContent .= '<Row></Row>';
        $excelContent .= '<Row></Row>';
        
        //-----------------------------------------------------------------------------------------------

        // Write header row HR
        
        $note = "--- staff cost ---";
        $excelContent .= '<Row>';
        $excelContent .= '<Cell><Data ss:Type="String">'.$note.'</Data></Cell>';
        $excelContent .= '</Row>';         
                
        $excelContent .= '<Row>';
        $excelContent .= '<Cell><Data ss:Type="String">SortlyID</Data></Cell>';
        $excelContent .= '<Cell><Data ss:Type="String">Item</Data></Cell>';
        $excelContent .= '<Cell><Data ss:Type="String">Quantity</Data></Cell>';
        $excelContent .= '<Cell><Data ss:Type="String">Price €</Data></Cell>';
        $excelContent .= '</Row>';    

        // Write content rows
        $sql1 = "Select distinct
                    tl_sortlyTemplatesIVM.name,
                    tl_bom.sortlyId,
                    sortly.name As item,
                    sortly.price,
                    tl_bom.bomQuantity,
                    sortly.price,
                    tl_bom.hr
                From
                    tl_sortlyTemplatesIVM Inner Join
                    tl_bom On tl_sortlyTemplatesIVM.id = tl_bom.pid Inner Join
                    sortly On sortly.sortlyId = tl_bom.sortlyId
                Where 
                    tl_bom.pid = $id And
                    tl_bom.hr = 1
                Order By
                    sortly.name";
        $result1 = $db->query($sql1);

        while ($row = $result1->fetch_assoc()) {
            // sum HR    
            $hrTotal += $row['price']*$row['bomQuantity'];  

            // write content
            $excelContent .= '<Row>';
            $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($row['sortlyId']) . '</Data></Cell>';
            $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($row['item']) . '</Data></Cell>';
            $excelContent .= '<Cell><Data ss:Type="Number">' . htmlspecialchars($row['bomQuantity']) . '</Data></Cell>';
            $excelContent .= '<Cell><Data ss:Type="Number">' . htmlspecialchars(round($row['price'],2)) . '</Data></Cell>';             
            $excelContent .= '</Row>';
        }      
        
        // Write total    
        $excelContent .= '<Row>';
        $excelContent .= '<Cell></Cell>';
        $excelContent .= '<Cell></Cell>';
        $excelContent .= '<Cell></Cell>';
        $excelContent .= '<Cell ss:StyleID="sBoldRed"><Data ss:Type="Number">' . htmlspecialchars(round($hrTotal,2)) . '</Data></Cell>';             
        $excelContent .= '</Row>';  
        
        
        // empty row
        $excelContent .= '<Row></Row>';
        $excelContent .= '<Row></Row>';
//        
        // Write total over all
        $total = $hrTotal + $priceTotal;
        $text = "Total over all";
        $excelContent .= '<Row>';
        $excelContent .= '<Cell ss:StyleID="sBold"><Data ss:Type="String">' . htmlspecialchars($text) . '</Data></Cell>';
        $excelContent .= '<Cell></Cell>';
        $excelContent .= '<Cell></Cell>';
        $excelContent .= '<Cell ss:StyleID="sBoldRed"><Data ss:Type="Number">' . htmlspecialchars(round($total,2)) . '</Data></Cell>';             
        $excelContent .= '</Row>';          
 
             
    // Close tags sheet
    $excelContent .= '</Table>';
    $excelContent .= '</Worksheet>';
    } 
    return $excelContent;
}