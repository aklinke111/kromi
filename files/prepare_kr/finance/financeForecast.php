<?php
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/_includes.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/finance/_includes.php";

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/stats/sortlyStats.php";
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
    $HistoryPeriod = globalVal($db, 'HistoryPeriod');
    $msg .= "Period in months for considering German installations. ".$HistoryPeriod."<br>"; 
    
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
            echo shipping($db, $id, $forecastDate, $ForecastPeriod);
            break;
        case 2:
        //calculate monthly total for all IVM
            echo forecastTotalCostIVM($db, $id, $forecastDate, $ForecastPeriod);
            break;  
        case 4:
            echo workwear($db, $id, $forecastDate, $ForecastPeriod);
            break;        
        case 5:
            echo forcastDGUV3($db, $id, $forecastDate, $ForecastPeriod);
            break;
        case 11:
            echo travelling($db, $id, $forecastDate, $ForecastPeriod);
            break;        
        case 12:
            echo pendingOrdersByDateMonth($db, $id, $forecastDate, $ForecastPeriod, 'external');
            break;  
        case 13:
            // region = South America = 2           
            echo totalCostFacelift($db, $id, $forecastDate, 2);
            break;          
        case 16:
            echo pendingOrdersByDateMonth($db, $id, $forecastDate, $ForecastPeriod, 'internal');
            break;          
        case 17:
            echo paymentsHeliotronic($db, $id, $forecastDate, $ForecastPeriod, '1'); // maintenance and updates
            break;  
        case 18:
            echo paymentsHeliotronic($db, $id, $forecastDate, $ForecastPeriod, '5'); // Features and support
            break; 
        case 19:
            echo paymentsHeliotronic($db, $id, $forecastDate, $ForecastPeriod, '4'); // TCMobile
            break; 
        case 20:
            echo paymentsHeliotronic($db, $id, $forecastDate, $ForecastPeriod, '2'); // single licence fee
            break;  
        case 21:
            echo paymentsHeliotronic($db, $id, $forecastDate, $ForecastPeriod, '3'); // TCWeb SAAS
            break;  
        case 23:
            // region = Europe = 1
            echo totalCostFacelift($db, $id, $forecastDate, 1);
            break;         
        default:
        }
    }
}


function truncateForecast($db){
        // truncate table 'bomCalculations'
    $sql = "TRUNCATE TABLE kr_forecastEngineering";
    $db->query($sql);
}
