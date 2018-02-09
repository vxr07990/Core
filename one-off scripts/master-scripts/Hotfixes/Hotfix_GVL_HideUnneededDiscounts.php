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


/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 9/6/2016
 * Time: 3:27 PM
 */

$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('Contracts');
$block = Vtiger_Block::getInstance('LBL_CONTRACTS_TARIFF', $module);

$fields = ['linehaul_disc', 'accessorial_disc', 'packing_disc', 'fuel_disc'];

if ($module && $block) {
    $db = PearDatabase::getInstance();
    foreach ($fields as $fieldName) {
        $field1 = Vtiger_Field::getInstance($fieldName, $module);
        if ($field1) {
            $db->pquery('UPDATE vtiger_field SET presence=1 WHERE fieldname=? AND columnname=? AND tabid=?',
                        [$fieldName, $field1->column, $module->id]);
            echo 'Hiding '.$fieldName.' field'.PHP_EOL;
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";