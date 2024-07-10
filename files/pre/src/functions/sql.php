<?php
include_once $_SERVER['DOCUMENT_ROOT']."/files/pre/db/dbConfig.php"; 

function globalVal($db, $var){
    
    // lookup for value from var tl_Globals
    
     $sql_val = "Select val from tl_Globals where var like '$var'";
    $result_val = $db->query($sql_val);
    
    while ($row_val = $result_val->fetch_assoc()) {
        return $row_val['val'];
    }
}