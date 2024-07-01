<?php
// App\EventListener\DataContainer\SortlyFunctions.php
namespace App\EventListener\DataContainer;

use Contao\DataContainer;
use Contao\Database;
use Contao\DC_Table;
use Contao\System;
use Contao\Backend;
use Contao\Input;


//use App\EventListener\DataContainer\SortlyFunctions;
//use App\EventListener\DataContainer\MyFunctions;
//use App\EventListener\DataContainer\MailFunctions;



class SortlyFunctions
{
    
    public function averagePrice(){
        
//        $sql = "SELECT AVG(price) FROM tl_orders WHERE articleNo LIKE '$kromiArticleNo'";
//        $result = $this->Database->prepare($sql)
//                              ->execute();
//        while($result->next())
//        {
//            $value[$result->kromiArticleNo] = $result->kromiArticleNo.' - '.$result->name.' ['.$result->supplierArticleNo.']';
//        }
//        
//        $sql = "SELECT AVG(price) FROM tl_orders WHERE articleNo LIKE '$kromiArticleNo'";
//        $result = $this->Database->prepare($sql)
//                              ->execute();
//        while($result->next())
//        {
//            $value[$result->kromiArticleNo] = $result->kromiArticleNo.' - '.$result->name.' ['.$result->supplierArticleNo.']';
//        }
    }
    
    public function updatePrice(DataContainer $dc){

    $db = Database::getInstance();

        // Perform actions or modifications on form submission
        if ($dc->activeRecord !== null) {

            $price = $dc->activeRecord->price;
            $sortlyId = $dc->activeRecord->sortlyId;

            $sql = "Update sortly set price = $price WHERE sortlyId LIKE '$sortlyId'";
            $db->prepare($sql)->execute();

        //log
        $text = $sql;
        $category = "DATA";
        MyFunctions::log($text, $category, __METHOD__);
        }
    }
   

        public function SortlyItemsToTable(DataContainer $dc): void {
            // Gets sortly items from API and insert them into table tl_sortly
            if (!$dc->activeRecord){
                return;
            }

            // If check = checked => run
            if($dc->activeRecord->test == 1){
                // fetch data from REST API Sortly
                $url = 'https://api.sortly.co/api/v1/items?per_page=100000&page=1&include=custom_attributes%2Cphotos%2Coptions';
                $data = $this->getSortlyJSON($url);
                
                
                // insert data in table "tl_sortly"
                $this->insertSortlyItems($data);
                
                // call some more routines
                $this->fillSortlyTables();

            }
            
            
        }

    // fills some proxy tables based on sortly items
    public function fillSortlyTables()
    {
//        $this->fillSortlyCountry();
        
        echo $var = $this->recursiveParentsAndChilds("58670979");
        die();
    }

//        public function recursiveParentsAndChilds($startParent_Id)
//    {
//        $db = Database::getInstance();
//        $sql = "SELECT self_id, parent_id, name FROM `tl_sortly` WHERE parent_id = '$startParent_Id' AND self_id NOT LIKE '*70455490*'";
//        $result = $db->prepare($sql)->execute();
//        //$content = "";
//        //$allContent = "";
//        
//        while($result->next())
//        {
//            $startParent_Id = $result->self_id;
//            $content = $this->recursiveParentsAndChilds($startParent_Id);
//            $content.= "<br>".$content.$result->parent_id.' --- '.$result->self_id.' --- '.$result->name;
//            //return $content;  
////                $msg.= $result->parent_id.' --- '.$result->self_id.' --- '.$result->name;
//        }
//        
//        return $content;
////        die ();
//    }
    
    
//    public function recursiveParentsAndChilds($startParent_Id)
//    {
//        $db = Database::getInstance();
//        $sql = "SELECT sid, pid, name FROM `tl_sortly` WHERE pid = '$startPid' AND self_id NOT LIKE '*70455490*'";
//        $result = $db->prepare($sql)->execute();
//        $content = "";
//        $allContent = "";
//        
//        while($result->next())
//        {
//            $startParent_Id = $result->self_id;
//            $content = $this->recursiveParentsAndChilds($startParent_Id);
//            $allContent.= "<br>".$content.$result->name.' --- '.$result->parent_id.' --- '.$result->self_id;
//            //return $content;  
////                $msg.= $result->parent_id.' --- '.$result->self_id.' --- '.$result->name;
//        }
//        
//        return $allContent;
////        die ();
//    }
    
    
     public function fillSortlyCountry(){
    // fill table tl_contry for sortly countries
        
        $db = Database::getInstance();
        
        // truncate table tl_scountry
        MyFunctions::truncateTable("tl_country");

        // Countries in to table
//        $sql = "INSERT INTO tl_country (self_id, parent_id, name)
//                SELECT self_id, parent_id, name
//                FROM tl_sortly
//                WHERE parent_id = '58670979'
//                AND self_id NOT IN ('70455490')";
        
        $sql = "INSERT INTO tl_country (self_id, parent_id, name) SELECT self_id, parent_id, name FROM tl_sortly WHERE parent_id = '58670979' AND self_id NOT IN ('*70455490*')";
        $db->prepare($sql)
            ->execute();

        //log

//        $text = str_replace("'", "#", $sql);
//        $text = "SQL: ".substr($sql,1,20)." ...";
//        $text = substr($sql,1,50).".".substr($sql,51,100);
//        $text = "S-Q-L: [INSERT] INTO tl_country... successful executed";
        //$text = "SQL: \'".$sql."'";
//                $text = str_replace("'", "#", $sql);
//                
//                
//        $text = str_replace("'", "\'", $sql);
        $text = $sql;
        $category = "DATA";
        MyFunctions::log($text, $category, __METHOD__);
    }

//      public function fillSortlySubsidiary(){
//    // fill table tl_Subsidiary for retreiving sortly subsidiaries
//        
//        $db = Database::getInstance();
//        
//        // truncate table tl_scountry
////        MyFunctions::truncateTable("tl_subsidiary");
//        
//        // Prepare data
//        $sql = "SELECT self_id, name FROM `tl_sortly` where parent_id = '58670979' and self_id not in ('70455490')";
//        $result = $db->prepare($sql)->execute();
//        $msg = "";
//        while($result->next())
//        {
//            $msg.= $result->parent_id.' --- '.$result->self_id.' --- '.$result->name;
//        }
////        var_dump($msg);
////        die();
//        
//        $sql = "INSERT INTO tl_subsidiary (self_id, parent_id, name) SELECT self_id, parent_id, name FROM tl_sortly WHERE parent_id IN '58670979'";
//        
//        $db->prepare($sql)
//            ->execute();
//        
//        //log
//        $text = "SQL: ".$sql;
//        $category = "DATA";
//        MyFunctions::log($text, $category, __METHOD__);
//    }
    
    public function getSortlyJSON($url) {
        
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
        //CHECK: echo $response;
        
        // Decode and return JSON
        $data = json_decode($response, true);
        return $data;
    }
    
  
    public function insertSortlyItems($data)
    {
        MyFunctions::truncateTable("tl_sortly");
        
        // Prepare and execute INSERT query
        $db = Database::getInstance();
        
        $msg = ""; // meassage
        foreach ($data['data'] as $item) 
        {
            $sid = $item['id'];
            $pid = $item['parent_id'];  
            $name = $item['name'];
            $price = $item['price'];
            $min_quantity = $item['min_quantity'];
            $quantity = $item['quantity'];
            $notes = $item['notes'];            
            $sortlyId = $item['sid'];
            $type = $item['type'];  
            
            $packageUnit = "";
            $supplierArticleNo = "";
            $ean = "";
            $supplier = "";
            $serialNo = "";
            $discontinued = "";
            $kromiArticleNo = "";
            $customerNo = "";            
            $storageLocation = "";
            $inventoryNo = "";
            $technicalSpecification = "";
            $reserved = "";
            $lastDGUV3check = "";            
            $CEdeclaration = "";
            $active = "";
            
            $msg.=  '<b>sid: '.$sid.'  /  name: '.$name.'</b>'
                    .'<br/> price: '.$price
                    .'<br/> min_quantity: '.$min_quantity
                    .'<br/> quantity: '.$quantity
                    .'<br/> notes: '.$notes
                    .'<br/> pid: '.$pid
                    .'<br/> sid: '.$sid;
            $msg.= "<p><b>Custom fields</b><br/>";
            
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
                    case "customerNo":
                            $customerNo = $attribute['value'];
                        $msg.=' '.$customerNo;
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
                    case "last DGUV3 check":
                            $lastDGUV3check = $attribute['value'];
                        $msg.=' '.$lastDGUV3check;
                      break;  
                    case "CE declaration":
                            $CEdeclaration = $attribute['value'];
                        $msg.=' '.$CEdeclaration;
                      break;
                    case "active":
                            $active= $attribute['value'];
                        $msg.=' '.$active;
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

            $db->prepare(
            "INSERT INTO tl_sortly 
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
                discontinued,
                kromiArticleNo,
                customerNo,
                storageLocation,
                inventoryNo,
                technicalSpecification,
                reserved,
                lastDGUV3check,
                CEdeclaration,
                active,
                photoUrl
            )
            VALUES
            (
                ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
            )          
            "
            )
            ->execute
            (
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
                    
                $packageUnit,
                $supplierArticleNo,
                $ean,
                $supplier,
                $serialNo,
                $discontinued,
                $kromiArticleNo,
                $customerNo,
                $storageLocation,
                $inventoryNo,
                $technicalSpecification,
                $reserved,
                $lastDGUV3check,           
                $CEdeclaration,
                $active,
                $photoUrl
            );
        }
        echo $msg;
        die();
     }
           
}