<?php

// src/EventListener/DataContainer/MemberDeleteCallbackListener.php
namespace App\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Doctrine\DBAL\Connection;

#[AsCallback(table: 'tl_orders', target: 'config.onsubmit')]
class MemberDeleteCallbackListener
{
    /** @var Connection */
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function __invoke(DataContainer $dc): void
    {
        if (!$dc->id) {
            return;}
        else{
            
//            $records = $this->connection->fetchAllAssociative("SELECT * FROM tl_foobar");
            
//            $articleNo = $this->db->fetchOne("SELECT articleNo FROM tl_orders WHERE id = ?", [$dc->id]);
//            $itemName = $this->db->fetchOne("SELECT name FROM tl_orders WHERE id = ?", [$dc->id]);
          
//            $sql = "Update tl_orders SET articleNo itemName = ". WHERE";
//            $result = $this->Database->prepare($sql)
//            ->execute();
            echo $dc->activeRecord->id;
            die();
//        echo $articleNo = $this->db->fetchOne("SELECT articleNo FROM tl_orders WHERE id = ?", [$dc->id]); 
        //die();
            
//        $sql = "SELECT * FROM tl_orders WHERE id = ".$dc->id;      
//        $result = $this->db->prepare($sql)
//                                 ->execute();
//        while($result->next())
//        {
//                echo $result->articleNo;
//        }

            
//            $sql = "SELECT articleNo FROM tl_orders WHERE id = ".$dc->id;  
//            //$articleNo = $this->db->fetchOne("SELECT articleNo FROM tl_orders WHERE id = ?", $dc->id);
//            echo $msg = "SQL: ".$sql."\r\n<p>articleNo: ".$articleNo."";
//            die(); 
        }
    }
}
