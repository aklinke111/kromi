<?php

// Original Answer

header('Content-Type: application/json');

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
    

