<?php
// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";

// main function for calculating prices according BOM for all IVMs listed in sortly templates
if (isset($_GET['webhookFunction'])) {

    $function = $_GET['webhookFunction'];
    
    if($function == "calculatePricesBOM"){
        calculatingIVMs($db);
        balanceNeededPartsAndPartsOnStock($db);
        createReport($db);
    }
}

function calculatingIVMs($db){
//    echo "arsch";
//    die();
    $sql = "TRUNCATE TABLE bomCalculations";
    $result = $db->query($sql);
    
    $msg = "";
   // loop all IVMs
    $sql = "Select id, quantity from tl_sortlyTemplatesIVM";
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        $pid = $item['id']; 
        $quantityNeeded = $item['quantity'];   
        
        if($quantityNeeded > 0){
            fetchNeededParts($db,$pid,$quantityNeeded);
        }
    } 
    return $msg;
}  

function balanceNeededPartsAndPartsOnStock($db){
$msg = "";    
    // iterate table bomCalculations
    $sql = "Select
            bomCalculations.supplier,
            bomCalculations.name,
            bomCalculations.sortlyId,
            Sum(bomCalculations.quantity) As quantity,
            bomCalculations.supplierArticleNo,
            bomCalculations.bomPrice,
            Group_Concat(bomCalculations.pid) As pid
        From
            bomCalculations
        Group By
            bomCalculations.supplier,
            bomCalculations.name,
            bomCalculations.sortlyId,
            bomCalculations.bomPrice";
    $result = $db->query($sql);
    
    $sql_0 = "TRUNCATE TABLE bomCalculations";
    $result_0 = $db->query($sql_0);
    
    while($item = $result->fetch_assoc()){ 
        $quantityNeeded = round($item['quantity'],0); 
        $sortlyId = $item['sortlyId']; 
        $name = $item['name']; 
        $supplier = $item['supplier']; 
        $supplierArticleNo = $item['supplierArticleNo']; 
        $bomPrice = round($item['bomPrice'],2);
        $bomQuantity = 0; 
        $pid = $item['pid']; 
        $toOrder = 0;
        
        // lookup for available quantity on stock
        $sql_1 = "Select round(quantity,0) as quantity from sortly WHERE sortlyId LIKE '$sortlyId'";
        $result_1 = $db->query($sql_1);
        while($item_1 = $result_1->fetch_assoc()){ 
            $quantityStock = $item_1['quantity']; 
        } 
        $quantity = $quantityNeeded - $quantityStock;
        $price = $quantity * $bomPrice; 
        $price = round($price,2);
        
//        $price = $quantity * $bomPrice; 
//        $price = number_format($price, 2, ',', '.');
//        $price = '€' . $price;
        
        
        $msg.=    "<br/><b>".$sortlyId." - ".$name."</b><br/>".
                "quantity needed: ".$quantityNeeded."<br>".
                "quantity on stock: ".$quantityStock."<br>".   
                "quantity to order: ".$quantity."<br>".
                "single price: ".$bomPrice."<br>".                    
                "total price: ".$price."<br>".                
                        
        "</p>";
        
        if($quantity > 0){
            $toOrder = 1;
        }
                           // Overwrite table bomCalculations
                $sql = "INSERT INTO bomCalculations(
                    tstamp,
                    pid,
                    sortlyId,
                    name,
                    price,
                    quantity,
                    supplier,
                    supplierArticleNo,
                    bomQuantity,
                    bomPrice,
                    quantityStock,
                    quantityNeeded,
                    toOrder
                    ) 
                VALUES
                    (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                    )";

        $stmt = $db->prepare($sql);
        $parameterTypes = "iissddssddddi";
        $stmt->bind_param($parameterTypes,
            time(),
            $pid,
            $sortlyId,                        
            $name,
            $price,
            $quantity,
            $supplier, 
            $supplierArticleNo,
            $bomQuantity,
            $bomPrice,
            $quantityStock,
            $quantityNeeded,
            $toOrder,
        );

        // Execute the statement
        $stmt->execute();  
      
        
    } 
    return $msg;
}

// Create BOM list with all needed parts according needed qunatity
function fetchNeededParts($db,$pid,$quantityNeeded){
 
$sql_1 = "Select Distinct
    sortly.name,
    sortly.price,
    Sum(Distinct tl_bom.bomQuantity) As bomQuantity,
    tl_bom.sortlyId,
    Group_Concat(distinct sortly.supplierArticleNo) As supplierArticleNo,
    Group_Concat(distinct tl_supplier.name) As supplier    
From
    sortly Right Join
    tl_bom On sortly.sortlyId = tl_bom.sortlyId Inner Join
    tl_orders On tl_bom.sortlyId = tl_orders.sortlyId Inner Join
    tl_supplier On tl_supplier.id = tl_orders.pid
Where
    tl_bom.pid In ($pid) And
    tl_bom.calculate = 1 And
    sortly.discontinued = 0
    
Group By
    tl_supplier.name,
    sortly.name,
    sortly.price,
    tl_bom.sortlyId
Order By
    supplier,
    sortly.name";

        $result_1 = $db->query($sql_1);
        while($item_1 = $result_1->fetch_assoc()){ 
            
        $sortlyId = $item_1['sortlyId'];    
        $name = $item_1['name'];  
        $bomPrice = round($item_1['price'],2); 
        $price = round($bomPrice,2);
        $supplier = $item_1['supplier'];
        $supplierArticleNo = $item_1['supplierArticleNo'];
        $bomQuantity = $item_1['bomQuantity'];  
        $quantity = $bomQuantity*$quantityNeeded;  
        // Prepare the SQL statement
        $sql = "INSERT INTO bomCalculations(
                    tstamp,
                    pid,
                    sortlyId,
                    name,
                    price,
                    quantity,
                    supplier,
                    supplierArticleNo,
                    bomQuantity,
                    bomPrice
                    ) 
                VALUES
                    (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                    )";

        $stmt = $db->prepare($sql);
        $parameterTypes = "iissddssdd";
        $stmt->bind_param($parameterTypes,
            time(),
            $pid,
            $sortlyId,                        
            $name,
            $price,
            $quantity,
            $supplier, 
            $supplierArticleNo,
            $bomQuantity,
            $bomPrice,            
        );

        // Execute the statement
        $stmt->execute();       
        }
    } 
    
function createReport($db){

require('mysql_report.php');


// the PDF is defined as normal, in this case a Landscape, measurements in points, A4 page.
$pdf = new PDF('L','pt','A4');
$pdf->SetFont('Arial','',10);


// change the below to establish the database connection.
// Database configuration
$dbHost     = "127.0.0.1:3307";
$dbUsername = "xm3xbj34_aklinke";
$dbPassword = "Kromi2000!";
$dbName     = "xm3xbj34_kromiag";

// Create database connection
//$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);


// should not need changing, change above instead.
$pdf->connect($dbHost, $dbUsername, $dbPassword, $dbName);

//$pdf->$db;


// attributes for the page titles
$attr = array('titleFontSize'=>18, 'titleText'=>'BOM list to order.');

// $sql_statement = "Select bomCalculations.supplier,bomCalculations.name,bomCalculations.sortlyId,ROUND(Sum(bomCalculations.quantity),0) As quantity,ROUND(bomCalculations.bomPrice,2) AS bomPrice From bomCalculations Group By bomCalculations.supplier,bomCalculations.name,bomCalculations.sortlyId,bomCalculations.bomPrice";
//$sql_statement = "Select * from bomCalculations WHERE toOrder = 1";
//$sql_statement = "Select supplier,name,sortlyId,bomPrice,quantityStock as stock,quantityNeeded as needed,quantity as quantity where toOrder =1";
$sql_statement = "Select supplier,name,supplierArticleNo,sortlyId,round(bomPrice,2) as bomPrice,round(price,2) as price,quantityStock as stock,quantityNeeded as needed,quantity as purchase from bomCalculations where toOrder =1";

//$sql_statement = "Select supplier,name,supplierArticleNo,sortlyId, CONCAT('€', FORMAT(bomPrice, 2, 'de_DE')) AS bomPrice,CONCAT('€', FORMAT(price, 2, 'de_DE')) AS price,quantityStock as stock,quantityNeeded as needed,quantity as purchase from bomCalculations where toOrder =1";
// Generate report
$pdf->mysql_report($sql_statement, false, $attr );

$pdf->Output();

}
