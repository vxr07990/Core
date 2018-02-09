<?php

/***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class ModuleDesigner
{
    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
     */
    public function vtlib_handler($module_name, $event_type)
    {
        //require_once('include/utils/utils.php');
        global $adb;
    
        $module = Vtiger_Module::getInstance($module_name);
    
        if ($event_type == 'module.postinstall') {
            //************* Set access right for all profiles ***********************//
            //Don't display module name in menu
            $adb->pquery("UPDATE vtiger_profile2tab SET permissions=? WHERE tabid=?", array(1, $module->id));
            
            //Don't allow action on the module
            $adb->pquery("UPDATE vtiger_profile2standardpermissions SET permissions=? WHERE tabid=?", array(1, $module->id));
            
            //Add link to the module in the Setting Panel
            $fieldid = $adb->getUniqueID('vtiger_settings_field');
            $blockid = getSettingsBlockId('LBL_MODULE_MANAGER');
            
            $seq_res = $adb->query("SELECT max(sequence) AS max_seq FROM vtiger_settings_field WHERE blockid =1");
            $seq = 1;
            if ($adb->num_rows($seq_res) > 0) {
                $cur_seq = $adb->query_result($seq_res, 0, 'max_seq');
                
                if ($cur_seq != null) {
                    $seq = $cur_seq + 1;
                }
            }
                
            $adb->pquery(
                'INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence,active) VALUES (?,?,?,?,?,?,?,?)',
                array(
                    $fieldid,
                    3,
                    $module_name,
                    'layouts/vlayout/modules/Settings/'.$module_name.'/assets/images/'.$module_name.'.png',
                    'LBL_'.strtoupper($module_name).'_DESCRIPTION',
                    'index.php?module='.$module_name.'&view=Index&parent=Settings',
                    $seq,
                    0
                )
            );
        } elseif ($event_type == 'module.disabled') {
            // TODO Handle actions when this module is disabled.
        } elseif ($event_type == 'module.enabled') {
            // TODO Handle actions when this module is enabled.
        } elseif ($event_type == 'module.preuninstall') {
            $adb->pquery('DELETE FROM vtiger_settings_field WHERE name = ?', array($module_name));
        } elseif ($event_type == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } elseif ($event_type == 'module.postupdate') {
            $query = "SELECT * FROM vtiger_settings_field WHERE name = ?";
            $result = $adb->pquery($query, array($module_name));
            
            if ($adb->num_rows($result) == 0) {
                $seq_res = $adb->query("SELECT max(sequence) AS max_seq FROM vtiger_settings_field WHERE blockid =1");
                $seq = 1;
                if ($adb->num_rows($seq_res) > 0) {
                    $cur_seq = $adb->query_result($seq_res, 0, 'max_seq');
                    
                    if ($cur_seq != null) {
                        $seq = $cur_seq + 1;
                    }
                }
                    
                $adb->pquery(
                    'INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence,active) VALUES (?,?,?,?,?,?,?,?)',
                    array(
                        $fieldid,
                        3,
                        $module_name,
                        'layouts/vlayout/modules/Settings/'.$module_name.'/assets/images/'.$module_name.'.png',
                        'LBL_'.strtoupper($module_name).'_DESCRIPTION',
                        'index.php?module='.$module_name.'&view=Index&parent=Settings',
                        $seq,
                        0
                    )
                );
            }
        }
    }
}
