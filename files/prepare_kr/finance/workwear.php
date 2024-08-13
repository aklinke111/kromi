<?php

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";

function workwear($db, $id, $forecastDate, $ForecastPeriod){
    
    $msg = "";
    $HistoryPeriod = globalVal($db, 'HistoryPeriod');

    $sql = "Select
        Sum(tl_costWorkwear.payment) As payment
    From
        tl_costWorkwear
    Where
        tl_costWorkwear.exclude = 0 And
        tl_costWorkwear.receiptDate Between CurDate() - Interval $HistoryPeriod Month And CurDate()
        ";
    $result = $db->query($sql);
    
    while ($row = $result->fetch_assoc()) {
        $payment = round($row['payment'],2);
//        $costcenter = $row['costcenter'];
    }

    $payment /= $HistoryPeriod;
   
    // Insert
    $sql = "INSERT INTO kr_forecastEngineering (tstamp, forecastDate, categoryId, cost) VALUES (".time().", '$forecastDate', $id, $payment)";
    if($result = $db->query($sql)){
//        $totalPayment = number_format($totalPayment,2);
        $msg .= "Payments of $payment € for history period of $HistoryPeriod months inserted successfully in table 'kr_forecastEngineering'<p>";
    }
    return $msg;
}