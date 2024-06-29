<?php

// src/EventListener/DataContainer/EditButtonsCallbackListener.php
namespace App\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;

#[AsCallback(table: 'tl_orders', target: 'edit.buttons')]
class EditButtonsCallbackListener
{
    public function __invoke(array $buttons, DataContainer $dc): array
    {
        // Remove the "Save and close" button
        unset($buttons['saveNclose']);
        
//        echo $id = $dc->id.' Mist!';
//        die();
        
        return $buttons;
    }
}
