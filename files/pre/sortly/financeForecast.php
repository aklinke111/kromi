<?php
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/sql.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/date.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/calculate.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/sortly/ivmBomDisplayAndUpdate.php";

if (isset($_GET['webhookFunction'])) {

    $function = $_GET['webhookFunction'];
    
    if($function == "financeForecast"){
        
//        ivmBomDisplayAndUpdate($db);
        
        echo main_financeForecast($db);
    }
}


function main_financeForecast($db){

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

function stockValue($db){
    //  Total value of material on sortly stock
    $msg = "";
    

    $sql = "Select
            ROUND(SUM(sortly.quantity * sortly.price),2) as totalStockValueMaterial
        From
            sortly
        Where
            sortly.quantity > 0 And
            sortly.discontinued = 0 And
            sortly.pid = '58670984'";
    $result = $db->query($sql);
    
    while ($row = $result->fetch_assoc()) {
         $stockValue = $row['totalStockValueMaterial'];
    }

    return $stockValue;
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



//
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
    $totalCostFacelifts = calculationCostModels($db, $modelIdsFacelift);
    
    // IVMs purchse
    $modelIdsPurchase = "3, 4, 8, 9, 10, 11, 12";
    $totalCostPurchase =  calculationCostModels($db, $modelIdsPurchase);
    
    // IVMs KROMI new
    $modelIdsNew = "2, 5";
    $totalCostKromiNew = calculationCostModels($db, $modelIdsNew);
    
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
    
   return $msg;
}



// identifiziert offene Bestellungen zum angegebenen Lieferdatum (wird mit Rechnungsdatum gleichgesetzt). Dies gestattet z.B. die Berücksichtigung 
// von Abrufaufträgen 
function pendingOrdersByDateMonth($db, $id, $forecastDate, $forecastPeriod, $internalExternal){

    $msg = "";
    $totalOrderCost = 0;
    
    //  Offene Berstellungen nach Monat
    $sql = "Select Sum(
    (tl_orders.price 
    - (tl_orders.price * tl_orders.discount / 100) 
    + (tl_orders.price * tl_orders.surcharge / 100)
    ) 
    * tl_orders.orderQuantity) As totalOrderCost,
    Date_Format(tl_orders.estimatedDeliveryDate, '%Y-%m') As estimatedDateMonth
From
    tl_orders
Where
    tl_orders.delivered = 0 And
    tl_orders.internalExternal Like '$internalExternal'
Group By
    Date_Format(tl_orders.estimatedDeliveryDate, '%Y-%m')";
    
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
         $estimatedDateMonth = $row['estimatedDateMonth'];
        
//        echo $forecastDate."  test ----   ". $estimatedDateMonth." ---------------- <p>"; 
        
        if($estimatedDateMonth == $forecastDate){
            $totalOrderCost = round($row['totalOrderCost'],2);
            break;
        }
    }

    // Insert
    $sql = "INSERT INTO kr_forecastEngineering (tstamp, forecastDate, categoryId, cost) VALUES (".time().", '$forecastDate', $id, $totalOrderCost)";
    if($result = $db->query($sql)){
        $cost = number_format($totalOrderCost,2);
        $msg .= "Cost of $cost € for $internalExternal estimated orders at forecast date $forecastDate inserted successfully in table 'kr_forecastEngineering'<p>";
    }
    return $msg;
}



function paymentsHeliotronic($db, $id, $forecastDate, $ForecastPeriod, $categoryId){
    $msg = "";
    $HistoryPeriod = globalVal($db, 'HistoryPeriod');

        echo $sql = "Select
        SUM(tl_hel_invoices.payment) as total,
        tl_hel_category.name
    From
        tl_hel_invoices Inner Join
        tl_hel_category On tl_hel_category.id = tl_hel_invoices.categoryId
    Where
        tl_hel_invoices.invoiceDate Between CurDate() - Interval $HistoryPeriod Month And CurDate() And
        tl_hel_category.id = $categoryId ";
    $result = $db->query($sql);
    
    while ($row = $result->fetch_assoc()) {
        $totalPayment = round($row['total'],2);
        $category = $row['name'];
    }
    
    $totalPayment /= $HistoryPeriod;
   
    // Insert
    $sql = "INSERT INTO kr_forecastEngineering (tstamp, forecastDate, categoryId, cost) VALUES (".time().", '$forecastDate', $id, $totalPayment)";
    if($result = $db->query($sql)){
        $totalPayment = number_format($totalPayment,2);
        $msg .= "Payments of $totalPayment € for category '$category' and history period of $HistoryPeriod months inserted successfully in table 'kr_forecastEngineering'<p>";
    }
    return $msg;
}




function pendingOrders($db, $whereClause, $ForecastPeriod){
    
    // Identifizierung des Gesamtbetrages aller offenen Bestellungen
    $sql = "Select
        Sum((tl_orders.price - (tl_orders.price * tl_orders.discount / 100)) * tl_orders.orderQuantity) As total
    From
        tl_orders Inner Join
        sortly On sortly.sortlyId = tl_orders.sortlyId
    Where
        tl_orders.estimatedDeliveryDate BETWEEN CURDATE() AND CURDATE() + INTERVAL $ForecastPeriod MONTH AND
        tl_orders.delivered = 0 $whereClause";
    
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        return round($row['total'],2);
    }
}




function totalPriceFromBOM($db){
    // Summe der benötigten Teile laut BOM

    $sql = "Select Distinct
        Round(Sum((sortly.price * (bomCalculations.quantityNeeded - bomCalculations.quantityStock -
        bomCalculations.quantityOrdered))),2) As purchasePrice
    From
        bomCalculations Left Join
        sortly On bomCalculations.sortlyId = sortly.sortlyId
    where
        (sortly.price * (bomCalculations.quantityNeeded - bomCalculations.quantityStock -
        bomCalculations.quantityOrdered)) > 0";
    $result = $db->query($sql);
    
    while ($row = $result->fetch_assoc()) {
        return $row['purchasePrice'];
    }
}




function forcastDGUV3($db, $id, $forecastDate, $forecastPeriod ){
    
    $msg = "";
    $totalCost = 0;
  
    $Period_Passed_Installations = globalVal($db, 'Period_Passed_Installations');

    // lookup forestimated new customers / year for counting approaches
    
//    // Lookup existing count of IVM in Germany
//    $EstimatedNewGermanCustomers = globalVal($db, 'EstimatedNewGermanCustomers');
//    $msg .=  $EstimatedNewGermanCustomers."<br>";
    
    // Lookup passed german projects over last year to etimate new installations next year
    $sql ="Select
        Count(tl_sortlyTemplatesIVM.active) As CountInstalledIVM
    From
        tl_toolcenterProjectCategory Inner Join
        tl_toolcenterProjects On tl_toolcenterProjectCategory.id = tl_toolcenterProjects.projectCategory Inner Join
        tl_toolcenterProjectStatus On tl_toolcenterProjectStatus.id = tl_toolcenterProjects.projectStatus Inner Join
        tl_toolcenterProjectComponents On tl_toolcenterProjects.id = tl_toolcenterProjectComponents.pid Inner Join
        tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.id = tl_toolcenterProjectComponents.componentModel Inner Join
        sortly_ktc On tl_toolcenterProjects.ktcId = sortly_ktc.name Inner Join
        sortly_customer On sortly_customer.sid = sortly_ktc.pid Inner Join
        sortly_subsidiary On sortly_subsidiary.sid = sortly_customer.pid Inner Join
        sortly_country On sortly_country.sid = sortly_subsidiary.pid
    Where
        tl_toolcenterProjects.ktcId Not Like 'KTC-000' And
        sortly_country.name = 'Germany' And
        tl_toolcenterProjectCategory.category In ('implementation', 'change configuration') And
        tl_toolcenterProjects.projectDateFinished BETWEEN CURDATE() - INTERVAL $Period_Passed_Installations MONTH AND CURDATE() And
        tl_toolcenterProjectComponents.`usage` = 'install'
    Group By
        sortly_country.name";
        $result = $db->query($sql);
        
        while ($row = $result->fetch_assoc()) {
           $CountInstalledIVM = $row['CountInstalledIVM'];
    //        
        }

        // 1. step - calculating 
        $totalDevices = $CountInstalledIVM / $Period_Passed_Installations;
        
        $totalCost += round($totalDevices,0) * globalVal($db, 'DGUV3_PricePerIVM');
        
        $msg .= "Count of passed German installations: ".$CountInstalledIVM." leads to an average of ".$totalCost." €<br>";
        $msg .= $totalCost." € total cost for DGUV3 at 1st step<br>";

    // Lookup passed DGUV checks and add 48 months to each IVM
    $DGUV3_Period = globalVal($db, 'DGUV3_Period');
//    $DGUV3_Period = 15;
    $sql = "Select
        Date_Format(Date_Add(sortly.DGUV3, Interval $DGUV3_Period Month), '%Y-%m') As nextDGUV3,
        Count(sortly.active) As Count_IVM
    From
        sortly
    Where
        sortly.DGUV3No Not Like ''
    Group By
        nextDGUV3
    Order By
        nextDGUV3";
    
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $nextDGUV3 = $row['nextDGUV3'];
        $Count_IVM = round($row['Count_IVM'],0);
//        $msg .= $nextDGUV3." --- ".$Count_IVM."<br>"; 
        if($nextDGUV3 == $forecastDate){
            
            $totalDevices += round($Count_IVM,1);
            $costs = $totalDevices * globalVal($db, 'DGUV3_PricePerIVM');
            $totalCost += $costs;
            
            $msg .= "Count of DGUV3 checks must be done during  forecast-month ".$nextDGUV3.": ".$Count_IVM." <br>"; 
            $msg .= round($costs,2)." € cost for DGUV3 at 2nd step<br>";
        }
        
    }
    
    // approached KTCs
    $sql = "Select
        Count(sortly.active) As Count_IVM,
        Count(Distinct sortly_ktc.name) As Count_KTC
    From
        sortly Inner Join
        sortly_ktc On sortly_ktc.sid = sortly.pid
    Where
        sortly.DGUV3No Not Like '' ";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $Count_IVM = $row['Count_IVM'];
        $Count_KTC = $row['Count_KTC'];
        $quote = $Count_KTC / $Count_IVM ;

        // 
        $approachedKtc = round($totalDevices * $quote,1);
        $approachingCost = round($approachedKtc * globalVal($db, 'DGUV3_PricePerApproach'));
        
        $msg .= "Approached ".$approachedKtc." KTCs for total approaching costs of ".$approachingCost." € for ".round($totalDevices,0)." IVMs <br>";
        
        $totalCost += $approachingCost;
        
        $msg .= round($approachingCost,2)." € total cost for DGUV3 at 3d step<p>";
    }
    
    $totalCost =  round($totalCost,2);
    $totalCost = number_format($totalCost,2);
    $totalCost *= (1);

    $msg .= "TOTAL monthly cost for DGUV3: $totalCost €";
            
    // Insert
    $sql = "INSERT INTO kr_forecastEngineering (tstamp, forecastDate, categoryId, cost) VALUES (".time().", '$forecastDate', $id, $totalCost)";
    if($result = $db->query($sql)){
        $msg .= "Monthly cost DGUV3 of $totalCost € for forecast date $forecastDate and forecast period of $forecastPeriod months inserted successfully in table 'kr_forecastEngineering'<br>";
    }

    $msg .= "<p>";
    
    return $msg;
}