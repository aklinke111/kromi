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
//        echo $sql;
//        die();
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

}