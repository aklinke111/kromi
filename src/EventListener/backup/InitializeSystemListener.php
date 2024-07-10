<?php

// src/EventListener/InitializeSystemListener.php
namespace src\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;

#[AsHook('initializeSystem')]
class InitializeSystemListener
{
    public function __invoke(): void
    {
//    //Prepare mail
//    $mailTo = "aklinke111@gmail.com";
//    $title = "Mail von KromiAG.de";
//    $mailFrom = "From: Andreas Klinke <ak@kromiag.de>\r\n";
//    $mailFrom .= "Reply-To:  ak@kromiag.de\r\n";
//    $mailFrom .= "Content-Type: text/html\r\n";
//    $msg = "<b>Initialize system</b>:\r\n\r\n";
//
//    //Send mail
//    mail($mailTo, $title, $msg, $mailFrom);
//    
////        echo "<b>Initialize system</b>:\r\n\r\n";
////        die();
    }
}