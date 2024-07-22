<?php
// contao/dca/tl_toolcenterProjectComponents.php
use Contao\Database;
use Contao\DC_Table;
use Contao\Input;
use Contao\Backend;

$GLOBALS['TL_DCA']['tl_toolcenterProjectComponents'] = [
    
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'ptable' => 'tl_toolcenterProjects',
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'tstamp' => 'index',
            ],
        ],
        'onload_callback' => [
            function () {
                $db = Database::getInstance();
                $pid = Input::get('pid');
                if (empty($pid)) {
                    return;
                }
                $result = $db->prepare('SELECT `ktcId` FROM `tl_toolcenterProjects` WHERE `id` = ?')
                             ->execute([$pid]);
                $prefix = strtoupper(substr($result->ktcId, 0, 2));
                $GLOBALS['TL_DCA']['tl_toolcenterProjectComponents']['fields']['serial']['default'] = $prefix;
            },
        ]
    ],
    'list' => [
        'sorting' => [
            'mode' => 4,
            'fields' => ['componentModel'],
            'headerFields' => ['componentModel'],
            'panelLayout' => 'search,limit',
            'child_record_callback' => function (array $row) {
                return '<div class="tl_content_left">Inventory No. '.$row['serial'].' ['.$row['usage'].']   '.$row['note'].'</div>';
            },
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
        'pid' => [
            'foreignKey' => 'tl_toolcenterProjects.ktcId',
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
            'relation' => ['type'=>'belongsTo', 'load'=>'lazy']
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0]
        ],
        'componentModel' => array
        (
             'inputType'               => 'select',
             'filter'                  => true,
             'search'                  => true,
             'sorting'                  => true,
//             'options_callback'        => array('tl_toolcenterProjectComponents', 'componentFullName'),  
             'foreignKey'              => "tl_sortlyTemplatesIVM.name",                                          
             'eval'                    => array('includeBlankOption'=>false,'tl_class'=>'w50 wizard','mandatory' => true),
             'label_callback'          => array('tl_toolcenterProjectComponents', 'modifyLabel_componentModel'),
             'sql'                     => ['type' => 'integer', 'unsigned' => true, 'default' => 0]
        ), 
        'usage' => [
            'inputType' => 'radio',
            'options' => array('remove', 'install'),
            'eval' => array('mandatory' => true),
            'sql' => "varchar(16) NOT NULL default ''",            
        ],
        'serial' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 100, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 100, 'default' => '']
        ], 
        'title' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_example']['title'],
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255),
            'sql' => "varchar(255) NOT NULL default ''",
            'label_callback' => array('tl_toolcenterProjectComponents', 'modifyTitleLabel'),
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
            'eval' => ['tl_class' => 'w50', 'maxlength' => 1000, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 1000, 'default' => '']
        ], 
    ],
    'palettes' => [
        'default' => '{legend},componentModel,serial;usage;note'
    ],
];



class tl_toolcenterProjectComponents extends Backend
{
    public function modifyTitleLabel($label, $dc)
    {
        // Access the value of the 'status' field
        $statusValue = $dc->activeRecord->usage;

        // Add a flag based on the 'status' field value
        if ($statusValue == 'remove') {
            $label = '<span style="color: red;">🚫</span> ' . $label; // You can use any HTML or Unicode character for the flag
        } else {
            $label = '<span style="color: green;">✅</span> ' . $label;
        }

        return $label;
    }
    
    
    
//    public function componentFullName()
//    {
//        $value = array();        componentFullName
//        $result = $this->Database->prepare("SELECT id, CONCAT(model,' - ',description) as fullComponentName FROM tl_kr_componentsBasics ORDER BY model")
//                                 ->execute();
//        while($result->next())
//        {
//                $value[$result->id] = $result->fullComponentName;
//        }
//        
//        return $value;
//    }
//    public function projectFullName()
//    {
//        $value = array();        
//        $result = $this->Database->prepare("SELECT id, CONCAT('KTC-',ktcId,', ','Confluence-ID ',confluenceId) as fullProjectName FROM tl_toolcenterProjects ORDER BY ktcId")
//                                 ->execute();
//        while($result->next())
//        {
//                $value[$result->id] = $result->fullProjectName;
//        }
//        
//        return $value;
//    }
//    
//    public function modifyLabel_componentModel($label, $dc)
//    {
//        // Access the value of the 'status' field
//        $componentModelId = $dc->activeRecord->componentModel;
//
////        // Modify the label based on the 'status' field value
////        if ($statusValue == 'inactive') {
////            $label .= ' (Inactive)';
////        }
//        $value = string();  
//        $result = $this->Database->prepare("SELECT CONCAT(model,' - ',description) as fullComponentName FROM tl_kr_componentsBasics WHERE id =".$componentModelId)
//                                 ->execute();
//                while($result->next())
//        {
//                $value = $result->fullProjectName;
//        }
//        $label.= "Tester".$value;
//
//        return $label;
//    }
}
