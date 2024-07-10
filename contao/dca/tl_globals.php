<?php
// Define global parameters and specs
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_Globals'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
//        'onload_callback' => array
//        (
//                array('tl_country', 'country')
//        ),
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
            'fields' => ['var'],
            'flag' => 11,
            'panelLayout' => 'search,sort'
        ],
        'label' => [
            'fields' => ['var','val','category','note'],
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
        'var' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'val' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'category' => array
        (
                'inputType'               => 'select',
                'filter'                  => true,
                'search'                  => true,
                'foreignKey'              => "tl_hel_category.category",                                          
                'eval'                    => array('includeBlankOption'=>true,'tl_class'=>'w50 wizard'),
                'sql' => ['type' => 'string', 'length' => 3, 'default' => '']
        ),   
        'note' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 1000],
            'sql' => ['type' => 'string', 'length' => 1000, 'default' => '']
        ],
    ],
    'palettes' => [
        'default' => '{legend},var,val;category;note'
    ],
];

    