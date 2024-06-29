<?php
// contao\dca\tl_diverse.php

use Contao\DC_Table;
use Contao\Backend;
use Contao\DataContainer;

use App\EventListener\DataContainer\SortlyFunctions;
use App\EventListener\DataContainer\MyFunctions;
use App\EventListener\DataContainer\MailFunctions;

$GLOBALS['TL_DCA']['tl_diverse'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'onsubmit_callback' => [
//            [MailFunctions::class, 'sendMail'],
            [SortlyFunctions::class, 'SortlyItemsToTable']
        ],
        'switchToEdit' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => 2,
            'fields' => ['test'],
            'flag' => 8,
            'panelLayout' => 'search,limit,sort'
        ],
        'label' => [
            'fields' => ['test'],
            'format' => '%s',
            'showColumns' => true,
        ],
        'operations' => [
            'editheader' => [
                'href' => 'act=edit',
                'icon' => 'header.svg',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg'
            ],
        ],
    ],
    
    'fields' => [
        'id' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true],
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0]
        ],
        'test' => array
        (
             'inputType'        => 'checkbox',
             'save_callback'    => [
//            [SortlyFunctions::class, 'SortlyItemsToTable'],
//            [MyFunctions::class, 'function_1']
        ],                                                      
             'eval'             => array('includeBlankOption'=>false,'tl_class'=>'w50 wizard'),
             'sql'              => ['type' => 'string', 'length' => 10, 'default' => '']
        ), 
    ],
    'palettes' => [
        'default' => '{legend},test'
    ],
];

class tl_diverse extends Backend
{
      
     public function articleNoToArray()
    {

        $value = array();        
        $result = $this->Database->prepare("SELECT DISTINCT kromiArticleNo, id, name FROM tl_sortly ORDER BY kromiArticleNo")
                                 ->execute();
        while($result->next())
        {
                $value[$result->kromiArticleNo] = $result->kromiArticleNo.' --- '.$result->name;
        }
        
        var_dump($value);
        die();
    } 
    
}