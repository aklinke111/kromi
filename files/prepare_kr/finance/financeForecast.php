<?php
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/sql.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/date.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/calculate.php";

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/stats/financeStats.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/stats/sortlyStats.php";

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/dguv3.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/abcAnalyses.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/pendingOrders.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/payments.php";

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/sortly/ivmBomDisplayAndUpdate.php";

if (isset($_GET['webhookFunction'])) {

    $function = $_GET['webhookFunction'];
    
    if($function == "financeForecast"){
        
//        ivmBomDisplayAndUpdate($db);
        
        echo financeForecast_main($db);
    }
}


function financeForecast_main($db){

    $msg = "";
        
    $msg .= buildPivotSql()."<p>";
    
    $msg .= truncateForecast($db);
   
    // lookup ForecastPeriod
    $ForecastPeriod = globalVal($db, 'ForecastPeriod');
    $msg .= "Forecast period: $ForecastPeriod months<br>";
    
    // lookup DGUV3 period
    $DGUV3_Period = globalVal($db, 'DGUV3_Period');
    $msg .= "Period between DGUV3 checks: ".$DGUV3_Period."<br>";
    
    // lookup for pice per each IVM checked e.g. => 80.00€
    $DGUV3_PricePerIVM = globalVal($db, 'DGUV3_PricePerIVM');
    $msg .= "Price per checked IVM DGUV3: ".$DGUV3_PricePerIVM."<br>";
    
    // lookup for pice per each approach e.g. => 79.00€
    $DGUV3_PricePerApproach = globalVal($db, 'DGUV3_PricePerApproach');
    $msg .= "DGUV3 price per approach: ".$DGUV3_PricePerApproach."<br>";
    
    // lookup period of passed IVM installations 
    $Period_Passed_Installations = globalVal($db, 'Period_Passed_Implementations');
    $msg .= "Period in months for considering German installations. ".$Period_Passed_Installations."<br>"; 
    
    $msg .=  "---------------------------------------------------------------------- <p>";
    

    // IVMs KROMI facelift
    $modelIdsFacelift = "16, 17, 18";
    $msg .= "IVMs KROMI facelift: ".number_format(calculationCostModels($db, $modelIdsFacelift),2)."<br>";
     
    // IVMs KROMI new
    $modelIdsNew = "2, 5";
    $msg .= "IVMs KROMI new: ".number_format(calculationCostModels($db, $modelIdsNew),2)."<br>";
    
    // IVMs ordered 
    $whereClause = "And sortly.IVM = 1 ";
    $msg .= "IVMs ordered: ".number_format(pendingOrders($db, $whereClause, $ForecastPeriod), 2)."<br>";
    
    // Stock value
    $msg .= "Total value of material on stock: ".number_format(stockValue($db,), 2)."<br>";
    
    $msg .=  "---------------------------------------------------------------------- <p>";
//    $msg .=  "<p>";
    
    $msg .= "Total costs for all needed parts from BOM list <b>: ".number_format(totalPriceFromBOM($db),2)." €</b><p>";

    echo $msg;
    
    for ($i = 0; $i <= ($ForecastPeriod-1); $i++) {

    $forecastDate =  forecastDate($i); //files/prepare_kr/src/functions/date.php
    
    $msg .= "<b>Forecast Year-Month: " . $forecastDate. "</b><br>";
    
    addEntriesToTable($db, $forecastDate, $ForecastPeriod);
    }
}



function addEntriesToTable($db, $forecastDate, $ForecastPeriod){

    $sql = "Select * from tl_forecastCategory";
    $result = $db->query($sql);
    
    // iterate price and quantity needed for each IVM-model
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
//        $functionName = $row['function'];
//        echo $functionName."<br>";
        
//        if (function_exists($functionName)) {
//            echo $functionName($db, $id, $forecastDate, $ForecastPeriod);
//            break;
//        }
//         echo $functionName($db, $id, $forecastDate, $ForecastPeriod);
        switch ($id) {
        case 1:
            break;
        case 2:
        //calculate monthly total for all IVM
            echo forcastTotalCostIVM($db, $id, $forecastDate, $ForecastPeriod);
            break;                
        case 5:
            echo forcastDGUV3($db, $id, $forecastDate, $ForecastPeriod);
            break;
        case 12:
            echo pendingOrdersByDateMonth($db, $id, $forecastDate, $ForecastPeriod, 'external');
            break;  
        case 16:
            echo pendingOrdersByDateMonth($db, $id, $forecastDate, $ForecastPeriod, 'internal');
            break;          
        case 13:
            echo totalCostFacelift($db, $id, $forecastDate, $ForecastPeriod);
            break;  
        case 17:
            echo paymentsHeliotronic($db, $id, $forecastDate, $ForecastPeriod, '1'); // maintenance and updates
            break;  
        case 20:
            echo paymentsHeliotronic($db, $id, $forecastDate, $ForecastPeriod, '2'); // single licence fee
            break;  
        case 18:
            echo paymentsHeliotronic($db, $id, $forecastDate, $ForecastPeriod, '3'); // TCWeb SAAS
            break;  
        case 19:
            echo paymentsHeliotronic($db, $id, $forecastDate, $ForecastPeriod, '4'); // TCMobile
            break; 
        case 17:
            echo paymentsHeliotronic($db, $id, $forecastDate, $ForecastPeriod, '5'); // Features and support
            break;           
        default:
        }
    }
}



function totalCostFacelift($db, $id, $forecastDate, $ForecastPeriod){

    $msg = "";
    $totalPerMonth = 0;

    $modelIds = "16, 17, 18"; // Facelift models
    $totalAllDevices = calculationCostModels($db, $modelIds);
    
    $dateEndOfFacelift = globalVal($db, 'EndOfFaceliftMeasures');
    $countMonthEndOfFaceliftMeasures = getMonthsToDate($dateEndOfFacelift);
    

    $date = date_create($dateEndOfFacelift);
    $date_1 = date_format($date,"Y-m");
    $date_2 = $forecastDate;
    
    // Dieser Betrag wird nur über die Laufzeit in Monaten hinzugefügt, danach wird er zu 0
    $answer = compareMonthYear($date_1, $date_2);
    
    if($answer == "higher date"){
        $totalPerMonth = Round(($totalAllDevices / $countMonthEndOfFaceliftMeasures),2);
    } 

    // Insert
    $sql = "INSERT INTO kr_forecastEngineering (tstamp, forecastDate, categoryId, cost) VALUES (".time().", '$forecastDate', $id, $totalPerMonth)";
    if($result = $db->query($sql)){
    $msg .= "Monthly cost for KTC-facelifts of $totalPerMonth € for forecast date $forecastDate and forecast period of $countMonthEndOfFaceliftMeasures months inserted successfully in table 'kr_forecastEngineering'<p>";
    }
    
    return $msg;
}



function truncateForecast($db){
        // truncate table 'bomCalculations'
    $sql = "TRUNCATE TABLE kr_forecastEngineering";
    $result = $db->query($sql);
}



function forcastTotalCostIVM($db, $id, $forecastDate, $ForecastPeriod){
    
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
