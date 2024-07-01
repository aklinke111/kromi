<?php
// Include the PhpSpreadsheet library
include_once $_SERVER['DOCUMENT_ROOT']."/files/pre/src/functionsMail.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/pre/db/dbConfig.php";

// Sample data array
$data = [
    ['ID', 'Name', 'Email'],
    [1, 'John Doe', 'john@example.com'],
    [2, 'Jane Smith', 'jane@example.com'],
    [3, 'Sam Brown', 'sam@example.com'],
];

// Set the headers to make the browser download the file
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Data KTC-Engineering.xls"');
header('Cache-Control: max-age=0');

// Start XML file
echo '<?xml version="1.0"?>';
echo '<?mso-application progid="Excel.Sheet"?>';
echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ';
echo 'xmlns:o="urn:schemas-microsoft-com:office:office" ';
echo 'xmlns:x="urn:schemas-microsoft-com:office:excel" ';
echo 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" ';
echo 'xmlns:html="http://www.w3.org/TR/REC-html40">';

// Worksheet
echo '<Worksheet ss:Name="Sheet1">';
echo '<Table>';

// Write header row
echo '<Row>';
foreach ($data[0] as $header) {
    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($header) . '</Data></Cell>';
}
echo '</Row>';

// Write data rows
for ($i = 1; $i < count($data); $i++) {
    echo '<Row>';
    foreach ($data[$i] as $cell) {
        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($cell) . '</Data></Cell>';
    }
    echo '</Row>';
}

// Close tags
echo '</Table>';
echo '</Worksheet>';




// Worksheet
echo '<Worksheet ss:Name="Testsheet">';
echo '<Table>';

// Write header row
echo '<Row>';
foreach ($data[0] as $header) {
    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($header) . '</Data></Cell>';
}
echo '</Row>';

// Write data rows
for ($i = 1; $i < count($data); $i++) {
    echo '<Row>';
    foreach ($data[$i] as $cell) {
        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($cell) . '</Data></Cell>';
    }
    echo '</Row>';
}

// Close tags
echo '</Table>';
echo '</Worksheet>';






echo '</Workbook>';
