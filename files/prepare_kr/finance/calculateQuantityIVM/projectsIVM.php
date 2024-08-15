<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/_includes.php";
include_once '_includes.php';



function calculateQuantityIvmProjects($db, $removeInstall, $faceliftNew){
    
    $whereClause = "";

    switch ($faceliftNew) {
        case 'facelift':
            $title = "IVMs to facelift in planned projects";
            $exclude = 0; 
            $whereClause = " tl_toolcenterProjectStatus.status In ('planned') And
                            tl_toolcenterProjectComponents.`usage` Like 'install' And 
                            tl_toolcenterProjectCategory.category IN ('facelift') ";
        break;
        case 'new':
            $title = "IVMs to implement new in planned or pending projects";
            $exclude = 0; 
            $whereClause = " tl_toolcenterProjectStatus.status In ('planned', 'waiting for contract') And
                            tl_toolcenterProjectComponents.`usage` Like 'install' And 
                            tl_toolcenterProjectCategory.category IN ('implementation', 'change configuration') ";
        break;
        case 'all':
            $title = "IVMs to remove in planned projects";
            $exclude = 1; 
            $whereClause = " tl_toolcenterProjectStatus.status In ('planned') And
                            tl_toolcenterProjectComponents.`usage` Like 'remove' ";
        break;
    }
    
    $msg = "<b>$title:</b><p>";
    
// Calculating all devices from planned projects & components exclude Brazil--->  SQL in FlySQL  query 'projectComponents'    
    $sql = 
    "Select
        tl_sortlyTemplatesIVM.id As id,
        tl_sortlyTemplatesIVM.name As model,
        tl_sortlyTemplatesIVM.note,
        tl_region.id As regionId,
        tl_region.name As regionName,
        Count(tl_toolcenterProjects.ktcId) As quantity
    From
        tl_toolcenterProjectCategory Inner Join
        tl_toolcenterProjects On tl_toolcenterProjectCategory.id = tl_toolcenterProjects.projectCategory Inner Join
        tl_toolcenterProjectStatus On tl_toolcenterProjectStatus.id = tl_toolcenterProjects.projectStatus Inner Join
        tl_toolcenterProjectComponents On tl_toolcenterProjects.id = tl_toolcenterProjectComponents.pid Inner Join
        tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.id = tl_toolcenterProjectComponents.componentModel Inner Join
        sortly_country On tl_toolcenterProjects.countryId = sortly_country.id Inner Join
        tl_country2Region On sortly_country.id = tl_country2Region.countryId Inner Join
        tl_region On tl_region.id = tl_country2Region.regionId
    Where $whereClause
    Group By
        tl_sortlyTemplatesIVM.name,
        tl_region.id,
        tl_region.name
    Order By
        regionId,
        id
    ";
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        
        $id = $item['id'];
        $quantity = $item['quantity'];
        $regionId = $item['regionId'];
        $regionName = $item['regionName'];

        // overwrite  Helix Master (5 to 17) and Slave (2 to 16) to assign Facelift ids - DELETE after facelift measures!
        if($id == 5){$id = 17;}
        if($id == 2){$id = 16;}        
       
        $quantityName = "quantityProjects_".$removeInstall."_".$faceliftNew;
        $note= " $title in $regionName [$exclude]";

        $msg .= insertQuantity($db, $id, $quantityName, $quantity, $note, $exclude, $regionId);
        $msg .= "<br>";
        
    } 
    return $msg;
}
