<?php
include_once $_SERVER['DOCUMENT_ROOT']."/files/pre/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/pre/src/functions/sql.php";

forcastMain($db);

function forcastMain($db){

    for ($i = 0; $i <= 23; $i++) {
    // Create a DateTime object for the current date
    $forecastDate = new DateTime();

    //Add one month to the current date
    $modifyer = "+$i month";
    $forecastDate->modify($modifyer);
    
    // Format the new date as 'YYYY-MM'
    $forecastDate = $forecastDate->format('Y-m');
    
    echo "<b>Forecast Year-Month: " . $forecastDate. "</b><p>";
    
    $sql = "Select id from tl_forecastCategory";
    $result = $db->query($sql);
        // iterate price and quantity needed for each IVM-model
        while ($row = $result->fetch_assoc()) {
            $id = $row['id']; // category ID
//            echo forcastTotalCostIVM($db, $id, $forecastDate);
            
            switch ($id) {
            case 1:
                break;
            case 2:
            //calculate monthly total for all IVM
                echo forcastTotalCostIVM($db, $id, $forecastDate);
                break;                
            case 5:
                echo forcastDGUV3($db, $id, $forecastDate);
                break;
            default:
              //code block
            }
        }
    }
}

function forcastDGUV3($db, $id, $forecastDate){
    
    $msg = "";
      
    $totalCost = 0;
    
    // lookup DGUV3 period
    $DGUV3_Period = globalVal($db, 'DGUV3_Period');
    $msg .= "Period between DGUV3 checks: ".$DGUV3_Period."<br>";
    
    // 
    // lookup for pice per each IVM checked e.g. => 80.00€
    $DGUV3_PricePerIVM = globalVal($db, 'DGUV3_PricePerIVM');
    $msg .= "Price per checked IVM DGUV3: ".$DGUV3_PricePerIVM."<br>";
    
    // lookup for pice per each approach e.g. => 79.00€
    $DGUV3_PricePerApproach = globalVal($db, 'DGUV3_PricePerApproach');
    $msg .= "DGUV3 price per approach: ".$DGUV3_PricePerApproach."<br>";
    
    // lookup period of passed IVM installations 
    $Period_Passed_Installations = globalVal($db, 'Period_Passed_Installations');
    $msg .= "Period in months for considering German installations. ".$Period_Passed_Installations."<br>";
    // lookup forestimated new customers / year for counting approaches
    
//    // Lookup existing count of IVM in Germany
//    $EstimatedNewGermanCustomers = globalVal($db, 'EstimatedNewGermanCustomers');
//    $msg .=  $EstimatedNewGermanCustomers."<br>";
    
    // Lookup passed german projects over last year to etimate new installations next year
    $sql ="Select
        Count(tl_sortlyTemplatesIVM.active) As CountInstalledIVM
    From
        tl_toolcenterProjectCategory Inner Join
        tl_toolcenterProjects On tl_toolcenterProjectCategory.id = tl_toolcenterProjects.projectCategory Inner Join
        tl_toolcenterProjectStatus On tl_toolcenterProjectStatus.id = tl_toolcenterProjects.projectStatus Inner Join
        tl_toolcenterProjectComponents On tl_toolcenterProjects.id = tl_toolcenterProjectComponents.pid Inner Join
        tl_sortlyTemplatesIVM On tl_sortlyTemplatesIVM.id = tl_toolcenterProjectComponents.componentModel Inner Join
        sortly_ktc On tl_toolcenterProjects.ktcId = sortly_ktc.name Inner Join
        sortly_customer On sortly_customer.sid = sortly_ktc.pid Inner Join
        sortly_subsidiary On sortly_subsidiary.sid = sortly_customer.pid Inner Join
        sortly_country On sortly_country.sid = sortly_subsidiary.pid
    Where
        tl_toolcenterProjects.ktcId Not Like 'KTC-000' And
        sortly_country.name = 'Germany' And
        tl_toolcenterProjectCategory.category In ('implementation', 'change configuration') And
        tl_toolcenterProjects.projectDateFinished BETWEEN CURDATE() - INTERVAL $Period_Passed_Installations MONTH AND CURDATE() And
        tl_toolcenterProjectComponents.`usage` = 'install'
    Group By
        sortly_country.name";
    
        $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $CountInstalledIVM = $row['CountInstalledIVM'];
        $msg .= "Count of passed German installations: ".$CountInstalledIVM."<br>";
    }

    // Lookup passed DGUV checks and add 48 months to each IVM
    $sql = "Select
        Date_Format(Date_Add(sortly.DGUV3, Interval $DGUV3_Period Month), '%Y-%m') As nextDGUV3,
        Count(sortly.active) As Count_IVM
    From
        sortly
    Where
        sortly.DGUV3No Not Like ''
    Group By
        nextDGUV3
    Order By
        nextDGUV3";
    
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $nextDGUV3 = $row['nextDGUV3'];
        $Count_IVM = $row['Count_IVM'];
//        $msg .= $nextDGUV3." --- ".$Count_IVM."<br>"; 
        if($nextDGUV3 == $forecastDate){
           $msg .= "Count of DGUV3 checks must be done during  forecast-month $nextDGUV3: $Count_IVM<br>"; 
        }
        
    }
   
    $msg .= "<p>";
    return $msg;
    
}

function forcastTotalCostIVM($db, $id, $forecastDate){
    
    $msg = "";
    $totalCost = 0;
    
    // lookup for months of period for this calculation - e.g. => 24
    $sql = "Select val from tl_Globals where var like 'ForecastPeriod'";
    $result_2 = $db->query($sql);
    while ($row_2 = $result_2->fetch_assoc()) {
        $periodMonth = $row_2['val'];
    }

    
    
    $sql = "Select quantity,  price from tl_sortlyTemplatesIVM";
    $result_1 = $db->query($sql);


    // fetch price and quantity  for each IVM-model
    while ($row_1 = $result_1->fetch_assoc()) {
        // sum up to total
         $totalCost += $row_1['quantity'] * $row_1['price'];
  }
          
  // Monthly costs 
    $monthlyCost = $totalCost / $periodMonth;
 
   
   // Insert
    $sql = "INSERT INTO kr_forecastEngineering (tstamp, forecastDate, categoryId, cost) VALUES (".time().", '$forecastDate', $id, $monthlyCost)";
    if($result = $db->query($sql)){
       $msg .= "Monthly cost of $monthlyCost € for forecast date $forecastDate inserted successfully in table 'kr_forecastEngineering'<br>";
    }
   return $msg;
}