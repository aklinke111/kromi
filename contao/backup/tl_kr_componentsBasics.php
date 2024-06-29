<?php
// contao/dca/tl_kr_componentsBasics.php
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_kr_componentsBasics'] = [
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
            'fields' => ['model'],
            'flag' => 11,
            'panelLayout' => 'search,limit'
        ],
        'label' => [
            'fields' => ['model', 'description', 'purchasePrice','salesPrice', 'minimumStock', 'note'],
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
        'model' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 10, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 50, 'default' => '']
        ],
        'description' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'maxlength' => 255],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'photoUrl' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'maxlength' => 255],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'photoName' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'maxlength' => 255],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'purchasePrice' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'mandatory' => true],
            'sql' => "DECIMAL(10,2)",
        ],
        'salesPrice' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'mandatory' => true],
            'sql' => "DECIMAL(10,2)",
        ],
        'minimumStock' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'mandatory' => true],
            'sql' => "INT(10)",
        ],
        'note' => [
            'inputType' => 'textarea',
            'eval' => ['tl_class' => 'clr', 'mandatory' => false],
            'sql' => ['type' => 'text', 'notnull' => false]
        ],
    ],
    'palettes' => [
        'default' => '{componentsBasics_legend},model,description;purchasePrice,salesPrice;sortlyPictureName,sortlyPictureUrl;minimumStock,note'
    ],
];