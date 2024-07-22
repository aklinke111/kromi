<?php

use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_toolcenterInventory'] = [
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
            'fields' => ['ktcId', 'inventoryConducted', 'inventoryPlanned', 'difference','paperDocument','note'],
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
        'ktcId' => array
        (
                'inputType'               => 'select',
                'filter'                  => true,
                'search'                  => true,
                'options_callback'        => array('tl_toolcenterInventory', 'ktcId'),                                                          
                'eval'                    => array('includeBlankOption'=>true,'tl_class'=>'w50 wizard'),
                'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ), 
        'inventoryPlanned' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'maxlength' => 255, 'datepicker' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'inventoryConducted' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 wizard', 'maxlength' => 255, 'datepicker' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'difference' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 2, 'mandatory' => false],
            'sql' => ['type' => 'string', 'unsigned' => true,'default' => '']
        ],
        'countedBy' => array
        (
                'inputType'               => 'select',
                'filter'                  => true,
                'search'                  => true,     
                'sorting'                 => true,
                'foreignKey'              => "tl_member.CONCAT(lastname,', ',firstname)",                                         
                'eval'                    => array('includeBlankOption'=>true,'tl_class'=>'w50 wizard'),
                'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ),
        'writtenBy' => array
        (
                'inputType'               => 'select',
                'filter'                  => true,
                'search'                  => true,     
                'sorting'                 => true,
                'foreignKey'              => "tl_member.CONCAT(lastname,', ',firstname)",                                       
                'eval'                    => array('includeBlankOption'=>true,'tl_class'=>'w50 wizard'),
                'sql' => ['type' => 'string', 'length' => 10, 'default' => '']
        ),
        'paperDocument' => [
            'inputType' => 'checkbox',
            'sql' => ['type' => 'boolean','default' => false]
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
        'default' => '{toolcenterInventory_legend},ktcId;inventoryConducted,inventoryPlanned;difference;countedBy,writtenBy;paperDocument,singleSRC;note'
    ],
];

use Contao\Backend;

class tl_toolcenterInventory extends Backend
{
    public function ktcId()
    {
        //\System::log('The e-mail was sent successfully', __METHOD__, TL_GENERAL);
        $value = array();        
        $result = $this->Database->prepare("SELECT * FROM tl_toolcenter ORDER BY ktcId")
                                 ->execute();
        while($result->next())
        {
                $value[$result->ktcId] = $result->ktcId;
        }
        return $value;
    }
}