    <style> 
        /* Define CSS styles for colored text */ 
        .red { 
            color: red; 
        } 
        .blue { 
            color: blue; 
        } 
        .green { 
            color: green; 
        } 
    </style> 
    
<?php
// Load the database configuration file

include_once '../db/dbConfig.php';

//echo $class = "../db/dbConfig.php";
//
//if (include_once($class) == TRUE) {
//    echo 'OK';
//}else{
//  echo 'Nö';   
//}



$startPid = "58670979";
$i=0;
//$data = dataFromSortlyAPI();

// truncate sortly tables from MySQL
truncateSortlyTables($db);

//insert data in table "tl_sortly"
insertSortlyItems(dataFromSortlyAPI(),$db);

//fill sortly nested list tables
recursiveParentsAndChilds($db,$startPid,$oldSid,$i);          

function check(){
    return "Testerarsch";
}

function truncateSortlyTables($db){
    
    $sql = "TRUNCATE TABLE tl_sortly_country";
    $result = $db->query($sql)."<br<";

    $sql = "TRUNCATE TABLE tl_sortly_subsidiary";
    $result = $db->query($sql)."<br<";

    $sql = "TRUNCATE TABLE tl_sortly_customer";
    $result = $db->query($sql)."<br<";

    $sql = "TRUNCATE TABLE tl_sortly_ktc";
    $result = $db->query($sql)."<br<";

    $sql = "TRUNCATE TABLE tl_sortly";
    $result = $db->query($sql)."<br<";
}


function dataFromSortlyAPI(){
   //Require data from Sortly API
   $url = 'https://api.sortly.co/api/v1/items?per_page=100000&page=1&include=custom_attributes%2Cphotos%2Coptions';
   return $data = getSortlyJSON($url);
}
    
// sortly items and folders into MySQL tl_sortly
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

        $packageUnit = "";
        $supplierArticleNo = "";
        $ean = "";
        $supplier = "";
        $serialNo = "";
        $inventoryNo = "";  
        $kromiArticleNo = "";
        $DGUV3No = "";
        $storageLocation = "";

        $technicalSpecification = "";
        $CEdeclaration = "";

        $DGUV3 = "";            
        $built = "";  
        $overhaul= "";

        $reserved = "";
        $discontinued = "";
        $active = "";
        $available = "";
        $IVM = "";
        $criticalSourcing = "";

        $fieldbus = ""; 
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
                case "packageUnit":
                        $packageUnit = $attribute['value'];
                    $msg.=' '.$packageUnit;
                  break;
                case "supplierArticleNo":
                        $supplierArticleNo = $attribute['value'];
                    $msg.=' '.$supplierArticleNo;
                  break;
                case "ean":
                        $ean = $attribute['value'];
                    $msg.=' '.$ean;
                  break;
                case "supplier":
                        $supplier = $attribute['value'];
                    $msg.=' '.$supplier;
                  break;
                case "serialNo":
                        $serialNo = $attribute['value'];
                    $msg.=' '.$serialNo;
                  break;
                case "inventoryNo":
                        $inventoryNo = $attribute['value'];
                    $msg.=' '.$inventoryNo;
                  break;
                case "KromiArticleNo":
                        $kromiArticleNo = $attribute['value'];
                    $msg.=' '.$kromiArticleNo;
                  break;
                case "DGUV3No":
                        $DGUV3No = $attribute['value'];
                    $msg.=' '.$DGUV3No;
                  break;  
                case "storageLocation":
                        $storageLocation = $attribute['value'];
                    $msg.=' '.$storageLocation;
                  break;


                case "technicalSpecification":
                        $technicalSpecification = $attribute['value'];
                    $msg.=' '.$technicalSpecification;
                  break;
                case "CE declaration":
                        $CEdeclaration = $attribute['value'];
                    $msg.=' '.$CEdeclaration;
                  break;

                case "DGUV3":
                        $DGUV3 = $attribute['value'];
                    $msg.=' '.$DGUV3;
                  break;  
                case "built":
                        $built = $attribute['value'];
                    $msg.=' '.$built;
                  break;
                case "overhaul":
                        $overhaul = $attribute['value'];
                    $msg.=' '.$overhaul;
                  break;


                case "reserved":
                        $reserved = $attribute['value'];
                    $msg.=' '.$reserved;
                  break;
                case "discontinued":
                        $discontinued = $attribute['value'];
                    $msg.=' '.$discontinued;
                  break;
                case "active":
                        $active= intval($attribute['value']);
                    $msg.=' '.$active;
                  break; 
                case "available":
                        $available= $attribute['value'];
                    $msg.=' '.$available;
                  break; 
                case "IVM":
                        $IVM = $attribute['value'];
                    $msg.=' '.$IVM;
                  break;
                case "criticalSourcing":
                        $criticalSourcing = $attribute['value'];
                    $msg.=' '.$criticalSourcing;
                  break;

                case "fieldbus":
                        $fieldbus= $attribute['value'];
                    $msg.=' '.$fieldbus;
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

        $sql = "INSERT INTO tl_sortly
                (
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

                packageUnit,
                supplierArticleNo,
                ean,
                supplier,
                serialNo,
                inventoryNo,
                kromiArticleNo,
                DGUV3No,
                storageLocation,

                technicalSpecification,
                CEdeclaration,

                DGUV3,
                built,
                overhaul,

                reserved,
                discontinued,
                active,
                available,
                IVM,
                criticalSourcing,

                fieldbus,

                photoName,
                photoUrl,
                tags
                ) 
                VALUES 
                (
                '".time()."',
                '$sid',
                '$pid',
                '$sortlyId',                        
                '$name',
                '$price',
                '$min_quantity',
                '$quantity',
                '$notes',
                '$type',

                '$packageUnit',
                '$supplierArticleNo',
                '$ean',
                '$supplier',
                '$serialNo',
                '$inventoryNo',
                '$kromiArticleNo',
                '$DGUV3No',
                '$storageLocation',

                '$technicalSpecification',
                '$CEdeclaration',

                '$DGUV3',   
                '$built',
                '$overhaul', 

                '$reserved',                        
                '$discontinued',
                '$active',
                '$available',
                '$IVM',  
                '$criticalSourcing',

                '$fieldbus', 

                '$photoName',
                '$photoUrl',
                '$tagNames' 
                )";

        $db->query($sql);
        $z +=1;
//        echo $sql_1."<br>";

    }
    echo "<b class='red'>".$z." datasets inserted in tl_sortly </b><p>";
    echo $msg;
//        die();
 }


function recursiveParentsAndChilds($db,$startPid,$oldSid,$i){
//        $i=1;
$allContent = "";
$content = "";
$sql = "SELECT DISTINCT sid, pid, name FROM `tl_sortly` WHERE pid = '$startPid' AND type LIKE 'folder' AND sid NOT IN ('70455490')";
echo $sql."<br>";
//        die();
$result = $db->query($sql);

if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){

        // only initializing workaround
                if($oldSid == ""){
                 echo "### initializing no: ".$i." ###<br>";
                 $oldSid = $startPid;
                 echo "no id, now ".$oldSid." as oldID<p>";
            }

            $sid = $row[sid];
            $pid = $row[pid];
            $name = $row[name];
            $customerNo = $row[customerNo];                    

            if($row[pid] == $oldSid){
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
                        $sql = "INSERT INTO tl_sortly_country (tstamp, sid, pid, name) VALUES ('".time()."','$sid','$pid','$name')";
                        $db->query($sql);
                    }
                    break;
                case 2: //subsidiary
                    if($pid <> '70900463' AND strpos($name, '[') === false){
                        $sql = "INSERT INTO tl_sortly_subsidiary (tstamp, sid, pid, name) VALUES ('".time()."','$sid','$pid','$name')";
//                                echo $sql."<br>";
                        $db->query($sql);
                    }
                    break;
                case 3: //customer
                    if($pid <> '70900630' AND $sid <> '70900631' AND strpos($name, '[') === false){
//                            if(strpos($name, '[') === false){                                
                        $sql = "INSERT INTO tl_sortly_customer (tstamp, sid, pid, customerNo, name) VALUES ('".time()."','$sid','$pid','$customerNo','$name')";
//                                echo $sql."<br>";
                        $db->query($sql);
                    }
                    break;
                case 4: //KTC
                        if(strpos($name, '[') === false){
                        $sql = "INSERT INTO tl_sortly_ktc (tstamp, sid, pid, name) VALUES ('".time()."','$sid','$pid','$name')";
//                                echo $sql."<br>";
                        $db->query($sql);
                    }
                    break;   
                default:
            }

          echo "Name: ".$row[name]."<br>";
          echo "pid: ".$row[pid]."<br>";
          echo "sid: ".$row[sid]."<br>";
          echo "CustomerNo: ".$row[customerNo]."<br>";                  
          echo "Old Id (must match parentId when looping further: ".$oldSid."<p>";

        $startPid = $row[sid];  
        $oldSid = $row[sid];  
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
