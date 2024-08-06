<?php
// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
//include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/css/styles.css";
//
//
// main function for calculating prices according BOM for all IVMs listed in sortly templates
if (isset($_GET['webhookFunction'])) {

    $function = $_GET['webhookFunction'];
    
    if($function == "calculatePricesBOM"){
        
        echo calculatingIVMs($db);
        echo balanceNeededPartsAndPartsOnStock($db);
        //createReport($db);
        
//        calculatingIVMs($db);
//        balanceNeededPartsAndPartsOnStock($db);
//        createReport($db);
    }
}

function calculatingIVMs($db){

    $articlesMain = [];
    $z = 0;
    
    $sql = "TRUNCATE TABLE bomCalculations";
    $result = $db->query($sql);
    
    $msg = "";
   // loop all IVMs
    $sql = "Select id, sortlyId, name, quantity, price from tl_sortlyTemplatesIVM";
    $result = $db->query($sql);
    while($item = $result->fetch_assoc()){ 
        
        $pid = $item['id'];
        
        // fetch needed quantity
        $quantityNeeded = $item['quantity'];   
        if($quantityNeeded > 0){
            $articlesMain[] = fetchNeededParts($db, $pid, $quantityNeeded);
            
        }
        $z = count($articlesMain);
        
//        print_r($articlesMain);
    } 
    
    $articlesFinal = [];
//    echo $z;
    // Durchlauf des gesamten Arrays - jeder Artikel wird auf Vorkommen im finalen Array überprüft.
    for ($i = 0; $i < $z; $i++) {
        foreach ($articlesMain[$i] as $articleMain) {
            $sortlyId = $articleMain['sortlyId'];
            $quantity = $articleMain['quantity'];
            
//            if($sortlyId == 'SD0M4T0721'){
//                echo $quantity."<br>";
//            }


                if(empty($articlesFinal)){
    //                echo "arsch";
                $articlesFinal[] = $articleMain; // Beim Start der Routine ist das Array leer, daher wird hier der erste Eintrag hinzugefügt
                } 
                else {

                foreach ($articlesFinal as $articleFinal) {


                    // nun ist das Array gesetzt und mit jedem Schleifendurchlauf wird gepüft, ob die SortlyId schon vorhanden ist. Falls ja, wird 
                    if ($articleFinal['sortlyId'] === $sortlyId) {
                        $index = array_search($sortlyId, array_column($articlesFinal, 'sortlyId'));
                        $articlesFinal[$index]['quantity'] = $quantity + $articleFinal['quantity'];
                        
                        
                                                    if($sortlyId == 'SD0M4T0919'){
                echo $quantity."<br>";
//                echo $index = array_search($sortlyId, array_column($articlesFinal, 'sortlyId'));
//                echo $articlesFinal[];
            }
//                                                die();
//                        break;
                    } else{
//                          print_r($articleMain);
//                            echo "<p>";

                        $articlesFinal[] = $articleMain; // Wenn im Array noch kein Eintrag mit der SortlyId existiert, wird er hinzugefügt
//                        break; // If und Foreach verlassen
                    }
                } 
            }
        } 
    }
    print_r($articlesFinal);
    
    return $msg;
}  




// Create BOM list with all needed parts according needed qunatity
function fetchNeededParts($db, $pid, $quantityNeeded){
    $msg = "";
    $articles = [];
    
    $sql = "Select Distinct
        sortly.name,
        sortly.IVM,
        ROUND(sortly.price,2) as price,
        tl_bom.bomQuantity,
        tl_bom.sortlyId,
        Group_Concat(Distinct sortly.supplierArticleNo) As supplierArticleNo,
        Group_Concat(Distinct tl_supplier.name) As supplier
    From
        sortly Right Join
        tl_bom On sortly.sortlyId = tl_bom.sortlyId Left Join
        tl_orders On tl_bom.sortlyId = tl_orders.sortlyId Left Join
        tl_supplier On tl_supplier.id = tl_orders.supplierId
    Where
        tl_bom.pid in ($pid) And
        tl_bom.calculate = 1 And
        sortly.discontinued = 0
    Group By
        sortly.name,
        sortly.price,
        tl_bom.sortlyId,
        tl_supplier.name";

        $result = $db->query($sql);
        while($item = $result->fetch_assoc()){ 
            
        $name = $item['name']; 
        $ivm = $item['IVM'];         
        $bomPrice = $item['price']; 
        $bomQuantity = $item['bomQuantity'];  
        $sortlyId = $item['sortlyId']; 
        $supplierArticleNo = $item['supplierArticleNo'];
        $supplier = $item['supplier'];        
        $quantity = $bomQuantity * $quantityNeeded;
        $quantity = $bomQuantity;        
        
        $articles[]= [
            'name'                  => $name, 
            'ivm'                   => $ivm, 
            'bomPrice'              => $bomPrice, 
            'bomQuantity'           => $bomQuantity, 
            'sortlyId'              => $sortlyId, 
            'supplierArticleNo'     => $supplierArticleNo, 
            'supplier'              => $supplier, 
            'quantity'              => $quantity] ;
         
//        print_r($newArticle);
        
        
        // Eintrag hinzufügen, wenn diese SortlyId noch nicht durchlaufen wurde - ansonsten wird die benötigte Menge erhöht

//            if(empty($articles)){
//            $articles[] = $newArticle; // Beim Start der Routine ist das Array leer, daher wird hier der erste Eintrag hinzugefügt
//            } 
//            else {
//                foreach ($articles as $article) {
//                    // nun ist das Array gesetzt und mit jedem Schleifendurchlauf wird gepüft, ob die SortlyId schon vorhanden ist. Falls ja, wird 
//                    if ($article['sortlyId'] === $sortlyId) {
//                        $article['quantity'] += $quantity;
//                        break; // If und Foreach verlassen
//                    } else{
////                        $articles[] = $newArticle; // Wenn im Array noch kein Eintrag mit der SortlyId existiert, wird er hinzugefügt
//                        break; // If und Foreach verlassen
//                    }
//                }            
//            }
        }
     return $articles;   
    }    
        
        
        // result-Array für weitere Verarbeitung vorbereiten
        
        // 1. Jeden Datensatz mit mit der benötigten Menge anreichern
        
        // 2. Gleiche Artikel Mengen aufsummieren
        
        // 3. Sortierung nach Lieferant und Artikelbeschreibung
        
    
    
//            // Menge des Artikels ändern
//            if (updateArticlePrice($articles, $searchName, $newQuantity)) {
//                $msg .= "Die Menge von '" . $searchName . "' wurde um " . $newQuantity . " erhöht.\n";
//            } else {
//                $articles[] = $newArticle;
//                $msg .= "Der Artikel '" . $searchName . "' wurde nicht gefunden und dem Array hinzugefügt.\n";
//                
//// Funktion, um den Preis eines Artikels im Array zu ändern
//function updateArticlePrice($articles, $articleName, $newQuantity) {
//    
//    foreach ($articles as $article) {
//
//        if ($article['sortlyId'] === $articleName) {
//            $article['quantity'] += $newQuantity;
//            return true; // Änderung vorgenommen, daher true zurückgeben
//        }
//    }
//    return false; // Artikel nicht gefunden, daher false zurückgeben
//}


function balanceNeededPartsAndPartsOnStock($db){
$msg = "";    
    // iterate table bomCalculations
    $sql = "Select
            bomCalculations.supplier,
            bomCalculations.name,
            bomCalculations.ivm,
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
    
    // Truncate only after fetching data
    $sql_0 = "TRUNCATE TABLE bomCalculations";
    $result_0 = $db->query($sql_0);
    
    while($item = $result->fetch_assoc()){ 
        $quantityNeeded = round($item['quantity'],0); 
        $sortlyId = $item['sortlyId']; 
        $name = $item['name']; 
        $ivm = $item['ivm'];         
        $supplier = $item['supplier']; 
        $supplierArticleNo = $item['supplierArticleNo']; 
        $bomPrice = round($item['bomPrice'],2);
        $bomQuantity = 0; 
        $pid = $item['pid']; 
        $toOrder = 0;
        $quantityOrdered = 0;        
        
        // lookup for available quantity on KTC- material stock (58670984) 
        
        if($ivm){ 
            $sql_1 = "Select SUM(quantity) as quantity from sortly WHERE name LIKE '$name' and pid Like '58670984'";
        } else { 
            $sql_1 = "Select SUM(quantity) as quantity from sortly WHERE sortlyId LIKE '$sortlyId' and pid Like '58670984'";
        }
        
        $result_1 = $db->query($sql_1);
        while($item_1 = $result_1->fetch_assoc()){ 
            $quantityStock = $item_1['quantity']; 
        } 
        
        // lookup for ordered quantity in tl_orders

        $sql_1 = "Select SUM(orderQuantity) as orderQuantity from tl_orders WHERE sortlyId LIKE '$sortlyId' and delivered = 0";
//        die();
        $result_1 = $db->query($sql_1);
        while($item_1 = $result_1->fetch_assoc()){ 
            $quantityOrdered = $item_1['orderQuantity']; 
        } 
        
        $quantity = $quantityNeeded - $quantityStock - $quantityOrdered;
        $price = $quantity * $bomPrice; 
        $price = round($price,2);
        
//        $price = $quantity * $bomPrice; 
//        $price = number_format($price, 2, ',', '.');
//        $price = '€' . $price;
        
        $msg.=    "<br/><b>".$sortlyId." - ".$name."</b><br/>".
                "quantity needed: ".$quantityNeeded."<br>".
                "quantity on stock: ".$quantityStock."<br>". 
                "quantity ordered: ".$quantityOrdered."<br>".                  
                "quantity to order: ".$quantity."<br>".
                "single price: ".$bomPrice."<br>".                    
                "total price: ".$price."<br>".                
                        
        "</p>";
        
//        // Exclude items with enough stock from list
//        if($quantity > 0){
//            $toOrder = 1;
//        }
        
        $toOrder = 1;
        
        // Overwrite table bomCalculations
        $sql = "INSERT INTO bomCalculations(
            tstamp,
            pid,
            sortlyId,
            name,
            ivm,
            price,
            quantity,
            supplier,
            supplierArticleNo,
            bomQuantity,
            bomPrice,
            quantityStock,
            quantityNeeded,
            quantityOrdered,                    
            toOrder
            ) 
        VALUES
            (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";

        $stmt = $db->prepare($sql);
        $parameterTypes = "iissiddssdddddi";
        $stmt->bind_param($parameterTypes,
            time(),
            $pid,
            $sortlyId,                        
            $name,
            $ivm,
            $price,
            $quantity,
            $supplier, 
            $supplierArticleNo,
            $bomQuantity,
            $bomPrice,
            $quantityStock,
            $quantityNeeded,
            $quantityOrdered,                
            $toOrder,
        );

        // Execute the statement
        $stmt->execute();  
      
        
    } 
    return $msg;
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
$sql_statement = "Select supplier,name,supplierArticleNo,sortlyId,round(bomPrice,2) as bomPrice,round(price,2) as price,quantityStock as stock,quantityNeeded as needed,quantityOrdered as orderd, quantity as purchase, ivm as IVM from bomCalculations where toOrder =1";
//$sql_statement ="Select 
//                    supplier,
//                    name,
//                    supplierArticleNo,
//                    sortlyId,
//                    round(bomPrice,2) as bomPrice,
//                    round(price,2) as price,
//                    quantityStock as stock,
//                    quantityNeeded as needed,
//                    quantityOrdered as orderd, 
//                    quantity as purchase 
//                from bomCalculations 
//                where toOrder is true";

//$sql_statement = "Select supplier,name,supplierArticleNo,sortlyId, CONCAT('€', FORMAT(bomPrice, 2, 'de_DE')) AS bomPrice,CONCAT('€', FORMAT(price, 2, 'de_DE')) AS price,quantityStock as stock,quantityNeeded as needed,quantity as purchase from bomCalculations where toOrder =1";
// Generate report
$pdf->mysql_report($sql_statement, false, $attr );

$pdf->Output();

}