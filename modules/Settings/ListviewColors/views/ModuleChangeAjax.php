<?php
/* * *******************************************************************************
 * The content of this file is subject to the VTE List View Colors ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
 
class Settings_ListviewColors_ModuleChangeAjax_View extends Settings_Vtiger_Index_View {

    function __construct() {
        parent::__construct();
    }

    public function process(Vtiger_Request $request) {
        $active_module = $request->get('module_name', '');
        $viewer = $this->getViewer($request);

        $moduleModel = Vtiger_Module_Model::getInstance($active_module);
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel);

        $recordStructure = $recordStructureInstance->getStructure();
        $viewer->assign('RECORD_STRUCTURE', $recordStructure);

        $advance_criteria = null;
        $advanceFilterOpsByFieldType = Vtiger_Field_Model::getAdvancedFilterOpsByFieldType();
        $viewer->assign('ADVANCED_FILTER_OPTIONS', Vtiger_Field_Model::getAdvancedFilterOptions());
        $viewer->assign('ADVANCED_FILTER_OPTIONS_BY_TYPE', $advanceFilterOpsByFieldType);

        $dateFilters = Vtiger_Field_Model::getDateFilterTypes();
        foreach($dateFilters as $comparatorKey => $comparatorInfo) {
            $comparatorInfo['startdate'] = DateTimeField::convertToUserFormat($comparatorInfo['startdate']);
            $comparatorInfo['enddate'] = DateTimeField::convertToUserFormat($comparatorInfo['enddate']);
            $comparatorInfo['label'] = vtranslate($comparatorInfo['label'], $active_module);
            $dateFilters[$comparatorKey] = $comparatorInfo;
        }
        $viewer->assign('DATE_FILTERS', $dateFilters);
        $viewer->assign('ADVANCE_CRITERIA', $advance_criteria);
		$viewer->assign('SOURCE_MODULE', $active_module);

        $viewer->view('AdvanceFilter.tpl', 'Vtiger');

        die;
    }
}