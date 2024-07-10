<?php

// Needed to return pretty JSON
//header('Content-Type: application/json');
//
// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/pre/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/pre/sortly/updateSortly.php";

// main function for calculating quantites of IVM and update table tl_sortlyTemplatesIVM
if (isset($_GET['webhookFunction'])) {

    $function = $_GET['webhookFunction'];
    
    if($function == "calculateIVM"){
        echo clearQuantity($db);
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
        
        echo calculateTotalNeededQuantity($db);
        echo "<p>";   
        
    }
}

function clearQuantity($db){
   $sql = "UPDATE tl_sortlyTemplatesIVM SET
            quantity = 0, 
            quantityRaw = 0, 
            quantityProjects = 0,
            quantityAvailable = 0, 
            quantityReturn = 0, 
            quantityOrdered = 0, 
            quantityOverAll = 0, 
            quantityOverhaul = 0";
   
   if ($result = $db->query($sql)){
       return "cleared quantity columns in table 'tl_sortlyTemplatesIVM'"; 
    }
}


// sum of all qautity columns
function calculateTotalNeededQuantity($db){
    $msg = "";
    $sql = "Select * FROM tl_sortlyTemplatesIVM";
    $result = $db->query($sql);
    
    while($item = $result->fetch_assoc()){ 
        $id = $item['id'];
        $sortlyId = $item['sortlyId']; $sortlyId = $item['sortlyId']; 
        $model = $item['name'];      
        $quantityRaw = $item['quantityRaw'];
        $quantityProjects = $item['quantityProjects'];  
        $quantityAvailable = $item['quantityAvailable'];  
        $quantityMinimum = $item['quantityMinimum'];  
        $quantityForecast = $item['quantityForecast'];
        
        // lookup and update ordered quantity
        $msg.= lookupQuantityOrderedIVM($db, $sortlyId);
        
        // lookup and update quantity over all IVMs
        
        // formular to calculate
        $quantity = 
            $quantityRaw 
            + $quantityProjects 
            + $quantityMinimum 
            + $quantityForecast 
            - $quantityAvailable
            - $quantityOrdered; 
        
        $sql_1 = "UPDATE tl_sortlyTemplatesIVM SET quantity = $quantity WHERE id = $id";
        if ($result_1 = $db->query($sql_1)){
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

function lookupQuantityOrderedIVM($db, $sortlyId){
    $msg = "";
    $sql = "Select SUM(orderQuantity) as quantityOrdered from tl_orders where sortlyId LIKE '$sortlyId'";
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        $quantityOrdered = $item['quantityOrdered'];
        
        // convert if there are no orders and value  = Null
        if(!$quantityOrdered > 0){
            $quantityOrdered = 0;
        }
        
        $sqlUpdate = "Update tl_sortlyTemplatesIVM SET quantityOrdered = $quantityOrdered where sortlyId LIKE '$sortlyId'";
        if($db->query($sqlUpdate)){
           $msg.= "Field in table 'tl_sortlyTemplatesIVM.quantityOrdered' updated successfully for sortlyID: $sortlyId with a quantity ordered of <b>$quantityOrdered pc.</b><br>"; 
        }else{
           $msg.= "Error updating record: " . $db->error . "<br>"; 
        }
    }
    return $msg;
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
        sortly.active = 1
    Group By
        tl_sortlyTemplatesIVM.name
    ";
    //        sortly_ktc.name Not Like 'Archive T11' And
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        $model = $item['model'];
        $note = $item['note'];  
        $quantity = $item['quantity'];
        $id = $item['id'];
        
            // Update table
            $sql_1 = "UPDATE tl_sortlyTemplatesIVM SET quantityAvailable = $quantity WHERE id = $id";
            $result_1 = $db->query($sql_1);
            
        $msg.= "$model (ID $id) updated with quantity $quantity <br/>";
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
        sortly.active = 0 And
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
        
//         overwrite  Helix Master (5 to 17) and Slave (2 to 16) to assign Facelift ids
        if($id == 5){$id = 17;}
        if($id == 2){$id = 16;}
        
            // Update table
            $sql_1 = "UPDATE tl_sortlyTemplatesIVM SET quantityOverhaul = $quantity WHERE id = $id";
            $result_1 = $db->query($sql_1);
            
        $msg.= "$model (ID $id) updated with quantity $quantity <br/>";
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


//function changeBoards(){
//    
//    $sql ="Select
//            tl_toolcenterProjects.id As projectId,
//            tl_sortlyTemplatesIVM.name As modelName,
//            tl_toolcenterProjects.ktcId As ktc,
//            tl_sortlyTemplatesIVM.id As modelID,
//            tl_toolcenterProjectComponents.serial,
//            tl_toolcenterProjectComponents.`usage`
//        From
//            tl_toolcenterProjectCategory Inner Join
//            tl_toolcenterProjects On tl_toolcenterProjectCategory.id = tl_toolcenterProjects.projectCategory Inner Join
//            tl_toolcenterProjectStatus On tl_toolcenterProjectStatus.id = tl_toolcenterProjects.projectStatus Inner Join
//            tl_toolcenterProjectComponents On tl_toolcenterProjects.id = tl_toolcenterProjectComponents.pid Inner Join
//            tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.id = tl_toolcenterProjectComponents.componentModel
//        Where
//            tl_toolcenterProjectStatus.status Like 'planned' And
//            tl_toolcenterProjectComponents.`usage` Like 'remove' And
//            tl_sortlyTemplatesIVM.name Like 'KTC-HX/S' And
//            tl_toolcenterProjects.ktcId Like 'KTC-3%'
//        Order By
//            projectId,
//            ktc";
//            $result = $db->query($sql);
//    
//    while($item = $result->fetch_assoc()){ 
//        $sql_1 = "UPDATE tl_toolcenterProjectComponents SET componentModel = $quantity WHERE id = $id";
//        if ($result_1 = $db->query($sql_1)){
//            echo "Updated quantity column in table 'tl_sortlyTemplatesIVM' for id $id<br>"; 
//        }
//    }    
//}