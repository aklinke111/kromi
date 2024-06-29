<?php
// Load the database configuration file
include_once 'dbConfig.php';

if(isset($_POST['importSubmit'])){
    
    // Allowed mime types
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
    
    // Validate whether selected file is a CSV file
    if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)){
        
        // If the file is uploaded
        if(is_uploaded_file($_FILES['file']['tmp_name'])){
            
            // Open uploaded CSV file with read-only mode
            $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
            
            // Skip the first line
            fgetcsv($csvFile);
            
            // Parse data from CSV file line by line
            while(($line = fgetcsv($csvFile)) !== FALSE){
                // Get row data
                $description   = $line[0];
                $sortlyId  = $line[1];
                $primaryFolder  = $line[16];
                //$kromiId = $line[34];
                $supplier = $line[35];
                $tstamp = time();
                
                // Check whether SortlyId already exists in the database
                $prevQuery = "SELECT id FROM tl_sortly WHERE $sortlyId = '".$line[1]."'";
                $prevResult = $db->query($prevQuery);
                
                if($prevResult->num_rows > 0){
                    // Update sortly data in the database
                    $db->query("UPDATE tl_sortly SET description = '".$description."', supplier = '".$supplier."', primaryFolder = '".$primaryFolder."', modified = NOW() WHERE sortlyId = '".$sortlyId."'");
                }else{
                    // Insert sortly data in the database
                    $db->query("INSERT INTO tl_sortly (tstamp, description, sortlyId, primaryFolder, created, modified, supplier) VALUES ('".$tstamp."', '".$description."', '".$sortlyId."', '".$primaryFolder."', NOW(), NOW(), '".$supplier."')");
                }
            }
            
            // Close opened CSV file
            fclose($csvFile);
            
            $qstring = '?status=succ';
        }else{
            $qstring = '?status=err';
        }
    }else{
        $qstring = '?status=invalid_file';
    }
}

// Redirect to the listing page
header("Location: index.php".$qstring);