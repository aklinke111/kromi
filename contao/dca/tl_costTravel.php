<?php
// contao/dca/tl_costTravel.php
use Contao\DC_Table;
use Contao\Backend;
use Contao\Database;
use Contao\Input;

use App\EventListener\DataContainer\MyFunctions;

$GLOBALS['TL_DCA']['tl_costTravel'] = [
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
            'mode' => 2,
            'fields' => ['receiptNo'],
            'flag' => 8,
            'panelLayout' => 'search,limit,sort'
        ],
        'label' => [
            'fields' => ['receiptNo','receiptDate','costcenter','travelReason','category','payment','employee','note'],
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
        'receiptNo' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'maxlength' => 10, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ],
        'receiptDate' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25 wizard', 'maxlength' => 50, 'datepicker' => true],
            'sql' => ['type' => 'string', 'length' => 50, 'default' => '']
        ],
        'costcenter' => array
        (
                'sorting' => true,
                'inputType'               => 'select',
                'filter'                  => true,
                'search'                  => true,
                'options_callback' => [
                    MyFunctions::class, 'costUnit'
                ],                                         
                'eval'                    => array('includeBlankOption'=>true,'tl_class'=>'w50 wizard'),
                'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ), 
        'travelReason' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'category' => array
        (
                'inputType'               => 'select',
                'filter'                  => true,
                'search'                  => true,
                'foreignKey'              => "tl_costTravelCategory.name",                                          
                'eval'                    => array('includeBlankOption'=>true,'tl_class'=>'w50 wizard'),
                'sql' => ['type' => 'string', 'length' => 3, 'default' => '']
        ), 
        'employee' => array
        (
                'inputType'               => 'select',
                'filter'                  => true,
                'search'                  => true,     
                'sorting'                 => true,
                'foreignKey'              => "tl_member.CONCAT(lastname,', ',firstname)",                                       
                'eval'                    => array('includeBlankOption'=>true,'tl_class'=>'w50 wizard'),
                'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ),
        'payment' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'mandatory' => false],
            'sql' => "DECIMAL(10,2)",
        ],
        'note' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ]
    ],
    'palettes' => [
        'default' => '{legend},receiptNo,receiptDate,costcenter;travelReason,category;employee,payment;note'
    ],
];