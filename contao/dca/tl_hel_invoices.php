<?php
// contao/dca/tl_hel_.php
use Contao\DC_Table;
use App\EventListener\DataContainer\MyFunctions;

$GLOBALS['TL_DCA']['tl_hel_invoices'] = [
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
            'fields' => ['supplierId'],
            'flag' => 8,
            'panelLayout' => 'search,limit,sort'
        ],
        'label' => [
            'fields' => ['supplierId','invoiceNo','invoiceDate','payment','totalKTCs','brazilianKTCs','categoryId','note'],
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
        'invoiceNo' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'maxlength' => 255, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 50, 'default' => '']
        ],
        'invoiceDate' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25 wizard', 'maxlength' => 255, 'datepicker' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'supplierId' => [
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => true,           
            'foreignKey'              => 'tl_supplier.name',
            'eval'                    => array('includeBlankOption'=>true,'tl_class'=>'w50 wizard'),            
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0]
        ],        
        'payment' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'mandatory' => false],
            'sql' => "DECIMAL(10,2)",
        ],
        'totalKTCs' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'mandatory' => false],
        'sql' => ['type' => 'integer', 'notnull' => false, 'unsigned' => false],
        ],
        'brazilianKTCs' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'mandatory' => false],
        'sql' => ['type' => 'integer', 'notnull' => false, 'unsigned' => false],
        ],  
        'categoryId' => array
        (
                'label'                   => &$GLOBALS['TL_LANG']['tl_hel_category']['category'],
                'inputType'               => 'select',
                'filter'                  => true,
                'search'                  => true,
                'foreignKey'              => 'tl_hel_category.category',                                                        
                'eval'                    => array('includeBlankOption'=>true,'tl_class'=>'w50 wizard'),
                'sql' => ['type' => 'integer', 'length' => 10, 'default' => 0]
        ), 
        'exclude' => [
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
        'default' => '{legend},supplierId;invoiceNo,invoiceDate;totalKTCs,brazilianKTCs;categoryId,payment;note'
    ],
];