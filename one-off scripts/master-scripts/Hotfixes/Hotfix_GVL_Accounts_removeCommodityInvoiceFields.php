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


/*
 *
 *Goals:
 * Remove three fields from Accounts.
 *
 * commodity
 * invoice_document_format
 * invoice_delivery_format
 *
 */
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$blockName = 'LBL_ACCOUNT_INVOICESETTINGS';
foreach (['Accounts'] as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
    } else {
        $block = Vtiger_Block::getInstance($blockName, $module);
        //no harm in making sure.
        if ($block) {
            $hideFields = [
                'commodity',
                'invoice_document_format',
                'invoice_delivery_format'
            ];

            print "<h2>finished hide fields to $moduleName module. </h2>\n";
            hideFields_GARCII($hideFields, $module);
            print "<h2>finished hide fields to $moduleName module. </h2>\n";
        }
    }
}


function hideFields_GARCII($fields, $module)
{
    if (is_array($fields)) {
        $db = PearDatabase::getInstance();
        foreach ($fields as $field_name) {
            $field0 = Vtiger_Field::getInstance($field_name, $module);
            if ($field0) {
                echo "<li>The $field_name field exists</li><br>";
                //update the presence
                if ($field0->presence != 1) {
                    echo "Updating $field_name to be a have presence = 1 <br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `presence` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, ['1', $field0->id]);
                }
            }
        }
    }
    return false;
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";