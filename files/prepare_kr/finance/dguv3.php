<?php

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/_includes.php";


function forcastDGUV3($db, $id, $forecastDate, $forecastPeriod ){
    
    $msg = "";
    $totalCost = 0;
  
    echo $Period_Passed_Installations = globalVal($db, 'Period_Passed_Installations');

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
        }

        // 1. step - calculating 
        $totalDevices = $CountInstalledIVM / $Period_Passed_Installations;
        
        $totalCost += round($totalDevices,0) * globalVal($db, 'DGUV3_PricePerIVM');
        
        $msg .= "Count of passed German installations: ".$CountInstalledIVM." leads to an average of ".$totalCost." €<br>";
        $msg .= $totalCost." € total cost for DGUV3 at 1st step<br>";

    // Lookup passed DGUV checks and add 48 months to each IVM
    $DGUV3_Period = globalVal($db, 'DGUV3_Period');
//    $DGUV3_Period = 15;
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
        $Count_IVM = round($row['Count_IVM'],0);
//        $msg .= $nextDGUV3." --- ".$Count_IVM."<br>"; 
        if($nextDGUV3 == $forecastDate){
            
            $totalDevices += round($Count_IVM,1);
            $costs = $totalDevices * globalVal($db, 'DGUV3_PricePerIVM');
            $totalCost += $costs;
            
            $msg .= "Count of DGUV3 checks must be done during  forecast-month ".$nextDGUV3.": ".$Count_IVM." <br>"; 
            $msg .= round($costs,2)." € cost for DGUV3 at 2nd step<br>";
        }
        
    }
    
    // approached KTCs
    $sql = "Select
        Count(sortly.active) As Count_IVM,
        Count(Distinct sortly_ktc.name) As Count_KTC
    From
        sortly Inner Join
        sortly_ktc On sortly_ktc.sid = sortly.pid
    Where
        sortly.DGUV3No Not Like '' ";
    $result = $db->query($sql);
    while ($row = $result->fetch_assoc()) {
        $Count_IVM = $row['Count_IVM'];
        $Count_KTC = $row['Count_KTC'];
        $quote = $Count_KTC / $Count_IVM ;

        // 
        $approachedKtc = round($totalDevices * $quote,1);
        $approachingCost = round($approachedKtc * globalVal($db, 'DGUV3_PricePerApproach'));
        
        $msg .= "Approached ".$approachedKtc." KTCs for total approaching costs of ".$approachingCost." € for ".round($totalDevices,0)." IVMs <br>";
        
        $totalCost += $approachingCost;
        
        $msg .= round($approachingCost,2)." € total cost for DGUV3 at 3d step<p>";
    }
    
    $totalCost =  round($totalCost,2);
    $totalCost = number_format($totalCost,2);
    $totalCost *= (1);

    $msg .= "TOTAL monthly cost for DGUV3: $totalCost €";
            
    // Insert
    $sql = "INSERT INTO kr_forecastEngineering (tstamp, forecastDate, categoryId, cost) VALUES (".time().", '$forecastDate', $id, $totalCost)";
    if($result = $db->query($sql)){
        $msg .= "Monthly cost DGUV3 of $totalCost € for forecast date $forecastDate and forecast period of $forecastPeriod months inserted successfully in table 'kr_forecastEngineering'<br>";
    }

    $msg .= "<p>";
    
    return $msg;
}