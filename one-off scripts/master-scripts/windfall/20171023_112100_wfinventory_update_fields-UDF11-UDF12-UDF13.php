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

//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('include/database/PearDatabase.php');

$moduleInstance = Vtiger_Module::getInstance('WFInventory');

if(!$moduleInstance){
    return;
}

if(!$db) {
    $db = PearDatabase::getInstance();
}

foreach(['udf_11', 'udf_12', 'udf_13'] as $udfColumn){
    Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_wfinventory` MODIFY COLUMN $udfColumn DEC(50,2)");
}


//@NOTE: Specify uitype 7 since the STEP attribute is only applied for those.
$sql = "SELECT fieldid, typeofdata FROM vtiger_field WHERE fieldname IN ('udf_11', 'udf_12', 'udf_13') AND tablename='vtiger_wfinventory' AND uitype=7";
$res = $db->query($sql);
print "Updating UDF fields to use a better step.<br />\n";
while($row = $res->fetchRow()) {
    $id = $row[0];
    $typeofdata = $row[1];
    if(stripos('step', $typeofdata) === false) {
        $typeofdata .= "~STEP=0.01";
        $sql = "UPDATE vtiger_field SET typeofdata=? WHERE fieldid=?";
        if(!$db->pquery($sql, [$typeofdata, $id])) {
            print "Error updating udf field of ID $id. Check MySQL fail log.<br />\n";
        }
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
