<?php

// Load the database configuration file
include_once $_SERVER['DOCUMENT_ROOT']."/files/pre/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/pre/fpdf/fpdf.php";

// main function for calculating prices according BOM for all IVMs listed in sortly templates
if (isset($_GET['webhookFunction'])) {

    $function = $_GET['webhookFunction'];
    
    if($function == "fpdf"){

        pdfFromMySqlTable($db);
    }
}


function pdfFromMySqlTable($db){
    
// Fetch data from database
$sql = "SELECT id, sortlyId, name FROM sortly";
$result = $db->query($sql);

// Create instance of FPDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->SetFont('Arial', 'B', 6);
$pdf->AddPage();

// Table Header
$pdf->Cell(20, 10, 'id', 1);
$pdf->Cell(30, 10, 'sortlyId', 1);
$pdf->Cell(150, 10, 'name', 1);
$pdf->Ln();

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        
        $pdf->Cell(20, 10, $row['id'], 1);
        $pdf->Cell(30, 10, $row['sortlyId'], 1);
        $pdf->Cell(150, 10, $row['name'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(160, 10, 'No data found', 1);
}

// Output PDF
$pdf->Output('D', 'database_data.pdf');

}