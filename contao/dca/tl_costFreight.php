<?php
// contao/dca/tl_costFreight.php
use Contao\DC_Table;

use App\EventListener\DataContainer\SortlyFunctions;
use App\EventListener\DataContainer\UpdateSortly;
use App\EventListener\DataContainer\MyFunctions;
$GLOBALS['TL_DCA']['tl_costFreight'] = [
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
            'fields' => ['costcenter','projectDescription','freightDescription','departure','destination','price','invoiceDate','invoiceNo','delivered', 'exclude','note'],
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
        'projectDescription' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 100, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 100, 'default' => '']
        ],            
        'freightDescription' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 100, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 100, 'default' => '']
        ], 
        'departure' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 100, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 100, 'default' => '']
        ], 
        'destination' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 100, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 100, 'default' => '']
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
        'price' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'mandatory' => false],
            'sql' => "DECIMAL(10,2)",
        ],
        'invoiceNo' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 50, 'default' => '']
        ],
        'invoiceDate' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'maxlength' => 255, 'datepicker' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],  
        'exclude' => [
            'search' => true,
            'sorting' => true,  
            'inputType' => 'checkbox',
            'sql' => ['type' => 'boolean','default' => false]
        ],     
        'delivered' => [
            'search' => true,
            'sorting' => true,  
            'inputType' => 'checkbox',
            'sql' => ['type' => 'boolean','default' => false]
        ],
        'note' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ]
    ],
    'palettes' => [
        'default' => '{legend},projectDescription,freightDescription,costcenter;departure,destination;invoiceNo,invoiceDate;price,note;exclude,delivered'
    ],
];