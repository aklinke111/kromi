<?php

function deleteFiles($directory){

// Set the age threshold (e.g., 30 days)
$days = 30;

// Convert days to seconds
$ageThreshold = $days * 24 * 60 * 60;

// Get the current time
$currentTime = time();

// message
$msg = "";

    // Open the directory
    if ($handle = opendir($directory)) {

        // Loop through the files in the directory
        while (false !== ($file = readdir($handle))) {
             $filePath = $directory . '/' . $file;

            // Skip the current and parent directories
            if ($file == '.' || $file == '..') {
                continue;
            }

            // Check if it is a file (and not a directory)
            if (is_file($filePath)) {
    //            echo "isso";
                // Get the file's last modification time
                $fileModTime = filemtime($filePath);

                // Calculate the file's age
                $fileAge = $currentTime - $fileModTime;
                $fileAgeInDays = round(($fileAge/(24 * 60 * 60)),2);

                // Check if the file is older than the age threshold
                if ($fileAge > $ageThreshold) {
                    // Delete the file
                    if (unlink($filePath)) {
                        $msg.= "Deleted after $fileAgeInDays days: $filePath \"\r\n";
                    } else {
                        $msg.= "Failed to delete after $fileAgeInDays days: $filePath \"\r\n";
                    }
                }else{
                    $msg.= "Threshold with fileage $fileAgeInDays days not reached: $filePath \"\r\n";
                }
            }
        }
        // Close the directory handle
        closedir($handle);
    } else {
        $msg.= "Could not open the directory: $directory";
    }  
    return $msg;
}
