<?php

// tl_toolcenterProjects.projectDateFinished BETWEEN CURDATE() - INTERVAL $Period_Passed_Installations MONTH AND CURDATE()

function forecastDate($i){
    // Create a DateTime object for the current date
    $forecastDate = new DateTime();
    
    //Add one month to the current date
    $modifyer = "+$i month";
    $forecastDate->modify($modifyer);
    
    // Format the new date as 'YYYY-MM'
    return $forecastDate = $forecastDate->format('Y-m');
}