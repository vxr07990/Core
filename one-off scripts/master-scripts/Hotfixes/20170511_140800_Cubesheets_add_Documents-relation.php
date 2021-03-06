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

//OT18562 - Adding Documents related list to Cubesheets module

$cubesheetsModule = Vtiger_Module::getInstance('Cubesheets');
$documentsModule = Vtiger_Module::getInstance('Documents');

if($cubesheetsModule && $documentsModule) {
    $cubesheetsModule->setRelatedList($documentsModule, 'Documents', [], 'get_attachments');
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
