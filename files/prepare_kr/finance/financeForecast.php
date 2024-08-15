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

    $sql = "TRUNCATE TABLE kr_forecastEngineering";
    $db->query($sql);
    $msg = "";

    echo  buildPivotSql($db)."<p>";

    $msg .= personnelCostIVMs($db);
    
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
    
    for ($i = 0; $i <= ($ForecastPeriod-1); $i++) {

        $forecastDate =  forecastDate($i);
        $msg .= "<b>Forecast Year-Month: " . $forecastDate. "</b><br>";

        addEntriesToTable($db, $forecastDate, $ForecastPeriod);
    }
    return $msg;
}

function addEntriesToTable($db, $forecastDate, $ForecastPeriod){

    $sql = "Select * from tl_forecastCategory";
    $result = $db->query($sql);
    
    // iterate price and quantity needed for each IVM-model
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];

        switch ($id) {
        case 1:
            echo shipping($db, $id, $forecastDate, $ForecastPeriod);
            break;
        case 2:
            echo costNewIVM($db, $id, $forecastDate, 1);
            break;  
        case 26:
            echo costNewIVM($db, $id, $forecastDate, 2);
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
            echo costFaceliftIVM($db, $id, $forecastDate, 2);
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
        case 22:
            echo paymentsSoftwareSortly($db, $id, $forecastDate, $ForecastPeriod, '3'); // TCWeb SAAS
            break;          
        case 23:
            // region = Europe = 1
            echo costFaceliftIVM($db, $id, $forecastDate, 1);
            break;   
        case 24:
            echo personnelNewIVM($db, $id, $forecastDate, 1);
            break;
        case 25:
            echo personnelNewIVM($db, $id, $forecastDate, 2);
            break;          
        case 27:
            echo personellFaceliftIVM($db, $id, $forecastDate, 1);
            break;
        case 28:
            echo personellFaceliftIVM($db, $id, $forecastDate, 2);
            break; 
        case 29:
            echo materialOnStock($db, $id, $forecastDate, $ForecastPeriod, '58670984'); // Europe
            break; 
        case 30:
            echo materialOnStock($db, $id, $forecastDate, $ForecastPeriod, '71412763'); // South America
            break;           
        default:
        }
    }
}