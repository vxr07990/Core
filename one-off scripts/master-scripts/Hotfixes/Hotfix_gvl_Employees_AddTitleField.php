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

echo "<h3>Starting AddTitleField</h3>\n";

$moduleName = 'Employees';
$module = Vtiger_Module::getInstance($moduleName);

$blockName = 'LBL_EMPLOYEES_INFORMATION';
$tableName = 'vtiger_employees';

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    echo "<p>The $blockName block exists</p>\n";

    $fieldName = 'employees_title';
    $field = Vtiger_Field::getInstance($fieldName, $module);
    if ($field) {
        echo "<p>$fieldName Field already present</p>\n";
    } else {
        $field = new Vtiger_Field();
        $field->label = 'LBL_'.strtoupper($moduleName.'_'.$fieldName);
        $field->name = $fieldName;
        $field->table = $tableName;
        $field->column = $fieldName;
        $field->columntype = 'VARCHAR(100)';
        $field->uitype = '1';
        $field->typeofdata = 'V~O';

        $block->addField($field);

        echo "<p>Added $fieldName Field</p>\n";
    }
} else {
    echo "<p>The $blockName block could not be found</p>\n";
}

echo "<h3>Ending AddTitleField</h3>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";