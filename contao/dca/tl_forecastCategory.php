<?php
// contao/dca/tl_forecastCategory.php
use Contao\DC_Table;

use App\EventListener\DataContainer\SortlyFunctions;
use App\EventListener\DataContainer\UpdateSortly;
use App\EventListener\DataContainer\MyFunctions;


$GLOBALS['TL_DCA']['tl_forecastCategory'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'switchToEdit' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => 1,
            'fields' => ['positionNo', 'costcenter', 'category'],
            'flag' => 11,
            'panelLayout' => 'search,limit'
        ],
        'label' => [
            'fields' => ['id', 'positionNo', 'costcenter', 'category', 'function', 'note'],
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
        'category' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 100, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 100, 'default' => '']
        ],  
        'positionNo' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'enabled' => true],
            'sql' => "INT(10)",
        ],            
        'costcenter' => array
        (
                'sorting' => true,
                'inputType'               => 'select',
                'filter'                  => true,
                'search'                  => true,
                'options_callback' => [
                    MyFunctions::class, 'costcenter'
                ],                                         
                'eval'                    => array('includeBlankOption'=>true,'tl_class'=>'w50 wizard'),
                'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ), 
        'function' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],           
        'note' => [
            'inputType' => 'textarea',
            'eval' => ['tl_class' => 'clr', 'rte' => 'tinyMCE', 'mandatory' => false],
            'sql' => ['type' => 'text', 'notnull' => false]
        ],
    ],
    'palettes' => [
        'default' => '{legend}, positionNo, category, costcenter , function, note'
    ],
];