<?php
// ---------- ATTENTION - EXCEL SAVE DOESN'T WORK WITH 'ECHO' ------------

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/excelCalculations/f2/_includes.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/_includes.php";

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/sortly/ivmBomDisplayAndUpdate.php";



if (isset($_GET['webhookFunction'])) {

    $function = $_GET['webhookFunction'];
    
    if($function == "financeBomExcel"){
        
        //Update prices in tl_sortlyTemplatesIVM
        ivmBomDisplayAndUpdate($db);
        
        // build excel file
        productionBomIVMbuildExcel($db);
    }
}


function productionBomIVMbuildExcel($db){
    
// parameters ----------
// 
    // period
    $dateStart = globalVal($db, 'halfYearStart');
    $dateEnd = globalVal($db, 'halfYearEnd');
    
     // define file & path
    $filename = "KTC-Engineering report $dateStart until $dateEnd";
    $fileExtension = ".xls";
    $filePath = $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/BackupFiles/Production_BOM_IVM/";
    $dateprint = date("Y-m-d_H:i");
    $file = $filePath.$filename."_".$dateprint.$fileExtension;

// ------------------------------   
//  
    // initialize workbook
    $excelContent = workbookInit();
    
    //fetch content
    $excelContent .= fetchBOM($db);
    $excelContent .= fetchRawIVM($db);
    $excelContent .= fetchProducedIVM($db, $dateStart, $dateEnd); 

    // close workbook
     $excelContent .= '</Workbook>';
    
    // Save the content to a file
    file_put_contents($file, $excelContent);
    
    echo $excelContent;
}