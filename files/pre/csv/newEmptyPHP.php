<?php
// recipient email address
$to = "recipient@example.com";

// subject of the email
$subject = "Email with Attachment";

// message body
$message = "This is a sample email with attachment.";

// from
$from = "sender@example.com";

// boundary
$boundary = uniqid();

// header information
$headers = "From: $from\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\".$boundary.\"\r\n";

// attachment
$attachment = chunk_split(base64_encode(file_get_contents('file.pdf')));

// message with attachment
$message = "--".$boundary."\r\n";
$message .= "Content-Type: text/plain; charset=UTF-8\r\n";
$message .= "Content-Transfer-Encoding: base64\r\n\r\n";
$message .= chunk_split(base64_encode($message));
$message .= "--".$boundary."\r\n";
$message .= "Content-Type: application/octet-stream; name=\"file.pdf\"\r\n";
$message .= "Content-Transfer-Encoding: base64\r\n";
$message .= "Content-Disposition: attachment; filename=\"file.pdf\"\r\n\r\n";
$message .= $attachment."\r\n";
$message .= "--".$boundary."--";

// send email
if (mail($to, $subject, $message, $headers)) {
    echo "Email with attachment sent successfully.";
} else {
    echo "Failed to send email with attachment.";
}
?>
