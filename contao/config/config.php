<?php
// contao/config/config.php


$GLOBALS['BE_MOD']['engineering']['orders'] = [
    'tables' => ['tl_orders'],
];
$GLOBALS['BE_MOD']['engineering']['bom'] = [
    'tables' => ['tl_sortlyTemplatesIVM', 'tl_bom'],
];


//----------------------------------------------------

$GLOBALS['BE_MOD']['expenses']['costTravel'] = [
    'tables' => ['tl_costTravel'],
];
$GLOBALS['BE_MOD']['expenses']['costDguv'] = [
    'tables' => ['tl_costDguv'],
];
$GLOBALS['BE_MOD']['expenses']['costFreight'] = [
    'tables' => ['tl_costFreight'],
];
$GLOBALS['BE_MOD']['expenses']['costWorkwear'] = [
    'tables' => ['tl_costWorkwear'],
];

//----------------------------------------------------

$GLOBALS['BE_MOD']['software']['heliotronicLicence'] = [
    'tables' => ['tl_hel_licence'],
];
$GLOBALS['BE_MOD']['software']['heliotronicInvoices'] = [
    'tables' => ['tl_hel_invoices'],
];


//----------------------------------------------------

$GLOBALS['BE_MOD']['income']['customerPayments'] = [
    'tables' => ['tl_payments'],
];

//----------------------------------------------------

$GLOBALS['BE_MOD']['enrichment']['customer'] = [
    'tables' => ['tl_customer'],
];
$GLOBALS['BE_MOD']['enrichment']['supplier'] = [
    'tables' => ['tl_supplier'],
];
$GLOBALS['BE_MOD']['enrichment']['toolcenter'] = [
    'tables' => ['tl_toolcenter'],
];
$GLOBALS['BE_MOD']['enrichment']['region'] = [
    'tables' => ['tl_region'],
];
$GLOBALS['BE_MOD']['enrichment']['country2region'] = [
    'tables' => ['tl_country2Region'],
];

//----------------------------------------------------

$GLOBALS['BE_MOD']['projects']['ToolcenterProjects'] = [
    'tables' => ['tl_toolcenterProjects', 'tl_toolcenterProjectComponents'],
];
$GLOBALS['BE_MOD']['projects']['ToolcenterProjectStatus'] = [
    'tables' => ['tl_toolcenterProjectStatus'],
];

//----------------------------------------------------

$GLOBALS['BE_MOD']['finance']['costcenter'] = [
    'tables' => ['tl_costcenter'],
];
$GLOBALS['BE_MOD']['finance']['forecastCategory'] = [
    'tables' => ['tl_forecastCategory'],
];

//----------------------------------------------------

$GLOBALS['BE_MOD']['inventory']['ToolcenterInventory'] = [
    'tables' => ['tl_toolcenterInventory'],
];

//----------------------------------------------------

$GLOBALS['BE_MOD']['misc.']['myLogs'] = array
(
	'tables' => array('tl_myLogs')
);
$GLOBALS['BE_MOD']['misc.']['myGlobals'] = array
(
	'tables' => array('tl_globals')
);
$GLOBALS['BE_MOD']['misc.']['notes'] = array
(
	'tables' => array('tl_notes')
);
$GLOBALS['BE_MOD']['misc.']['kpi'] = array
(
	'tables' => array('tl_kpi')
);

//----------------------------------------------------


$GLOBALS['BE_MOD']['dimensions']['heliotronicCategory'] = [
    'tables' => ['tl_hel_category'],
];
$GLOBALS['BE_MOD']['dimensions']['costTravelCategory'] = [
    'tables' => ['tl_costTravelCategory'],
];
$GLOBALS['BE_MOD']['dimensions']['toolcenterProjectCategory'] = [
    'tables' => ['tl_toolcenterProjectCategory'],
];
$GLOBALS['BE_MOD']['dimensions']['notesCategory'] = [
    'tables' => ['tl_notesCategory'],
];
$GLOBALS['BE_MOD']['dimensions']['globalsCategory'] = [
    'tables' => ['tl_globalsCategory'],
];
$GLOBALS['BE_MOD']['dimensions']['kpiCategory'] = [
    'tables' => ['tl_kpiCategory'],
];

//----------------------------------------------------


//// Define DCA configuration for tl_my_table
//$GLOBALS['TL_DCA']['tl_orders'] = array(
//    'config' => array(
//        'dataContainer' => 'Table',
//        'onsubmit_callback' => array(array('MyClass', 'myOnSubmitCallback')),
//    ),
//    // Other DCA configuration...
//);
//
//// Define your callback function in a class
//class MyClass
//{
//    public function myOnSubmitCallback($dc)
//    {
//        // Access the active record using $dc->activeRecord
//        $activeRecord = $dc->activeRecord;
//
//        // Perform actions or modifications on form submission
//        if ($activeRecord !== null) {
//            // For example, log the submitted data
//            \System::log('Form submitted: ' . json_encode($activeRecord->row()), __METHOD__, TL_GENERAL);
//        }
//    }
//}

//$GLOBALS['BE_MOD']['your_group_name'] = array();
//$GLOBALS['BE_MOD']['your_group_name']['module_name'] = array(
//    'tables' => ['tl_orders'],
//    'icon' => 'contao/assets/download.png',
//);
