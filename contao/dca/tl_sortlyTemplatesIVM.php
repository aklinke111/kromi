<?php
// contao/dca/tl_sortlyTemplatesIVM.php

// !!!! DO NOT DELETE - PARENT-TABLE !!!!!!

use Contao\DataContainer;
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_sortlyTemplatesIVM'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'ctable' => ['tl_bom'],
        'enableVersioning' => true,
        'switchToEdit' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'tstamp' => 'index',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => DataContainer::MODE_SORTED,
            'fields' => ['name'],
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'panelLayout' => 'search,limit'
        ],
        'label' => [
            'fields' => ['name','note','sortlyId','quantity','quantityRaw','quantityForecast','quantityOverhaul','quantityReturn','quantityProjects','quantityAvailable','quantityMinimum','sid','pid'],
            'format' => '%s',
            'showColumns' => true,
        ],
        'operations' => [
            'edit' => [
                'href' => 'table=tl_bom',
                'icon' => 'children.svg',
            ],
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
        'sid' => [
           'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'enabled' => true],
            'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ],
        'pid' => [
           'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'enabled' => true],
            'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0]
        ],
        'name' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'enabled' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],        
        'sortlyId' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'enabled' => true],
            'sql' => ['type' => 'string', 'length' => 20, 'default' => '']
        ],
        'price' => [
            'inputType' => 'text',
            'search' => true,
            'sorting' => true,  
            'eval' => ['tl_class' => 'w50'],
            'sql' => "DECIMAL(10,2)",
        ],
        'priceHr' => [
            'inputType' => 'text',
            'search' => true,
            'sorting' => true,  
            'eval' => ['tl_class' => 'w50'],
            'sql' => "DECIMAL(10,2)",
        ],
        'photoUrl' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'enabled' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'photoName' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'enabled' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
       'quantityOverAll' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'enabled' => false],
            'sql' => "INT(10)",
        ],         
       'quantityOrdered' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'enabled' => false],
            'sql' => "INT(10)",
        ],         
       'quantity' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'enabled' => false],
            'sql' => "INT(10)",
        ],         
       'quantityRaw' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'enabled' => false],
            'sql' => "INT(10)",
        ], 
        'quantityAvailable' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'enabled' => false],
            'sql' => "INT(10)",
        ],        
        'quantityForecast' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'enabled' => true],
            'sql' => "INT(10)",
        ],
        'quantityOverhaul' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'enabled' => false],
            'sql' => "INT(10)",
        ],
        'quantityProjects' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'enabled' => false],
            'sql' => "INT(10)",
        ],
       'quantityReturn' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'enabled' => false],
            'sql' => "INT(10)",
        ], 
        'quantityMinimum' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'enabled' => true],
            'sql' => "INT(10)",
        ],
        'noteJedox' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'enabled' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'notnull' => false]
        ],
        'note' => [
            'inputType' => 'textarea',
            'eval' => ['tl_class' => 'clr', 'enabled' => true],
            'sql' => ['type' => 'text', 'notnull' => false]
        ],
        'created' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard','enabled' => true],
            'sql' => ['type' => 'string', 'length' => 50, 'notnull' => false]
        ],
    ],
    'palettes' => [
        'default' => '{sortlyTemplatesIVM},name,sortlyId;photoUrl,photoName;quantityForecast,quantityMinimum;created,sid,pid,note'
    ],
];