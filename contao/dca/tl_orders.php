<?php
// contao/dca/tl_orders.php
use Contao\DC_Table;
use Contao\Backend;
use Contao\Database;
use Contao\Input;

use App\EventListener\DataContainer\SortlyFunctions;
use App\EventListener\DataContainer\UpdateSortly;
use App\EventListener\DataContainer\MyFunctions;

$GLOBALS['TL_DCA']['tl_orders'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'onsubmit_callback' => [
            [UpdateSortly::class, 'updatePrice']
        ],        
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
            'mode' => 2,
            'fields' => ['supplierId'],
            'flag' => 8,
            'panelLayout' => 'search,limit,sort'
        ],
        'label' => [
            'fields' => ['supplierId','sortlyId','invoiceNoDMS','invoiceDate','supplierArticleNo','orderQuantity', 'price','discount','surcharge','vat','orderDate', 'estimatedDeliveryDate', 'delivered', 'note'],
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
        'supplierId' => [
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => true, 
            'sorting'                 => true,            
            'foreignKey'              => 'tl_supplier.name',
            'eval'                    => array('includeBlankOption'=>true,'tl_class'=>'w50 wizard'),            
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0]
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0]
        ],
        'sortlyId' => [
            'inputType' => 'select',
            'filter'                  => true,
            'search'                  => true,
            'sorting'                  => true,
            'options_callback' => [
                SortlyFunctions::class, 'sortlyId'
            ],             
            'eval' => ['tl_class' => 'w50 wizard', 'enabled' => false],
            'sql' => ['type' => 'string', 'length' => 20, 'default' => '']
        ],
        'supplierArticleNo' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'maxlength' => 100],
            'sql' => ['type' => 'string', 'length' => 100, 'default' => '']
        ],
        'orderQuantity' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'mandatory' => true],
            'sql' => ['type' => 'integer', 'notnull' => false, 'unsigned' => true],
        ],
        'packageUnit' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'mandatory' => true],
            'sql' => ['type' => 'integer', 'notnull' => false, 'unsigned' => true, 'default' => '1'],
        ],
        'orderDate' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'maxlength' => 50, 'datepicker' => true],
            'sql' => ['type' => 'string', 'length' => 50, 'default' => '']
        ],
        'estimatedDeliveryDate' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'maxlength' => 50, 'datepicker' => true],
            'sql' => ['type' => 'string', 'length' => 50, 'default' => '']
        ],        
        'invoiceDate' => [
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'maxlength' => 50, 'datepicker' => true],
            'sql' => ['type' => 'string', 'length' => 50, 'default' => '']
        ],
        'invoiceNoDMS' => [
            'sorting' => true,            
            'inputType' => 'text',
            'search' => true,
            'sorting' => true,            
            'eval' => ['tl_class' => 'w50 wizard', 'maxlength' => 50],
            'sql' => ['type' => 'string', 'length' => 50, 'default' => '']
        ],
        'price' => [
            'inputType' => 'text',
            'search' => true,
            'sorting' => true,  
            'eval' => ['tl_class' => 'w25', 'mandatory' => true],
            'sql' => "DECIMAL(10,4)",
        ],
        'priceUpdate' => [
            'search' => true,
            'sorting' => true,  
            'inputType' => 'checkbox',
            'sql' => ['type' => 'boolean','default' => true]
        ],          
        'discount' => [
            'inputType' => 'text',
            'search' => true,
            'sorting' => true,  
            'eval' => ['tl_class' => 'w50', 'mandatory' => false],
            'sql'  => "DECIMAL(10,2) NOT NULL default '0.00'"
        ],
        'surcharge' => [
            'inputType' => 'text',
            'search' => true,
            'sorting' => true,  
            'eval' => ['tl_class' => 'w50', 'mandatory' => false],
            'sql'  => "DECIMAL(10,2) NOT NULL default '0.00'"
        ],        
        'internalExternal' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_orders']['internalExternal'],
            'inputType' => 'radio',
            'options'   => array('internal', 'external'),
            'eval'      => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'       => "varchar(32) NOT NULL default 'external'"
        ),
        'delivered' => [
            'search' => true,
            'sorting' => true,  
            'inputType' => 'checkbox',
            'sql' => ['type' => 'boolean','default' => false]
        ],
        'calculated' => [
            'search' => true,
            'sorting' => true,  
            'inputType' => 'checkbox',
            'sql' => ['type' => 'boolean','default' => true]
        ],
        'vat' => [
            'inputType' => 'radio',
            'options'   => array('0', '16', '19'),
            'eval'      => array('mandatory'=>true, 'tl_class'=>'w50'),
            'sql'       => "varchar(3) NOT NULL default '19'"
        ], 
        'vatIncluded' => [
            'search' => true,
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
        'default' => '{article_legend},sortlyId;{suppliers_legend},supplierId,supplierArticleNo;{orders_legend};orderQuantity,packageUnit,price;discount,surcharge;orderDate,estimatedDeliveryDate,invoiceDate,invoiceNoDMS;{delivery_legend},internalExternal;calculated,vat,vatIncluded,priceUpdate,delivered;{note_legend:hide},note'
    ],
];