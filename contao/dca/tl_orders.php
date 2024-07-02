<?php
// contao/dca/tl_orders.php
use Contao\DC_Table;
use Contao\Backend;
use Contao\Database;
use Contao\Input;

use App\EventListener\DataContainer\UpdateSortly;
use App\EventListener\DataContainer\MyFunctions;
use App\EventListener\DataContainer\MailFunctions;

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
            'mode' => 1,
            'fields' => ['supplierId'],
            'flag' => 11,
            'panelLayout' => 'search,limit,sort'
        ],
        'label' => [
            'fields' => ['supplierId','sortlyId','supplierArticleNo','orderQuantity', 'price','orderDate', 'invoiceDate', 'delivered', 'note'],
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
            'options_callback'        => array('tl_orders', 'sortlyId'),  
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
        'default' => '{article_legend},sortlyId;{suppliers_legend},supplierId,supplierArticleNo;{orders_legend};orderQuantity,packageUnit,price,discount;orderDate,invoiceDate;{delivery_legend},invoiceNoDMS, delivered;{note_legend:hide},note'
    ],
];


class tl_orders extends Backend
{
   
    public function sortlyId()
    {
        //\System::log('The e-mail was sent successfully', __METHOD__, TL_GENERAL);
        $value = array();        
        $result = $this->Database->prepare("SELECT DISTINCT sortlyId, name FROM sortly WHERE pid IN(58670984,72430051) ORDER BY sortlyId")
                                 ->execute();
        while($result->next())
        {
                $value[$result->sortlyId] = $result->sortlyId." - ".$result->name;
        }
        
        return $value;
    }
    
//        public function supplier()
//    {
//        //\System::log('The e-mail was sent successfully', __METHOD__, TL_GENERAL);
//        $value = array();        
//        $result = $this->Database->prepare("SELECT DISTINCT sortlyId, name FROM sortly WHERE pid IN(58670984,72430051) ORDER BY sortlyId")
//                                 ->execute();
//        while($result->next())
//        {
//                $value[$result->sortlyId] = $result->sortlyId." - ".$result->name;
//        }
//        
//        return $value;
//    }
            
}