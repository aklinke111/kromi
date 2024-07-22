<?php
// App\EventListener\DataContainer\MyFunctions.php

namespace App\EventListener\DataContainer;

use Contao\DataContainer;
use Contao\Database;
use Contao\DC_Table;

class MyFunctions
{
    // write log entry in tl_myLogs
    public static function log($text,$category,$method)
    {
        // insert new log entry
        $db = Database::getInstance();
        $text = str_replace("'", "\'", $text);
        $sql = "INSERT INTO tl_myLogs (tstamp,text,category,method) VALUES ('".time()."','$text','$category','$method')";
        $db->prepare($sql)
            ->execute();
    }

    public static function truncateTable($tableName){
        //Empty table 
        $db = Database::getInstance();
        
        $sql = "Truncate table $tableName";
        $db->prepare($sql)->execute(); 
        
        //log
        $text = "SQL: ".$sql;
        $category = "DATA";
        MyFunctions::log($text, $category, __METHOD__);
   }
    
    public static function function_1(DataContainer $dc): void
    {
        if (!$dc->activeRecord)
        {
            return;
        }
        var_dump($dc->activeRecord->test);
        die();
    }
    
 
    
    public function category(DataContainer $dc)
    {
        $db = Database::getInstance();

        if ($dc->activeRecord) {
            // Get the table name
            $tableName = $dc->table;
        }
        
    switch ($tableName) {
    case 'tl_hel_invoices':
        $pid = 6;
        break;
    case 1:
//        echo "i ist gleich 1";
        break;
    case 2:
//        echo "i ist gleich 2";
        break;
}    
        
        $value = array();        
        $result = $db->prepare("SELECT * FROM tl_categorySub Where pid = $pid Order BY name")
                                 ->execute();
        while($result->next())
        {
                $value[$result->id] = $result->name;
        }
        return $value;
    }
    
    public function costcenter()
    {
        $db = Database::getInstance();
        $value = array();        
        $result = $db->prepare("SELECT * FROM tl_costcenter ORDER BY costcenter")
                                 ->execute();
        while($result->next())
        {
                $value[$result->costcenter] = $result->costcenter." ".$result->description;
        }
        return $value;
    }
    
    
    
    public function ktcId()
    {
        $db = Database::getInstance();
        $value = array();   
        
        $sql = "Select
                    sortly_ktc.name As ktc,
                    sortly_customer.name As customer
                From
                    sortly_customer Inner Join
                    sortly_ktc On sortly_customer.sid = sortly_ktc.pid
                Where
                    sortly_ktc.name Like 'KTC-%'
                Order By
                    ktc";
        
        $result = $db->prepare($sql)->execute();
            while($result->next())
            {
                $value[$result->ktc] = $result->ktc.' - '.$result->customer;
            }
        return $value;
    }
    
    
        public function customerSid()
    {
        $db = Database::getInstance();
        $value = array();   
        
        $sql = "Select
                sortly_customer.name As customer,
                sortly_subsidiary.name As subsidiary,
                sortly_customer.sid                
            From
                sortly_customer Inner Join
                sortly_subsidiary On sortly_subsidiary.sid = sortly_customer.pid Inner Join
                sortly_country On sortly_country.sid = sortly_subsidiary.pid
            Where
                sortly_country.name Like 'Germany'
            Order By
                customer";
        
        $result = $db->prepare($sql)->execute();
            while($result->next())
            {
                $value[$result->sid] = $result->customer.' - '.$result->subsidiary;
            }
        return $value;
    }
    
    
    
}