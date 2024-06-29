<?php
// tl_errorNote
use Contao\DC_Table;
use Contao\Backend;
use Contao\Database;
use Contao\Input;

$GLOBALS['TL_DCA']['tl_errorNote'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
//        'onload_callback' => array
//        (
//                array('tl_country', 'country')
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
            'mode' => 1,
            'fields' => ['errorDate','category'],
            'flag' => 11,
            'panelLayout' => 'search,limit,sort'
        ],
        'label' => [
            'fields' => ['category', 'description','measure','errorDate','solutionDate', 'errorCausedBy','note'],
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
        'category' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_errorNote']['category'],
            'inputType'               => 'select',
            'filter'                  => true,
            'search'                  => true,
            //'options_callback'        => array('tl_hel_toolcentersPlus', 'ktcId'),                      
            'foreignKey'              => "tl_errorCategory.category",                                          
            'eval'                    => array('includeBlankOption'=>true,'tl_class'=>'w50 wizard', 'mandatory' => true),
            'sql'                     => ['type' => 'string', 'length' => 3, 'default' => '']
        ),  
        'description' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 1000, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 1000, 'default' => '']
        ],
        'measure' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 1000, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 1000, 'default' => '']
        ],
        'errorDate' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w33 wizard', 'maxlength' => 255, 'datepicker' => true, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'errorTimeHour' => [
            'inputType' => 'select',
            'options'   => array('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','00'),
            'eval'      => array('includeBlankOption'=>true,'tl_class'=>'w33 wizard', 'mandatory' => true),
            'sql'       => ['type' => 'string', 'length' => 3, 'default' => '']
        ],
        'errorTimeMinute' => [
            'inputType' => 'select',
            'options'   => array('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20',
                                 '21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39','40',
                                 '41','42','43','44','45','46','47','48','49','50','51','52','53','54','55','56','57','58','59','00'),
            'eval'      => array('includeBlankOption'=>true,'tl_class'=>'w33 wizard', 'mandatory' => true),
            'sql'       => ['type' => 'string', 'length' => 3, 'default' => '00']
        ],
        'solutionDate' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w33 wizard', 'maxlength' => 255, 'datepicker' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'solutionTimeHour' => [
            'inputType' => 'select',
            'options'   => array('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','00'),
            'eval'      => array('includeBlankOption'=>true,'tl_class'=>'w33 wizard', 'mandatory' => false),
            'sql'       => ['type' => 'string', 'length' => 3, 'default' => '']
        ],          
        'solutionTimeMinute' => [
            'inputType' => 'select',
            'options'   => array('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20',
                                 '21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39','40',
                                 '41','42','43','44','45','46','47','48','49','50','51','52','53','54','55','56','57','58','59','00'),
            'eval'      => array('includeBlankOption'=>true,'tl_class'=>'w33 wizard', 'mandatory' => false),
            'sql'       => ['type' => 'string', 'length' => 3, 'default' => '00']
        ],     
        'errorCausedBy' => [
            'inputType'               => 'radio',
            'options'                 => array('Be1Eye', 'HELIOTRONIC', 'INTEGRA', 'KROMI'),
            'eval'                    => array('mandatory'=>false),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'note' => [
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 1000],
            'sql' => ['type' => 'string', 'length' => 1000, 'default' => '']
        ],
    ],
    'palettes' => [
        'default' => '{legend},category; description, measure; errorDate, errorTimeHour, errorTimeMinute; solutionDate, solutionTimeHour, solutionTimeMinute; errorCausedBy; note'
    ],
];


class tl_sortlyTemplatesIVMplus extends Backend
{
   
        public function sortlyId()
    {
        //\System::log('The e-mail was sent successfully', __METHOD__, TL_GENERAL);
        $value = array();        
        $result = $this->Database->prepare("SELECT DISTINCT sortlyId, kromiArticleNo, name FROM sortly WHERE pid IN(72430051) ORDER BY name")
                                 ->execute();
        while($result->next())
        {
                $value[$result->sortlyId] = $result->name." [".$result->sortlyId."]";
        }
        
        return $value;
    }
            
}

    