<?php


// Send Mail with attachment
function sendFile($to, $subject, $message, $file, $filename){

    $msg = "";
//    // Email configuration
//    $to = 'aklinke111@gmail.com';
//    $subject = 'CSV File Attachment';
//    $message = 'Please find the attached CSV file.';

    // Read the file content
    $content = file_get_contents($file);

    $content = chunk_split(base64_encode($content));

    // a unique boundary string
    $boundary = md5(uniqid(time()));

    // Headers
    $headers = "From: Andreas Klinke <ak@kromiag.de>\r\n";
    $headers .= "Reply-To: andreas.klinke@kromi.de\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

    // Message body
    $body = "--{$boundary}\r\n";
    $body .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n";
    $body .= "\r\n";
    $body .= $message . "\r\n";
    $body .= "\r\n";
    $body .= "--{$boundary}\r\n";
    $body .= "Content-Type: text/csv; name=\"{$filename}\"\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= "Content-Disposition: attachment; filename=\"{$filename}\"\r\n";
    $body .= "\r\n";
    $body .= $content . "\r\n";
    $body .= "\r\n";
    $body .= "--{$boundary}--\r\n";

    // Send email
    if (mail($to, $subject, $body, $headers)) {
         $msg.= 'Email sent successfully to '.$to.'<br>';
    } else {
        $msg.= 'Failed to send email.<br>';
    }
    
    return $msg;
}