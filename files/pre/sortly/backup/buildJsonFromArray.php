<?php

header('Content-Type: application/json');

class BuildJsonFromArray {
}

class data {
}

class custom_attribute_values {
}

$custom_attribute_values1 = new custom_attribute_values();
$custom_attribute_values1->value = 111111;
$custom_attribute_values1->custom_attribute_id = "287983";
$custom_attribute_values1->custom_attribute_name = "inventoryNo";

$custom_attribute_values = array($custom_attribute_values1);

//$custom_attribute_values2 = new custom_attribute_values();
//$custom_attribute_values2->value = 222222;
//$custom_attribute_values2->custom_attribute_id = "287983";
//$custom_attribute_values3->custom_attribute_name = "inventoryNo";
//$custom_attribute_values = array($custom_attribute_values1, $custom_attribute_values2, $custom_attribute_values3);


$data = new data();

$data->name = 'testname';
$data->sid = 1234568;
$data->custom_attribute_values = $custom_attribute_values;

$BuildJsonFromArray = new BuildJsonFromArray();

$BuildJsonFromArray->data = $data;
$jsonData = json_encode($BuildJsonFromArray,JSON_PRETTY_PRINT);

echo $jsonData;