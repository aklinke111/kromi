<?php
include_once $_SERVER['DOCUMENT_ROOT']."/files/pre/src/functionsMail.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/pre/db/dbConfig.php";


// Query to fetch data
$sql = "SELECT id, CONCAT(lastname,',' ,firstname) as name, email FROM tl_member";
$result = $db->query($sql);

if ($result->num_rows > 0) {
    // Set the headers to make the browser download the file
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="data_export.xls"');
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
    echo '<Cell><Data ss:Type="String">ID</Data></Cell>';
    echo '<Cell><Data ss:Type="String">Name</Data></Cell>';
    echo '<Cell><Data ss:Type="String">Email</Data></Cell>';
    echo '</Row>';

    // Write data rows
    while ($row = $result->fetch_assoc()) {
        echo '<Row>';
        echo '<Cell><Data ss:Type="Number">' . htmlspecialchars($row['id']) . '</Data></Cell>';
        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['name']) . '</Data></Cell>';
        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['email']) . '</Data></Cell>';
        echo '</Row>';
    }

    // Close tags
    echo '</Table>';
    echo '</Worksheet>';
    
    
    
    
    echo '</Workbook>';
} else {
    echo 'No records found.';
}

