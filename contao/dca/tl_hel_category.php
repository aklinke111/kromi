<?php
// contao/dca/tl_hel_category.php
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_hel_category'] = [
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
            'flag' => 11,
            'panelLayout' => 'search,limit'
        ],
        'label' => [
            'fields' => ['name', 'note'],
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
        'name' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 100, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 100, 'default' => '']
        ],       
        'note' => [
            'inputType' => 'textarea',
            'eval' => ['tl_class' => 'clr', 'rte' => 'tinyMCE', 'mandatory' => false],
            'sql' => ['type' => 'text', 'notnull' => false]
        ],
    ],
    'palettes' => [
        'default' => '{category_legend},name,note'
    ],
];