<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Surveys_QuickCreateAjax_View extends Vtiger_QuickCreateAjax_View
{
    public function process(Vtiger_Request $request)
    {
        file_put_contents('logs/QuickCreateMapping.log', date('Y-m-d H:i:s - ').print_r($request, true)."\n", FILE_APPEND);
        $moduleName       = $request->getModule();
        $recordModel      = Vtiger_Record_Model::getCleanInstance($moduleName);
        $moduleModel      = $recordModel->getModule();
        $fieldList        = $moduleModel->getFields();
        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);
        foreach ($requestFieldList as $fieldName => $fieldValue) {
            $fieldModel = $fieldList[$fieldName];
            if ($fieldModel->isEditable()) {
                $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
            }
        }
        $recordStructureInstance      = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_QUICKCREATE);
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        $viewer                       = $this->getViewer($request);
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('SINGLE_MODULE', 'SINGLE_'.$moduleName);
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
        echo $viewer->view('QuickCreate.tpl', $moduleName, true);
    }
}
