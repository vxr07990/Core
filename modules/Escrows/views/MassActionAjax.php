<?php

class Escrows_MassActionAjax_View extends Vtiger_MassActionAjax_View {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('generateNewBlock');
        $this->exposeMethod('duplicateBlock');

    }

    function process(Vtiger_Request $request) {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);

            return;
        }
    }

    function generateNewBlock(Vtiger_Request $request) {
        global $adb;
        $moduleName = $request->getModule();
        $rowno = $request->get('rowno');
        $viewer = $this->getViewer($request);
        $recordModel = Vtiger_Record_Model::getCleanInstance('Escrows');

        $moduleModel = $recordModel->getModule();

        $moduleFields = $moduleModel->getFields('LBL_DETAIL');
        $viewer->assign('ITEMCODES_MAPPING_RECORD_MODEL', $recordModel);
        $viewer->assign('ROWNO', $rowno+1);

        $viewer->assign('FIELDS_LIST', $moduleFields);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        echo $viewer->view('BlockEditFields.tpl','Escrows',true);
    }

    function duplicateBlock(Vtiger_Request $request) {
        global $adb;
        $moduleName = $request->getModule();
        $rowno = $request->get('rowno');
        $copyRowNo = $request->get('copy_rowno');
        $viewer = $this->getViewer($request);
        $recordModel = Vtiger_Record_Model::getCleanInstance('Escrows');

        $moduleModel = $recordModel->getModule();
        $moduleFields = $moduleModel->getFields('LBL_DETAIL');

        foreach($moduleFields as $fieldName=>$fieldModel){
            $fieldValue = $request->get($fieldName."_{$copyRowNo}");
            if($fieldModel->isEditable()) {
                $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
            }
        }

        $viewer->assign('ITEMCODES_MAPPING_RECORD_MODEL', $recordModel);
        $viewer->assign('ROWNO', $rowno+1);

        $viewer->assign('FIELDS_LIST', $moduleFields);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        echo $viewer->view('BlockEditFields.tpl','Escrows',true);
    }
}