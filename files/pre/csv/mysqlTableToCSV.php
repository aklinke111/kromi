<?php
// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/db/dbConfig.php";

tableToCSV($db);
 
function tableToCSV($db){
 
    $sql = "Select
    sortly_country.name As country,
    sortly_subsidiary.name As subsidiary,
    tl_customer.customerNo As customerNo,
    sortly_customer.name As customer,
    sortly_ktc.name As location,
    sortly.name As model,
    tl_sortlyTemplatesIVM.note As description,
    sortly.inventoryNo As inventoryNo
From
    sortly_ktc Inner Join
    sortly_customer On sortly_customer.sid = sortly_ktc.pid Inner Join
    sortly_subsidiary On sortly_subsidiary.sid = sortly_customer.pid Inner Join
    sortly_country On sortly_country.sid = sortly_subsidiary.pid Inner Join
    tl_customer On sortly_customer.sid = tl_customer.sid Inner Join
    sortly On sortly_ktc.sid = sortly.pid Left Join
    tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.name = sortly.name
Where
    sortly_ktc.name Like 'KTC-%'
Order By
    country,
    subsidiary,
    customer,
    location,
    inventoryNo,
    model";
    
    $result = $db->query($sql);

    if ($result->num_rows > 0) {
        // Open the file "export.csv" for writing
        $dateprint = date("Y-m-d_H:i");
        $filename = 'ControllingReportIVM_'.$dateprint.'.csv';
        $file = fopen($filename, 'w');

        // Save the column headers
        fputcsv($file, array('country', 'subsidiary', 'customerNo', 'customer', 'ktc', 'model', 'description', 'inventoryNo'));

        // Output data of each row
        while($row = $result->fetch_assoc()) {
            fputcsv($file, $row);
        }

        fclose($file);
        echo $text = "Data successfully exported to file '".$filename."'";
    } else {
        echo $text = "0 results";
    }
    
    // write log
    writeLog($text, 'WEBHOOK', " function: csv/mysqlTableToCSV", $db);
}


// write log entry in tl_myLogs
function writeLog($text,$category,$method,$db)
{
    // insert new log entry
    $text = str_replace("'", "\'", $text);
    $sql = "INSERT INTO tl_myLogs (tstamp,text,category,method) VALUES ('".time()."','$text','$category','$method')";
    $db->prepare($sql)
        ->execute();
}
