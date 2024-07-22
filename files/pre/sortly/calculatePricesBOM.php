<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT'] . "/files/pre/db/dbConfig.php";

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

    // loop all IVMs
    $sql = "Select id, sortlyId, name, quantity, price from tl_sortlyTemplatesIVM";
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

    $sql = "Select Distinct
        sortly.name,
        sortly.IVM,
        sortly.price,
        SUM(Distinct tl_bom.bomQuantity) As bomQuantity,
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
        tl_supplier.name
    Order By
        supplier,
        sortly.name";

    $result = $db->query($sql);
    while ($item = $result->fetch_assoc()) {

        $sortlyId = $item['sortlyId'];
        $bomQuantity = $item['bomQuantity'];
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
            insertBomCalculation($db, $sortlyId, $quantityCurrent, $quantityStock, $quantityOrdered); // nicht vorhanden - er wird eingefügt
        } else {
            while ($item = $result_check->fetch_assoc()) {
                $quantityNeededOld = $item['quantityNeeded']; // 
                $quantityNeededNew = $quantityCurrent + $quantityNeededOld;
                updateBomCalculation($db, $sortlyId, $quantityNeededNew, $quantityStock, $quantityOrdered); // vorhanden, er wird aktualisiert
            }
        }
    }
}



function insertBomCalculation($db, $sortlyId, $quantityCurrent, $quantityStock, $quantityOrdered) {
    // Prepare the SQL statement
    $sql = "INSERT INTO bomCalculations(
                tstamp,
                sortlyId,
                quantityNeeded,
                quantityStock,
                quantityOrdered
                ) 
            VALUES
                (
                ?, ?, ?, ?, ?
                )";

    $stmt = $db->prepare($sql);
    $parameterTypes = "isiii";
    $stmt->bind_param($parameterTypes,
            time(),
            $sortlyId,
            $quantityCurrent,
            $quantityStock,
            $quantityOrdered
    );
    // Execute the statement
    $stmt->execute();
}



function updateBomCalculation($db, $sortlyId, $quantityNeededNew, $quantityStock, $quantityOrdered) {
    // Prepare the SQL statement
    $sql = "UPDATE bomCalculations SET 
                    quantityNeeded = $quantityNeededNew,
                    quantityStock = $quantityStock,
                    quantityOrdered = $quantityOrdered
                WHERE sortlyId like '$sortlyId' ";
    $result = $db->query($sql);
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
    // lookup forordered quantity
    $sql = "Select SUM(orderQuantity) as orderQuantity from tl_orders WHERE sortlyId LIKE '$sortlyId' and delivered = 0";
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
    $sql_statement = "Select distinct(sortly.sortlyId) As sortlyId, sortly.name, tl_supplier.name As supplier, $str_purchaseOrder as purchaseOrder, bomCalculations.quantityStock, bomCalculations.quantityNeeded, bomCalculations.quantityOrdered, sortly.price As singlePrice, $str_purchasePrice as purchasePrice From bomCalculations Left Join sortly On bomCalculations.sortlyId = sortly.sortlyId Left Join tl_orders On tl_orders.sortlyId = sortly.sortlyId Left Join tl_supplier On tl_supplier.id = tl_orders.supplierId";
    
    // Generate report only to order
    $sql_statementWhereClause = " where $str_purchaseOrder > 0 and sortly.IVM = 0";
    $sql_statementOrderClause = " order by tl_supplier.name, sortlyId";
    $sql_statementGroup = " group By (sortly.sortlyId), sortly.name, ROUND((bomCalculations.quantityNeeded - bomCalculations.quantityStock - bomCalculations.quantityOrdered), 2), bomCalculations.quantityStock, bomCalculations.quantityNeeded, bomCalculations.quantityOrdered, sortly.price, ROUND((sortly.price * (bomCalculations.quantityNeeded - bomCalculations.quantityStock - bomCalculations.quantityOrdered)), 2)";
    
    // Generate report only to order
     $sql = $sql_statement.$sql_statementWhereClause.$sql_statementGroup.$sql_statementOrderClause;
     $pdf->mysql_report($sql, false, $attr);
     
    // Generate report full
    $sql = $sql_statement.$sql_statementGroup.$sql_statementOrderClause;
    $pdf->mysql_report($sql, false, $attr);
    
    // Output table
    $pdf->Output();
}
