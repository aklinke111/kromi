<?php

function sendMail($content){
    
    $boundary = "";
    $database = "xm3xbj34_kromiag";

//    //Delete files older threshold
//    $backupFilePath = "../db/backupMysql";
//    $msgDelete = deleteFiles($backupFilePath);

    // Recipient email
    $to = 'aklinke111@gmail.com';
    
    // Email subject
    $subject = 'Database Dump File from '.$database;
    
    // Email body
    $body = "--{$boundary}\r\n";
    $body .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= "Database dump file in path '".$backupFile."' succesfully generated!\r\n\r\n";

    // Email headers
    $headers = "From: Andreas Klinke <ak@kromiag.de>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

    // Send email
    if (mail($to, $subject, $body.$msgDelete, $headers)) {
        echo "Backupfile ''$backupFile'' successfully created and email sent.";;
    } else {
        echo 'Failed to send email';
    }
}


function mailCSV($csvContents){
    
    // Generate CSV data
//    $csvContents = generateCsvData();
    
    // Boundary 
    $boundary = md5(time());

    // Recipient email
    $to = 'aklinke111@gmail.com';
    
    // Email subject
    $subject = 'Here is your CSV file';
    
    // Email body
    $body = "--{$boundary}\r\n";
    $body .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= 'Please find the attached CSV file containing user data.';
    
    // Email headers
    $headers = "From: Andreas Klinke <ak@kromiag.de>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

//    // Attachment
//    $attachmentName = "users.csv";
//    $attachmentContent = chunk_split(base64_encode($csvContents));
    
//    // Email headers
//    $headers = "From: Andreas Klinke <ak@kromiag.de>\r\n";
//    $headers .= "Content-Type: text/csv; name=\"$attachmentName\"\r\n";
//    $headers .= "Content-Transfer-Encoding: base64\r\n";
//    $headers .= "Content-Disposition: attachment; filename=\"$attachmentName\"\r\n\r\n";
//    $headers .= "$attachmentContent\r\n";
//    $headers .= "--$boundary--";
    
    // Send email
    if (mail($to, $subject, $body, $headers)) {
        echo 'Email sent successfully';
    } else {
        echo 'Failed to send email';
    }
}



//function sendMail($msg){
//    //Prepare mail
//    $mailTo = "aklinke111@gmail.com";
//    $title = "Mail von import.kromiag.de";
//    $mailFrom = "From: Andreas Klinke <ak@kromiag.de>\r\n";
//    $mailFrom .= "Reply-To:  ak@kromiag.de\r\n";
//    $mailFrom .= "Content-Type: text/html\r\n";
////    $mailFrom .= "Content-Type: application/json\r\n"; 
//    
//    mail($mailTo, $title, $msg, $mailFrom);
//    
//    // Send email
//    if (mail($mailTo, $title, $msg, $mailFrom)) {
//        return "Email sent with message: $msg ";
//    } else {
//        return "Failed to send email with message: $msg ";
//    }
//}

    
    