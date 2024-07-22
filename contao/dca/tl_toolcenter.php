<?php
// contao/dca/tl_toolcenter.php
use Contao\DC_Table;
use Contao\Backend;
use Contao\Input;

$GLOBALS['TL_DCA']['tl_toolcenter'] = [
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
            'fields' => ['ktcId'],
            'flag' => 11,
            'panelLayout' => 'search,limit,sort'
        ],
        'label' => [
            'fields' => ['ktcId', 'dateOfImplementation', 'costcenter','countOfCabinets', 'servicefee','active','note'],
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
        'sid' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 20, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 20, 'default' => '']
        ],
        'ktcId' => [
            'sorting' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 10, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ],
        'dateOfImplementation' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'maxlength' => 255, 'datepicker' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'costcenter' => array
        (
                'sorting' => true,
                'inputType'               => 'select',
                'filter'                  => true,
                'search'                  => true,
                'options_callback'        => array('tl_toolcenter', 'costcenter'),                      
//                'foreignKey'              => "tl_costcenter.costcenter",                                          
                'eval'                    => array('includeBlankOption'=>true,'tl_class'=>'w50 wizard'),
                'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ), 
        'servicefee' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'mandatory' => false],
            'sql' => "DECIMAL(10,2)",
        ],
        'noteServicefee' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'countOfCabinets' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 2, 'mandatory' => false],
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0]
        ],
        'active' => [
            'inputType' => 'checkbox',
            'filter'                  => true,
            'search'                  => true,
            'sql' => ['type' => 'boolean','default' => false]
        ],
        'exclude' => [
            'search' => true,
            'sorting' => true,  
            'inputType' => 'checkbox',
            'sql' => ['type' => 'boolean','default' => false]
        ],        
        'note' => [
            'inputType' => 'textarea',
            'eval' => ['tl_class' => 'clr', 'mandatory' => false],
            'sql' => ['type' => 'text', 'notnull' => false]
        ],
    ],
    'palettes' => [
        'default' => '{legend},ktcId,costcenter,dateOfImplementation,countOfCabinets,servicefee,noteServicefee;active;note'
    ],
];


class tl_toolcenter extends Backend
{
    public function costcenter()
    {
        //\System::log('The e-mail was sent successfully', __METHOD__, TL_GENERAL);
        $value = array();        
        $result = $this->Database->prepare("SELECT * FROM tl_costcenter ORDER BY costcenter")
                                 ->execute();
        while($result->next())
        {
                $value[$result->costcenter] = $result->costcenter." ".$result->description;
        }
        return $value;
    }
}