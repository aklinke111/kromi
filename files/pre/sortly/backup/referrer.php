<?php
// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/db/dbConfig.php";

// Get the protocol (http or https)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// Get the server name
$serverName = $_SERVER['SERVER_NAME'];

// Get the request URI
$requestUri = $_SERVER['REQUEST_URI'];

// Combine to form the full URL
$currentUrl = $protocol . $serverName . $requestUri;;



// Get the referring URL
$referrer = isset($_SERVER['HTTP_REFERER']) ? 'Referrer: '.$_SERVER['HTTP_REFERER'] : 'No referrer';

writeLog($referrer.'/'.$currentUrl, 'REFERRER', 'Check referred URL',$db);

    // write log entry in tl_myLogs
    function writeLog($text,$category,$method,$db)
    {
        // insert new log entry
        $text = str_replace("'", "\'", $text);
        $sql = "INSERT INTO tl_myLogs (tstamp,text,category,method) VALUES ('".time()."','$text','$category','$method')";
        $db->prepare($sql)
            ->execute();
    }