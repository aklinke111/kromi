<?php
// contao/dca/tl_bom.php 
use Contao\DC_Table;
use Contao\Backend;
use Contao\Database;
use Contao\Input;

$GLOBALS['TL_DCA']['tl_bom'] = [
    
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'ptable' => 'tl_sortlyTemplatesIVM',
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
//                $result = $db->prepare('SELECT `name` FROM `tl_sortlyTemplatesIVM` WHERE `id` = ?')
//                             ->execute([$pid]);
//                $prefix = strtoupper(substr($result->name, 0, 2));
//                $GLOBALS['TL_DCA']['tl_bom']['fields']['name']['default'] = $prefix;
            },
        ]
    ],
    'list' => [
        'sorting' => [
            'mode' => 4,
            'fields' => ['sortlyId'],
            'headerFields' => ['name'],
            'panelLayout' => 'search,limit,filter,sort',
            'child_record_callback' => function (array $row) {
                
            // variables    
            $sortlyId = $row['sortlyId']; 
            $bomQuantity = $row['bomQuantity'];
            
            $note = $row['note'];
                if(!empty($note)){
                   $note = "[".$note."]";
                }

                // fetch price
                $db = Database::getInstance();
                $sql = "SELECT price FROM sortly WHERE sortlyId LIKE '$sortlyId'";
                $result = $db->prepare($sql)->execute();
                $price = round($result->price,2);

                // part or parts
                if($bomQuantity > 1){
                    $parts = "parts";
                } else {
                    $parts = "part"; 
                }
                
                // calculated or excluded
                if($row['calculate'] == 0 or $row['hr'] == 1){
                    $text = $bomQuantity." ".$parts." WITH PRICE OF ".$price."€ EXCLUDED FROM CALCULATION";
                return '<div class="tl_content_left"><span style="color:red">'.$text.'</span></div>';
                } else {
                return '<div class="tl_content_left"><span style="color:blue;"><b>'.$bomQuantity.'</b></span>
                         '.$parts.' needed for <span style="color:blue;"><b>'.$price.'€/pc. </b></span>'.$note.'</div>';
                }
            },
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
        'pid' => [
            'foreignKey' => 'tl_sortlyTemplatesIVM.name',
            'sql' => ['type' => 'string', 'length' => 10, 'default' => ''],
            'relation' => ['type'=>'belongsTo', 'load'=>'lazy']
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0]
        ],
        'sortlyId' => [
            'inputType' => 'select',
             'filter'                  => true,
             'search'                  => true,
             'sorting'                  => true,
             'options_callback'        => array('tl_bom', 'sortlyId'),  
            'eval' => ['tl_class' => 'w50 wizard', 'enabled' => false],
            'sql' => ['type' => 'string', 'length' => 20, 'default' => '']
        ],
        'bomQuantity' => [
            'inputType' => 'text',
            'filter'                  => true,
            'search'                  => true,
            'sorting'                  => true,
            'eval' => ['tl_class' => 'w50', 'mandatory' => true],
            'sql' => "DECIMAL(10,2)",
        ],
        'calculate' => [
            'search' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 wizard'],
            'sql' => ['type' => 'boolean','default' => true]
        ],
        'exclude' => [
            'search' => true,
            'sorting' => true,  
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 wizard'],            
            'sql' => ['type' => 'boolean','default' => false]
        ],
        'hr' => [
            'search' => true,
            'sorting' => true,  
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 wizard'],            
            'sql' => ['type' => 'boolean','default' => false]
        ],            
        'note' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w25', 'maxlength' => 255, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
    ],
    'palettes' => [
        'default' => '{{bom_legend}, sortlyId, bomQuantity, hr, calculate ; note'
    ],
];

class tl_bom extends Backend
{
    public function sortlyId()
    {
        //\System::log('The e-mail was sent successfully', __METHOD__, TL_GENERAL);
        $value = array();        
        $result = $this->Database->prepare("SELECT DISTINCT sortlyId, name FROM sortly WHERE pid IN(58670984,73134913,72430051) ORDER BY sortlyId")
                                 ->execute();
        while($result->next())
        {
                $value[$result->sortlyId] = $result->sortlyId." - ".$result->name;
        }
        
        return $value;
    }
}