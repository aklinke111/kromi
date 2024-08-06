<?php

// Needed to return pretty JSON
//header('Content-Type: application/json');
//
// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/sql.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/calculate.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/sortly/updateSortly.php";

// main function for calculating quantites of IVM and update table tl_sortlyTemplatesIVM
if (isset($_GET['webhookFunction'])) {

    $function = $_GET['webhookFunction'];
    
    if($function == "calculateIVM"){
        echo clearQuantity($db);
        echo "<p>";  
        
        echo quantityForecastDeinstallations($db);
        echo "<p>";  
        echo calculateQuantityOrdered($db);
        echo "<p>";          
        echo quantityForecastImplementations($db);
        echo "<p>";          
        echo calculateQuantityIvmProjectsReturn($db);
        echo "<p>";    
        echo calculateQuantityIvmProjects($db);
        echo "<p>";        
        echo calculateAvailableQuantityIvmOnStock($db);
        echo "<p>";  
        echo calculateInaktiveIvmOnStock($db);
        echo "<p>";  
        echo calculateRawIvmOnStock($db);
        echo "<p>";  
        
        echo calculateTotalNeededQuantity($db);
        echo "<p>";  
        
        
    }
}



function lookupGlobals($db){
    $msg = "";
    
    // lookup ForecastPeriod
    $ForecastPeriod = globalVal($db, 'ForecastPeriod');
    $msg .= "Forecast period: $ForecastPeriod months<br>";
    
       // lookup DGUV3 period
    $DGUV3_Period = globalVal($db, 'DGUV3_Period');
    $msg .= "Period between DGUV3 checks: ".$DGUV3_Period."<br>";
    
    // 
    // lookup for pice per each IVM checked e.g. => 80.00€
    $DGUV3_PricePerIVM = globalVal($db, 'DGUV3_PricePerIVM');
    $msg .= "Price per checked IVM DGUV3: ".$DGUV3_PricePerIVM."<br>";
    
    // lookup for pice per each approach e.g. => 79.00€
    $DGUV3_PricePerApproach = globalVal($db, 'DGUV3_PricePerApproach');
    $msg .= "DGUV3 price per approach: ".$DGUV3_PricePerApproach."<br>";
    
    // lookup period of passed IVM installations 
    $Period_Passed_Installations = globalVal($db, 'Period_Passed_Installations');
    $msg .= "Period in months for considering German installations. ".$Period_Passed_Installations."<br>"; 
    
    $msg .=  "<p>";
    
    return $msg;
}




function clearQuantity($db){
   $sql = "UPDATE tl_sortlyTemplatesIVM SET
            quantity = 0, 
            quantityRaw = 0, 
            quantityProjects = 0,
            quantityAvailable = 0, 
            quantityReturn = 0, 
            quantityOrderedExternal = 0, 
            quantityOrderedInternal = 0,             
            quantityOverAll = 0, 
            quantityForecastDeinstallations = 0,
            quantityForecastInstallations = 0,
            quatityScrapped = 0,
            quantityOverhaul = 0,
            remainingValue = 0,            
            priceHr = 0 ";
   
   if ($result = $db->query($sql)){
       return "cleared quantity and price columns in table 'tl_sortlyTemplatesIVM'"; 
    }
}



function quantityForecastDeinstallations($db){
    
    // lookup period of passed IVM deinstallations 
    $Period_Passed_Deinstallations = globalVal($db, 'Period_Passed_Deinstallations');
    $ForecastPeriod = globalVal($db, 'ForecastPeriod');
    
    $msg = "<b>IVMs forecast based on removed/installed devices according installations and changes in projects for a period of $Period_Passed_Deinstallations months </b><p>";

    $sql = "Select
            tl_sortlyTemplatesIVM.name As model,
            tl_sortlyTemplatesIVM.id,
            Count(tl_sortlyTemplatesIVM.id) As quantity
        From
            tl_toolcenterProjectCategory Inner Join
            tl_toolcenterProjects On tl_toolcenterProjectCategory.id = tl_toolcenterProjects.projectCategory Inner Join
            tl_toolcenterProjectStatus On tl_toolcenterProjectStatus.id = tl_toolcenterProjects.projectStatus Inner Join
            tl_toolcenterProjectComponents On tl_toolcenterProjects.id = tl_toolcenterProjectComponents.pid Inner Join
            tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.id = tl_toolcenterProjectComponents.componentModel
        Where
            tl_toolcenterProjectComponents.`usage` Like 'remove' And
            tl_toolcenterProjectCategory.category In ('discontinued', 'change configuration') And
            tl_toolcenterProjects.projectDateFinished BETWEEN CURDATE() - INTERVAL $Period_Passed_Deinstallations MONTH AND CURDATE() and
            tl_toolcenterProjectStatus.status Like 'done'
        Group By
            tl_sortlyTemplatesIVM.name";
    $result = $db->query($sql);
    
    while($item = $result->fetch_assoc()){ 
        $model = $item['model'];
        $id = $item['id'];
        $quantity = $item['quantity'];
        
        // factor
        $factor = $ForecastPeriod / $Period_Passed_Deinstallations;
        $quantity *= $factor;
        
        // Update table
        $sql = "UPDATE tl_sortlyTemplatesIVM SET quantityForecastDeinstallations = $quantity WHERE id = $id";
        $result_update = $db->query($sql);
            
        $msg.= "$model (ID $id) updated with quantity $quantity total deinstalled over period of $factor * $Period_Passed_Deinstallations months <br/>";
    } 
    return $msg;
}



function quantityForecastImplementations($db){
    
    // lookup period of passed IVM installations 
    $Period_Passed_Installations = globalVal($db, 'Period_Passed_Installations');
    $ForecastPeriod = globalVal($db, 'ForecastPeriod');
    
    $msg = "<b>IVMs forecast based on installed devices according implementations and changes in projects for a period of $Period_Passed_Installations months:</b><p>";
    

    
    $sql = "Select
            tl_sortlyTemplatesIVM.name As model,
            tl_sortlyTemplatesIVM.id,
            Count(tl_sortlyTemplatesIVM.id) As quantity
        From
            tl_toolcenterProjectCategory Inner Join
            tl_toolcenterProjects On tl_toolcenterProjectCategory.id = tl_toolcenterProjects.projectCategory Inner Join
            tl_toolcenterProjectStatus On tl_toolcenterProjectStatus.id = tl_toolcenterProjects.projectStatus Inner Join
            tl_toolcenterProjectComponents On tl_toolcenterProjects.id = tl_toolcenterProjectComponents.pid Inner Join
            tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.id = tl_toolcenterProjectComponents.componentModel
        Where
            tl_toolcenterProjectComponents.`usage` Like 'install' And
            tl_toolcenterProjectCategory.category In ('implementation', 'change configuration') And
            tl_toolcenterProjects.projectDateFinished BETWEEN CURDATE() - INTERVAL $Period_Passed_Installations MONTH AND CURDATE() and
            tl_toolcenterProjectStatus.status Like 'done'
        Group By
            tl_sortlyTemplatesIVM.name";
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        $model = $item['model'];
        $id = $item['id'];
        $quantity = $item['quantity'];
        
        // factor
        $factor = $ForecastPeriod / $Period_Passed_Installations;
        $quantity *= $factor;
        
        // Update table
        $sql = "UPDATE tl_sortlyTemplatesIVM SET quantityForecastInstallations = $quantity WHERE id = $id";
        $result_update = $db->query($sql);
            
        $msg.= "$model (ID $id) updated with quantity $quantity total deinstalled over period of $factor * $Period_Passed_Installations months <br/>";
    } 
    return $msg;
}




// sum of all quantity columns
function calculateTotalNeededQuantity($db){

    $msg = "<b>Calculate needed quantities</b><p>";
    
    $sql = "Select * FROM tl_sortlyTemplatesIVM";
    $result = $db->query($sql);
    
    while($item = $result->fetch_assoc()){ 
        
        
        $id = $item['id'];
        $sortlyId = $item['sortlyId'];
        $model = $item['name'];  
        $quantityExtra = $item['quantityExtra'];
        $quantityRaw = $item['quantityRaw'];
        $quantityOrderedInternal = $item['quantityOrderedInternal'];        
        $quantityProjects = $item['quantityProjects'];  
        $quantityAvailable = $item['quantityAvailable'];  
        $quantityMinimum = $item['quantityMinimum'];  
        $quantityForecastInstallations = $item['quantityForecastInstallations'];
        $quantityForecastDeinstallations  = $item['quantityForecastDeinstallations'];
        
        // calculate 'tl_sortlyTemplatesIVM.quantity'
             $quantity = 
              $quantityExtra
            + $quantityProjects 
            + $quantityMinimum 
            + $quantityForecastInstallations
            + $quantityOrderedInternal
            - $quantityForecastDeinstallations 
            - $quantityAvailable;

        // update relevant quantity over all IVMs
        $sql= "UPDATE tl_sortlyTemplatesIVM SET quantity = $quantity WHERE id = $id";
        if ($result_update = $db->query($sql)){
            $msg.= "Field in table 'tl_sortlyTemplatesIVM.quantity' updated successfully for id $id with a total calculated quantity of <b>$quantity pc.</b><br>"; 
        }
        
        $msg.= lookupQuantityOverAll($db, $model);
        $msg.= "<p>";
    }
    return $msg;
}


function lookupQuantityOverAll($db, $model){

    $msg = "";
    
    $sql = "Select COUNT(name) as quantityOverAll from sortly where name LIKE '$model' and name Not Like 'SCRAP'";
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        $quantityOverAll = $item['quantityOverAll']; 
        
        $sqlUpdate = "Update tl_sortlyTemplatesIVM SET quantityOverAll = $quantityOverAll where name LIKE '$model'";
        if($db->query($sqlUpdate)){
           $msg.= "Field in table 'tl_sortlyTemplatesIVM.quantityOverAll' updated successfully for model: $model with a quantity over all of <b>$quantityOverAll pc.</b><br>"; 
        }else{
           $msg.= "Error updating record: " . $db->error . "<br>"; 
        }
    }
    return $msg;
}


 function calculateQuantityOrdered($db){
      // lookup and update ordered quantity

    $msg = "<b>IVMs ordered internally and ecternally:</b><p>";
    
    $sql = "Select sortlyId from tl_sortlyTemplatesIVM";

    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        $sortlyId = $item['sortlyId'];
        
        $msg .= lookupQuantityOrderedIVM($db, $sortlyId, 'quantityOrderedExternal', 'external');
        $msg .= lookupQuantityOrderedIVM($db, $sortlyId, 'quantityOrderedInternal', 'internal');  
        
    } 
    return $msg;
}


function lookupQuantityOrderedIVM($db, $sortlyId, $column, $internalExternal){

    $msg = "";
    
    $ForecastPeriod = globalVal($db, 'ForecastPeriod');
    
    $sql = "Select 
            SUM(orderQuantity) as quantityOrdered 
        FROM tl_orders 
        WHERE sortlyId LIKE '$sortlyId' 
            AND delivered = false 
            AND calculated = true
            AND internalExternal = '$internalExternal'
            AND tl_orders.estimatedDeliveryDate BETWEEN CURDATE() AND CURDATE() + INTERVAL $ForecastPeriod MONTH";
    
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        $quantityOrdered = $item['quantityOrdered'];
        
        // convert if there are no orders and value  = Null
        if(!$quantityOrdered > 0){
            $quantityOrdered = 0;
        }
        
        $sqlUpdate = "Update tl_sortlyTemplatesIVM SET $column = $quantityOrdered where sortlyId LIKE '$sortlyId'";
        if($db->query($sqlUpdate)){
           $msg.= "Field in table 'tl_sortlyTemplatesIVM.$column' updated successfully for sortlyID: $sortlyId with a quantity ordered $internalExternal of <b>$quantityOrdered pc.</b><br>"; 
        }else{
           $msg.= "Error updating record: " . $db->error . "<br>"; 
        }
    }
    return $msg;
//    return $quantityOrdered;
}




function calculateQuantityIvmProjectsReturn($db){

    $msg = "<b>IVMs returned from planned projects:</b><p>";
    
// Calculating all devices from planned projects & components excluded Brazil--->  SQL in FlySQL  query 'projectComponents'    
    $sql = 
    "Select
        tl_sortlyTemplatesIVM.name As model,
        Count(tl_sortlyTemplatesIVM.id ) As quantity,
        tl_sortlyTemplatesIVM.id As id,
        tl_sortlyTemplatesIVM.note
    From
        tl_toolcenterProjectCategory Inner Join
        tl_toolcenterProjects On tl_toolcenterProjectCategory.id = tl_toolcenterProjects.projectCategory Inner Join
        tl_toolcenterProjectStatus On tl_toolcenterProjectStatus.id = tl_toolcenterProjects.projectStatus Inner Join
        tl_toolcenterProjectComponents On tl_toolcenterProjects.id = tl_toolcenterProjectComponents.pid Inner Join
        tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.id = tl_toolcenterProjectComponents.componentModel
    Where
        tl_toolcenterProjectStatus.status Like 'planned' And
        tl_toolcenterProjectComponents.`usage` Like 'remove' and
        tl_toolcenterProjects.ktcId not like 'KTC-3%'
    Group By
        tl_sortlyTemplatesIVM.name
    ";
    
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        $model = $item['model'];
        $note = $item['note'];          
        $quantity = $item['quantity'];
        $id = $item['id'];
        
//        // overwrite  Helix Master (5 to 17) and Slave (2 to 16) to assign Facelift ids
//        if($id == 5){$id = 17;}
//        if($id == 2){$id = 16;}
        
            // Update table
            $sql_1 = "UPDATE tl_sortlyTemplatesIVM SET quantityReturn = $quantity WHERE id = $id";
            $result_1 = $db->query($sql_1);
            
        $msg.= "$model (ID $id) updated with quantity $quantity <br/>";
    } 
    return $msg;
}



function calculateQuantityIvmProjects($db){

    $msg = "<b>IVMs needed for planned projects:</b><p>";
// Calculating all devices from planned projects & components excluded Brazil--->  SQL in FlySQL  query 'projectComponents'    
    $sql = 
    "Select
        tl_sortlyTemplatesIVM.name As model,
        Count(tl_toolcenterProjects.ktcId) As quantity,
        tl_sortlyTemplatesIVM.id As id,
        tl_sortlyTemplatesIVM.note
    From
        tl_toolcenterProjectCategory Inner Join
        tl_toolcenterProjects On tl_toolcenterProjectCategory.id = tl_toolcenterProjects.projectCategory Inner Join
        tl_toolcenterProjectStatus On tl_toolcenterProjectStatus.id = tl_toolcenterProjects.projectStatus Inner Join
        tl_toolcenterProjectComponents On tl_toolcenterProjects.id = tl_toolcenterProjectComponents.pid Inner Join
        tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.id = tl_toolcenterProjectComponents.componentModel
    Where
        tl_toolcenterProjectStatus.status Like 'planned' And
        tl_toolcenterProjectComponents.`usage` Like 'install'
    Group By
        tl_sortlyTemplatesIVM.name
    ";
    
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        $model = $item['model'];
        $note = $item['note'];          
        $quantity = $item['quantity'];
        $id = $item['id'];
        
//        // overwrite  Helix Master (5 to 17) and Slave (2 to 16) to assign Facelift ids
//        if($id == 5){$id = 17;}
//        if($id == 2){$id = 16;}
        
            // Update table
            $sql_1 = "UPDATE tl_sortlyTemplatesIVM SET quantityProjects = $quantity WHERE id = $id";
            $result_1 = $db->query($sql_1);
            
        $msg.= "$model (ID $id) updated with quantity $quantity <br/>";
    } 
    return $msg;
}



function calculateAvailableQuantityIvmOnStock($db){

    $msg = "<b>IVMs available on KROMI stock:</b><p>";
    
    // Calculating all devices on stock   
    $sql = 
    "Select
        tl_sortlyTemplatesIVM.name As model,
        tl_sortlyTemplatesIVM.note,        
        Count(sortly.IVM) As quantity,
        Count(IF(sortly.overhaul <> '', 'overhaul', NULL)) as quantityOverhaul,      
        tl_sortlyTemplatesIVM.id As id


    From
        sortly Inner Join
        sortly_ktc On sortly.pid = sortly_ktc.sid Inner Join
        tl_sortlyTemplatesIVM On sortly.name = tl_sortlyTemplatesIVM.name
    Where
        sortly.IVM = 1 And
        sortly_ktc.name Not Like 'SCRAP' And
        sortly_ktc.name Not Like 'KTC-%' And
        sortly.available = 1 And
        sortly.raw = 0
    Group By
        tl_sortlyTemplatesIVM.name
    ";
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        
        $id = $item['id'];
        $model = $item['model'];
        $quantity = $item['quantity'];
        $quantityOverhaul = $item['quantityOverhaul'];

        // in Sortly is made no difference between regular Helix or facelifted Helix. We need to adjust this according flag 'overhaul'
        // if we have en entry there, it is a facelifted device and assigned like that. Change id from 5 to 15 for master and from 2 to 16 for slaves

            // first update the regular devices with quantity minus quantity overhauled. We can subtract because in case there are no overhauled IVMs, $quantityOverhaul is 0 
            $quantity -= $quantityOverhaul;
            
            // Update regular IVM - Sortly only knows these ones
            $sql = "UPDATE tl_sortlyTemplatesIVM SET quantityAvailable = $quantity WHERE id = $id";
            $result_update = $db->query($sql);
            $msg.= "$model (ID $id) updated with quantity $quantity <br/>";
                
            if($quantityOverhaul > 0){
                switch ($id) {
                    case 5:
                        $id = 17;
                    break;
                    case 2:
                        $id = 16;
                    break;
                }
                // Update rows for facelifted IVMs
                $sql = "UPDATE tl_sortlyTemplatesIVM SET quantityAvailable = $quantityOverhaul WHERE id = $id";
                $result_update = $db->query($sql);
                $msg.= "$model (ID $id) updated with quantity $quantityOverhaul <br/>";
            }
    } 
    return $msg;
}



function calculateInaktiveIvmOnStock($db){

    $msg = "<b>IVMs inactive/return for overhoul on Kromi stock:</b><p>";
// Calculating all devices on stock   
    $sql = 
    "Select
        tl_sortlyTemplatesIVM.name As model,
        Count(sortly.IVM) As quantity,
        tl_sortlyTemplatesIVM.id As id,
        tl_sortlyTemplatesIVM.note
    From
        sortly Inner Join
        sortly_ktc On sortly.pid = sortly_ktc.sid Inner Join
        tl_sortlyTemplatesIVM On sortly.name = tl_sortlyTemplatesIVM.name
    Where
        sortly.IVM = 1 And
        sortly_ktc.name Not Like 'SCRAP' And
        sortly_ktc.name Not Like 'KTC-%' And
        sortly.available = 0 And
        sortly.raw = 0
    Group By
        tl_sortlyTemplatesIVM.name
    ";
    
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        $model = $item['model'];
        $note = $item['note'];        
        $quantity = $item['quantity'];
        $id = $item['id'];
        
//         overwrite  Helix Master (5 to 17) and Slave (2 to 16) to assign Facelift ids - all used for Brazil and not calculated
        if($id == 5 or $id == 2){$id = 17;}
 
        
            // Update table
        $sql_1 = "UPDATE tl_sortlyTemplatesIVM SET quantityOverhaul = $quantity WHERE id = $id";
        $result_1 = $db->query($sql_1);
            
        $msg.= "$model (ID $id) updated with return/overhaul quantity $quantity <br/>";
    } 
    return $msg;
}


function calculateRawIvmOnStock($db){
    $msg = "<b>IVMs on Kromi stock raw for build new:</b><p>";

// Calculating all devices on stock   
    $sql = 
    "Select
        tl_sortlyTemplatesIVM.name As model,
        Count(sortly.IVM) As quantity,
        tl_sortlyTemplatesIVM.id As id,
        sortly.sid
    From
        sortly Inner Join
        sortly_ktc On sortly.pid = sortly_ktc.sid Inner Join
        tl_sortlyTemplatesIVM On sortly.name = tl_sortlyTemplatesIVM.name
    Where
        sortly.IVM = 1 And
        sortly_ktc.name Not Like 'SCRAP' And
        sortly_ktc.name Not Like 'KTC-%' And
        sortly.active = 0 and
        sortly.raw = 1
    Group By
        tl_sortlyTemplatesIVM.name
    ";
    
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        
        $model = $item['model'];
        $sid = $item['sid'];          
        $quantity = $item['quantity'];
        $id = $item['id'];
        
        // Update inTable IVM
        $sql_1 = "UPDATE tl_sortlyTemplatesIVM SET quantityRaw = $quantity WHERE id = $id";
        $result_1 = $db->query($sql_1);
      
        // Update quantity in Sortly via API 
        // KTC-HX/S Helx Korpus => sortlyID SD0M4T2603, sid = 73933218
        // KTC-HX/M Helx Korpus => sortlyID SD0M4T2604, sid = 73933243
        
        // assign ,model
        if($model == "KTC-HX/S"){
           $sid = 73933218;
        } elseif ($model == "KTC-HX/M"){
            $sid = 73933243;
        }
        
        $msg.= "$model (ID $id) updated in Sortly with quantity $quantity <p>";
        
        // update quantity in Sortly
        $newValue = $quantity;
        $msg.= singleUpdateQuantity($db, $sid, $newValue);
    } 
    return $msg;
}



function singleUpdateQuantity($db, $sid, $newValue){
    
    $msg = "";
    //Basic URL to GET sortly items
    $sortlyUrlPrefix = 'https://api.sortly.co/api/v1/items/';
    $sortlyUrlAppendix = '/?&include=custom_attributes%2Cphotos%2Coptions';
    
//    echo "Update";
    // prepare the update payload
    $payload = updatePayload($newValue);

    // Run the update
    sortlyItemUpdate($sortlyUrlPrefix, $sid, $payload);

    // Output updated item
    $msg.= getSortlyJSON($sortlyUrlPrefix, $sid, $sortlyUrlAppendix)."<p>";
    
    // log
    writeLog('function: singleUpdateQuantity from sortly/calculateQuantityIVM.php', 'UPDATE', $msg, $db);

    return $msg; 
}                   
   


// Update quantity
function updatePayload($newValue){
    
    // payload array
    $array = [
        'quantity' => $newValue,
    ];

    return $payload = json_encode($array,JSON_PRETTY_PRINT);
}