<?php

// src/Controller/MyController.php
namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MyController
{
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function myAction(Request $request): Response
    {
        $dbResult = $this->db->executeQuery("SELECT * FROM tl_log")->fetchAll();
        var_dump($dbResult);
        // …
    }
} 