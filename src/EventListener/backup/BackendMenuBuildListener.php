<?php
// src/EventListener/BackendMenuBuildListener.php
namespace App\EventListener;

use Contao\CoreBundle\Event\ContaoCoreEvents;
use Contao\CoreBundle\Event\MenuEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(ContaoCoreEvents::BACKEND_MENU_BUILD)]
class BackendMenuBuildListener
{
    public function __invoke(MenuEvent $event): void
    {
        
    //Prepare mail
    $mailTo = "aklinke111@gmail.com";
    $title = "Mail von KromiAG.de";
    $mailFrom = "From: Andreas Klinke <ak@kromiag.de>\r\n";
    $mailFrom .= "Reply-To:  ak@kromiag.de\r\n";
    $mailFrom .= "Content-Type: text/html\r\n";
    $msg = "<b>BackendMenuBuildListener/b>:\r\n\r\n";

    //Send mail
    //mail($mailTo, $title, $msg, $mailFrom);
    
//        echo "<b>Initialize system</b>:\r\n\r\n";
//        die();
    }
}
