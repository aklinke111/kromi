<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/pre/db/dbConfig.php";

if (isset($_GET['webhookFunction'])) {

    $function = $_GET['webhookFunction'];
    
    if($function == "sortlyToMySQL"){
        updateSortly($db);
    }
}

function updateSortly($db){
    
$startPid = "58670979";
$oldSid = "";
$i=0;
$data = dataFromSortlyAPI();

// truncate sortly tables from MySQL
truncateSortlyTables($db);

//insert data in table "sortly"
insertSortlyItems($data,$db);

// fill templates table for IVMs
//sortlyTemplatesIVM($db);

//fill sortly nested list tables
recursiveParentsAndChilds($db,$startPid,$oldSid,$i);     
}
        

function truncateSortlyTables($db){
             
    $sql = "TRUNCATE TABLE sortly_country";
    $result = $db->query($sql)."<br<";

    $sql = "TRUNCATE TABLE sortly_subsidiary";
    $result = $db->query($sql)."<br<";

    $sql = "TRUNCATE TABLE sortly_customer";
    $result = $db->query($sql)."<br<";

    $sql = "TRUNCATE TABLE sortly_ktc";
    $result = $db->query($sql)."<br<";

    $sql = "TRUNCATE TABLE sortly";
    $result = $db->query($sql)."<br<";
    
//    $sql = "TRUNCATE TABLE tl_sortlyTemplatesIVM";
//    $result = $db->query($sql)."<br<";
    
}


function dataFromSortlyAPI(){
   //Require data from Sortly API
   $url = 'https://api.sortly.co/api/v1/items?per_page=100000&page=1&include=custom_attributes%2Cphotos%2Coptions';
   return $data = getSortlyJSON($url);
}
    
// sortly items and folders into MySQL sortly
 function insertSortlyItems($data,$db)
{
    $z=1;
    $msg = ""; // meassage
    foreach ($data['data'] as $item) 
    {
        $sid = $item['id'];
        $name = $item['name'];
        $price = floatval($item['price']);
        $min_quantity = intval($item['min_quantity']);
        $quantity = intval($item['quantity']);
        $notes = $item['notes'];            
        $pid = $item['parent_id'];           
        $sortlyId = $item['sid'];
        $type = $item['type']; 

        // custom attributes - receive from Sortly API by https://api.sortly.co/api/v1/custom_fields?per_page=100&page=1
        $supplierArticleNo = ""; // 286283
        $supplier = ""; // 286300       
        $kromiArticleNo = ""; // 286415
        $storageLocation = ""; // 286416
        $packageUnit = ""; // 286667     
        $discontinued = ""; // 286785
        $ean = ""; // 287616       
        $inventoryNo = "";  // 287983
        $technicalSpecification = ""; //291474
        $CEdeclaration = ""; // 293437
        $reserved = ""; // 316079       
        $DGUV3 = ""; // 316329       
        $active = ""; // 317639
        $built = "";  // 320033 
        $overhaul= ""; // 320034
        $available = ""; //320035      
        $IVM = ""; // 322290   
        $fieldbus = ""; // 322300
        $serialNo = ""; // 322311
         $DGUV3No = ""; // 322428
        $criticalSourcing = ""; // 322429    
        $raw = ""; // 322605        
        $partgroup = ""; // 327551
        $EOL = ""; // 328273
        $MTTF = ""; // 328274
        $SortlyLabel = ""; // 328275       


        // Photos
        $photoName = "";
        $photoUrl = "";
        $tagNames = "";

        $msg.=  '<b>selfId: '.$sid.'  /  name: '.$name.'</b>'
                .'<br/> price: '.$price
                .'<br/> min_quantity: '.$min_quantity
                .'<br/> quantity: '.$quantity
                .'<br/> notes: '.$notes
                .'<br/> pid: '.$pid
                .'<br/> sid: '.$sid
                .'<br/> sortlyId: '.$sortlyId;
        $msg.= "<br/><b>Custom fields</b><br/>";

        //$custom_attribute_values = array();
        $custom_attribute_values = $item['custom_attribute_values'];

        // go through sub-array custom_attribute_values 
        foreach ($custom_attribute_values as $attribute) 
        {
            // extract custom_attribute_name (e.g. 'packageUnit'
            $custom_attribute_name = $attribute['custom_attribute_name'];

            $msg.= $custom_attribute_name.': ';

            // Check all attribute name and extract values
            switch ($custom_attribute_name) {
                case "supplierArticleNo":
                        $supplierArticleNo = $attribute['value'];
                    $msg.=' '.$supplierArticleNo;
                  break;
                case "supplier":
                        $supplier = $attribute['value'];
                    $msg.=' '.$supplier;
                  break;  
                case "KromiArticleNo":
                        $kromiArticleNo = $attribute['value'];
                    $msg.=' '.$kromiArticleNo;
                  break;   
                case "storageLocation":
                        $storageLocation = $attribute['value'];
                    $msg.=' '.$storageLocation;
                  break;              
                case "packageUnit":
                        $packageUnit = $attribute['value'];
                    $msg.=' '.$packageUnit;
                  break;  
                case "discontinued":
                        $discontinued = $attribute['value'];
                    $msg.=' '.$discontinued;
                  break;              
                case "ean":
                        $ean = $attribute['value'];
                    $msg.=' '.$ean;
                  break;
                case "inventoryNo":
                        $inventoryNo = $attribute['value'];
                    $msg.=' '.$inventoryNo;
                  break;
                case "technicalSpecification":
                        $technicalSpecification = $attribute['value'];
                    $msg.=' '.$technicalSpecification;
                  break;  
                case "CE declaration":
                        $CEdeclaration = $attribute['value'];
                    $msg.=' '.$CEdeclaration;
                  break; 
                case "reserved":
                        $reserved = $attribute['value'];
                    $msg.=' '.$reserved;
                  break; 
              case "DGUV3":
                        $DGUV3 = $attribute['value'];
                    $msg.=' '.$DGUV3;
                  break;                
                case "active":
                        $active= intval($attribute['value']);
                    $msg.=' '.$active;
                  break; 
                case "built":
                        $built = $attribute['value'];
                    $msg.=' '.$built;
                  break;  
                case "overhaul":
                        $overhaul = $attribute['value'];
                    $msg.=' '.$overhaul;
                  break;  
                case "available":
                        $available= $attribute['value'];
                    $msg.=' '.$available;
                  break;   
                case "IVM":
                        $IVM = $attribute['value'];
                    $msg.=' '.$IVM;
                  break;  
                case "fieldbus":
                        $fieldbus= $attribute['value'];
                    $msg.=' '.$fieldbus;
                  break;               
                  case "serialNo":
                        $serialNo = $attribute['value'];
                    $msg.=' '.$serialNo;
                  break;
                case "DGUV3No":
                        $DGUV3No = $attribute['value'];
                    $msg.=' '.$DGUV3No;
                  break;  
                case "criticalSourcing":
                        $criticalSourcing = $attribute['value'];
                    $msg.=' '.$criticalSourcing;
                  break;
                case "raw":
                        $raw= $attribute['value'];
                    $msg.=' '.$raw;
                  break; 
                  case "partgroup":
                        $partgroup = $attribute['value'];
                    $msg.=' '.$partgroup;
                  break;
                case "EOL":
                        $EOL = $attribute['value'];
                    $msg.=' '.$EOL;
                  break;  
                case "MTTF":
                        $MTTF = $attribute['value'];
                    $msg.=' '.$MTTF;
                  break;
                case "SortlyLabel":
                        $SortlyLabel= $attribute['value'];
                    $msg.=' '.$SortlyLabel;
                  break;               

                default:
            }  
            $msg.= '<br/>';
        }

        // go through sub-array photos
        $msg.= "<br/><b>Photos</b><br/>";
        $photos = $item['photos'];

        foreach ($photos as $photo) 
        {
            // extract url and name
            $photoName = $photo['name'];
            $msg.= 'Photo name: ';
            $msg.=' '.$photoName;
            $msg.= '<br/>';

            $photoUrl = $photo['url'];
            $msg.= 'Photo URL: ';
            $msg.=' '.$photoUrl;
            $msg.= '<br/>';

            break; // Exit after first photo
        }

        // go through sub-array tags
        $msg.= "<br/><b>Tags</b><br/>";
        $tags = $item['tags'];

        foreach ($tags as $tag) 
        {
            // combine tags to one tag string with seperator #
            $tagNames .= $tag['name'].";";
        }
        $msg.= 'Tag names: ';
        $msg.=' '.$tagNames;
        $msg.= '<br/>';


        $msg.= '----------------------------------------------------------------------------------------------------<p/>';

        // Prepare the SQL statement
        $sql = "INSERT INTO sortly(
                    tstamp,
                    sid,
                    pid,
                    sortlyId,
                    name,
                    price,
                    min_quantity,
                    quantity,
                    notes,
                    type,

                    supplierArticleNo,
                    supplier,  
                    kromiArticleNo,
                    storageLocation,
                    packageUnit,   
                    discontinued,
                    ean,
                    inventoryNo,
                    technicalSpecification,
                    CEdeclaration,
                    reserved,
                    DGUV3,  
                    active,
                    built,
                    overhaul,
                    available,   
                    IVM, 
                    fieldbus,
                    serialNo,
                    DGUV3No,
                    criticalSourcing,  
                    raw,      
                    partgroup,
                    EOL,
                    MTTF,
                    sortlyLabel, 

                    photoName,
                    photoUrl,
                    tags
                    ) 
                VALUES
                    (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?, ?, ?, ?
                    )";

        $stmt = $db->prepare($sql);
        $parameterTypes = "issssdiisssssssissssisissiisssiissiisss";
        $stmt->bind_param($parameterTypes,

            time(),
            $sid,
            $pid,
            $sortlyId,                        
            $name,
            $price,
            $min_quantity,
            $quantity,
            $notes,
            $type,

            $supplierArticleNo,
            $supplier,  
            $kromiArticleNo,
            $storageLocation,
            $packageUnit,   
            $discontinued,
            $ean,
            $inventoryNo,
            $technicalSpecification,
            $CEdeclaration,
            $reserved,
            $DGUV3,  
            $active,
            $built,
            $overhaul,
            $available,   
            $IVM, 
            $fieldbus,
            $serialNo,
             $DGUV3No,
            $criticalSourcing,  
            $raw,      
            $partgroup,
            $EOL,
            $MTTF,
            $SortlyLabel, 
                
            $photoName,
            $photoUrl,
            $tagNames 
        );

        // Execute the statement
        if ($stmt->execute()) {
//            echo "New record created successfully";
        } else {
//            echo "Error: " . $stmt->error;
        }
        $z +=1;
    }
    
    // Close the statement and connection
    $stmt->close();
    echo "<b class='red'>".$z." datasets inserted in sortly </b><p>";
    echo $msg;
//    die();
 }
 

function recursiveParentsAndChilds($db,$startPid,$oldSid,$i){

$allContent = "";
$content = "";
$sql = "SELECT DISTINCT sid,pid,name,type,tags,notes,active FROM `sortly` WHERE pid = '$startPid' AND type LIKE 'folder' AND sid NOT IN ('70455490')";
$sql."<br>";
$result = $db->query($sql);

if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){

        // only initializing workaround
                if($oldSid == ""){
                 echo "### initializing no: ".$i." ###<br>";
                 $oldSid = $startPid;
                 echo "no id, now ".$oldSid." as oldID<p>";
            }

            $sid = $row['sid'];
            $pid = $row['pid'];
            $name = $row['name'];
            $type = $row['type'];
            $tags = $row['tags'];
            $notes = $row['notes'];
            $active = $row['active'];  


            if($row['pid'] == $oldSid){
                $i++;
                echo "### match no: ".$i." ###<br>";

            }else{
                echo "### missmatch no: ".$i." ###<br>";
            }
            //Filling tables depending on level
                                
            switch ($i) {                
                case 0:
                    break;
                case 1: //country
                        if(strpos($name, '[') === false){
                        $sql = "INSERT INTO sortly_country (tstamp,sid,pid,name,type,tags,notes,active) 
                                VALUES ('".time()."','$sid','$pid','$name','$type','$tags','$notes','$active')";
                        $db->query($sql);
//                        echo $sql = "UPDATE sortly SET subfolderLevel1 = '".$name."' WHERE sid LIKE ".$sid;
//                        $db->query($sql);

                        
                    }
                    break;
                case 2: //subsidiary
                    if($pid <> '70900463' AND strpos($name, '[') === false){
                        $sql = "INSERT INTO sortly_subsidiary (tstamp,sid,pid,name,type,tags,notes,active) 
                                VALUES ('".time()."','$sid','$pid','$name','$type','$tags','$notes','$active')";
                        $db->query($sql);
//                        $sql = "UPDATE sortly SET subfolderLevel2 = '".$name."' WHERE sid LIKE ".$sid;
//                        $db->query($sql);
                    }
                    break;
                case 3: //customer
                    if($pid <> '70900630' AND $sid <> '70900631' AND strpos($name, '[') === false){
//                            if(strpos($name, '[') === false){                                
                        $sql = "INSERT INTO sortly_customer (tstamp,sid,pid,name,type,tags,notes,active) 
                                VALUES ('".time()."','$sid','$pid','$name','$type','$tags','$notes','$active')";
                        $db->query($sql);
//                        $sql = "UPDATE sortly SET subfolderLevel3 = '".$name."' WHERE sid LIKE ".$sid;
//                        $db->query($sql);
                    }
                    break;
                case 4: //KTC
                        if(strpos($name, '[') === false){
                        $sql = "INSERT INTO sortly_ktc (tstamp,sid,pid,name,type,tags,notes,active) 
                                VALUES ('".time()."','$sid','$pid','$name','$type','$tags','$notes','$active')";
                        $db->query($sql);
//                        $sql = "UPDATE sortly SET subfolderLevel4 = '".$name."' WHERE sid LIKE ".$sid;
//                        $db->query($sql);
                    }
                    break;   
                default:
            }

          echo "Name: ".$row['name']."<br>";
          echo "pid: ".$row['pid']."<br>";
          echo "sid: ".$row['sid']."<br>";             
          echo "Old Id (must match parentId when looping further: ".$oldSid."<p>";

        $startPid = $row['sid'];  
        $oldSid = $row['sid'];  
        $content = recursiveParentsAndChilds($db,$startPid,$oldSid,$i);
    }
}
return $allContent;  
}



//request data from sortly API
function getSortlyJSON($url) {
 // Bearer token for authentication
 $token = 'sk_sortly_oCDxewcXoQSyWNxNohQ_';

 // Set up HTTP headers with Authorization header containing the bearer token
 $options = [
     'http' => [
         'header' => "Authorization: Bearer $token\r\n"
     ]
 ];

 // Create a stream context
 $context = stream_context_create($options);

 // Use file_get_contents with the created context to fetch data from the URL
 $response = file_get_contents($url, false, $context);
//        CHECK: echo $response;

 // Decode and return JSON
 $data = json_decode($response, true);
 return $data;
}


//Templates IVMs from Sortly to MySQL table
function sortlyTemplatesIVM($db){
    
    echo $sql = "INSERT INTO tl_sortlyTemplatesIVM
            (
            tstamp,
            sid,
            pid,
            sortlyId,
            name,
            quantity,
            min_quantity,
            note,
            photoName,
            photoUrl,
            created
            ) 
            
            SELECT
            
            tstamp,
            sid,
            pid,
            sortlyId,
            name,
            quantity,
            min_quantity,
            notes,
            photoName,
            photoUrl,
            NOW()
            
            FROM sortly WHERE pid LIKE '72430051'";

    // Execute the query
    $result = $db->query($sql);
    
    $msg = "";
    if ($result) {
        //Count inserted datasets
        $sql = "SELECT sid FROM tl_sortlyTemplatesIVM WHERE created LIKE NOW()";
        $count = $db->query($sql)->num_rows;
        $msg.= "$count Records inserted successfully in table 'tl_sortlyTemplatesIVM'";
    } else {
        $msg.= "Error: " . $sql . "<br>" . $db->error;
    }
    echo $msg.'<p>';
}