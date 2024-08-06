<?php
// ---------- ATTENTION - EXCEL SAVE DOESN'T WORK WITH 'ECHO' ------------

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/sortly/ivmBomDisplayAndUpdate.php";

if (isset($_GET['webhookFunction'])) {

    $function = $_GET['webhookFunction'];
    
    if($function == "financeBom"){
        //Update prices in tl_sortlyTemplatesIVM
        ivmBomDisplayAndUpdate($db);
        // build excel file
        ivmBom($db);
    }
}

function ivmBom($db){
// parameters ----------
    // period
    $dateStart = "2024-01-01";
    $dateEnd = "2024-06-30";
    
     // define file & path
    $filename = "KTC-Engineering report $dateStart until $dateEnd";
    $fileExtension = ".xls";
    $filePath = $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/ktc/finance/BackupIVMs/";
    $dateprint = date("Y-m-d_H:i");
    $file = $filePath.$filename."_".$dateprint.$fileExtension;

// ------------------------------    
    // initialize workbook
    $excelContent = workbookInit();
    // fetch content
    $excelContent .= fetchBOM($db);
    $excelContent .= fetchRawIVM($db);
    $excelContent .= fetchProducedIVM($db,$dateStart,$dateEnd);    
    
    // close workbook
     $excelContent .= '</Workbook>';
     
//   $excelContent .= '<p>';
//   var_dump($excelContent);
    
    // Save the content to a file
    file_put_contents($file, $excelContent);
    
    echo $excelContent;

}

function workbookInit(){
    
    // Start the Excel content
    $excelContent = '<?xml version="1.0"?>';
    $excelContent .= '<?mso-application progid="Excel.Sheet"?>';
    $excelContent .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ';
    $excelContent .= 'xmlns:o="urn:schemas-microsoft-com:office:office" ';
    $excelContent .= 'xmlns:x="urn:schemas-microsoft-com:office:excel" ';
    $excelContent .= 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" ';
    $excelContent .= 'xmlns:html="http://www.w3.org/TR/REC-html40">';
    
    // Add Styles
    $excelContent .= '<Styles>';
    $excelContent .= '<Style ss:ID="sBold">';
    $excelContent .= '<Font ss:Bold="1"/>'; // Bold style
    $excelContent .= '</Style>';
    $excelContent .= '<Style ss:ID="sRed">';
    $excelContent .= '<Font ss:Color="#FF0000"/>'; // Red color style
    $excelContent .= '</Style>';
    $excelContent .= '<Style ss:ID="sBoldRed">';
    $excelContent .= '<Font ss:Bold="1" ss:Color="#FF0000"/>'; // Bold and red color
    $excelContent .= '</Style>';
    $excelContent .= '</Styles>';
    
    return $excelContent;
}


function fetchBOM($db){
    $excelContent = "";
    $group = "G1_";
    
//    // empty column price
//    $sql = "Update tl_sortlyTemplatesIVM set price = 0";
//    $result = $db->query($sql); 
    
    // loop templates for finance relevant devices
    $sql = "Select * from tl_sortlyTemplatesIVM WHERE financeReport = 1";
    $result = $db->query($sql);
    
    while ($item = $result->fetch_assoc()) {
        $id = $item['id']; 
        $note = $item['note'];
        $priceTotal = 0;
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
            Where tl_bom.pid = $id 
            Order By
                sortly.name";
    $result1 = $db->query($sql1);

        while ($row = $result1->fetch_assoc()) {
            $calculate = $row['calculate'];
    //            $price = str_replace(".", ",", $row['price']); 

            if($calculate){
            
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
        }
    // Write total    
        $excelContent .= '<Row>';
        $excelContent .= '<Cell></Cell>';
        $excelContent .= '<Cell></Cell>';
        $excelContent .= '<Cell></Cell>';
        $excelContent .= '<Cell ss:StyleID="sBoldRed"><Data ss:Type="Number">' . htmlspecialchars(round($priceTotal,2)) . '</Data></Cell>';             
        $excelContent .= '</Row>';
        

//    // update price
//    $sql = "Update tl_sortlyTemplatesIVM set price = round($priceTotal,2) where id = $id";
//    $resultUpdate = $db->query($sql);    
             
    // Close tags sheet
    $excelContent .= '</Table>';
    $excelContent .= '</Worksheet>';
    } 
    return $excelContent;
}

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

function fetchProducedIVM($db,$dateStart,$dateEnd ){
    $excelContent = "";
    //Group for better use of sheets
    $group = "G3_";
    // open sheet
    $sheetName = "IVM produced";  
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
    
    // query items
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
        (sortly.overhaul >= '$dateStart' Or sortly.built >= '$dateStart') And
        (sortly.overhaul <= '$dateEnd' And sortly.built <= '$dateEnd' and tl_sortlyTemplatesIVM.note Like 'Helix%') OR
        (isnull(sortly.overhaul) And sortly.built >= '$dateStart') And   
        tl_sortlyTemplatesIVM.note Like 'Helix%'
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
