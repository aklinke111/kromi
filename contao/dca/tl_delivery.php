<?php
// contao/dca/tl_delivery.php
use Contao\DC_Table;
use Contao\Backend;
use Contao\Database;
use Contao\Input;

use App\EventListener\DataContainer\SortlyFunctions;
use App\EventListener\DataContainer\UpdateSortly;
use App\EventListener\DataContainer\MyFunctions;
use App\EventListener\DataContainer\MailFunctions;

$GLOBALS['TL_DCA']['tl_delivery'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
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
            'mode' => 1,
            'fields' => ['customerId'],
            'flag' => 11,
            'panelLayout' => 'search,limit,sort'
        ],
        'label' => [
            'fields' => ['customerId','sortlyId','deliveryQuantity', 'price','orderDate', 'invoiceDate', 'delivered', 'note'],
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
        'customerId' => [
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => true,           
            'foreignKey'              => 'tl_customer.name',
            'eval'                    => array('includeBlankOption'=>true,'tl_class'=>'w50 wizard'),            
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
        'deliveryQuantity' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'mandatory' => true],
            'sql' => ['type' => 'integer', 'notnull' => false, 'unsigned' => true],
        ],
        'packageUnit' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'mandatory' => true],
            'sql' => ['type' => 'integer', 'notnull' => false, 'unsigned' => true, 'default' => '1'],
        ],
        'orderDate' => [
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
            'eval' => ['tl_class' => 'w50', 'mandatory' => true],
            'sql' => "DECIMAL(10,4)",
        ],
        'discount' => [
            'inputType' => 'text',
            'search' => true,
            'sorting' => true,  
            'eval' => ['tl_class' => 'w50', 'mandatory' => false],
            'sql' => "DECIMAL(10,4)",
        ],
        'delivered' => [
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
        'default' => '{article_legend},sortlyId;{suppliers_legend},customerId;{orders_legend};deliveryQuantity,packageUnit,price,discount;orderDate,invoiceDate;{delivery_legend},invoiceNoDMS, delivered;{note_legend:hide},note'
    ],
];