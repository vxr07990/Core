<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 8/15/2017
 * Time: 9:07 AM
 */
if (function_exists("call_ms_function_ver")) {
    $version = 5;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

//*/
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
require_once 'include/Webservices/Update.php';
require_once 'modules/Users/Users.php';
require_once 'include/utils/utils.php';

//*/
$db = PearDatabase::getInstance();
global $current_user;

$moduleName = 'WFSlotConfiguration';


$moduleInstance = Vtiger_Module::getInstance($moduleName);
if(!$moduleInstance){
    return;
}


Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET `presence` = 0 WHERE `name` = '$moduleName'");


create_tab_data_file();

$current_user = new Users();
$activeAdmin = $current_user->getActiveAdminUser();
$current_user = Users_Record_Model::getInstanceById($activeAdmin->id);


//update Agent Owner field to uitype 1020 (includes vanlines)
$agentOwnerField = Vtiger_Field::getInstance('agentid', $moduleInstance);
$db->pquery("UPDATE `vtiger_field` SET uitype = 1020 WHERE fieldid = ?", [$agentOwnerField->id]);

generateDefaultRecords($moduleName, 0, $activeAdmin);


