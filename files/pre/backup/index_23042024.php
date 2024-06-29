<?php
// Load the database configuration file
include_once 'dbConfig.php';

$startParent_Id = "58670979";
$i=0;
$sql = "TRUNCATE TABLE tl_sortlyCountry";
echo $result = $db->query($sql);

$sql = "TRUNCATE TABLE tl_sortlySubsidiary";
echo $result = $db->query($sql);

$sql = "TRUNCATE TABLE tl_sortlyCustomer";
echo $result = $db->query($sql);

$sql = "TRUNCATE TABLE tl_sortlyKtc";
echo $result = $db->query($sql);

recursiveParentsAndChilds($db,$startParent_Id,$oldSid,$i);

   $url = 'https://api.sortly.co/api/v1/items?per_page=100000&page=1&include=custom_attributes%2Cphotos%2Coptions';
//   $data = getSortlyJSON($url);

     // insert data in table "tl_sortly"
//    insertSortlyItems($data);

    // call some more routines
//    fillSortlyTables();               
    
                            
    function recursiveParentsAndChilds($db,$startParent_Id,$oldSid,$i)
    {
//        $i=1;
        $allContent = "";
        $content = "";
        $sql = "SELECT self_id, parent_id, name FROM `tl_sortly` WHERE parent_id = '$startParent_Id' AND type LIKE 'folder' AND self_id NOT IN ('70455490')";
//        echo $sql."<br>";
        $result = $db->query($sql);

       if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){

                // only initializing workaround
                        if($oldSid == ""){
                         echo "### initializing no: ".$i." ###<br>";
                         $oldSid = $startParent_Id;
                         echo "no id, now ".$oldSid." as oldID<p>";
                    }
                   
                    $self_id = $row[self_id];
                    $parent_id = $row[parent_id];
                    $name = $row[name];
                    
                //$allContent.= $content."<br>"."<<".$i.">>".$row[name].' --- '.$row[parent_id].' --- '.$row[self_id];

                    if($row[parent_id] == $oldSid){
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
                                $sql = "INSERT INTO tl_sortlyCountry (tstamp, self_id, toplevel_id, name) VALUES ('".time()."','$self_id','$parent_id','$name')";
                                $db->query($sql);
                            }
                            break;
                        case 2: //subsidiary
                            if($parent_id <> '70900463' AND strpos($name, '[') === false){
                                $sql = "INSERT INTO tl_sortlySubsidiary (tstamp, self_id, country_id, name) VALUES ('".time()."','$self_id','$parent_id','$name')";
//                                echo $sql."<br>";
                                $db->query($sql);
                            }
                            break;
                        case 3: //customer
                            if($parent_id <> '70900630' AND $parent_id <> '70900631' AND strpos($name, '[') === false){
                                $sql = "INSERT INTO tl_sortlyCustomer (tstamp, self_id, subsidiary_id, name) VALUES ('".time()."','$self_id','$parent_id','$name')";
//                                echo $sql."<br>";
                                $db->query($sql);
                            }
                            break;
                        case 4: //KTC
                                if(strpos($name, '[') === false){
                                $sql = "INSERT INTO tl_sortlyKtc (tstamp, self_id, customer_id, name) VALUES ('".time()."','$self_id','$parent_id','$name')";
//                                echo $sql."<br>";
                                $db->query($sql);
                            }
                            break;   
                        default:
                    }
                    
                  echo "Name: ".$row[name]."<br>";
                  echo "ParentId: ".$row[parent_id]."<br>";
                  echo "SelfId: ".$row[self_id]."<br>";
                  echo "Old Id (must match parentId when looping further: ".$oldSid."<p>";
                  
                $startParent_Id = $row[self_id];  
                $oldSid = $row[self_id];  
                $content = recursiveParentsAndChilds($db,$startParent_Id,$oldSid,$i);
            }
        }
        return $allContent;  
    }


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
    
    
     function insertSortlyItems($data)
    {
        
        $msg = ""; // meassage
        foreach ($data['data'] as $item) 
        {
            $self_Id = $item['id'];
            $name = $item['name'];
            $price = $item['price'];
            $min_quantity = $item['min_quantity'];
            $quantity = $item['quantity'];
            $notes = $item['notes'];            
            $parent_id = $item['parent_id'];           
            $sid = $item['sid'];
            $type = $item['type'];            
            $packageUnit = "";
            $supplierArticleNo = "";
            $ean = "";
            $supplier = "";
            $serialNo = "";
            $discontinued = "";
            $kromiArticleNo = "";
            $productionstate = "";
            $storageLocation = "";
            $inventoryNo = "";
            $technicalSpecification = "";
            $reserved = "";
            $yearofmanufacture = "";
            $lastDGUV3check = "";            
            $CEdeclaration = "";
            $lastbuilt = "";     
            $Ordered = "";
            $fieldbus = ""; 
            $active = "";
            $customerNo = "";
            
            $msg.=  '<b>selfId: '.$self_Id.'  /  name: '.$name.'</b>'
                    .'<br/> price: '.$price
                    .'<br/> min_quantity: '.$min_quantity
                    .'<br/> quantity: '.$quantity
                    .'<br/> notes: '.$notes
                    .'<br/> parent_id: '.$parent_id
                    .'<br/> sid: '.$sid;
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
                    case "discontinued":
                            $discontinued = $attribute['value'];
                        $msg.=' '.$discontinued;
                      break;
                       case "KromiArticleNo":
                            $kromiArticleNo = $attribute['value'];
                        $msg.=' '.$kromiArticleNo;
                      break;
                    case "production state":
                            $productionstate = $attribute['value'];
                        $msg.=' '.$productionstate;
                      break;
                    case "storageLocation":
                            $storageLocation = $attribute['value'];
                        $msg.=' '.$storageLocation;
                      break;
                    case "storageLocation":
                            $storageLocation = $attribute['value'];
                        $msg.=' '.$storageLocation;
                      break;
                    case "inventoryNo":
                            $inventoryNo = $attribute['value'];
                        $msg.=' '.$inventoryNo;
                      break;
                    case "technicalSpecification":
                            $technicalSpecification = $attribute['value'];
                        $msg.=' '.$technicalSpecification;
                      break;
                    case "reserved":
                            $reserved = $attribute['value'];
                        $msg.=' '.$reserved;
                      break;
                    case "year of manufacture":
                            $yearofmanufacture = $attribute['value'];
                        $msg.=' '.$yearofmanufacture;
                      break;
                    case "last DGUV3 check":
                            $lastDGUV3check = $attribute['value'];
                        $msg.=' '.$lastDGUV3check;
                      break;  
                    case "CE declaration":
                            $CEdeclaration = $attribute['value'];
                        $msg.=' '.$CEdeclaration;
                      break;
                    case "last built":
                            $lastbuilt = $attribute['value'];
                        $msg.=' '.$lastbuilt;
                      break;
                    case "Ordered":
                            $Ordered = $attribute['value'];
                        $msg.=' '.$Ordered;
                      break;
                    case "fieldbus":
                            $fieldbus= $attribute['value'];
                        $msg.=' '.$fieldbus;
                      break; 
                    case "active":
                            $active= $attribute['value'];
                        $msg.=' '.$active;
                      break; 
                    case "customerNo":
                            $customerNo= $attribute['value'];
                        $msg.=' '.$customerNo;
                      break; 
                    default:
                }  
                $msg.= '<br/>';
            }
            
            $msg.= "<br/><b>Photos</b><br/>";
                        
            //$photos = array();
            $photos = $item['photos'];
                    
            // go through sub-array custom_attribute_values 
            foreach ($photos as $photo) 
            {
                // extract custom_attribute_name (e.g. 'packageUnit'
                $photoUrl = $photo['url'];
                $msg.= 'Photo URL: ';
                $msg.=' '.$photoUrl;
                $msg.= '<br/>';
                break; // Exit after first photo
            }
            
            $msg.= '----------------------------------------------------------------------------------------------------<p/>';
        }
        echo $msg;
        die();
     }
     
     
     
        
        

//    function recursiveParentsAndChilds($db, $startParent_Id)
//    {
//        $sql = "SELECT self_id, parent_id, name FROM `tl_sortly` WHERE parent_id = '$startParent_Id' AND self_id NOT LIKE '*70455490*'";
//        $result = $db->query($sql);
//       if($result->num_rows > 0){
//            while($row = $result->fetch_assoc()){
//                $startParent_Id = $result->self_id;
//                $content = $this->recursiveParentsAndChilds($startParent_Id);
//                $content.= "<br>".$content.$result->parent_id.' --- '.$result->self_id.' --- '.$result->name; 
//            }
//        }
//        
//        return $content;
//
//    }
//    
//    
//    function recursiveParentsAndChilds($db, $startParent_Id)
//    {
//        $sql = "SELECT self_id, parent_id, name FROM `tl_sortly` WHERE parent_id = '$startParent_Id' AND self_id NOT LIKE '*70455490*'";
//        $result = $db->query($sql);
//       if($result->num_rows > 0){
//            while($row = $result->fetch_assoc()){
//                $startParent_Id = $result->self_id;
//                $content = $this->recursiveParentsAndChilds($startParent_Id);
//                $content.= "<br>".$content.$result->parent_id.' --- '.$result->self_id.' --- '.$result->name; 
//            }
//        }
//        
//        return $content;
//
//    }
     
     
     
//    function recursiveParentsAndChilds($db,$startParent_Id,$oldSid,$i)
//    {
////        $i=1;
//        $allContent = "";
//        $content = "";
//        $sql = "SELECT self_id, parent_id, customerNo, name FROM `tl_sortly` WHERE parent_id = '$startParent_Id' AND type LIKE 'folder' AND self_id NOT IN ('70455490')";
////        echo $sql."<br>";
//        $result = $db->query($sql);
//
//       if($result->num_rows > 0){
//            while($row = $result->fetch_assoc()){
//
//                // only initializing workaround
//                        if($oldSid == ""){
//                         echo "### initializing no: ".$i." ###<br>";
//                         $oldSid = $startParent_Id;
//                         echo "no id, now ".$oldSid." as oldID<p>";
//                    }
//                   
//                    $self_id = $row[self_id];
//                    $parent_id = $row[parent_id];
//                    $name = $row[name];
//                    $customerNo = $row[customerNo];                    
//                //$allContent.= $content."<br>"."<<".$i.">>".$row[name].' --- '.$row[parent_id].' --- '.$row[self_id];
//
//                    if($row[parent_id] == $oldSid){
//                        $i++;
//                        echo "### match no: ".$i." ###<br>";
//                        
//                    }else{
//                        echo "### missmatch no: ".$i." ###<br>";
//                    }
//                    //Filling tables depending on level
//                    switch ($i) {
//                        case 0:
//                            break;
//                        case 1: //country
//                                if(strpos($name, '[') === false){
//                                $sql = "INSERT INTO tl_sortlyCountry (tstamp, self_id, toplevel_id, name) VALUES ('".time()."','$self_id','$parent_id','$name')";
//                                $db->query($sql);
//                            }
//                            break;
//                        case 2: //subsidiary
//                            if($parent_id <> '70900463' AND strpos($name, '[') === false){
//                                $sql = "INSERT INTO tl_sortlySubsidiary (tstamp, self_id, country_id, name) VALUES ('".time()."','$self_id','$parent_id','$name')";
////                                echo $sql."<br>";
//                                $db->query($sql);
//                            }
//                            break;
//                        case 3: //customer
//                            if($parent_id <> '70900630' AND $parent_id <> '70900631' AND strpos($name, '[') === false){
//                                $sql = "INSERT INTO tl_sortlyCustomer (tstamp, self_id, subsidiary_id, customerNo, name) VALUES ('".time()."','$self_id','$parent_id','$customerNo',$name')";
////                                echo $sql."<br>";
//                                $db->query($sql);
//                            }
//                            break;
//                        case 4: //KTC
//                                if(strpos($name, '[') === false){
//                                $sql = "INSERT INTO tl_sortlyKtc (tstamp, self_id, name) VALUES ('".time()."','$self_id','$parent_id','$name')";
////                                echo $sql."<br>";
//                                $db->query($sql);
//                            }
//                            break;   
//                        default:
//                    }
//                    
//                  echo "Name: ".$row[name]."<br>";
//                  echo "ParentId: ".$row[parent_id]."<br>";
//                  echo "SelfId: ".$row[self_id]."<br>";
//                  echo "CustomerNo: ".$row[customerNo]."<br>";                  
//                  echo "Old Id (must match parentId when looping further: ".$oldSid."<p>";
//                  
//                $startParent_Id = $row[self_id];  
//                $oldSid = $row[self_id];  
//                $content = recursiveParentsAndChilds($db,$startParent_Id,$oldSid,$i);
//            }
//        }
//        return $allContent;  
//    }



