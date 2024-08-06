<?php
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";


function stockValue($db){
    // Total value of material on sortly stock
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