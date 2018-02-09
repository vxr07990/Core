<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
include_once 'modules/Settings/Picklist/models/Module.php';
include_once 'modules/Settings/Picklist/models/Field.php';

$moduleName = 'WFOrders';
$blockName = 'LBL_WFORDER_INFORMATION';
$module = Vtiger_Module::getInstance($moduleName);
if(!$module){
    echo "$moduleName not present. <br>\n";
    return;
}

$block = Vtiger_Block::getInstance($blockName, $module);
if(!$block){
    echo "$blockName not present. <br>\n";
    return;
}

$db = PearDatabase::getInstance();


$field = Vtiger_Field::getInstance('warehouse_status', $module);
if ($field) {
    echo '<p> wfwarehouse_status Field already present</p>';
    $db->pquery("update `vtiger_field` set `defaultvalue`='In Storage' where `fieldid`=?;", array($field->id));
} else {
    $picklistOptions = [
        'In Storage',
        'Out of Storage',
    ];

    $field = new Vtiger_Field();
    $field->label = 'LBL_WFWAREHOUSE_STATUS';
    $field->name = 'warehouse_status';
    $field->table = 'vtiger_wforders';
    $field->column = 'warehouse_status';
    $field->columntype = 'VARCHAR(150)';
    $field->uitype = '16';
    $field->typeofdata = 'V~M';
    $field->sequence = 2;
    $field->defaultvalue = 'In Storage';
    $field->setPicklistValues($picklistOptions);

    $block->addField($field);

    echo '<p>Added warehouse_status field to WFOrders</p>';
}
