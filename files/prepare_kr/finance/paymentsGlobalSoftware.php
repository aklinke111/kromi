<?php

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
//include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/globals.php";

function paymentsSoftwareSortly($db, $id, $forecastDate, $ForecastPeriod, $categoryId){
    
    $msg = "";
    $HistoryPeriod = globalVal($db, 'HistoryPeriod');

        $sql = "Select
        SUM(tl_hel_invoices.payment) as total,
        tl_hel_category.name
    From
        tl_hel_invoices Inner Join
        tl_hel_category On tl_hel_category.id = tl_hel_invoices.categoryId
    Where
        tl_hel_invoices.invoiceDate Between CurDate() - Interval $HistoryPeriod Month And CurDate() And
        tl_hel_invoices.supplierId = 82
    ";
    $result = $db->query($sql);
    
    while ($row = $result->fetch_assoc()) {
        $totalPayment = round($row['total'],2);
        $category = $row['name'];
    }
    
    $totalPayment /= $HistoryPeriod;
   
    // Insert
    $sql = "INSERT INTO kr_forecastEngineering (tstamp, forecastDate, categoryId, cost) VALUES (".time().", '$forecastDate', $id, $totalPayment)";
    if($db->query($sql)){
        $totalPayment = number_format($totalPayment,2);
        $msg .= "Payments of $totalPayment € for category '$category' and history period of $HistoryPeriod months inserted successfully in table 'kr_forecastEngineering'<p>";
    }
    return $msg;
}