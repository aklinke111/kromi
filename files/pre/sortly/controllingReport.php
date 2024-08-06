<?php

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/files.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/mail.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";

$tableName = "controllingReportKtc";
    
if (isset($_GET['webhookFunction'])) {

    $function = $_GET['webhookFunction'];
    if($function == "controllingReport"){

        echo fillTable($db, $tableName);
        echo tableToCSV($db, $tableName);
    }
}

function fillTable($db, $tableName){
    
    $sql = "TRUNCATE TABLE $tableName";
    $result = $db->query($sql);

    $sql = "Select
        'Actual' As Version,
        Date_Format(CurDate(), '%Y-%m') As Period,
        tl_customer.customerNo As CostObject,
        tl_toolcenter.costcenter As costcenter,
        Replace(sortly_ktc.name, '-', '') As KTCID,
        tl_sortlyTemplatesIVM.noteJedox As Item,
        'NA' As Currency,
        Count(sortly.inventoryNo) As Quantity
    From
        sortly_ktc Inner Join
        sortly_customer On sortly_customer.sid = sortly_ktc.pid Inner Join
        sortly_subsidiary On sortly_subsidiary.sid = sortly_customer.pid Inner Join
        sortly_country On sortly_country.sid = sortly_subsidiary.pid Inner Join
        tl_customer On sortly_customer.sid = tl_customer.sid Inner Join
        sortly On sortly_ktc.sid = sortly.pid Left Join
        tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.name = sortly.name Inner Join
        tl_toolcenter On sortly_ktc.name = tl_toolcenter.ktcId Right Join
        tl_costcenter On tl_toolcenter.costcenter = tl_costcenter.costcenter
    Where
        sortly_ktc.name Like 'KTC-%' And
        tl_customer.active = 1
    Group By
        tl_customer.customerNo,
        tl_toolcenter.costcenter,
        Replace(sortly_ktc.name, '-', ''),
        tl_sortlyTemplatesIVM.noteJedox,
        tl_costcenter.description,
        sortly_subsidiary.name,
        sortly_customer.name,
        sortly_ktc.active,
        sortly_country.name,
        sortly_ktc.name,
        sortly.name,
        tl_customer.active
    Order By
        KTCID";
    $result = $db->query($sql);

    $KTCID = "";

    while($item = $result->fetch_assoc()){ 

        $Version = $item['Version'];
        $Period = $item['Period'];
        $CostObject = $item['CostObject'];
        $costcenter = $item['costcenter'];
        $NewKTCID = $item['KTCID']; 
        $Currency = $item['Currency'];
        $Quantity = $item['Quantity'];

        // group title for JEDOX on top of all regular entrys
        if($KTCID != $NewKTCID){
            $KTCID = $item['KTCID']; 
            // Title 
            $Item = "Quantity of KTC-ID";
            // Insert dataset title
            executeQuery($db,$tableName,$Version,$Period,$CostObject,$costcenter,$KTCID,$Item,$Currency,$Quantity);
        } 

        // Regular Entry
        $Item = "Quantity of ".$item['Item'];
        // Insert dataset standard
        executeQuery($db,$tableName,$Version,$Period,$CostObject,$costcenter,$KTCID,$Item,$Currency,$Quantity);
    }
}    
  

function executeQuery($db,$tableName,$Version,$Period,$CostObject,$costcenter,$KTCID,$Item,$Currency,$Quantity){
    // Prepare the SQL statement
    $sql = "INSERT INTO $tableName(
        Version,
        Period,
        CostObject,
        costcenter,
        KTCID,
        Item,
        Currency,
        Quantity) 
    VALUES
        (?,?,?,?,?,?,?,?)"; 
    
    $stmt = $db->prepare($sql);
    $parameterTypes = "sssssssi";
    $stmt->bind_param($parameterTypes,
        $Version,
        $Period,                        
        $CostObject,
        $costcenter,
        $KTCID,
        $Item, 
        $Currency,
        $Quantity           
    );  
    // Execute the statement
    $stmt->execute(); 
}


function tableToCSV($db, $tableName){
    
    $msg = "";
        
    // location backupfile tmp
    $backupFilePath = $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/ktc/finance/backupAllocation/";
    
    // location backupfile new
    $database = "xm3xbj34_kromiag";
    $dateprint = date("Y-m-d_H:i");
    $prefixFilename = $database."_";
    $filename = 'KTC-Allocation.csv';
    $filepathNew = $backupFilePath.$dateprint."_".$filename;
    
    // Query the table
    $sql = "SELECT * FROM $tableName";
    $result = $db->query($sql);

    if ($result->num_rows > 0) {
        // File name and path
        $filepath = $backupFilePath.$filename;

        // Open file in write mode
        $file = fopen($filepath, 'w');

        // Fetch the column names
        $fields = $result->fetch_fields();
        $header = array();
        foreach ($fields as $field) {

            if($field->name == "Quantity"){
                $header[] = "#Value";
            } elseif ($field->name == "KTCID"){
              $header[] = "KTC-ID";  
            } else {
              $header[] = $field->name;  
            } 
        }
        // Write Header
        fputcsv($file, $header);

        // Write rows
        while ($row = $result->fetch_assoc()) {
            fputcsv($file, $row);
        }

        //Close the file
        fclose($file);
        
        // prepare Mail with attachment
//        $to = 'Christiane.Roeller@kromi.de, Holger.Schneidereit@kromi.de, andreas.klinke@kromi.de';
        $to = 'andreas.klinke@kromi.de';
        $subject = $filename;
        $message = "Please find the attached file '".$filename."'";
        
        $msg.= sendFile($to, $subject, $message, $filepath, $filename);
        
    } else {
        $msg.=  "0 results";
    }
    
    // rename file
    $msg.= renameFile($filepath, $filepathNew);
    
    return $msg;
}
