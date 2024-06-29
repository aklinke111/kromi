<?php
// contao/dca/tl_sortly.php
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_sortly'] = [
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
            'panelLayout' => 'search,limit'
        ],
        'label' => [
            'fields' => ['supplier', 'name', 'kromiArticleNo','customerNo','supplierArticleNo', 'sid','pid', 'type', 'notes'],
            'format' => '%s',
            'showColumns' => true,
        ],
        'operations' => [
            'edit' => [
                'href' => 'act=edit',
                'icon' => 'edit.svg',
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
            'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ],
        'pid' => [
            'sql' => ['type' => 'string', 'length' => 10, 'notnull' => false]
        ],
        'sortlyId' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 20, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 20, 'notnull' => false]
        ],
        'name' => [
            'search' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'notnull' => false]
        ],
        'price' => [
            'inputType' => 'text',
            'search' => true,
            'sorting' => true,  
            'eval' => ['tl_class' => 'w50', 'mandatory' => true],
            'sql' => "DECIMAL(10,4)",
        ],
        'min_quantity' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'mandatory' => true],
        'sql' => ['type' => 'integer', 'notnull' => false, 'unsigned' => true],
        ],
        'quantity' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'mandatory' => true],
        'sql' => ['type' => 'integer', 'notnull' => false, 'unsigned' => true],
        ],
        'notes' => [
            'inputType' => 'textarea',
            'eval' => ['tl_class' => 'clr', 'rte' => 'tinyMCE', 'mandatory' => false],
            'sql' => ['type' => 'text', 'notnull' => false]
        ],
        'type' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 20, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 20, 'notnull' => false]
        ],
        
        
        // -------------- attributes -------------
        
        'packageUnit' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 10, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 10, 'notnull' => false]
        ],
        'supplierArticleNo' => [
            'search' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 100, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 100, 'notnull' => false]
        ],
        'ean' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 100, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 100, 'notnull' => false]
        ],
        'supplier' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'notnull' => false]
        ],
         'serialNo' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 50, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 50, 'notnull' => false]
        ],
        'inventoryNo' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 50, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 50, 'notnull' => false]
        ],
        'kromiArticleNo' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 10, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 10, 'notnull' => false]
        ],
        'DGUV3No' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 15, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 15, 'notnull' => false]
        ],
        'storageLocation' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 100, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 100, 'notnull' => false]
        ],

        
        'technicalSpecification' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'notnull' => false]
        ],
        'CEdeclaration' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'notnull' => false]
        ],
        
        

        'DGUV3' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'maxlength' => 50, 'datepicker' => true],
            'sql' => ['type' => 'string', 'length' => 50, 'notnull' => false]
        ],
        'built' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'maxlength' => 50, 'datepicker' => true],
            'sql' => ['type' => 'string', 'length' => 50, 'notnull' => false]
        ],
        'overhaul' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'maxlength' => 50, 'datepicker' => true],
            'sql' => ['type' => 'string', 'length' => 50, 'notnull' => false]
        ],
        'created' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'maxlength' => 50, 'datepicker' => true],
            'sql' => ['type' => 'string', 'length' => 50, 'notnull' => false]
        ],

        
        'reserved' => [
            'search' => true,
            'sorting' => true,  
            'inputType' => 'checkbox',
            'sql' => ['type' => 'boolean']
//            'sql' => ['type' => 'string','length' => 10, 'default' => false]
        ],
        'discontinued' => [
            'search' => true,
            'sorting' => true,  
            'inputType' => 'checkbox',
            'sql' => ['type' => 'boolean']
//            'sql' => ['type' => 'string','length' => 10, 'default' => false]
        ],
        'active' => [
            'search' => true,
            'sorting' => true,  
            'inputType' => 'checkbox',
//            'sql' => ['type' => 'string','length' => 10, 'default' => false]           
            'sql' => ['type' => 'boolean']
        ],  
        'available' => [
            'search' => true,
            'sorting' => true,  
            'inputType' => 'checkbox',
//            'sql' => ['type' => 'string','length' => 10, 'default' => false]           
            'sql' => ['type' => 'boolean']
        ],         
        'IVM' => [
            'search' => true,
            'sorting' => true,  
            'inputType' => 'checkbox',        
            'sql' => ['type' => 'boolean']
        ],
        'criticalSourcing' => [
            'search' => true,
            'sorting' => true,  
            'inputType' => 'checkbox',        
            'sql' => ['type' => 'boolean']
        ],  
        
        
        'fieldbus' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 100, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 100, 'notnull' => false]
        ],
        
//        'supplierId' => [
//            'search' => true,
//            'sorting' => true,
//            'inputType' => 'select',
//            'foreignKey' => 'tl_supplier.name', // Will use `name` as label, and the vendor `id` as value
//            'eval' => ['chosen' => true, 'tl_class' => 'w50 wizard'], // Adds a search box to filter the options
//            'sql' => ['type' => 'string', 'length' => 10, 'notnull' => false]
//        ],
        
                // -------------- photos -------------
        'photoName' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'notnull' => false]
        ],        
        'photoUrl' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'notnull' => false]
        ],
                // -------------- tags -------------
        'tags' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'notnull' => false]
        ], 

    ],
    'palettes' => [
        'default' => '{sortly_legend},sid,pid,sortlyId,name;price,min_quantity,quantity;notes,type;'
        . 'packageUnit,supplierArticleNo,ean, supplier,serialNo,inventoryNo,kromiArticleNo,DGUV3No,storageLocation;DGUV3,built,overhaul;'
        . 'reserved,discontinued,active,available,IVM,criticalSourcing;fieldbus,photoName,photoUrl;tags'
    ],
];