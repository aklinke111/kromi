<?php
// App\EventListener\DataContainer\MailFunctions.php

namespace App\EventListener\DataContainer;

use Contao\DataContainer;
use Contao\Database;
use Contao\DC_Table;

//use Contao\Automator;
//use Contao\Config;
//use Contao\Controller;
//use Contao\CoreBundle\Exception\AccessDeniedException;
//use Contao\CoreBundle\Framework\ContaoFramework;
//use Contao\Date;
//use Contao\Input;
//use Contao\PageModel;
//use Contao\System;
//use Doctrine\DBAL\Connection;
//use Doctrine\DBAL\Exception;
//use Oveleon\ContaoRecommendationBundle\Model\RecommendationArchiveModel;
//use Symfony\Component\HttpFoundation\Session\SessionInterface;
//use Symfony\Component\Security\Core\Security;

class MailFunctions
{
    public function sendMail(DataContainer $dc): void {
    //Prepare mail
    $mailTo = "aklinke111@gmail.com";
    $title = "Mail von c1.kromiag.de";
    $mailFrom = "From: Andreas Klinke <ak@kromiag.de>\r\n";
    $mailFrom .= "Reply-To: ak@kromiag.de\r\n";
    $mailFrom .= "Content-Type: text/html\r\n";
    $msg = "<b>Check from CONTAO C1</b>:\r\n\r\n";

    //Send mail
    mail($mailTo, $title, $msg, $mailFrom);
    }
    
    public function getSortlyJSON($url) {
        
        // Bearer token for authentication
        $token = 'sk_sortly_oCDxewcXoQSyWNxNohQ_';

        // Set up HTTP headers with Authorization header containing the bearer token
        $options = [
            'http' => [
                'header' => "Authorization: Bearer $token\r\n"
            ]
        ];

        // Create a stream context
        $context = stream_context_create($options);

        // Use file_get_contents with the created context to fetch data from the URL
        $response = file_get_contents($url, false, $context);
        //CHECK: echo $response;
        
        // Decode and return JSON
        $data = json_decode($response, true);
        return $data;
    }
    
    
        public function function_2(DataContainer $dc): void
    {
        // Return if there is no active record (override all)
        if (!$dc->activeRecord)
        {
            return;
        }
        var_dump($dc->activeRecord->test);
        die();
    }
    
}