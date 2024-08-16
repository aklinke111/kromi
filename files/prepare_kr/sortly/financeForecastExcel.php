<?php
// ---------- ATTENTION - EXCEL SAVE DOESN'T WORK WITH 'ECHO' ------------

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/_includes.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/excelCalculations/f2/_includes.php";

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/abcAnalyses.php";

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/sortly/ivmBomDisplayAndUpdate.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/financeForecast.php";





if (isset($_GET['webhookFunction'])) {

    $function = $_GET['webhookFunction'];
    
    if($function == "financeForecastExcel"){
        
        // Update prices in tl_sortlyTemplatesIVM
        ivmBomDisplayAndUpdate($db);
        
        // Forecast routines
        financeForecast_main($db);
        
        // build excel file
        forecastBuildExcel($db);
    }
}


function forecastBuildExcel($db){
    
    // define file & path
    $filePath = $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/BackupFiles/Forecasts/";
    $filename = "Forecast Report Finance";    
    $dateprint = date("Y-m-d_H:i");
    $fileExtension = ".xls";
    $file = $filePath.$filename."_".$dateprint.$fileExtension;


    // initialize workbook
    $excelContent = workbookInit();
    
    //fetch content
    
    // Forecast for definded period
    $ForecastPeriod = globalVal($db, 'ForecastPeriod');
    $excelContent .= forecastExcel($db, $ForecastPeriod); 
    
    // ABC supplier
    $excelContent .= abcAnalysesExcel($db);
    
    
    // close workbook
     $excelContent .= '</Workbook>';
     
//   $excelContent .= '<p>';
//   var_dump($excelContent);
    
    // Save the content to a file
    file_put_contents($file, $excelContent);
    
    echo $excelContent;

}



function forecastExcel($db, $ForecastPeriod){
    
    $msg = "";
    
    $excelContent = "";
    $group = "G1_";
    // open sheet
    $sheetName = "Forecast $ForecastPeriod months";  
    $excelContent .= '<Worksheet ss:Name="'.$group.$sheetName.'">';
    $excelContent .= '<Table>';
    
    // Write header row -------------------------

    // query string
    $sql = buildPivotSql($db);
    
    // extract titles from string of pivot-query
    $titles = extract_titles_from_sql($sql);
    
    // open header row
    $excelContent .= '<Row>';
    
    // insert titles  
    foreach ($titles as $title) {

        $title = str_replace("'", "", $title);
//        echo $title. "<br>";
        $excelContent .= '<Cell><Data ss:Type="String">'.$title.'</Data></Cell>';
    }
    
    // close header row
    $excelContent .= '</Row>';  
    
    // ------------------------------

    // get result from pivot query
    $result = $db->query($sql);
    
    while ($item = $result->fetch_assoc()) {
        
        $category = $item['category'];
        $switchPositiveNegative = (-1);
        
        switch ($category) {
            case 'Pending Orders - internal':
                $switchPositiveNegative = (1);
            case 'Parts on Stock - Europe':
                $switchPositiveNegative = (1);
            case 'Parts on Stock - South America':
                $switchPositiveNegative = (1);
        }
        
        // fetch a data row
       $excelContent .= '<Row>'; 
       
       // run threw each column (title)...
        foreach ($titles as $title) {
            $title = str_replace("'", "", $title);
            
            $value = $item[$title];

            
            if(is_numeric($value)){
                $value = floatval($item[$title]);
                $value = round($value,2);
                $value *= $switchPositiveNegative;
                $value = number_format($value,2); 

            }

            // ... and insert values
            $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($value) . '</Data></Cell>';
        }
        // close row
        $excelContent .= '</Row>';   
    }
    
    // Close tags sheet
    $excelContent .= '</Table>';
    $excelContent .= '</Worksheet>';
    
    return $excelContent;   
}




function abcAnalysesExcel($db){
    
    // prepare table and data
    abcAnalysesSupplier($db);
    
   
    $excelContent = "";
    $group = "G2_";
    // open sheet
    $sheetName = "ABC Analyses";  
    $excelContent .= '<Worksheet ss:Name="'.$group.$sheetName.'">';
    $excelContent .= '<Table>';
    
    // Write header row -------------------------

    // query string
    $sql = "Select supplierName as 'supplier', percentValue as 'percentage share' FROM kr_abcAnalyses";
    
    // extract titles from string of query
    $titles = extract_titles_from_sql($sql);
    
    // open header row
    $excelContent .= '<Row>';
    
    // insert titles  
    foreach ($titles as $title) {

        $title = str_replace("'", "", $title);
//        echo $title. "<br>";
        $excelContent .= '<Cell><Data ss:Type="String">'.$title.'</Data></Cell>';
    }
    
    // close header row
    $excelContent .= '</Row>';  
    
    // ------------------------------

    // get result from pivot query
    $result = $db->query($sql);
    
    while ($item = $result->fetch_assoc()) {
        
        // fetch a data row
       $excelContent .= '<Row>'; 
       
       // run threw each column (title)...
        foreach ($titles as $title) {
            $title = str_replace("'", "", $title);
            
            $value = $item[$title];
            
            if(is_numeric($value)){
                $value = floatval($item[$title]);
                $value = round($value,2);
                $value = number_format($value,2)/100; 

            }

            // ... and insert values
            $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($value) . '</Data></Cell>';
        }
        // close row
        $excelContent .= '</Row>';   
    }
    
    // Close tags sheet
    $excelContent .= '</Table>';
    $excelContent .= '</Worksheet>';
    
    return $excelContent;   
}
