<?php


include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/sortly/deleteFiles.php";


// main function for calculating quantites of IVM and update table tl_sortlyTemplatesIVM
if (isset($_GET['webhookFunction'])) {

    $function = $_GET['webhookFunction'];
    
    if($function == "mySqlDump"){

        
        echo createMySqlDump($db);
    }
}

function createMySqlDump($db){
    // location backupfile stored
    $database = "xm3xbj34_kromiag";
    $backupFilePath = $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/backupMysql/";
    $dateprint = date("Y-m-d_H:i");
    $prefixFilename = $database."_";
    $backupFile = $backupFilePath.$prefixFilename.$dateprint.'.sql';
    $handle = fopen($backupFile, 'w');

    // Get all tables
    $tables = [];
    $result = $db->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }

    // Write each table's structure and data to the file
    foreach ($tables as $table) {
        $structure = getTableStructure($db, $table);
        fwrite($handle, $structure);

        $data = getTableData($db, $table);
        fwrite($handle, $data);
    }

    // Close the file
    fclose($handle);

    // Close the connection
    $db->close();
    
    // send mail
    sendMail($backupFilePath, $backupFile,$database);
}


// Function to get table structure
function getTableStructure($db, $table) {
    $createTable = "";
    $result = $db->query("SHOW CREATE TABLE `$table`");
    if ($result) {
        $row = $result->fetch_assoc();
        $createTable = $row['Create Table'] . ";\n\n";
    }
    return $createTable;
}

// Function to get table data
function getTableData($db, $table) {
    $data = "";
    $result = $db->query("SELECT * FROM `$table`");
    while ($row = $result->fetch_assoc()) {
        $values = array_map([$db, 'real_escape_string'], array_values($row));
        $values = "'" . implode("', '", $values) . "'";
        $data .= "INSERT INTO `$table` VALUES ($values);\n";
    }
    $data .= "\n";
    return $data;
}

function sendMail($backupFilePath,$backupFile,$database){
    $boundary = "";
    

    //Delete files older threshold
//    $backupFilePath = $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/backupMysql";
//    $backupFilePath = "..//backupMysql";
    $msgDelete = deleteFiles($backupFilePath);

    // Email headers
    $headers = "From: Andreas Klinke <ak@kromiag.de>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

    // Email body
    $body = "--{$boundary}\r\n";
    $body .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= "Database dump file in path '".$backupFile."' succesfully generated!\r\n\r\n";

    // Recipient email
    $to = 'aklinke111@gmail.com';

    // Email subject
    $subject = 'Database Dump File from '.$database;

    // Send email
    if (mail($to, $subject, $body.$msgDelete, $headers)) {
        echo "Backupfile ''$backupFile'' successfully created and email sent.";;
    } else {
        echo 'Failed to send email';
    }
}


//$boundary = "";
//$database = "xm3xbj34_kromiag";
//
////Delete files older threshold
//$backupFilePath = "../db/backupMysql";
//$msgDelete = delteFiles($backupFilePath);
//
//// Email headers
//$headers = "From: Andreas Klinke <ak@kromiag.de>\r\n";
//$headers .= "MIME-Version: 1.0\r\n";
//$headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";
//
//// Email body
//
//$body = "--{$boundary}\r\n";
//$body .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
//$body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
//$body .= "Database dump file in path '".$backupFile."' succesfully generated!\r\n\r\n";
////$body .= "--{$boundary}\r\n";
////$body .= "Content-Type: application/sql; name=\"$backupFile\"\r\n";
////$body .= "Content-Transfer-Encoding: base64\r\n";
////$body .= "Content-Disposition: attachment; filename=\"$backupFile\"\r\n\r\n";
////$body .= $file_content . "\r\n\r\n";
////$body .= "--{$boundary}--";
//
//// Recipient email
//$to = 'aklinke111@gmail.com';
//
//// Email subject
//$subject = 'Database Dump File from '.$database;
//
//// Send email
//if (mail($to, $subject, $body.$msgDelete, $headers)) {
//    echo "Backupfile ''$backupFile'' successfully created and email sent.";;
//} else {
//    echo 'Failed to send email';
//}