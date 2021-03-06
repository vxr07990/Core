<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Reports_Folder_Action extends Vtiger_Action_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('save');
        $this->exposeMethod('delete');
        $this->exposeMethod('filterByOwner');
    }

    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $moduleModel = Reports_Module_Model::getInstance($moduleName);

        $currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if (!$currentUserPriviligesModel->hasModulePermission($moduleModel->getId())) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    /**
     * Function that saves/updates the Folder
     * @param Vtiger_Request $request
     */
    public function save(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $folderModel = Reports_Folder_Model::getInstance();
        $folderId = $request->get('folderid');

        if (!empty($folderId)) {
            $folderModel->set('folderid', $folderId);
        }

        $folderModel->set('foldername', $request->get('foldername'));
        $folderModel->set('description', $request->get('description'));
        $folderModel->set('agentid', $request->get('agentid'));

        if ($folderModel->checkDuplicate()) {
            throw new AppException(vtranslate('LBL_DUPLICATES_EXIST', $moduleName));
        }

        $folderModel->save();
        $result = array('success' => true, 'message' => vtranslate('LBL_FOLDER_SAVED', $moduleName), 'info' => $folderModel->getInfoArray());

        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }

    /**
     * Function that deletes the Folder
     * @param Vtiger_Request $request
     */
    public function delete(Vtiger_Request $request)
    {
        $folderId = $request->get('folderid');
        $moduleName = $request->getModule();

        if ($folderId) {
            $folderModel = Reports_Folder_Model::getInstanceById($folderId);

            if ($folderModel->isDefault()) {
                throw new AppException(vtranslate('LBL_FOLDER_CAN_NOT_BE_DELETED', $moduleName));
            } else {
                if ($folderModel->hasReports()) {
                    throw new AppException(vtranslate('LBL_FOLDER_NOT_EMPTY', $moduleName));
                }
            }

            $folderModel->delete();
            $result = array('success'=>true, 'message'=>vtranslate('LBL_FOLDER_DELETED', $moduleName));

            $response = new Vtiger_Response();
            $response->setResult($result);
            $response->emit();
        }
    }
        
    public function validateRequest(Vtiger_Request $request)
    {
        $request->validateWriteAccess();
    }
    
    public function filterByOwner(Vtiger_Request $request)
    {
        global $adb;
        $ownerid = $request->get('ownerid');
        $folderid = $request->get('folderid');
        if($folderid && $folderid != 'All'){
            $rs = $adb->pquery("SELECT * FROM vtiger_reportfolder WHERE folderid = ? ORDER BY foldername ASC",array($folderid));
            $Instances = array();
            if($adb->num_rows($rs) > 0){
                $i = 0;
                while ($data = $adb->fetchByAssoc($rs)){
                    $Instances[$i]['folderid'] = $data['folderid'];
                    $Instances[$i]['foldername'] = $data['foldername'];
                    $Instances[$i]['description'] = $data['description'];
                    $Instances[$i]['agentid'] = $data['agentid'];
                    $i++;
                }
            }
        }else{
            $rs = $adb->pquery("SELECT * FROM vtiger_reportfolder WHERE agentid = ? ORDER BY foldername ASC",array($ownerid));
            $Instances = array();
            if($adb->num_rows($rs) > 0){
                $i = 0;
                while ($data = $adb->fetchByAssoc($rs)){
                    $Instances[$i]['folderid'] = $data['folderid'];
                    $Instances[$i]['foldername'] = $data['foldername'];
                    $Instances[$i]['description'] = $data['description'];
                    $Instances[$i]['agentid'] = $data['agentid'];
                    $i++;
                }
            }
        }
        
        $response = new Vtiger_Response();
        $response->setResult($Instances);
        $response->emit();
    }
}
