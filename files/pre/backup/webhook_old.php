<script async src="https://www.googletagmanager.com/gtag/js?id=G-XBDCBGL1MQ"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-3NLV667F9E');
</script>

<?php

// Retrieve JSON data from Sortly
$json_data = $_POST;
//// Decode JSON data into an associative array
//$data = json_decode($json_data, true);

    //Prepare mail
    $mailTo = "aklinke111@gmail.com";
    $title = "Mail von import.kromiag.de";
    $mailFrom = "From: Andreas Klinke <ak@kromiag.de>\r\n";
    $mailFrom .= "Reply-To:  ak@kromiag.de\r\n";
    $mailFrom .= "Content-Type: text/html\r\n";
//    $msg = "<b>Webhook from Sortly</b>:\r\n\r\n".print_r( $json_data, true );
    $msg = "<b>Webhook from Sortly</b>:\r\n\r\n".print_r($_GET);
    

    //Send mail
    mail($mailTo, $title, $msg, $mailFrom);

 Echo "Received POST: ";
 print_r($json_data);


//// Retrieve JSON data from Sortly
//
//$json_data = $_POST['jsonData'];
//
//// Decode JSON data into an associative array
//$data = json_decode($json_data, true);
//
//// Check if JSON data was successfully decoded
//if ($data !== null) {
//    
//$msg = wordwrap(json_encode($data),70);
// 
//    //Prepare mail
//    $mailTo = "aklinke111@gmail.com";
//    $title = "Mail von KromiAG.de";
//    $mailFrom = "From: Andreas Klinke <ak@kromiag.de>\r\n";
//    $mailFrom .= "Reply-To:  ak@kromiag.de\r\n";
//    $mailFrom .= "Content-Type: text/html\r\n";
//    $msg = "<b>Webhook from Sortly</b>:\r\n\r\n".$msg;
//
//    //Send mail
//    mail($mailTo, $title, $msg, $mailFrom);
////    print_r($msg); 
//} else {
//    // Print an error message if JSON decoding failed
//    echo "Error: Unable to decode JSON payload from SORTLY.\n";
//}

