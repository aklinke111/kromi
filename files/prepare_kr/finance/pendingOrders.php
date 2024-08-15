<?php

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";

// identifiziert offene Bestellungen zum angegebenen Lieferdatum (wird mit Rechnungsdatum gleichgesetzt). Dies gestattet z.B. die Berücksichtigung 
// von Abrufaufträgen 
function pendingOrdersByDateMonth($db, $id, $forecastDate, $forecastPeriod, $internalExternal){

    $msg = "";
    $totalOrderCost = 0;
    
    //  Offene Berstellungen nach Monat
    $sql = "Select
    Sum(Distinct (tl_orders.price - (tl_orders.price * tl_orders.discount / 100) + (tl_orders.price *
        tl_orders.surcharge / 100)) * tl_orders.orderQuantity / tl_orders.packageUnit)  As totalOrderCost,
        Date_Format(tl_orders.estimatedDeliveryDate, '%Y-%m') As estimatedDateMonth
    From
        tl_orders
    Where
        tl_orders.delivered = 0 And
        tl_orders.calculated = 1 And
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



// only statistics
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


// only statistics
function totalPendingOrders($db, $ForecastPeriod,$internalExternal){
    
    // Identifizierung des Gesamtbetrages aller offenen Bestellungen
   $sql = "Select
    Sum(Distinct (tl_orders.price - (tl_orders.price * tl_orders.discount / 100) + (tl_orders.price *
        tl_orders.surcharge / 100)) * tl_orders.orderQuantity / tl_orders.packageUnit)  As totalPendingOrders
    From
        tl_orders
    Where
        tl_orders.delivered = 0 And
        tl_orders.calculated = 1 And
        tl_orders.internalExternal Like '$internalExternal' And
        tl_orders.estimatedDeliveryDate BETWEEN CURDATE() AND CURDATE() + INTERVAL $ForecastPeriod MONTH            
        ";
    $result = $db->query($sql);
    
    while ($row = $result->fetch_assoc()) {
        return round($row['totalPendingOrders'],2);
    }
}

