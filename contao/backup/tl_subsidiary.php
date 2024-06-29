<?php
// Sortly Level 2
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_subsidiary'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
//        'onload_callback' => array
//        (
//                array('tl_subsidiary', 'country')
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
            'fields' => ['name'],
            'flag' => 11,
            'panelLayout' => 'search,limit,sort'
        ],
        'label' => [
            'fields' => ['name','sid','note'],
            'format' => '%s',
            'showColumns' => true,
        ],
        'operations' => [
//            'edit' => [
//                'href' => 'table=tl_subsidiary',
//                'icon' => 'edit.svg',
//            ],
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
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 20, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 20, 'default' => '']
        ],
        'name' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 100, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 100, 'default' => '']
        ],
        'note' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 1000],
            'sql' => ['type' => 'string', 'length' => 1000, 'default' => '']
        ],
    ],
    'palettes' => [
        'default' => '{legend},name,sid;note'
    ],
];

    