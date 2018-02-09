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
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;
$moduleInstance = Vtiger_Module::getInstance('SignedRecord');
if ($moduleInstance) {
    $adb->pquery("UPDATE `vtiger_entityname` SET `fieldname`=? WHERE `tabid`=?",array('filename',$moduleInstance->id));
    $adb->pquery("UPDATE `vtiger_field` SET `presence`='1' WHERE `tabid`=? AND `columnname`=?",array($moduleInstance->id, 'signedrecordno'));
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";