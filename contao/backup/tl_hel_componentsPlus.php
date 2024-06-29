<?php
// contao/dca/tl_hel_componentsPlus.php
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_hel_componentsPlus'] = [
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
            'fields' => ['ktcId','serial','machineNo','dateOfDguv3'],
            'flag' => 8,
            'panelLayout' => 'sort,filter,search,limit'
        ],
        'label' => [
            'fields' => ['ktcId', 'serial', 'machineNo', 'dateOfDguv3', 'note'],
            'format' => '%s',
            'showColumns' => true,
        ],
        'operations' => [
            'edit' => [
                'href' => 'table=tl_hel_componentsPlus',
                'icon' => 'edit.svg',
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
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true],
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0]
        ],
        'ktcId' => array
        (
                'label'                   => &$GLOBALS['TL_LANG']['tl_hel_componentsPlus']['ktcId'],
                'inputType'               => 'select',
                'filter'                  => true,
                'search'                  => true,
                //'options_callback'        => array('tl_toolcenterPlus', 'ktcId'),                      
                'foreignKey'              => "tl_toolcenter.ktcId",                                          
                'eval'                    => array('includeBlankOption'=>true,'tl_class'=>'w50 wizard'),
                'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ), 
        'serial' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 10, 'mandatory' => true, 'unique' => false],
            'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ],
        'machineNo' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 10, 'mandatory' => false,  'unique' => false],
            'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ],
        'dateOfDguv3' => [
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'maxlength' => 255, 'datepicker' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'note' => [
            'inputType' => 'textarea',
            'eval' => ['tl_class' => 'clr', 'mandatory' => false],
            'sql' => ['type' => 'text', 'notnull' => false]
        ],
    ],
    'palettes' => [
        'default' => '{componentsPlus_legend},ktcId,serial,machineNo;dateOfDguv3,note'
    ],
];



//use Contao\Backend;
//
//class tl_hel_componentsPlus extends Backend
//{
//    public function test()
//    {
//        //\System::log('The e-mail was sent successfully', __METHOD__, TL_GENERAL);
//        $value = array();        
//        $result = $this->Database->prepare("SELECT * FROM tl_hel_components ORDER BY ktcId")
//                                 ->execute();
//        while($result->next())
//        {
//                $value[$result->id] = $result->ktcId;
//        }
//        return $value;
//    }
//}