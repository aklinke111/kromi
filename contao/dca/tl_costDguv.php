<?php
// contao/dca/tl_costDguv.php
use Contao\DC_Table;

use App\EventListener\DataContainer\MyFunctions;

$GLOBALS['TL_DCA']['tl_costDguv'] = [
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
            'fields' => ['id'],
            'flag' => 8,
            'panelLayout' => 'search,limit,sort'
        ],
        'label' => [
            'fields' => ['customerSid','invoiceNo','invoiceDate','payment','payment','note'],
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
        'customerSid' => [
            'inputType' => 'select',
            'filter'                  => true,
            'search'                  => true,
            'sorting'                  => true,
            'options_callback' => [
                MyFunctions::class, 'customerSid'
            ],             
            'eval' => ['tl_class' => 'w50 wizard', 'enabled' => false],
            'sql' => ['type' => 'string', 'length' => 20, 'default' => '']
        ],
        'totalKtcs' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'mandatory' => true],
            'sql' => ['type' => 'integer', 'notnull' => false, 'unsigned' => true],
        ],
        'totalDevices' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'mandatory' => true],
            'sql' => ['type' => 'integer', 'notnull' => false, 'unsigned' => true],
        ],
        'tripFare' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'mandatory' => true],
            'sql' => ['type' => 'integer', 'notnull' => false, 'unsigned' => true],
        ],        
        'invoiceNo' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'maxlength' => 255, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 50, 'default' => '']
        ],
        'invoiceDate' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25 wizard', 'maxlength' => 255, 'datepicker' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
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
        'default' => '{legend},customerSid;totalKtcs,totalDevices,tripFare;invoiceNo,invoiceDate;payment;note'
    ],
];