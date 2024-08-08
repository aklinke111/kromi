<?php

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
