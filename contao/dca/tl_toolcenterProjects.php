<?php
// contao/dca/tl_toolcenterProjects.php
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Backend;

use App\EventListener\DataContainer\MyFunctions;
use App\EventListener\DataContainer\SortlyFunctions;

$GLOBALS['TL_DCA']['tl_toolcenterProjects'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'ctable' => ['tl_toolcenterProjectComponents'],
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
            'fields' => ['id'],
            'flag' => 11,
            'panelLayout' => 'search,limit,sort'
        ],
        'label' => [
            'fields' => ['id','ktcId','projectDatePlanned','projectDateFinished','projectCategory','projectStatus','note'],
            'format' => '%s',
            'showColumns' => true,
        ],
        'operations' => [
            'edit' => [
                'href' => 'table=tl_toolcenterProjectComponents',
                'icon' => 'children.svg',
            ],
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
            'filter'  => true,
            'search'  => true,
            'sorting' => true,              
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true]
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'projectDatePlanned' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'maxlength' => 255, 'datepicker' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'projectDateFinished' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'maxlength' => 255, 'datepicker' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'ktcId' => array
        (
                'inputType'               => 'select',
                'filter'                  => true,
                'search'                  => true,
                'sorting'                 => true,  
                'options_callback' => [
                    MyFunctions::class, 'ktcId'
                ],              
//                'options_callback'        => array('tl_toolcenterProjects', 'ktcId'),                      
//                'foreignKey'              => "sortly_ktc.name",                                          
                'eval'                    => array('includeBlankOption'=>true,'tl_class'=>'w50 wizard'),
                'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ), 
        'projectCategory' => array
        (
                'inputType'               => 'select',
                'filter'                  => true,
                'search'                  => true,   
                'sorting'                 => true,
                'foreignKey'              => "tl_toolcenterProjectCategory.category",                                          
                'eval'                    => array('includeBlankOption'=>true,'tl_class'=>'w50 wizard'),
                'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ),
        'projectStatus' => array
        (
                'inputType'               => 'select',
                'filter'                  => true,
                'search'                  => true,     
                'sorting'                 => true,
                'foreignKey'              => "tl_toolcenterProjectStatus.status",                                          
                'eval'                    => array('includeBlankOption'=>true,'tl_class'=>'w50 wizard'),
                'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ), 
        'employeeResponsible' => array
        (
                'inputType'               => 'select',
                'filter'                  => true,
                'search'                  => true,     
                'sorting'                 => true,
                'foreignKey'              => "tl_member.CONCAT(lastname,', ',firstname)",                                       
                'eval'                    => array('includeBlankOption'=>true,'tl_class'=>'w50 wizard'),
                'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ),
        'probability' => [
            'inputType' => 'text',
            'search' => true,
            'sorting' => true,  
            'eval' => ['tl_class' => 'w50', 'mandatory' => false],
            'sql' => "DECIMAL(5,2)",
        ],
        'travelExpense' => [
            'inputType' => 'text',
            'search' => true,
            'sorting' => true,  
            'eval' => ['tl_class' => 'w50', 'mandatory' => false],
            'sql' => "DECIMAL(10,4)",
        ],      
        'singleSRC' => [
            'inputType' => 'fileTree',
            'eval' => [
                'tl_class' => 'clr',
                'fieldType' => 'radio',
                'filesOnly' => true,
                'extensions' => \Contao\Config::get('validImageTypes'),
                'mandatory' => false,
            ],
            'sql' => ['type' => 'binary', 'length' => 16, 'notnull' => false, 'fixed' => true]
        ],
        'note' => [
            'inputType' => 'textarea',
            'eval' => ['tl_class' => 'clr', 'mandatory' => false],
            'sql' => ['type' => 'text', 'notnull' => false]
        ],
    ],
    'palettes' => [
        'default' => '{toolcenterProjects_legend},ktcId,employeeResponsible;projectCategory,projectStatus,projectDatePlanned,projectDateFinished;travelExpense,probability,singleSRC,note'
    ],
];



//class tl_toolcenterProjects extends Backend
//{
//    public function ktcId()
//    {
//        //\System::log('The e-mail was sent successfully', __METHOD__, TL_GENERAL);
//        $value = array();   
////        $sql = "SELECT DISTINCT id, sid, name FROM sortly_ktc WHERE name LIKE 'KTC-%' ORDER BY name";
//        
//        $sql = "Select
//                    sortly_ktc.name As ktc,
//                    sortly_customer.name As customer
//                From
//                    sortly_customer Inner Join
//                    sortly_ktc On sortly_customer.sid = sortly_ktc.pid
//                Where
//                    sortly_ktc.name Like 'KTC-%'
//                Order By
//                    ktc";
//        
//        $result = $this->Database->prepare($sql)->execute();
//            while($result->next())
//            {
//                $value[$result->ktc] = $result->ktc.' - '.$result->customer;
//            }
//        return $value;
//    }
//}