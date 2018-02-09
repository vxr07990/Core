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



$moduleInstance = Vtiger_Module::getInstance('Vehicles'); // The module1 your blocks and fields will be in.

// Remove any existing documents related lists and add documents related list to ensure that there's only one
$moduleInstance->unsetRelatedList(Vtiger_Module::getInstance('Documents'), 'Documents', 'get_attachments');
$moduleInstance->setRelatedList(Vtiger_Module::getInstance('Documents'), 'Documents', array('ADD', 'SELECT'), 'get_attachments', 1);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
