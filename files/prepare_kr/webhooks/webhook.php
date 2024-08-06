<?php
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

// write log
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
    
    
$request = file_get_contents('php://input');
$req_dump = print_r( $request, true );

    //Prepare mail
    $mailTo = "aklinke111@gmail.com";
    $title = "Mail von import.kromiag.de";
    $mailFrom = "From: Andreas Klinke <ak@kromiag.de>\r\n";
    $mailFrom .= "Reply-To:  ak@kromiag.de\r\n";
//    $mailFrom .= "Content-Type: text/html\r\n";
    $mailFrom .= "Content-Type: application/json\r\n";
    
$fp = file_put_contents( 'request.log', $req_dump );

// Updated Answer

if($json = json_decode(file_get_contents("php://input"), true)){

    $data = $json;
    $value = $data['body']['node_parent_name']; 
    $msgbody = json_encode($data,JSON_PRETTY_PRINT);
    $msgbody = $value;
}

//Send mail
$msg = "<b>Webhook from Sortly</b>:\r\n\r\n".$msgbody;
mail($mailTo, $title, $msg, $mailFrom);

//call functions for updating all MySQL Sortly tables after changes on Sortly platform
include_once $_SERVER['DOCUMENT_ROOT']."/sortly/sortlyToMySQL.php";
