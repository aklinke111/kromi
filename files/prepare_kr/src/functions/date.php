<?php

// tl_toolcenterProjects.projectDateFinished BETWEEN CURDATE() - INTERVAL $Period_Passed_Installations MONTH AND CURDATE()

function forecastDate($i){
    // Create a DateTime object for the current date
    $forecastDate = new DateTime();
    
    //Add one month to the current date
    $modifyer = "+$i month";
    $forecastDate->modify($modifyer);
    
    // Format the new date as 'YYYY-MM'
    return $forecastDate->format('Y-m');
//    return $forecastDate = $forecastDate->format('Y-m');
}



function compareMonthYear($date1, $date2){
    // Dates in "YYYY-MM" format
    // Create DateTime objects
    $dateTime1 = DateTime::createFromFormat('Y-m', $date1);
    $dateTime2 = DateTime::createFromFormat('Y-m', $date2);

    // Check if both dates are valid
    if ($dateTime1 && $dateTime2) {
        // Compare the dates
        if ($dateTime1 < $dateTime2) {
//            echo "$date1 is before $date2";
            return "lower date";
        } elseif ($dateTime1 > $dateTime2) {
            return "higher date";
//            echo "$date1 is after $date2";
        } else {
            return "same date";
//            echo "$date1 is the same as $date2";
        }
    } else {
//        echo "One or both dates are INVALID";
    }
}



function getMonthsToDate($date){

    // Set the timezone
    date_default_timezone_set('Your/Timezone'); // Example: 'America/New_York'

    // Current date
    $currentDate = new DateTime();

    // Target date
    $targetDate = new DateTime($date); // Change to your target date

    // Calculate the difference
    $interval = $currentDate->diff($targetDate);

    // Get the number of months
    $months = $interval->y * 12 + $interval->m;

    // Output the result
    return $months;
}



//function loopYearMonth($strToModify){
//    
//    for ($i = 0; $i <= 23; $i++) {
//    // Create a DateTime object for the current date
//    $forecastDate = new DateTime();
//
//    //Add one month to the current date
//    $modifyer = "+$i month";
//    $forecastDate->modify($modifyer);
//    
//    // Format the new date as 'YYYY-MM'
//    $forecastDate = $forecastDate->format('Y-m');
//    }
//}