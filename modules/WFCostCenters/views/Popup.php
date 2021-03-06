<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class WFCostCenters_Popup_View extends Vtiger_Popup_View
{
    /*
     * Function to initialize the required data in smarty to display the List View Contents
     */
    public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer)
    {
        $moduleName          = $this->getModule($request);
        $cvId                = $request->get('cvid');
        $pageNumber          = $request->get('page');
        $orderBy             = $request->get('orderby');
        $sortOrder           = $request->get('sortorder');
        $sourceModule        = $request->get('src_module');
        $sourceField         = $request->get('src_field');
        $sourceRecord        = $request->get('src_record');
        $searchKey           = $request->get('search_key');
        $searchValue         = $request->get('search_value');
        $currencyId          = $request->get('currency_id');
        $relatedParentModule = $request->get('related_parent_module');
        $relatedParentId     = $request->get('related_parent_id');
        $agentId             = $request->get('agentId');
        $currentUser = vglobal('current_user');

        if($request->get('contact_type') == 'Transferee') {
            $db = PearDatabase::getInstance();
            $res = $db->pquery("SELECT cvid FROM `vtiger_customview` WHERE viewname=? AND userid=?", ['Transferees', 1]);
            $cvId = $res->fields['cvid'];
        }
        //To handle special operation when selecting record from Popup
        $getUrl = $request->get('get_url');

        if ($sourceModule == 'WFInventory') {
          $searchKey = 'accounts';
          $searchValue = $request->get('accountId');
        }

        //Check whether the request is in multi select mode
        $multiSelectMode = $request->get('multi_select');
        if (empty($multiSelectMode)) {
            $multiSelectMode = false;
        }
        if (empty($cvId)) {
            $customView     = new CustomView();
            $cvId = $customView->getDefaultFilter($moduleName);
        }

        if (empty($pageNumber)) {
            $pageNumber = '1';
        }
        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        $moduleModel             = Vtiger_Module_Model::getInstance($moduleName);
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel);
        $isRecordExists          = Vtiger_Util_Helper::checkRecordExistance($relatedParentId);
        if ($isRecordExists) {
            $relatedParentModule = '';
            $relatedParentId     = '';
        } elseif ($isRecordExists === null) {
            $relatedParentModule = '';
            $relatedParentId     = '';
        }
        if (!empty($relatedParentModule) && !empty($relatedParentId)) {
            $parentRecordModel = Vtiger_Record_Model::getInstanceById($relatedParentId, $relatedParentModule);
            $listViewModel     = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $moduleName, $label);
            $searchParmams = $request->get('search_params');
            if (empty($searchParmams)) {
                $searchParmams = [];
            }
            $transformedSearchParams = $this->transferListSearchParamsToFilterCondition($searchParmams, Vtiger_Module_Model::getInstance($moduleName));
            // generate where conditions based on custom view and search params
            $queryGenerator = new QueryGenerator($moduleName, $currentUser);
            $queryGenerator->initForCustomViewById($cvId);

            if (empty($transformedSearchParams)) {
                $transformedSearchParams = array();
            }
            $glue = "";
            if (count($queryGenerator->getWhereFields()) > 0 && (count($transformedSearchParams)) > 0) {
                $glue = QueryGenerator::$AND;
            }
            $queryGenerator->parseAdvFilterList($transformedSearchParams, $glue);
            $whereCondition = $queryGenerator->getWhereClause();
            $listViewModel->set('query_generator', $queryGenerator);
            $listViewModel->set('whereCondition', $whereCondition);

        } else {
            $listViewModel = Vtiger_ListView_Model::getInstanceForPopup($moduleName, $cvId);
            $searchParmams = $request->get('search_params');
            if (empty($searchParmams)) {
                $searchParmams = [];
            }
            $transformedSearchParams = $this->transferListSearchParamsToFilterCondition($searchParmams, $listViewModel->getModule());
            $listViewModel->set('search_params', $transformedSearchParams);
            foreach ($searchParmams as $fieldListGroup) {
                foreach ($fieldListGroup as $fieldSearchInfo) {
                    $fieldSearchInfo['searchValue'] = $fieldSearchInfo[2];
                    $fieldSearchInfo['fieldName']   = $fieldName = $fieldSearchInfo[0];
                    $searchParmams[$fieldName]      = $fieldSearchInfo;
                }
            }
        }
        $transformedSearchParams = Vtiger_Util_Helper::transferListSearchParamsToFilterCondition($searchParmams, $listViewModel->getModule());
        $listViewModel->set('search_params', $transformedSearchParams);
        if (!empty($orderBy)) {
            $listViewModel->set('orderby', $orderBy);
            $listViewModel->set('sortorder', $sortOrder);
        } else {
            $defaulSorting = $listViewModel->getDefaultSorting($cvId);
            if ($defaulSorting['sort_field']) {
                $listViewModel->set('orderby', $defaulSorting['sort_field']);
                $listViewModel->set('sortorder', $defaulSorting['sort_order']);
            }
        }
        if ((!empty($searchKey)) && (!empty($searchValue))) {
            $listViewModel->set('search_key', $searchKey);
            $listViewModel->set('search_value', $searchValue);
        }
        if (!empty($relatedParentModule) && !empty($relatedParentId)) {
            $this->listViewHeaders = $listViewModel->getHeaders();
            $models                = $listViewModel->getEntries($pagingModel);
            $noOfEntries           = count($models);
            foreach ($models as $recordId => $recordModel) {
                foreach ($this->listViewHeaders as $fieldName => $fieldModel) {
                    $recordModel->set($fieldName, $recordModel->getDisplayValue($fieldName));
                }
                $models[$recordId] = $recordModel;
            }
            $this->listViewEntries = $models;
            if (count($this->listViewEntries) > 0) {
                $parent_related_records = true;
            }
        } else {
            if (!empty($agentId)){
                $listViewModel->set('agentId', $agentId);
                session_start();
                $_SESSION['agentId'] = $agentId;
            }else{
                if(!empty( $_SESSION['agentId'])){
                    $listViewModel->set('agentId',  $_SESSION['agentId']);
                }
            }
            $this->listViewHeaders = $listViewModel->getListViewHeaders();
            $this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
        }
        // If there are no related records with parent module then, we should show all the records
        if (!$parent_related_records && !empty($relatedParentModule) && !empty($relatedParentId)) {
            $relatedParentModule = null;
            $relatedParentId     = null;
            $listViewModel = Vtiger_ListView_Model::getInstanceForPopup($moduleName, $cvId);
            if (!empty($orderBy)) {
                $listViewModel->set('orderby', $orderBy);
                $listViewModel->set('sortorder', $sortOrder);
            } else {
                $defaulSorting = $listViewModel->getDefaultSorting($cvId);
                if ($defaulSorting['sort_field']) {
                    $listViewModel->set('orderby', $defaulSorting['sort_field']);
                    $listViewModel->set('sortorder', $defaulSorting['sort_order']);
                }
            }
            if (!empty($sourceModule)) {
                $listViewModel->set('src_module', $sourceModule);
                $listViewModel->set('src_field', $sourceField);
                $listViewModel->set('src_record', $sourceRecord);
            }
            if ((!empty($searchKey)) && (!empty($searchValue))) {
                $listViewModel->set('search_key', $searchKey);
                $listViewModel->set('search_value', $searchValue);
            }
            $this->listViewHeaders = $listViewModel->getListViewHeaders();
            $this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
        }
        // End
        $noOfEntries = count($this->listViewEntries);
        if (empty($sortOrder)) {
            $sortOrder = "ASC";
        }
        if ($sortOrder == "ASC") {
            $nextSortOrder = "DESC";
            $sortImage     = "downArrowSmall.png";
        } else {
            $nextSortOrder = "ASC";
            $sortImage     = "upArrowSmall.png";
        }
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('RELATED_MODULE', $moduleName);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('SOURCE_MODULE', $sourceModule);
        $viewer->assign('SOURCE_FIELD', $sourceField);
        $viewer->assign('SOURCE_RECORD', $sourceRecord);
        $viewer->assign('RELATED_PARENT_MODULE', $relatedParentModule);
        $viewer->assign('RELATED_PARENT_ID', $relatedParentId);
        $viewer->assign('SEARCH_KEY', $searchKey);
        $viewer->assign('SEARCH_VALUE', $searchValue);
        $viewer->assign('ORDER_BY', $orderBy);
        $viewer->assign('SORT_ORDER', $sortOrder);
        $viewer->assign('NEXT_SORT_ORDER', $nextSortOrder);
        $viewer->assign('SORT_IMAGE', $sortImage);
        $viewer->assign('GETURL', $getUrl);
        $viewer->assign('CURRENCY_ID', $currencyId);
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        $viewer->assign('PAGING_MODEL', $pagingModel);
        $viewer->assign('PAGE_NUMBER', $pageNumber);
        $viewer->assign('LISTVIEW_ENTRIES_COUNT', $noOfEntries);
        $viewer->assign('LISTVIEW_HEADERS', $this->listViewHeaders);
        $viewer->assign('LISTVIEW_ENTRIES', $this->listViewEntries);
        if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false)) {
            if (!$this->listViewCount) {
                $this->listViewCount = $listViewModel->getListViewCount();
            }
            $totalCount = $this->listViewCount;
            $pageLimit  = $pagingModel->getPageLimit();
            $pageCount  = ceil((int) $totalCount / (int) $pageLimit);
            if ($pageCount == 0) {
                $pageCount = 1;
            }
            $viewer->assign('PAGE_COUNT', $pageCount);
            $viewer->assign('LISTVIEW_COUNT', $totalCount);
        }
        $viewer->assign('CUSTOM_VIEWS', CustomView_Record_Model::getAllByGroup($moduleName));
        $viewer->assign('SEARCH_DETAILS', $searchParmams);
        $viewer->assign('VIEWID', $cvId);

        $viewer->assign('MULTI_SELECT', $multiSelectMode);
        $viewer->assign('AGENTID', $agentId);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
    }


}
