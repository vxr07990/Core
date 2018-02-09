<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 2/21/2017
 * Time: 7:45 AM
 */

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

require_once('vtlib/Vtiger/Menu.php');
require_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('ExtraStops');

if(!$module)
{
    return;
}

// we should really throw an error instead of generating this field
$field = Vtiger_Field::getInstance('ExtraStops_Actuals_autogeneratedlink', $module);
$db = &PearDatabase::getInstance();
$db->pquery('DELETE FROM vtiger_fieldmodulerel WHERE fieldid=?',
            [$field->id]);
if ($field) {
    $field->delete();
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
