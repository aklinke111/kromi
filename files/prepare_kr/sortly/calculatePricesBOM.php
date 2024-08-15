<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT'] . "/files/prepare_kr/db/dbConfig.php";

// main function for calculating prices according BOM for all IVMs listed in sortly templates
if (isset($_GET['webhookFunction'])) {

    $function = $_GET['webhookFunction'];

    if ($function == "calculatePricesBOM") {

        echo main($db);
        createReport($db);
    }
}


function main($db) {
    $msg = "";

    // truncate table 'bomCalculations'
    $sql = "TRUNCATE TABLE bomCalculations";
    $result = $db->query($sql);

    // loop all IVM
    $sql = "Select
        tl_sortlyTemplatesIVM.id,
        tl_sortlyTemplatesIVM.price,
        tl_sortlyTemplatesIVM.name,
        kr_quantityIVM.quantity,
        kr_quantityIVM.quantityName
    From
        tl_sortlyTemplatesIVM Inner Join
        kr_quantityIVM On tl_sortlyTemplatesIVM.id = kr_quantityIVM.id_ivm
    Where
        kr_quantityIVM.quantityName Like 'quantityNeeded'";
    
    // Without Facelifts
//    $sql = "Select id, sortlyId, name, quantity, price from tl_sortlyTemplatesIVM Where exclude = 0";
    $result = $db->query($sql);

    while ($item = $result->fetch_assoc()) {
        $pid = $item['id'];

        // fetch needed quantity of IVMs
        $quantityNeededIVM = $item['quantity'];
        if ($quantityNeededIVM > 0) {
            fetchNeededParts($db, $pid, $quantityNeededIVM);
        }
    }
    return $msg;
}



// Create BOM list with all needed parts according needed qunatity
function fetchNeededParts($db, $pid, $quantityNeededIVM) {
    
//    $sortlyId = "SD0M4T0719";
    $sortlyId = "%";
    
    $sql = "Select Distinct
    sortly.name,
    sortly.IVM,
    Avg(sortly.price) As Avg_price,
    tl_bom.bomQuantity As bomQuantity,
    tl_bom.sortlyId,
    Group_Concat(Distinct sortly.supplierArticleNo) As supplierArticleNo,
    Group_Concat(Distinct tl_supplier.name) As supplierName
From
    sortly Right Join
    tl_bom On sortly.sortlyId = tl_bom.sortlyId Left Join
    tl_orders On tl_bom.sortlyId = tl_orders.sortlyId Left Join
    tl_supplier On tl_supplier.id = tl_orders.supplierId
Where
    tl_bom.pid In ($pid) And
    tl_bom.calculate = 1 And
    sortly.discontinued = 0 And
    tl_orders.internalExternal Like 'external' And
    tl_orders.sortlyId Like '$sortlyId'
Group By
    sortly.name,
    sortly.IVM,
    tl_bom.bomQuantity,
    tl_bom.sortlyId
Order By
    supplier,
    sortly.name";

    $result = $db->query($sql);
    while ($item = $result->fetch_assoc()) {

        $sortlyId = $item['sortlyId'];
        $bomQuantity = $item['bomQuantity'];
        $supplierName = $item['supplierName'];
        $quantityCurrent = $bomQuantity * $quantityNeededIVM;

        // available stock for this item
        $quantityStock = quantityStock($db, $sortlyId);
        if (empty($quantityStock)) {
            $quantityStock = 0;
        }

        // quantity ordered for this item
        $quantityOrdered = quantityOrdered($db, $sortlyId);
        if (empty($quantityOrdered)) {
            $quantityOrdered = 0;
        }

        // Überprüfung, ob dieser Datensatz bereits in die Tabelle eingefügt wurde
        $sql_check = "Select * from bomCalculations where sortlyId Like '$sortlyId' ";
        $result_check = $db->query($sql_check);

        if (!$result_check->num_rows > 0) {
            insertBomCalculation($db, $pid, $sortlyId, $quantityCurrent, $quantityStock, $quantityOrdered, $supplierName); // nicht vorhanden - er wird eingefügt
        } else {
            while ($item = $result_check->fetch_assoc()) {
                $quantityNeededOld = $item['quantityNeeded']; // 
                $quantityNeededNew = $quantityCurrent + $quantityNeededOld;
                updateBomCalculation($db, $pid, $sortlyId, $quantityNeededNew, $quantityStock, $quantityOrdered, $supplierName); // vorhanden, er wird aktualisiert
            }
        }
    }
}



function insertBomCalculation($db, $pid, $sortlyId, $quantityCurrent, $quantityStock, $quantityOrdered, $supplierName) {
    // Prepare the SQL statement
    $sql = "INSERT INTO bomCalculations(
                tstamp,
                pid,
                sortlyId,
                quantityNeeded,
                quantityStock,
                quantityOrdered,
                supplierName
                ) 
            VALUES
                (
                ?, ?, ?, ?, ?, ?, ?
                )";

    $stmt = $db->prepare($sql);
    $parameterTypes = "iisiiis";
    $stmt->bind_param($parameterTypes,
            time(),
            $pid,
            $sortlyId,
            $quantityCurrent,
            $quantityStock,
            $quantityOrdered,
            $supplierName
    );
    // Execute the statement
    $stmt->execute();
}


function updateBomCalculation($db, $pid, $sortlyId, $quantityNeededNew, $quantityStock, $quantityOrdered, $supplierName) {
    // Prepare the SQL statement
    $sql = "UPDATE bomCalculations SET 
                pid = ?,
                quantityNeeded = ?,
                quantityStock = ?,
                quantityOrdered = ?,
                supplierName = ?
            WHERE sortlyId like ? ";
    
    $stmt = $db->prepare($sql);
    $parameterTypes = "iiiiss";
    $stmt->bind_param($parameterTypes,
            $pid,
            $quantityNeededNew,
            $quantityStock,
            $quantityOrdered,
            $supplierName,
            $sortlyId
    );
    // Execute the statement
    $stmt->execute();
}



function quantityStock($db, $sortlyId) {
    // lookup for available quantity on KTC- material stock (58670984) 
    $sql = "Select SUM(quantity) as quantity from sortly WHERE sortlyId LIKE '$sortlyId' and pid Like '58670984'";
    $result = $db->query($sql);

    while ($item = $result->fetch_assoc()) {
        return $quantityStock = $item['quantity'];
    }
}



function quantityOrdered($db, $sortlyId) {
    // lookup for ordered quantity
    $sql = "SELECT SUM(orderQuantity) as orderQuantity 
           FROM tl_orders 
           WHERE sortlyId LIKE '$sortlyId' and delivered = 0 and tl_orders.internalExternal Like 'external' ";
    $result = $db->query($sql);

    while ($item = $result->fetch_assoc()) {
        return $quantityOrdered = $item['orderQuantity'];
    }
}



function createReport($db) {

    require('mysql_report.php');

    // the PDF is defined as normal, in this case a Landscape, measurements in points, A4 page.
    $pdf = new PDF('L', 'pt', 'A4');
    $pdf->SetFont('Arial', '', 10);

    // change the below to establish the database connection.
    // Database configuration
    $dbHost = "127.0.0.1:3307";
    $dbUsername = "xm3xbj34_aklinke";
    $dbPassword = "Kromi2000!";
    $dbName = "xm3xbj34_kromiag";

    // should not need changing, change above instead.
    $pdf->connect($dbHost, $dbUsername, $dbPassword, $dbName);

    // attributes for the page titles
    $attr = array('titleFontSize' => 18, 'titleText' => 'BOM list to order.');
    
    
    $str_purchaseOrder = "ROUND((bomCalculations.quantityNeeded - bomCalculations.quantityStock - bomCalculations.quantityOrdered),2) ";
    $str_purchasePrice = "ROUND((sortly.price * (bomCalculations.quantityNeeded - bomCalculations.quantityStock - bomCalculations.quantityOrdered)),2) ";
    
    // $sql_statement = "Select sortlyId, quantityStock as stock, quantityNeeded as needed, quantityOrdered as ordered from bomCalculations ";
    // $sql_statement = "Select supplier,name,supplierArticleNo,sortlyId, CONCAT('€', FORMAT(bomPrice, 2, 'de_DE')) AS bomPrice,CONCAT('€', FORMAT(price, 2, 'de_DE')) AS price,quantityStock as stock,quantityNeeded as needed,quantity as purchase from bomCalculations where toOrder =1";
    $sql_statement = "Select distinct(sortly.sortlyId) As sortlyId, sortly.name, $str_purchaseOrder as purchaseOrder, bomCalculations.quantityStock, bomCalculations.quantityNeeded, bomCalculations.quantityOrdered, bomCalculations.supplierName, sortly.price As singlePrice, $str_purchasePrice as purchasePrice From bomCalculations Left Join sortly On bomCalculations.sortlyId = sortly.sortlyId ";
    
    // Generate report only to order
    $sql_statementWhereClause = " where $str_purchaseOrder > 0 and sortly.IVM = 0";
    
    $sql_statementOrderClause = " order by bomCalculations.supplierName, sortlyId";
    $sql_statementGroup = " group By (sortly.sortlyId), sortly.name, ROUND((bomCalculations.quantityNeeded - bomCalculations.quantityStock - bomCalculations.quantityOrdered), 2), bomCalculations.quantityStock, bomCalculations.quantityNeeded, bomCalculations.quantityOrdered, sortly.price, ROUND((sortly.price * (bomCalculations.quantityNeeded - bomCalculations.quantityStock - bomCalculations.quantityOrdered)), 2)";
    
//    // Generate report only to order
//     $sql = $sql_statement.$sql_statementWhereClause.$sql_statementGroup.$sql_statementOrderClause;
//     $pdf->mysql_report($sql, false, $attr);
     
    // Generate report full
    $attr = array('titleFontSize' => 18, 'titleText' => 'BOM list FULL.');
    $sql = $sql_statement.$sql_statementGroup.$sql_statementOrderClause;
    $pdf->mysql_report($sql, false, $attr);
    
    // Output table
    $pdf->Output();
}
