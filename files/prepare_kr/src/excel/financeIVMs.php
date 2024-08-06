<?php
// Database connection
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functionsMail.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";

// Query to fetch data
$sql = "SELECT id, CONCAT(lastname, ', ', firstname) as name, email FROM tl_member";
$result = $db->query($sql);

if ($result->num_rows > 0) {
    // Start the Excel content with heredoc syntax
    $excelContent = <<<EOD
<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:o="urn:schemas-microsoft-com:office:office"
          xmlns:x="urn:schemas-microsoft-com:office:excel"
          xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:html="http://www.w3.org/TR/REC-html40">
    <Worksheet ss:Name="Sheet1">
        <Table>
            <Row>
                <Cell><Data ss:Type="String">ID</Data></Cell>
                <Cell><Data ss:Type="String">Name</Data></Cell>
                <Cell><Data ss:Type="String">Email</Data></Cell>
            </Row>
EOD;

    // Write data rows
    while ($row = $result->fetch_assoc()) {
        $excelContent .= '<Row>';
        $excelContent .= '<Cell><Data ss:Type="Number">' . htmlspecialchars($row['id']) . '</Data></Cell>';
        $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($row['name']) . '</Data></Cell>';
        $excelContent .= '<Cell><Data ss:Type="String">' . htmlspecialchars($row['email']) . '</Data></Cell>';
        $excelContent .= '</Row>';
    }

    // Close tags
    $excelContent .= <<<EOD
        </Table>
    </Worksheet>
</Workbook>
EOD;

    // Save the content to a temporary file
    $tempFilePath = tempnam(sys_get_temp_dir(), 'excel_');
    file_put_contents($tempFilePath, $excelContent);

    // Email settings
    $to = 'aklinke111@gmail.com,andreas.klinke@kromi.de';
    $subject = 'Excel Data Attachment';
    $body = 'Please find the attached Excel file with the requested data.';
    $from = 'ak@kromiag.de';

    // Generate a boundary string
    $boundary = md5(time());

    // Headers
    $headers = "From: $from\r\n";
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
    $filename = "data_export.xls";

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
} else {
    echo 'No records found.';
}
