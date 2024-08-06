<?php
// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functionsMail.php";

echo tableBomToCSV($db, 2);

function tableBomToCSV($db, $pid) {
    $msg = "";

    // Validate and escape $pid to prevent SQL injection
    $pid = $db->real_escape_string($pid);

    // Set the charset to UTF-8 for the database connection
    if (!$db->set_charset("utf8mb4")) {
        $msg .= "Error loading character set utf8mb4: " . $db->error;
        return $msg;
    }

    // Query the table
    $sql = "SELECT DISTINCT
                    tl_sortlyTemplatesIVM.name,
                    tl_bom.sortlyId,
                    sortly.name AS item,
                    tl_bom.bomQuantity,
                    tl_bom.calculate
                FROM
                    tl_sortlyTemplatesIVM
                    INNER JOIN tl_bom ON tl_sortlyTemplatesIVM.id = tl_bom.pid
                    INNER JOIN sortly ON sortly.sortlyId = tl_bom.sortlyId
                WHERE tl_bom.pid = '$pid'
                ORDER BY
                    sortly.name";

    $result = $db->query($sql);

    if ($result->num_rows > 0) {
        // Open output stream
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment;filename=BOM.csv');
        $output = fopen('php://output', 'w');

        // Output BOM as UTF-8 with BOM for Excel compatibility
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Fetch the column names
        $fields = $result->fetch_fields();
        $header = array();
        foreach ($fields as $field) {
            $header[] = $field->name;
        }

        fputcsv($output, $header);

        // Write rows
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }

        // Close output stream
        fclose($output);

        // Send Mail - Assuming mailCSV function is defined elsewhere
        // Create a temporary file for the email attachment
        $tempFilePath = tempnam(sys_get_temp_dir(), 'csv');
        $tempFile = fopen($tempFilePath, 'w');

        // Add BOM for UTF-8 compatibility
        fprintf($tempFile, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Re-run the query to fetch data again for the email attachment
        $result = $db->query($sql);
        fputcsv($tempFile, $header);

        while ($row = $result->fetch_assoc()) {
            fputcsv($tempFile, $row);
        }

        fclose($tempFile);
        
        $msg .= mailBom($tempFilePath);

        // Remove the temporary file
        unlink($tempFilePath);

    } else {
        $msg .= "0 results";
    }

    return $msg;
}

// Dummy implementation of mailCSV for demonstration
function mailBom($filePath) {

// Example usage:
$recipientEmail = 'aklinke111@gmail.com';
$recipientName = 'Andreas Klinke';
$subject = 'BOM IVM';
$body = 'This is the body of the email';
$fromEmail = 'ak@kromiag.de';
$fromName = 'Contao kromiag.de';

return mailCSV($filePath, $recipientEmail, $recipientName, $subject, $body, $fromEmail, $fromName);

}

// write log entry in tl_myLogs
function writeLog($text,$category,$method,$db)
{
    // insert new log entry
    $text = str_replace("'", "\'", $text);
    $sql = "INSERT INTO tl_myLogs (tstamp,text,category,method) VALUES ('".time()."','$text','$category','$method')";
    $db->prepare($sql)
        ->execute();
}