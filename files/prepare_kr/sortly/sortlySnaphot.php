<?php

include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/src/functions/mail.php";
include_once $_SERVER['DOCUMENT_ROOT']."/files/prepare_kr/db/dbConfig.php";

if (isset($_GET['webhookFunction'])) {

    $function = $_GET['webhookFunction'];
    if($function == "sortlySnaphot"){
        echo sortlySnapshot($db);
    }
}

function sortlySnapshot($db){


    
    $sql = "INSERT INTO sortlySnapshot
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
            tags,
            created
            ) 
            SELECT
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
            tags,
            NOW()
            FROM sortly";
           

    // Execute the query
    $result = $db->query($sql);
    
    $msg = "";
    if ($result) {
        //Count inserted datasets
        $sql = "SELECT sid FROM sortlySnapshot WHERE created LIKE NOW()";
        $count = $db->query($sql)->num_rows;
        $msg.= "$count Records inserted successfully in table 'sortlySnapshot'";
    } else {
        $msg.= "Error: " . $sql . "<br>" . $db->error;
    }

    // Send mail   
    return $msg;
//    echo sendMail($msg);
}