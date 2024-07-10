<?php

function calculateTotalExpense2YearsIVM($db){
    $msg = "";
    // Check if the file exists before renaming
    if (file_exists($oldName)) {
        // Rename the file
        if (rename($oldName, $newName)) {
            $msg.= "File renamed successfully to $newName.";
        } else {
            $msg.= "Failed to rename the file.";
        }
    } else {
        $msg.= "File $oldName does not exist.";
    } 
    
    return $msg;
}