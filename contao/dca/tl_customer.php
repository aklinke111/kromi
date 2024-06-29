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
            'fields' => ['customer'],
            'flag' => 1,
            'panelLayout' => 'search,limit,sort'
        ],
        'label' => [
            'fields' => ['customer','customerNo','sid','city','street','active'],
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
        'customer' => [
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
//        'subsidiaryId' => array
//        (
//                'sorting' => true,
//                'inputType'               => 'select',
//                'filter'                  => true,
//                'search'                  => true,
//                //'options_callback'        => array('tl_hel_toolcentersPlus', 'ktcId'),                      
//                'foreignKey'              => "tl_sortly_subsidiary.name",                                          
//                'eval'                    => array('includeBlankOption'=>true,'tl_class'=>'w50 wizard'),
//                'sql' => ['type' => 'string', 'length' => 3, 'default' => '']
//        ), 
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
        'default' => '{customer_legend},sid,customer,customerNo;street,houseNo,zipcode,city;contact,email,telephone;active;note'
    ],
];