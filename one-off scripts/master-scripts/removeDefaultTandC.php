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



echo "Removing default terms and conditions <br>";

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_inventory_tandc` SET `tandc` = NULL");

echo "default terms and conditions removed!<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";