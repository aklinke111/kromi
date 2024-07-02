<?php
// contao/config/config.php mistekacke

$GLOBALS['BE_MOD']['heliotronic']['heliotronicLicence'] = [
    'tables' => ['tl_hel_licence'],
];
$GLOBALS['BE_MOD']['heliotronic']['heliotronicInvoices'] = [
    'tables' => ['tl_hel_invoices'],
];
$GLOBALS['BE_MOD']['heliotronic']['heliotronicCategory'] = [
    'tables' => ['tl_hel_category'],
];



$GLOBALS['BE_MOD']['income']['payments'] = [
    'tables' => ['tl_payments'],
];


$GLOBALS['BE_MOD']['enrichment']['customer'] = [
    'tables' => ['tl_customer'],
];
$GLOBALS['BE_MOD']['enrichment']['supplier'] = [
    'tables' => ['tl_supplier'],
];
$GLOBALS['BE_MOD']['enrichment']['toolcenter'] = [
    'tables' => ['tl_toolcenter'],
];
$GLOBALS['BE_MOD']['enrichment']['costUnits'] = [
    'tables' => ['tl_costUnits'],
];

//$GLOBALS['BE_MOD']['enrichment']['ivmPlus'] = [
//    'tables' => ['tl_sortlyTemplatesIVMplus'],
//];

$GLOBALS['BE_MOD']['projects']['ToolcenterProjects'] = [
    'tables' => ['tl_toolcenterProjects', 'tl_toolcenterProjectComponents'],
];
$GLOBALS['BE_MOD']['projects']['ToolcenterProjectCategory'] = [
    'tables' => ['tl_toolcenterProjectCategory'],
];
$GLOBALS['BE_MOD']['projects']['ToolcenterProjectStatus'] = [
    'tables' => ['tl_toolcenterProjectStatus'],
];



$GLOBALS['BE_MOD']['inventory']['ToolcenterInventory'] = [
    'tables' => ['tl_toolcenterInventory'],
];



$GLOBALS['BE_MOD']['engineering']['orders'] = [
    'tables' => ['tl_orders'],
];
$GLOBALS['BE_MOD']['engineering']['bom'] = [
    'tables' => ['tl_sortlyTemplatesIVM', 'tl_bom'],
];



//$GLOBALS['BE_MOD']['misc.']['myLogs'] = array
//(
//	'tables' => array('tl_myLogs')
//);
//$GLOBALS['BE_MOD']['misc.']['errorNote'] = array
//(
//	'tables' => array('tl_errorNote')
//);
//$GLOBALS['BE_MOD']['misc.']['errorCategory'] = array
//(
//	'tables' => array('tl_errorCategory')
//);
//$GLOBALS['BE_MOD']['misc.']['aGlobals'] = array
//(
//	'tables' => array('tl_aGlobals')
//);



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
