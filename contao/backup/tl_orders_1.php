<?php
// contao/dca/tl_orders.php
use Contao\DC_Table;
use Contao\Backend;
//use Contao\System;

$GLOBALS['TL_DCA']['tl_orders'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
//        'onsubmit_callback' => array
//        (
//                array('tl_orders', 'modifyEntryName')
//        ),
//        'onload_callback' => array
//        (
//                array('tl_orders', 'insertSortlyItems')
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
            'mode' => 2,
            'fields' => ['articleNo'],
            'flag' => 8,
            'panelLayout' => 'search,limit,sort'
        ],
        'label' => [
            'fields' => ['articleNo', 'supplierId', 'supplierArticleNo','orderQuantity', 'price','orderDate', 'delivered', 'invoiceNoDMS','invoiceDate'],
            'format' => '%s',
            'showColumns' => true,
        ],
        'operations' => [
//            'edit' => [
//                'href' => 'table=tl_orders',
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
        'articleNo' => array
        (
             'inputType'               => 'select',
             'filter'                  => true,
             'search'                  => true,
             'sorting'                  => true,
             'options_callback'        => array('tl_orders', 'articleNo'),                      
             'eval'                    => array('includeBlankOption'=>false,'tl_class'=>'w50 wizard'),
             'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ), 
        'supplierId' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'select',
            'foreignKey' => 'tl_supplier.name', // Will use `name` as label, and the vendor `id` as value
            'eval' => ['chosen' => true, 'tl_class' => 'w50 wizard'], // Adds a search box to filter the options
            'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
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
            'eval' => ['tl_class' => 'w50', 'mandatory' => true],
        'sql' => ['type' => 'integer', 'notnull' => false, 'unsigned' => true],
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
        'default' => '{article_legend},articleNo;{suppliers_legend},supplierId,supplierArticleNo;{orders_legend},orderQuantity,price,orderDate,invoiceDate;{delivery_legend},invoiceNoDMS, delivered;{note_legend:hide},note'
    ],
];


class tl_orders extends Backend
{
    public function articleNo()
    {
//        System::log('Get Sortly items', __METHOD__, TL_GENERAL);
        
        $value = array();        
        $result = $this->Database->prepare("SELECT DISTINCT kromiArticleNo, id, name FROM tl_sortly ORDER BY kromiArticleNo")
                                 ->execute();
        while($result->next())
        {
                $value[$result->kromiArticleNo] = $result->kromiArticleNo.' --- '.$result->name;
        }
        
        return $value;
    }
            
}