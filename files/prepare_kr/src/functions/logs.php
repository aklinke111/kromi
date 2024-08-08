<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";

// write log entry in tl_myLogs
function writeLog($text,$category,$method,$db)
{
    // insert new log entry
    $text = str_replace("'", "\'", $text);
    $sql = "INSERT INTO tl_myLogs (tstamp,text,category,method) VALUES ('".time()."','$text','$category','$method')";
    $db->prepare($sql)
        ->execute();
}