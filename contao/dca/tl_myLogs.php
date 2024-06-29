<?php
// contao\dca\tl_myLogs.php

use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_myLogs'] = [
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
            'fields' => ['triggered','category','method'],
            'flag' => 6,
            'panelLayout' => 'search,limit,sort'
        ],
        'label' => [
            'fields' => ['text','category','method','triggered'],
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
        'text' => [
            'sorting' => true, 
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50','disabled' => true],
            'sql' => ['type' => 'text', 'length' => 1000, 'notnull' => false]
        ],
        'category' => [
            'sorting' => true,             
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'disabled' => true],
            'sql' => ['type' => 'string', 'length' => 20, 'notnull' => false]
        ],
        'method' => [
            'sorting' => true,             
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'disabled' => true],
            'sql' => ['type' => 'text', 'length' => 1000, 'notnull' => false]
        ],
        'triggered' => [
            'sorting' => true, 
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'disabled' => true],
            'sql' => ['type' => 'datetime', 'default' => 'CURRENT_TIMESTAMP']
        ],
    ],
    'palettes' => [
        'default' => '{legend},text,category,method,triggered'
    ],
];