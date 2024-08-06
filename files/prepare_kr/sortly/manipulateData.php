<?php
// Load the database configuration file

//echo "arsch";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";



// main function for calculating quantites of IVM and update table tl_sortlyTemplatesIVM
if (isset($_GET['webhookFunction'])) {


    $function = $_GET['webhookFunction'];
    
    if($function == "manipulateData"){
//       echo insertSlavesAsBoards($db);
//       echo deleteBoards($db);
    }
}

function deleteBoards($db){
    
    $sql = "Select
        tl_toolcenterProjects.id As projectId,
        tl_sortlyTemplatesIVM.name As modelName,
        tl_toolcenterProjects.ktcId As ktc,
        tl_sortlyTemplatesIVM.id As modelID,
        tl_toolcenterProjectComponents.serial,
        tl_toolcenterProjectComponents.id
    From
        tl_toolcenterProjectCategory Inner Join
        tl_toolcenterProjects On tl_toolcenterProjectCategory.id = tl_toolcenterProjects.projectCategory Inner Join
        tl_toolcenterProjectStatus On tl_toolcenterProjectStatus.id = tl_toolcenterProjects.projectStatus Inner Join
        tl_toolcenterProjectComponents On tl_toolcenterProjects.id = tl_toolcenterProjectComponents.pid Inner Join
        tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.id = tl_toolcenterProjectComponents.componentModel
    Where
        tl_toolcenterProjectStatus.status Like 'planned' And
        tl_toolcenterProjectComponents.`usage` Like 'install' And
        tl_sortlyTemplatesIVM.name LIKE '%KTC-HX/S Electronic board' And
        tl_toolcenterProjects.ktcId Like 'KTC-3%'
    Order By
        projectId,
        ktc";
        $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        // Values
        $id = $item['id'];

        // Update table
        $sql_1 = "Delete from tl_toolcenterProjectComponents WHERE id = $id";
        $result_1 = $db->query($sql_1);

        if($result_1){
            $msg.= "Item id $id DELETED in 'tl_toolcenterProjectComponents'<b>";
        }
    } 
    return $msg;
    
}


function insertSlavesAsBoards($db){

    $msg = "";
// Calculating the Helix devices from planned projects & components --->  SQL in FlySQL  query 'projectComponents'    
    $sql = 
    "Select
        tl_toolcenterProjects.id As projectId,
        tl_sortlyTemplatesIVM.name As modelName,
        tl_toolcenterProjects.ktcId As ktc,
        tl_sortlyTemplatesIVM.id As modelID,
        tl_toolcenterProjectComponents.serial
    From
        tl_toolcenterProjectCategory Inner Join
        tl_toolcenterProjects On tl_toolcenterProjectCategory.id = tl_toolcenterProjects.projectCategory Inner Join
        tl_toolcenterProjectStatus On tl_toolcenterProjectStatus.id = tl_toolcenterProjects.projectStatus Inner Join
        tl_toolcenterProjectComponents On tl_toolcenterProjects.id = tl_toolcenterProjectComponents.pid Inner Join
        tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.id = tl_toolcenterProjectComponents.componentModel
    Where
        tl_toolcenterProjectStatus.status Like 'planned' And
        tl_toolcenterProjectComponents.`usage` Like 'remove' And
        tl_sortlyTemplatesIVM.name Like '%HX/S' And
        tl_toolcenterProjects.ktcId Like 'KTC-3%'
    Order By
        projectId,
        ktc
    ";
    
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        // Values
        $pid = $item['projectId'];
        $ktc = $item['ktc'];
        $modelID = $item['modelID'];
        $modelName = $item['modelName'];
        $usage = "install";
        $stamp = time();

        //temporarily overwrite from Helix Slave (id =2) to electronic board (id 0 18) for brazilian KTCs
            if($modelID == 2){
               $modelID = 18; 
            }
            // Update table
            $sql_1 = "INSERT INTO tl_toolcenterProjectComponents 
                      (pid, tstamp, componentModel, `usage`)
                      VALUES
                      ($pid, $stamp , $modelID, '$usage')";
            $result_1 = $db->query($sql_1);

            if($result_1){
                $msg.=    "Item id ".$modelID." (".$modelName.") INSERTED in 'tl_toolcenterProjectComponents' in Project  '".$projectId." for ".$ktc."<p>";
                }
    } 
    return $msg;
}




//function calculateQuantityIVM($db){
//
//    $msg = "";
//// Calculating all devices from planned projects & components excluded Brazil--->  SQL in FlySQL  query 'projectComponents'    
//    $sql = 
//    "Select
//        tl_sortlyTemplatesIVM.name As model,
//        Count(tl_toolcenterProjects.ktcId) As quantity,
//        tl_sortlyTemplatesIVM.id As id
//    From
//        tl_toolcenterProjectCategory Inner Join
//        tl_toolcenterProjects On tl_toolcenterProjectCategory.id = tl_toolcenterProjects.projectCategory Inner Join
//        tl_toolcenterProjectStatus On tl_toolcenterProjectStatus.id = tl_toolcenterProjects.projectStatus Inner Join
//        tl_toolcenterProjectComponents On tl_toolcenterProjects.id = tl_toolcenterProjectComponents.pid Inner Join
//        tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.id = tl_toolcenterProjectComponents.componentModel
//    Where
//        tl_toolcenterProjectStatus.status Like 'planned' And
//        tl_toolcenterProjectComponents.`usage` Like 'remove' And
//        tl_toolcenterProjects.ktcId NOT LIKE'KTC-3%'
//    Group By
//        tl_sortlyTemplatesIVM.name
//    ";
//    
//    $result = $db->query($sql);
//    while($item = $result->fetch_assoc()){ 
//        $model = $item['model'];
//        $quantity = $item['quantity'];
//        $id = $item['id'];
//        
//        // overwrite  Helix Master (5 to 17) and Slave (2 to 16) to assign Facelift ids
//        if($id == 5){$id = 17;}
//        if($id == 2){$id = 16;}
//        
//            // Update table
//            $sql_1 = "UPDATE tl_sortlyTemplatesIVM SET quantityProjects = $quantity WHERE id = $id";
//            $result_1 = $db->query($sql_1);
//            
//        $msg.=    "Item id ".$id." (".$model.") updated with quantity ".$quantity."<br/>";
//    } 
//    return $msg;
//}
//
//function calculateQuantityIVMonStock($db){
//
//    $msg = "";
//// Calculating all devices on stock   
//    $sql = 
//    "Select
//        tl_sortlyTemplatesIVM.name As model,
//        Count(sortly.IVM) As quantity,
//        tl_sortlyTemplatesIVM.id As id
//    From
//        sortly Inner Join
//        sortly_ktc On sortly.pid = sortly_ktc.sid Inner Join
//        tl_sortlyTemplatesIVM On sortly.name = tl_sortlyTemplatesIVM.name
//    Where
//        sortly.IVM = 1 And
//        sortly_ktc.name Not Like 'SCRAP' And
//        sortly_ktc.name Not Like 'KTC-%' And
//        sortly.active = 1
//    Group By
//        tl_sortlyTemplatesIVM.name
//    ";
//    //        sortly_ktc.name Not Like 'Archive T11' And
//    $result = $db->query($sql);
//    while($item = $result->fetch_assoc()){ 
//        $model = $item['model'];
//        $quantity = $item['quantity'];
//        $id = $item['id'];
//        
//            // Update table
//            $sql_1 = "UPDATE tl_sortlyTemplatesIVM SET quantityStock = $quantity WHERE id = $id";
//            $result_1 = $db->query($sql_1);
//            
//        $msg.=    "Item id ".$id." (".$model.") updated with quantity ".$quantity."<br/>";
//    } 
//    return $msg;
//}
//
//
//function calculateInaktiveIVMonStock($db){
//
//    $msg = "";
//// Calculating all devices on stock   
//    $sql = 
//    "Select
//        tl_sortlyTemplatesIVM.name As model,
//        Count(sortly.IVM) As quantity,
//        tl_sortlyTemplatesIVM.id As id
//    From
//        sortly Inner Join
//        sortly_ktc On sortly.pid = sortly_ktc.sid Inner Join
//        tl_sortlyTemplatesIVM On sortly.name = tl_sortlyTemplatesIVM.name
//    Where
//        sortly.IVM = 1 And
//        sortly_ktc.name Not Like 'SCRAP' And
//        sortly_ktc.name Not Like 'KTC-%' And
//        sortly.active = 0
//    Group By
//        tl_sortlyTemplatesIVM.name
//    ";
//    
//    $result = $db->query($sql);
//    while($item = $result->fetch_assoc()){ 
//        $model = $item['model'];
//        $quantity = $item['quantity'];
//        $id = $item['id'];
//        
////         overwrite  Helix Master (5 to 17) and Slave (2 to 16) to assign Facelift ids
//        if($id == 5){$id = 17;}
//        if($id == 2){$id = 16;}
//        
//            // Update table
//            $sql_1 = "UPDATE tl_sortlyTemplatesIVM SET quantityStock = $quantity WHERE id = $id";
//            $result_1 = $db->query($sql_1);
//            
//        $msg.=    "Item id ".$id." (".$model.") updated with quantity ".$quantity."<br/>";
//    } 
//    return $msg;
//}