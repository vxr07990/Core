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
 * Create the Branch Defaults module that is related to Agents.
 * Create at the Service Defaults as a guest block to the Branch Defaults.
 */
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

{
    createBranchDefaultsForAgents();
    createServiceDefaultsForBranchDefaults();
}

function createBranchDefaultsForAgents()
{
    foreach (['BranchDefaults'] as $moduleName) {
        echo "<br>begin create module script for $moduleName <br>";
        $moduleInstance = Vtiger_Module::getInstance($moduleName);
        $new_module     = false;
        if (!$moduleInstance) {
            echo "module doesn't exist";
            $moduleInstance       = new Vtiger_Module();
            $moduleInstance->name = $moduleName;
            $moduleInstance->save();
            $moduleInstance->initTables();
            $new_module = true;
        }
        echo "<br>creating blocks...";
        $block1 = Vtiger_Block::getInstance('LBL_BRANCHDEFAULTS_INFORMATION', $moduleInstance);
        if (!$block1) {
            $block1        = new Vtiger_Block();
            $block1->label = 'LBL_BRANCHDEFAULTS_INFORMATION';
            $moduleInstance->addBlock($block1);
        }
        $block2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleInstance);
        if (!$block2) {
            $block2        = new Vtiger_Block();
            $block2->label = 'LBL_CUSTOM_INFORMATION';
            $moduleInstance->addBlock($block2);
        }
        echo "done!<br> creating fields...";
        $fields        = [
            'created_user_id'                  => [
                'label'      => 'Created By',
                'name'       => 'created_user_id',
                'table'      => 'vtiger_crmentity',
                'column'     => 'smcreatorid',
                'columntype' => 'INT(19)',
                'uitype'     => 52,
                'typeofdata' => 'V~O',
                'presence'   => '2',
                'displaytype'   => '2',
                'quickcreate'   => '3',
                'block'      => $block1,
            ],
            'modifiedby'                  => [
                'label'      => 'modifiedby',
                'name'       => 'modifiedby',
                'table'      => 'vtiger_crmentity',
                'column'     => 'modifiedby',
                'columntype' => 'INT(19)',
                'uitype'     => 53,
                'typeofdata' => 'V~O',
                'presence'   => '0',
                'displaytype'   => '2',
                'replaceExisting' => true,
                'block'      => $block1,
            ],
            'modifiedtime'                => [
                'label'      => 'modifiedtime',
                'name'       => 'modifiedtime',
                'table'      => 'vtiger_crmentity',
                'column'     => 'modifiedtime',
                'columntype' => 'DATETIME',
                'uitype'     => 70,
                'typeofdata' => 'T~O',
                'presence'   => '0',
                'displaytype'   => '2',
                'replaceExisting' => true,
                'block'      => $block1,
            ],
            'createdtime'                 => [
                'label'      => 'createdtime',
                'name'       => 'createdtime',
                'table'      => 'vtiger_crmentity',
                'column'     => 'createdtime',
                'columntype' => 'DATETIME',
                'uitype'     => 70,
                'typeofdata' => 'T~O',
                'presence'   => '0',
                'displaytype'   => '2',
                'replaceExisting' => true,
                'block'      => $block1,
            ],
            'bd_agreement_name'           => [
                'label'               => 'LBL_BRANCHDEFAULTS_AGREEMENT_NAME',
                'name'                => 'bd_agreement_name',
                'table'               => 'vtiger_branchdefaults',
                'column'              => 'bd_agreement_name',
                'columntype'          => 'VARCHAR(255)',
                'uitype'              => 1,
                'typeofdata'          => 'V~M',
                'block'               => $block1,
                'summaryfield'        => 1,
                'setEntityIdentifier' => 1
            ],
            'bd_agent'                    => [
                'label'             => 'LBL_BRANCHDEFAULTS_AGENT',
                'name'              => 'bd_agent',
                'table'             => 'vtiger_branchdefaults',
                'column'            => 'bd_agent',
                'columntype'        => 'INT(10)',
                'uitype'            => 10,
                'typeofdata'        => 'V~O',
                'block'             => $block1,
                'summaryfield'      => 1,
                'setRelatedModules' => ['Agents']
            ],
            'agentid'                     => [
                'label'      => 'Owner',
                'name'       => 'agentid',
                'table'      => 'vtiger_crmentity',
                'column'     => 'agentid',
                'columntype' => 'INT(11)',
                'uitype'     => 1002,
                'typeofdata' => 'I~M',
                'block'      => $block1,
            ],
            'bd_ic_type'                  => [
                'label'        => 'LBL_BRANCHDEFAULTS_VA_IC_TYPE',
                'name'         => 'bd_ic_type',
                'table'        => 'vtiger_branchdefaults',
                'column'       => 'bd_ic_type',
                'columntype'   => 'VARCHAR(55)',
                'uitype'       => 15,
                'typeofdata'   => 'V~M',
                //'picklist' => ['Transportation Services','Terminal Servcies','TR & TS'],
                'picklist'     => ['TR', 'TS'], //, 'IT'], no IT on this mock up
                'block'        => $block1,
                'summaryfield' => 1,
            ],
            'assigned_user_id'            => [
                'label'      => 'Assigned To',
                'name'       => 'assigned_user_id',
                'table'      => 'vtiger_crmentity',
                'column'     => 'smownerid',
                'columntype' => 'INT(19)',
                'uitype'     => 53,
                'typeofdata' => 'V~M',
                'block'      => $block1,
            ],
        ];
        $createdFields = addFields_CBDAA($fields, $moduleInstance);
        if ($new_module) {
            $filter1            = new Vtiger_Filter();
            $filter1->name      = 'All';
            $filter1->isdefault = true;
            $moduleInstance->addFilter($filter1);
            $filter1->addField($createdFields['bd_agreement_name'])->addField($createdFields['assigned_user_id'], 1);
            $moduleInstance->setDefaultSharing();
            $moduleInstance->initWebservice();
            $parentModule = Vtiger_Module::getInstance('Agents');
            //$parentModule->setRelatedList(Vtiger_Module::getInstance($moduleName), 'Branch Defaults',['ADD','SELECT'],'get_dependents_list');
            //$parentModule->setRelatedList($moduleInstance, 'Branch Defaults',['ADD','SELECT'],'get_dependents_list');
            $parentModule->setRelatedList($moduleInstance, 'Branch Defaults', ['ADD', 'SELECT'], 'get_related_list');
            // Adds the Updates link to the vertical navigation menu on the right.
            ModTracker::enableTrackingForModule($moduleInstance->id);
            $commentsModule = Vtiger_Module::getInstance('ModComments');
            $fieldInstance  = Vtiger_Field::getInstance('related_to', $commentsModule);
            $fieldInstance->setRelatedModules([$moduleName]);
            //require_once 'modules/ModComments/ModComments.php';
            $detailviewblock = ModComments::addWidgetTo($moduleName);
        }
        echo "done!<br> module creation script complete";
    }
}

function createServiceDefaultsForBranchDefaults()
{
    //Create at the Service Provider Exceptions as a guest block to the Branch Defaults.
    //ServiceProviderExceptions
    //LBL_BD_DEFAULTS_
    //vtiger_servicedefaults

    foreach (['ServiceDefaults'] as $moduleName) {
        echo "<br>begin create module script for $moduleName <br>";
        $moduleInstance = Vtiger_Module::getInstance($moduleName);
        $new_module     = false;
        if (!$moduleInstance) {
            echo "module doesn't exist";
            $moduleInstance       = new Vtiger_Module();
            $moduleInstance->name = $moduleName;
            $moduleInstance->save();
            $moduleInstance->initTables();
            $new_module = true;
        }
        echo "<br>creating blocks...";
        $block1 = Vtiger_Block::getInstance('LBL_BD_DEFAULTS_INFORMATION', $moduleInstance);
        if (!$block1) {
            $block1        = new Vtiger_Block();
            $block1->label = 'LBL_BD_DEFAULTS_INFORMATION';
            $moduleInstance->addBlock($block1);
        }
        $block2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleInstance);
        if (!$block2) {
            $block2        = new Vtiger_Block();
            $block2->label = 'LBL_CUSTOM_INFORMATION';
            $moduleInstance->addBlock($block2);
        }
        echo "done!<br> creating fields...";
        $fields        = [
            'created_user_id'                  => [
                'label'      => 'Created By',
                'name'       => 'created_user_id',
                'table'      => 'vtiger_crmentity',
                'column'     => 'smcreatorid',
                'columntype' => 'INT(19)',
                'uitype'     => 52,
                'typeofdata' => 'V~O',
                'presence'   => '2',
                'displaytype'   => '2',
                'quickcreate'   => '3',
                'block'      => $block1,
            ],
            'modifiedby'                  => [
                'label'      => 'modifiedby',
                'name'       => 'modifiedby',
                'table'      => 'vtiger_crmentity',
                'column'     => 'modifiedby',
                'columntype' => 'INT(19)',
                'uitype'     => 53,
                'typeofdata' => 'V~O',
                'presence'   => '0',
                'displaytype'   => '2',
                'replaceExisting' => true,
                'block'      => $block1,
            ],
            'modifiedtime'                => [
                'label'      => 'modifiedtime',
                'name'       => 'modifiedtime',
                'table'      => 'vtiger_crmentity',
                'column'     => 'modifiedtime',
                'columntype' => 'DATETIME',
                'uitype'     => 70,
                'typeofdata' => 'T~O',
                'presence'   => '0',
                'displaytype'   => '2',
                'replaceExisting' => true,
                'block'      => $block1,
            ],
            'createdtime'                 => [
                'label'      => 'createdtime',
                'name'       => 'createdtime',
                'table'      => 'vtiger_crmentity',
                'column'     => 'createdtime',
                'columntype' => 'DATETIME',
                'uitype'     => 70,
                'typeofdata' => 'T~O',
                'presence'   => '0',
                'displaytype'   => '2',
                'replaceExisting' => true,
                'block'      => $block1,
            ],
            //Authority is a Multi­select field with drop down values: Interstate, Intrastate, Local, International Air, International Sea
            'sd_authority'           => [
                'label'               => 'LBL_BD_DEFAULTS_AUTHORITY',
                'name'                => 'sd_authority',
                'table'               => 'vtiger_servicedefaults',
                'column'              => 'sd_authority',
                'columntype'        => 'TEXT',
                'uitype'            => 33,
                'typeofdata'          => 'V~O',
                'block'               => $block1,
                'picklist'          => ['Interstate', 'Intrastate', 'Local', 'International Air', 'International Sea'],
                'setEntityIdentifier' => 1,
            ],
            //Standard Item Code is Multi­select and will be populated with values from GSM
            'sd_standard_item' => [
                'label'             => 'LBL_BD_DEFAULTS_SPE_STANDARD_ITEM',
                'name'              => 'sd_standard_item',
                'table'             => 'vtiger_servicedefaults',
                'column'            => 'sd_standard_item',
                'columntype'        => 'TEXT',
                'uitype'            => 33,
                'typeofdata'        => 'V~O',
                'block'             => $block1,
                'picklist'          => ['none'],
            ],
            //Tariff is a Multi­select field with choices of both lists for Interstate Tariffs and Local Tariffs within HQ.
            'sd_tariff'                  => [
                'label'        => 'LBL_BD_DEFAULTS_SPE_TARIFF',
                'name'         => 'sd_tariff',
                'table'        => 'vtiger_servicedefaults',
                'column'       => 'sd_tariff',
                'columntype'        => 'TEXT',
                'uitype'            => 33,
                'typeofdata'   => 'V~O',
                'block'        => $block1,
                'picklist'          => ['none'],
            ],
            //Mileage field only one choice can be selected. Should have values: None, 0­400, 401+, 0­100, 101+, 0­75, 76­125, 126+, 126­175, 176+, Straight Truck
            'sd_mileage'                  => [
                'label'        => 'LBL_BD_DEFAULTS_SPE_MILEAGE',
                'name'         => 'sd_mileage',
                'table'        => 'vtiger_servicedefaults',
                'column'       => 'sd_mileage',
                'columntype'   => 'VARCHAR(255)',
                'uitype'       => 15,
                'typeofdata'   => 'V~O',
                'picklist'     => ['None', '0 to 400', '401+', '0 to 100', '101+', '0 to 75', '76 to 125', '126+', '126 to 175', '176+', 'Straight Truck'],
                'block'        => $block1,
            ],
            //Class field only one choice can be selected. Should have values: B, P/
            'sd_class'                  => [
                'label'        => 'LBL_BD_DEFAULTS_SPE_CLASS',
                'name'         => 'sd_class',
                'table'        => 'vtiger_servicedefaults',
                'column'       => 'sd_class',
                'columntype'   => 'VARCHAR(5)',
                'uitype'       => 15,
                'typeofdata'   => 'V~O',
                'picklist'     => ['B','P'],
                'block'        => $block1,
            ],
            //Effective Date is a date value/
            'sd_effective_date'                  => [
                'label'        => 'LBL_BD_DEFAULTS_SPE_EFFECTIVE_DATE',
                'name'         => 'sd_effective_date',
                'table'        => 'vtiger_servicedefaults',
                'column'       => 'sd_effective_date',
                'columntype'   => 'DATE',
                'uitype'       => 5,
                'typeofdata'   => 'D~O',
                'block'        => $block1,
        ],
            //Cancel Date is a date value/
            'sd_cancel_date'                  => [
                'label'        => 'LBL_BD_DEFAULTS_SPE_CANCEL_DATE',
                'name'         => 'sd_cancel_date',
                'table'        => 'vtiger_servicedefaults',
                'column'       => 'sd_cancel_date',
                'columntype'   => 'DATE',
                'uitype'       => 5,
                'typeofdata'   => 'D~O',
                'block'        => $block1,
            ],
            //% Paid to IC , only one of these choices can be filled in on a line. If % paid to IC, display as a percentage out to 2 decimal places, if $ Per Service allow user to enter in a dollar amount to 2 decimal places.
            'sd_paid_to_ic'                  => [
                'label'        => 'LBL_BD_DEFAULTS_SPE_PAID_TO_IC',
                'name'         => 'sd_paid_to_ic',
                'table'        => 'vtiger_servicedefaults',
                'column'       => 'sd_paid_to_ic',
                'columntype'   => 'DECIMAL(10,2)',
                'uitype'       => 9,
                'typeofdata'   => 'N~O',
                'block'        => $block1,
            ],
            //$ Per Service, only one of these choices can be filled in on a line. If % paid to IC, display as a percentage out to 2 decimal places, if $ Per Service allow user to enter in a dollar amount to 2 decimal places.
            'sd_per_service'                  => [
                'label'        => 'LBL_BD_DEFAULTS_SPE_PER_SERVICE',
                'name'         => 'sd_per_service',
                'table'        => 'vtiger_servicedefaults',
                'column'       => 'sd_per_service',
                'columntype'   => 'DECIMAL(10,2)',
                'uitype'       => 71,
                'typeofdata'   => 'N~O',
                'block'        => $block1,
            ],
            'agentid'                     => [
                'label'      => 'Owner',
                'name'       => 'agentid',
                'table'      => 'vtiger_crmentity',
                'column'     => 'agentid',
                'columntype' => 'INT(11)',
                'uitype'     => 1002,
                'typeofdata' => 'I~M',
                'presence' => '1',
                'block'      => $block1,
            ],
            'assigned_user_id'            => [
                'label'      => 'Assigned To',
                'name'       => 'assigned_user_id',
                'table'      => 'vtiger_crmentity',
                'column'     => 'smownerid',
                'columntype' => 'INT(19)',
                'uitype'     => 53,
                'typeofdata' => 'V~M',
                'presence' => '1',
                'block'      => $block1,
            ],
            //REQUIRED FOR GUEST BLOCK
            'sd_relcrmid'     => [
                'label'             => 'LBL_BD_DEFAULTSS_RELCRMID',
                'name'              => 'sd_relcrmid',
                'table'             => 'vtiger_servicedefaults',
                'column'            => 'sd_relcrmid',
                'columntype'        => 'INT(10)',
                'uitype'            => 10,
                'typeofdata'        => 'V~O',
                'block'             => $block1,
                'setRelatedModules' => ['BranchDefaults']
            ],
        ];
        $createdFields = addFields_CBDAA($fields, $moduleInstance);
        if ($new_module) {
            $moduleInstance->setDefaultSharing();
            $moduleInstance->initWebservice();

            $parentModule = Vtiger_Module::getInstance('BranchDefaults');
            $parentModule->setGuestBlocks($moduleName, ['LBL_BD_DEFAULTS_INFORMATION']);
        }
        echo "done!<br> module creation script complete";
    }
}

function addFields_CBDAA($fields, $module)
{
    $returnFields = [];
    foreach ($fields as $field_name => $data) {
        $createBlock = true;
        $field0 = Vtiger_Field::getInstance($field_name, $module);
        if ($field0) {
            echo "<li>The $field_name field already exists</li><br>";
            $returnFields[$field_name] = $field0;
            if ($data['replaceExisting'] && $data['block']->id == $field0->getBlockId()) {
                $createBlock = false;
                $db          = PearDatabase::getInstance();
                if ($data['uitype'] && $field0->uitype != $data['uitype']) {
                    echo "Updating $field_name to uitype=".$data['uitype']." for lead source module<br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `uitype` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, [$data['uitype'], $field0->id]);
                }

                //update the typeofdata
                if (isset($data['typeofdata']) && $field0->typeofdata != $data['typeofdata']) {
                    echo "Updating $field_name to be a have typeofdata = '".$data['typeofdata']."'.<br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `typeofdata` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, [$data['typeofdata'], $field0->id]);
                }

                //update the displaytype
                if (isset($data['displaytype']) && $field0->displaytype != $data['displaytype']) {
                    echo "Updating $field_name to be a have displaytype = '".$data['displaytype']."'.<br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `displaytype` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, [$data['displaytype'], $field0->id]);
                }

                //update the presence
                if (isset($data['presence']) && $field0->presence != $data['presence']) {
                    echo "Updating $field_name to be a have presence = '".$data['presence']."'.<br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `presence` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, [$data['presence'], $field0->id]);
                }

                if (
                    array_key_exists('setRelatedModules', $data) &&
                    $data['setRelatedModules'] &&
                    count($data['setRelatedModules']) > 0
                ) {
                    echo "<li> setting relation to existing $field_name</li>";
                    $field0->setRelatedModules($data['setRelatedModules']);
                }
                if ($data['updateDatabaseTable'] && $data['columntype']) {
                    //hell you have to fix the created table!  ... sigh.
                    $stmt = 'EXPLAIN `'.$field0->table.'` `'.$field_name.'`';
                    if ($res = $db->pquery($stmt)) {
                        while ($value = $res->fetchRow()) {
                            if ($value['Field'] == $field_name) {
                                if (strtolower($value['Type']) != strtolower($data['columntype'])) {
                                    echo "Updating $field_name to be a " . $data['columntype'] . " type.<br />\n";
                                    $db   = PearDatabase::getInstance();
                                    $stmt = 'ALTER TABLE `' . $field0->table . '` MODIFY COLUMN `' . $field_name . '` ' . $data['columntype'] . ' DEFAULT NULL';
                                    $db->pquery($stmt);
                                }
                                //we're only affecting the $field_name so if we find it just break
                                break;
                            }
                        }
                    } else {
                        echo "NO $field_name column in The actual table?<br />\n";
                    }
                }
            } elseif ($data['block']->id == $field0->getBlockId()) {
                //already exists in this block
                $createBlock = false;
            } else {
                //need to add to a new block.
                $createBlock = true;  //even though it already is.
            }
        }

        if ($createBlock) {
            echo "<li> Attempting to add $field_name</li><br />";
            //@TODO: check data validity
            $field0 = new Vtiger_Field();
            //these are assumed to be filled.
            $field0->label        = $data['label'];
            $field0->name         = $data['name'];
            $field0->table        = $data['table'];
            $field0->column       = $data['column'];
            $field0->columntype   = $data['columntype'];
            $field0->uitype       = $data['uitype'];
            $field0->typeofdata   = $data['typeofdata'];
            $field0->summaryfield = ($data['summaryfield']?1:0);
            $field0->defaultvalue = $data['defaultvalue'];
            //these three MUST have values or it doesn't pop vtiger_field.
            $field0->displaytype = ($data['displaytype']?$data['displaytype']:1);
            $field0->readonly    = ($data['readonly']?$data['readonly']:1);
            $field0->presence    = ($data['presence']?$data['presence']:2);
            $data['block']->addField($field0);
            if ($data['setEntityIdentifier'] == 1) {
                $module->setEntityIdentifier($field0);
            }
            //just completely ensure there's stuff in the array before doing it.
            if (
                array_key_exists('setRelatedModules', $data) &&
                $data['setRelatedModules'] &&
                count($data['setRelatedModules']) > 0
            ) {
                $field0->setRelatedModules($data['setRelatedModules']);
            }
            if (
                array_key_exists('picklist', $data) &&
                $data['picklist'] &&
                count($data['picklist']) > 0
            ) {
                $field0->setPicklistValues($data['picklist']);
            }
            $returnFields[$field_name] = $field0;
        }
    }

    return $returnFields;
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";