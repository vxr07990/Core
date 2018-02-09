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
 * Change Billing address for Contracts  in: LBL_CONTRACTS_BILLING
 *
 * add labels for this in: languages/en_us/Contracts.php
 *
 */
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$blockName  = 'LBL_CONTRACTS_BILLING';
foreach (['Contracts'] as $moduleName) {
    print "<h2>START add Billing address fields to $moduleName module. </h2>\n";
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
    } else {
        $block = Vtiger_Block::getInstance($blockName, $module);
        if (!$block) {
            echo "<li>BLOCK $blockName DOES NOT EXIST!</li><br>";
        } else {
            $fields   = [
                'billing_name'    => [
                    'label'           => 'LBL_CONTRACTS_BILLING_NAME',
                    'name'            => 'billing_name',
                    'table'           => 'vtiger_contracts',
                    'column'          => 'billing_name',
                    'columntype'      => 'VARCHAR(255)',
                    'uitype'          => 1,
                    'typeofdata'      => 'V~O',
                    'displaytype'     => 1,
                    'block'           => $block,
                    'replaceExisting' => false,
                ],
                'billing_phone'   => [
                    'label'           => 'LBL_ORDERS_BILLING_PHONE',
                    'name'            => 'billing_phone',
                    'table'           => 'vtiger_contracts',
                    'column'          => 'billing_phone',
                    'columntype'      => 'VARCHAR(30)',
                    'uitype'          => 1,
                    'typeofdata'      => 'V~O',
                    'displaytype'     => 1,
                    'block'           => $block,
                    'replaceExisting' => false,
                ],
                'billing_email'  => [
                    'label'           => 'LBL_CONTRACTS_BILLING_EMAIL',
                    'name'            => 'billing_email',
                    'table'           => 'vtiger_contracts',
                    'column'          => 'billing_email',
                    'columntype'      => 'VARCHAR(250)',
                    'uitype'          => 1,
                    'typeofdata'      => 'V~O',
                    'displaytype'     => 1,
                    'block'           => $block,
                    'replaceExisting' => false,
                ],
                'billing_pobox'  => [
                    'label'           => 'LBL_CONTRACTS_BILLING_POBOX',
                    'name'            => 'billing_pobox',
                    'table'           => 'vtiger_contracts',
                    'column'          => 'billing_pobox',
                    'columntype'      => 'VARCHAR(100)',
                    'uitype'          => 1,
                    'typeofdata'      => 'V~O',
                    'displaytype'     => 1,
                    'presence'        => 1,
                    'block'           => $block,
                    'replaceExisting' => true,
                ],
            ];
            $rField   = addFields_ACITC($fields, $module, true);
            echo "<li>finished update to $moduleName</li><br>";
        }
    }
    print "<h2>END add fields to $moduleName module. </h2>\n";
}

//END process update
function addFields_ACITC($fields, $module)
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
                if ($data['typeofdata'] && $field0->typeofdata != $data['typeofdata']) {
                    echo "Updating $field_name to be a have typeofdata = '".$data['typeofdata']."'.<br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `typeofdata` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, [$data['typeofdata'], $field0->id]);
                }

                //update the presence
                if ($data['label'] && $field0->label != $data['label']) {
                    echo "Updating $field_name to be a have label = '".$data['label']."'.<br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `label` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, [$data['label'], $field0->id]);
                }
                
                //update the presence
                if ($data['presence'] && $field0->presence != $data['presence']) {
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