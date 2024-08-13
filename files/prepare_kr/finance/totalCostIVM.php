<?php

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/_includes.php";

function forecastTotalCostIVM($db, $id, $forecastDate, $ForecastPeriod){
    
    $msg = "";
    $totalCost = 0;
    
    // IVMs KROMI facelift
    $modelIdsFacelift = "16, 17, 18";
     $totalCost += $totalCostFacelifts = calculationCostModels($db, $modelIdsFacelift);
    
    // IVMs purchse
    $modelIdsPurchase = "3, 4, 8, 9, 10, 11, 12";
    $totalCost += $totalCostPurchase =  calculationCostModels($db, $modelIdsPurchase);
    
    // IVMs KROMI new
    $modelIdsNew = "2, 5";
    $totalCost += $totalCostKromiNew = calculationCostModels($db, $modelIdsNew);
    
    // stock
    $stockValue = stockValue($db);

  // Monthly costs 
     $monthlyCost = ($totalCostKromiNew - $stockValue) / $ForecastPeriod;
   
   // Insert
    $sql = "INSERT INTO kr_forecastEngineering (tstamp, forecastDate, categoryId, cost) VALUES (".time().", '$forecastDate', $id, $monthlyCost)";
    if($result = $db->query($sql)){
        $totalCost = number_format($totalCost,2);
        $monthlyCost = number_format($monthlyCost,2);
        $msg .= "Monthly cost of  $monthlyCost € (total cost of $totalCost €) for forecast date $forecastDate and forecast period of $ForecastPeriod months inserted successfully in table 'kr_forecastEngineering'<br>";
    }
    $whereClause = "";
    $pendingOrders = number_format(pendingOrders($db, $whereClause, $ForecastPeriod), 2);
    $msg .= "Pending orders of $pendingOrders €<p>";
    
    $msg .= "Total cost for all IVMs of $totalCost €<p>";
    
   return $msg;
}
