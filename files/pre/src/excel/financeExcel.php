<?php
// Create the Excel content
$data = [
    ['ID', 'Name', 'Email'],
    [1, 'John DÖ', 'john@example.com'],
    [2, 'Jane Smääth', 'jane@example.com'],
    [3, 'Sam ()/', 'sam@example.com'],
];

$excelContent = '<?xml version="1.0"?>';
$excelContent .= '<?mso-application progid="Excel.Sheet"?>';
$excelContent .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ';
$excelContent .= 'xmlns:o="urn:schemas-microsoft-com:office:office" ';
$excelContent .= 'xmlns:x="urn:schemas-microsoft-com:office:excel" ';
$excelContent .= 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" ';
$excelContent .= 'xmlns:html="http://www.w3.org/TR/REC-html40">';
$excelContent .= '<Worksheet ss:Name="Sheet1">';
$excelContent .= '<Table>';

// Write header row
$excelContent .= '<Row>';
foreach ($data[0] as $header) {
    $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($header) . '</Data></Cell>';
}
$excelContent .= '</Row>';

// Write data rows
for ($i = 1; $i < count($data); $i++) {
    $excelContent .= '<Row>';
    foreach ($data[$i] as $cell) {
        $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($cell) . '</Data></Cell>';
    }
    $excelContent .= '</Row>';
}

// Close tags
$excelContent .= '</Table>';
$excelContent .= '</Worksheet>';
$excelContent .= '</Workbook>';

// Save the content to a temporary file
$tempFilePath = tempnam(sys_get_temp_dir(), 'excel_');
file_put_contents($tempFilePath, $excelContent);

// Email settings
$to = 'aklinke111@gmail.com,andreas.klinke@kromi.de';
$subject = 'Excel Data Attachment';
$body = 'Please find the attached Excel file with the requested data.';
$from = 'your-email@example.com';


// Generate a boundary string
$boundary = md5(time());

// Headers
$headers = "From: Andreas Klinke <ak@kromiag.de>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

// Message Body
$message = "--$boundary\r\n";
$message .= "Content-Type: text/plain; charset=UTF-8\r\n";
$message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$message .= "$body\r\n";
$message .= "--$boundary\r\n";

// Attachment
$fileContent = chunk_split(base64_encode(file_get_contents($tempFilePath)));
$filename = "Data KTC-Engineering.xlsx";

$message .= "Content-Type: application/vnd.ms-excel; name=\"$filename\"\r\n";
$message .= "Content-Transfer-Encoding: base64\r\n";
$message .= "Content-Disposition: attachment; filename=\"$filename\"\r\n\r\n";
$message .= "$fileContent\r\n";
$message .= "--$boundary--";

// Send the email
mail($to, $subject, $message, $headers);

// Clean up the temporary file
unlink($tempFilePath);

echo "Email sent successfully.";
