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



//VehicleTransfer.php
include_once 'vtlib/Vtiger/Module.php';
$vehiclesTransfIsNew = false;

$moduleInstance = Vtiger_Module::getInstance('VehicleTransfers'); // The module1 your blocks and fields will be in.
if (!$moduleInstance) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'VehicleTransfers';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $vehiclesTransfIsNew = true;
}

$block1 = Vtiger_Block::getInstance('LBL_VEHICLES_TRANSFERS_INFORMATION', $moduleInstance);  // Must be the actual instance name, not just what appears in the browser.
if ($block1) {
    echo "<h3>The LBL_VEHICLES_TRANSFERS_INFORMATION block already exists</h3><br>";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_VEHICLES_TRANSFERS_INFORMATION';
    $moduleInstance->addBlock($block1);
}

//start block1 fields

$field01 = Vtiger_Field::getInstance('transfer_number', $moduleInstance);
if (!$field01) {
    $field01 = new Vtiger_Field();
    $field01->label = 'LBL_TRANSFERS_NO';
    $field01->name = 'transfer_number';
    $field01->table = 'vtiger_vehicletransfers';
    $field01->column = 'transfer_number';
    $field01->columntype = 'VARCHAR(10)';
    $field01->uitype = 4;
    $field01->typeofdata = 'V~M';

    $block1->addField($field01);

    global $adb;
    $numid = $adb->getUniqueId("vtiger_modentity_num");
    $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'VehicleTransfers', 'TRANSF', 1, 1, 1));
}

$moduleInstance->setEntityIdentifier($field01);

$field0 = Vtiger_Field::getInstance('transfer_vehicle', $moduleInstance);
if (!$field0) {
    $field0 = new Vtiger_Field();
    $field0->label = 'LBL_TRANSFERS_VEHICLE_NO';
    $field0->name = 'transfer_vehicle';
    $field0->table = 'vtiger_vehicletransfers';
    $field0->column = 'transfer_vehicle';
    $field0->columntype = 'INT(10)';
    $field0->uitype = 10;
    $field0->typeofdata = 'I~M';

    $block1->addField($field0);
    $field0->setRelatedModules(array('Vehicles'));
}

$field1 = Vtiger_Field::getInstance('transfer_driver', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_TRANSFERS_DRIVER_NO';
    $field1->name = 'transfer_driver';
    $field1->table = 'vtiger_vehicletransfers';
    $field1->column = 'transfer_driver';
    $field1->columntype = 'INT(10)';
    $field1->uitype = 10;
    $field1->typeofdata = 'I~M';

    $block1->addField($field1);
    $field1->setRelatedModules(array('Employees'));
}

$field2 = Vtiger_Field::getInstance('transfer_old_agent', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_TRANSFERS_OLD_AGENT_NO';
    $field2->name = 'transfer_old_agent';
    $field2->table = 'vtiger_vehicletransfers';
    $field2->column = 'transfer_old_agent';
    $field2->columntype = 'INT(10)';
    $field2->uitype = 10;
    $field2->typeofdata = 'I~M';

    $block1->addField($field2);
    $field2->setRelatedModules(array('Agents'));
}

$field3 = Vtiger_Field::getInstance('transfer_new_agent', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_TRANSFERS_NEW_AGENT_NO';
    $field3->name = 'transfer_new_agent';
    $field3->table = 'vtiger_vehicletransfers';
    $field3->column = 'transfer_new_agent';
    $field3->columntype = 'INT(10)';
    $field3->uitype = 10;
    $field3->typeofdata = 'I~M';

    $block1->addField($field3);
    $field3->setRelatedModules(array('Agents'));
}

$field4 = Vtiger_Field::getInstance('transfers_date', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_TRANSFERS_DATE';
    $field4->name = 'transfers_date';
    $field4->table = 'vtiger_vehicletransfers';
    $field4->column = 'transfers_date';
    $field4->columntype = 'DATE';
    $field4->uitype = 5;
    $field4->typeofdata = 'D~O';

    $block1->addField($field4);
}

$field5 = Vtiger_Field::getInstance('transfers_from_unit', $moduleInstance);
if (!$field5) {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_TRANSFER_FROM_UNIT';
    $field5->name = 'transfers_from_unit';
    $field5->table = 'vtiger_vehicletransfers';
    $field5->column = 'transfers_from_unit';
    $field5->columntype = 'VARCHAR(50)';
    $field5->uitype = 2;
    $field5->typeofdata = 'V~O';

    $block1->addField($field5);
}

$field6 = Vtiger_Field::getInstance('transfers_to_unit', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_TRANSFER_TO_UNIT';
    $field6->name = 'transfers_to_unit';
    $field6->table = 'vtiger_vehicletransfers';
    $field6->column = 'transfers_to_unit';
    $field6->columntype = 'VARCHAR(50)';
    $field6->uitype = 2;
    $field6->typeofdata = 'V~O';

    $block1->addField($field6);
}

$field7 = Vtiger_Field::getInstance('transfers_comments', $moduleInstance);
if (!$field7) {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_TRANSFERS_COMMENTS';
    $field7->name = 'transfers_comments';
    $field7->table = 'vtiger_vehicletransfers';
    $field7->column = 'transfers_comments';
    $field7->columntype = 'VARCHAR(255)';
    $field7->uitype = 19;
    $field7->typeofdata = 'V~O';

    $block1->addField($field7);
}

$field36 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if (!$field36) {
    $field36 = new Vtiger_Field();
    $field36->label = 'Assigned To';
    $field36->name = 'assigned_user_id';
    $field36->table = 'vtiger_crmentity';
    $field36->column = 'smownerid';
    $field36->uitype = 53;
    $field36->typeofdata = 'V~M';

    $block1->addField($field36);
}

$field37 = Vtiger_Field::getInstance('createdtime', $moduleInstance);
if (!$field37) {
    $field37 = new Vtiger_Field();
    $field37->label = 'Created Time';
    $field37->name = 'createdtime';
    $field37->table = 'vtiger_crmentity';
    $field37->column = 'createdtime';
    $field37->uitype = 70;
    $field37->typeofdata = 'T~O';
    $field37->displaytype = 2;

    $block1->addField($field37);
}

$field38 = Vtiger_Field::getInstance('modifiedtime', $moduleInstance);
if (!$field38) {
    $field38 = new Vtiger_Field();
    $field38->label = 'Modified Time';
    $field38->name = 'modifiedtime';
    $field38->table = 'vtiger_crmentity';
    $field37->column = 'modifiedtime';
    $field38->uitype = 70;
    $field38->typeofdata = 'T~O';
    $field38->displaytype = 2;

    $block1->addField($field38);
}

$agentField = Vtiger_Field::getInstance('agentid', $moduleInstance);
if (!$agentField) {
    $agentField = new Vtiger_Field();
    $agentField->label = 'Owner Agent';
    $agentField->name = 'agentid';
    $agentField->table = 'vtiger_crmentity';
    $agentField->column = 'agentid';
    $agentField->columntype = 'INT(10)';
    $agentField->uitype = 1002;
    $agentField->typeofdata = 'I~O';

    $block1->addField($agentField);
}

$block1->save($module);

if ($vehiclesTransfIsNew) {
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();

    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field01)
            ->addField($field0, 1)
            ->addField($field1, 2)
            ->addField($field2, 3)
            ->addField($field3, 4)
            ->addField($field4, 5)
            ->addField($field5, 6)
            ->addField($field6, 7)
            ->addField($field7, 8);
}

// Add documents related list
if ($vehiclesTransfIsNew) {
    $vehiclesInstance = Vtiger_Module::getInstance('Vehicles');
    $vehiclesInstance->setRelatedList($moduleInstance, 'Transfers', array('ADD'), 'get_dependents_list');
}

//De attach the module from the menu. Only accesible from vehicles

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent = '' WHERE name = 'VehicleTransfers'");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";