<?php

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";

function materialOnStock($db, $id, $forecastDate, $ForecastPeriod, $sortlyPid){
    
    $msg = "";
    $HistoryPeriod = globalVal($db, 'HistoryPeriod');

    $sql = "Select
        Sum(sortly.price * sortly.quantity) As totalMaterialValue,
        sortly1.name as pidName
    From
        sortly Right Join
        bomCalculations On sortly.sortlyId = bomCalculations.sortlyId Inner Join
        sortly sortly1 On sortly1.sid = sortly.pid
    Where
        sortly.pid = '$sortlyPid' And
        sortly.IVM = 0 And
        sortly.discontinued = 0
    Group By
        sortly.pid,
        sortly1.name
    ";
    $result = $db->query($sql);
    
    while ($row = $result->fetch_assoc()) {
        $totalMaterialValue = $row['totalMaterialValue'];
        $pidName = $row['pidName'];
    }

    $totalMaterialValue /= $ForecastPeriod;
    $totalMaterialValue = round($totalMaterialValue,2);
    
    // Insert
    $sql = "INSERT INTO kr_forecastEngineering (tstamp, forecastDate, categoryId, cost) VALUES (".time().", '$forecastDate', $id, $totalMaterialValue)";
    if($db->query($sql)){
        $msg .= "Value of $totalMaterialValue € for material on Stock '$pidName' forecasted by  period of $ForecastPeriod months inserted successfully in table 'kr_forecastEngineering'<p>";
    }
    return $msg;
}

