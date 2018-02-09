<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AgentManager_Edit_View extends Vtiger_Edit_View
{
    protected $record = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName       = $request->getModule();
        $record           = $request->get('record');
        $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'EditView', $record);
        if (!$recordPermission) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
    }

    public function process(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
        $viewer     = $this->getViewer($request);
        $moduleName = $request->getModule();
        $record     = $request->get('record');
        if (!empty($record) && $request->get('isDuplicate') == true) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('MODE', '');
            //While Duplicating record, If the related record is deleted then we are removing related record info in record model
            $mandatoryFieldModels = $recordModel->getModule()->getMandatoryFieldModels();
            foreach ($mandatoryFieldModels as $fieldModel) {
                if ($fieldModel->isReferenceField()) {
                    $fieldName = $fieldModel->get('name');
                    if (Vtiger_Util_Helper::checkRecordExistance($recordModel->get($fieldName))) {
                        $recordModel->set($fieldName, '');
                    }
                }
            }
        } elseif (!empty($record)) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
        }
        if (!$this->record) {
            $this->record = $recordModel;
        }
        $moduleModel      = $recordModel->getModule();
        $fieldList        = $moduleModel->getFields();
        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);
        foreach ($requestFieldList as $fieldName => $fieldValue) {
            $fieldModel   = $fieldList[$fieldName];
            $specialField = false;
            // We collate date and time part together in the EditView UI handling
            // so a bit of special treatment is required if we come from QuickCreate
            if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'time_start' && !empty($fieldValue)) {
                $specialField = true;
                // Convert the incoming user-picked time to GMT time
                // which will get re-translated based on user-time zone on EditForm
                $fieldValue = DateTimeField::convertToDBTimeZone($fieldValue)->format("H:i");
            }
            if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'date_start' && !empty($fieldValue)) {
                $startTime     = Vtiger_Time_UIType::getTimeValueWithSeconds($requestFieldList['time_start']);
                $startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($fieldValue." ".$startTime);
                list($startDate, $startTime) = explode(' ', $startDateTime);
                $fieldValue = Vtiger_Date_UIType::getDisplayDateValue($startDate);
            }
            if ($fieldModel->isEditable() || $specialField) {
                $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
            }
        }
        $recordStructureInstance      = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        /* VGS Global Business Line Blocks */
                if (!empty($record) && is_array($hiddenBlocksArray) && array_key_exists($moduleName, $hiddenBlocksArray)) {
                    $blocksToHide = $this->loadHiddenBlocksEditView($moduleName, $record);
                    $viewer->assign('HIDDEN_BLOCKS', $blocksToHide);
                } elseif (empty($record) && is_array($hiddenBlocksArray) && array_key_exists($moduleName, $hiddenBlocksArray)) {
                    $blocksToHide = $this->loadHiddenBlocksEditView($moduleName, '');
                    $viewer->assign('HIDDEN_BLOCKS', $blocksToHide);
                } else {
                    $blocksToHide=array();
                    $viewer->assign('HIDDEN_BLOCKS', $blocksToHide);
                }
        global $hiddenBlocksArrayField;
        $viewer->assign('BLFIELD', $hiddenBlocksArrayField[$moduleName]);
        /* VGS Global Business Line Blocks */
        $userModel              = Users_Record_Model::getCurrentUserModel();
        $isAdmin                = $userModel->isAdminUser();
        $currentUserId          = $userModel->getId();
        $db                     = PearDatabase::getInstance();
        $agentFamilyAdminRoles  = [];
        $validAgentFamilyAdmins = [];
        
        //old securities
        /* $db                     = PearDatabase::getInstance();
        $sql                    = "SELECT profileid FROM `vtiger_profile` WHERE profilename = ?";
        $result                 = $db->pquery($sql, ['Agent Family Administrator Profile']);
        $row                    = $result->fetchRow();
        $profileId              = $row[0];
        $sql                    = "SELECT roleid FROM `vtiger_role2profile` WHERE profileid = ?";
        $result                 = $db->pquery($sql, [$profileId]);
        $row                    = $result->fetchRow();
        while ($row != NULL) {
            $agentFamilyAdminRoles[] = $row[0];
            $row                     = $result->fetchRow();
        }
        if (!$isAdmin) {
            $sql         = "SELECT vanlineid FROM `vtiger_users2vanline` WHERE userid = ?";
            $result      = $db->pquery($sql, [$currentUserId]);
            $row         = $result->fetchRow();
            $vanlineId   = $row[0];
            $sql         = "SELECT vanline_name FROM `vtiger_vanlinemanager` WHERE vanlinemanagerid = ?";
            $result      = $db->pquery($sql, [$vanlineId]);
            $row         = $result->fetchRow();
            $vanlineName = $row[0];
        }
        foreach ($agentFamilyAdminRoles as $roleId) {
            $familyAdminIds = [];
            $sql            = "SELECT userid FROM `vtiger_user2role` WHERE roleid = ?";
            $result         = $db->pquery($sql, [$roleId]);
            $row            = $result->fetchRow();
            while ($row != NULL) {
                $familyAdminIds[] = $row[0];
                $row              = $result->fetchRow();
            }
            foreach ($familyAdminIds as $familyAdminId) {
                $sql             = "SELECT user_name FROM `vtiger_users` WHERE id = ?";
                $result          = $db->pquery($sql, [$familyAdminId]);
                $row             = $result->fetchRow();
                $familyAdminName = $row[0];
                if ($isAdmin) {
                    $validAgentFamilyAdmins[$familyAdminId] = $familyAdminName;
                } else {
                    $sql            = "SELECT vanlineid FROM `vtiger_users2vanline` WHERE userid = ?";
                    $result         = $db->pquery($sql, [$familyAdminId]);
                    $row            = $result->fetchRow();
                    $adminVanlineId = $row[0];
                    if ($adminVanlineId == $vanlineId) {
                        $validAgentFamilyAdmins[$familyAdminId] = $familyAdminName;
                    }
                }
            }
        }*/
        $viewer->assign('AGENT_FAMILY_ADMINS', $validAgentFamilyAdmins);
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        $viewer->assign('RECORD_MODEL', $recordModel);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('USER_MODEL', $userModel);
        $viewer->assign('USER_ID', $currentUserId);
        $viewer->assign('VANLINE_ID', $vanlineId);
        $viewer->assign('VANLINE_NAME', $vanlineName);
        //logic to include CapacityCalendarCounter
        $CapacityCalendarCounterModel = Vtiger_Module_Model::getInstance('CapacityCalendarCounter');
        if ($CapacityCalendarCounterModel && $CapacityCalendarCounterModel->isActive()) {
            $viewer->assign('CAPACITYCALENDARCOUNTER_MODULE_MODEL', $CapacityCalendarCounterModel);
            $viewer->assign('CAPACITYCALENDARCOUNTER_BLOCK_FIELDS', $CapacityCalendarCounterModel->getFields('LBL_ADDRESSSEGMENTS_INFORMATION'));
            $viewer->assign('CAPACITYCALENDARCOUNTER_LIST', $CapacityCalendarCounterModel->getCapacityCalendarCounter($record));
        }
        $vehicleLookupModel = Vtiger_Module_Model::getInstance('VehicleLookup');
        if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
            $viewer->assign('VEHICLE_LOOKUP', $vehicleLookupModel->isActive());
            $viewer->assign('PRESHIP_CHECKLIST', $vehicleLookupModel::getChecklist($record));
        }
        $isRelationOperation = $request->get('relationOperation');
        //if it is relation edit
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        if ($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
            $viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
        }
        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
        $viewer->assign('IMAGE_DETAILS', $recordModel->getImageDetails());
        $viewer->view('EditView.tpl', $moduleName);
    }

    public function loadHiddenBlocksEditView($moduleName, $record, $sourceModule = '')
    {
        global $hiddenBlocksArray, $hiddenBlocksArrayField;
        $blocksToHide = [];
        if (empty($record)) {
            $hiddenBlocks = $hiddenBlocksArray[$moduleName];
            $blocksToHide = [];
            foreach ($hiddenBlocks as $hiddenBlock) {
                $hiddenBlock = explode('::', $hiddenBlock);
                foreach ($hiddenBlock as $value) {
                    $blocksToHide[] = $value;
                }
            }
        } else {
            if (!empty($sourceModule)) {
                $recordModel   = Vtiger_Record_Model::getInstanceById($record, $sourceModule);
                $businessLines = $recordModel->entity->column_fields[$hiddenBlocksArrayField[$sourceModule]];
                $businessLines = array_map('trim', explode('|##|', $businessLines));
            } else {
                $recordModel   = Vtiger_Record_Model::getInstanceById($record, $moduleName);
                $businessLines = $recordModel->entity->column_fields[$hiddenBlocksArrayField[$moduleName]];
                $businessLines = array_map('trim', explode('|##|', $businessLines));
            }
            foreach ($hiddenBlocksArray[$moduleName] as $businessLine => $blocks) {
                if (!in_array($businessLine, $businessLines)) {
                    $blocksToHide = array_merge($blocksToHide, explode('::', $hiddenBlocksArray[$moduleName][$businessLine]));
                }
            }
        }

        return $blocksToHide;
    }

    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        $vehicleLookupModel    = Vtiger_Module_Model::getInstance('VehicleLookup');
        if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
            $jsFileNames = [
                "modules.VehicleLookup.resources.Edit",
            ];
        } else {
            $jsFileNames = [
                "modules.CapacityCalendarCounter.resources.EditBlock",
            ];
        }
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }
}
