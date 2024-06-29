<?php

// contao/dca/tl_supplier.php
use Contao\DataContainer;
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_supplier'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
//        'ctable' => ['tl_parts'],
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
            'fields' => ['id'],
            'flag' => 8,
            'panelLayout' => 'search,limit,filter,sort'
        ],
        'label' => [
//            'fields' => ['name', 'city', 'country', 'user_id:tl_user.name','critical'],
            'fields' => ['name', 'city', 'critical'],
            'format' => '%s',
            'showColumns' => true,            
        ],
        'operations' => [
//            'edit' => [
//                'href' => 'table=tl_supplier',
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
        'name' => [
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'street' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'postal' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'clr w50', 'maxlength' => 255, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'city' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'country' => [
            'inputType' => 'select',
            'options_callback' => static function(): array {
                return \Contao\System::getContainer()->get('contao.intl.countries')->getCountries();
            },
            'eval' => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => false],
            'sql' => ['type' => 'string', 'length' => 2, 'default' => '']
        ],
        'addressInfo' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'customerNo' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'contactName' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'contactPosition' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'contactMail' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'contactPhone' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'url' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'account' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],
        'critical' => [
            'inputType' => 'checkbox',
            'search' => true,            
            'filter' => true,
            'sorting' => true,
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => 'boolean','default' => false]
        ],
        'criticalNote' => [
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => false],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '']
        ],                   
        'note' => [
            'inputType' => 'textarea',
            'eval' => ['tl_class' => 'clr', 'rte' => 'tinyMCE', 'mandatory' => false],
            'sql' => ['type' => 'text', 'notnull' => false]
        ],
    ],
                    
    'palettes' => [
        'default' => '{vendor_legend},name;{address_legend},street,postal,city,country,addressInfo,url;{contact_legend},contactName,contactPosition,contactMail,contactPhone;{additional_legend},customerNo,account,critical,criticalNote,note'
    ],
];