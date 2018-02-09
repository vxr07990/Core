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


if (!function_exists('addPicklistValue')) {
    function addPicklistValue($pickListName, $moduleName, $newValue)
    {
        $moduleModel = Settings_Picklist_Module_Model::getInstance($moduleName);
        $fieldModel = Settings_Picklist_Field_Model::getInstance($pickListName, $moduleModel);
        $rolesSelected = array();
        if ($fieldModel->isRoleBased()) {
            $roleRecordList = Settings_Roles_Record_Model::getAll();
            foreach ($roleRecordList as $roleRecord) {
                $rolesSelected[] = $roleRecord->getId();
            }
        }
        try {
            $id = $moduleModel->addPickListValues($fieldModel, $newValue, $rolesSelected);
        } catch (Exception $e) {
        }
    }
}


addPicklistValue('orders_otherstatus', 'Orders', 'Non-Planned');


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";