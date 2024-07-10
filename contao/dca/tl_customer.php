<?php

use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_customer'] = [
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
            'fields' => ['name'],
            'flag' => 1,
            'panelLayout' => 'search,limit,sort'
        ],
        'label' => [
            'fields' => ['name','customerNo','sid','city','street','active'],
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
        'sid' => [
            'sorting' => true,            
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 10],
            'sql' => ['type' => 'string', 'length' => 10,'default' => '']
        ],        
        'name' => [
            'sorting' => true,            
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 100, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 100, 'default' => '']
        ],
        'customerNo' => [
            'sorting' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 10, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ], 
        'street' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 50, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 50, 'default' => '']
        ], 
        'houseNo' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 10, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ],
        'zipcode' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 10, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ],  
        'city' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 50, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 50, 'default' => '']
        ], 
        'contact' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 100, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 100, 'default' => '']
        ],
        'email' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 50, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 50, 'default' => '']
        ],  
        'telephone' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 50, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 50, 'default' => '']
        ], 
        'active' => [
            'sorting' => true,
            'inputType' => 'checkbox',
            'sql' => ['type' => 'boolean','default' => false]
        ],
        'note' => [
            'inputType' => 'textarea',
            'eval' => ['tl_class' => 'clr', 'rte' => 'tinyMCE', 'mandatory' => false],
            'sql' => ['type' => 'text', 'notnull' => false]
        ],
    ],
    'palettes' => [
        'default' => '{customer_legend},sid,name,customerNo;street,houseNo,zipcode,city;contact,email,telephone;active;note'
    ],
];