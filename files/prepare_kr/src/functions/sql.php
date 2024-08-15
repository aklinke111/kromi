<?php
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php"; 
//include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/globals.php";

//function globalVal($db, $var){
//    
//    // lookup for value from var tl_Globals
//    
//    $sql_val = "Select val from tl_globals where var like '$var'";
//    $result_val = $db->query($sql_val);
//    
//    while ($row_val = $result_val->fetch_assoc()) {
//        return $row_val['val'];
//    }
//}


function buildPivotSql($db){
    
    $ForecastPeriod = globalVal($db, 'ForecastPeriod');
    
    $sqlPivot = "";
    $sqlPivot .= "SUM(cost) AS 'Total 24 months', ";
    
    // Loop years
    for ($i = 0; $i <=2; $i++) {
        // Create a DateTime object for the current date
        $forecastYear = new DateTime();
        //Add one month to the current date
        $modifyer = "+$i years";
        $forecastYear->modify($modifyer);
        // Format the new date as 'YYYY-MM'
        $forecastYear = $forecastYear->format('Y');

        $sqlPivot .= "SUM(CASE WHEN forecastDate BETWEEN '$forecastYear-01' AND '$forecastYear-12' THEN cost ELSE 0 END) AS 'Total ".$forecastYear."', ";
    }

    // Loop months
    for ($i = 0; $i < $ForecastPeriod ; $i++) {
        
        // Create a DateTime object for the current date
        $forecastDate = new DateTime();
        //Add one month to the current date
        $modifyer = "+$i month";
        $forecastDate->modify($modifyer);
        // Format the new date as 'YYYY-MM'
        $forecastDate = $forecastDate->format('Y-m');

        $sqlPivot .= "MAX(CASE WHEN forecastDate LIKE '$forecastDate' THEN cost ELSE 0 END) AS '$forecastDate',"; 
    }


    // string zusammenbauen und modifizieren
    $sql = "SELECT category, costcenter, expenditure, ";
    
    $sql .= $sqlPivot;
        
    $sql = substr($sql, 0, -1);
    
    $sql .= "FROM kr_forecastEngineering 
            JOIN tl_forecastCategory ON tl_forecastCategory.id = categoryId 
            GROUP BY categoryId
            ORDER by positionNo ";
    
    return $sql;
}


function extract_titles_from_sql($sql_query) {
    
//    // Example
//    $sql_query = $sql;
//    $titles = extract_titles_from_sql($sql_query);
//    print_r($titles);
    
    
    // Identify the part of the query containing the column names
    $start = stripos($sql_query, "SELECT") + strlen("SELECT");
    $end = stripos($sql_query, "FROM");
    $columns_part = trim(substr($sql_query, $start, $end - $start));
    
    // Split the column names by comma and trim spaces
    $columns = array_map('trim', explode(',', $columns_part));
    
    // Extract alias names if present
    $extracted_columns = [];
    foreach ($columns as $column) {
        // Check if there is an alias (AS keyword)
        if (stripos($column, ' AS ') !== false) {
            // Split by 'AS' and take the alias part
            $parts = preg_split('/\s+AS\s+/i', $column);
            $extracted_columns[] = trim(end($parts));
        } else {
            // Take the column name or function
            $extracted_columns[] = $column;
        }
    }
    
    return $extracted_columns;
}
