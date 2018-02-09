<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Emails_InRelation_View extends Vtiger_RelatedList_View
{
    public function process(Vtiger_Request $request)
    {
        if(getenv('INSTANCE_NAME') != 'sirva') {
            return parent::process($request);
        }
        $moduleName        = $request->getModule();
        $relatedModuleName = $request->get('relatedModule');
        $parentId          = $request->get('record');
        $label             = $request->get('tab_label');
        $requestedPage     = $request->get('page');
        if (empty($requestedPage)) {
            $requestedPage = 1;
        }
        $viewName = $request->get('viewname');
        if (empty($viewName)) {
            $customView     = new CustomView();
            $viewName = $customView->getDefaultFilter($relatedModuleName);
        }
        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $requestedPage);
        $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $moduleName);
        $relationListView  = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $relatedModuleName, $label);
        $orderBy           = $request->get('orderby');
        $sortOrder         = $request->get('sortorder');
        if ($sortOrder == "ASC") {
            $nextSortOrder = "DESC";
            $sortImage     = "icon-chevron-down";
        } else {
            $nextSortOrder = "ASC";
            $sortImage     = "icon-chevron-up";
        }
        if (!empty($orderBy)) {
            $relationListView->set('orderby', $orderBy);
            $relationListView->set('sortorder', $sortOrder);
        }
        $models             = $relationListView->getEntries($pagingModel);
        $links              = $relationListView->getLinks();
        $header             = $relationListView->getHeaders();
        $noOfEntries        = count($models);
        $relationModel      = $relationListView->getRelationModel();
        $relatedModuleModel = $relationModel->getRelationModuleModel();
        $relationField      = $relationModel->getRelationField();
        $linkParams    = ['MODULE' => $moduleName, 'ACTION' => $request->get('view'), 'CVID' => $viewName];
        $linkModels    = $relationListView->getListViewMassActions($linkParams);
        $viewer             = $this->getViewer($request);
        $viewer->assign('LISTVIEW_MASSACTIONS', $linkModels['LISTVIEWMASSACTION']);
        $viewer->assign('RELATED_RECORDS', $models);
        $viewer->assign('PARENT_RECORD', $parentRecordModel);
        $viewer->assign('RELATED_LIST_LINKS', $links);
        $viewer->assign('RELATED_HEADERS', $header);
        $viewer->assign('RELATED_MODULE', $relatedModuleModel);
        $viewer->assign('RELATED_ENTIRES_COUNT', $noOfEntries);
        $viewer->assign('RELATION_FIELD', $relationField);
        if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false)) {
            $totalCount = $relationListView->getRelatedEntriesCount();
            $pageLimit  = $pagingModel->getPageLimit();
            $pageCount  = ceil((int) $totalCount / (int) $pageLimit);
            if ($pageCount == 0) {
                $pageCount = 1;
            }
            $viewer->assign('PAGE_COUNT', $pageCount);
            $viewer->assign('TOTAL_ENTRIES', $totalCount);
            $viewer->assign('PERFORMANCE', true);
        }
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('PAGING', $pagingModel);
        $viewer->assign('ORDER_BY', $orderBy);
        $viewer->assign('SORT_ORDER', $sortOrder);
        $viewer->assign('NEXT_SORT_ORDER', $nextSortOrder);
        $viewer->assign('SORT_IMAGE', $sortImage);
        $viewer->assign('COLUMN_NAME', $orderBy);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('IS_EDITABLE', $relationModel->isEditable());
        $viewer->assign('IS_DELETABLE', $relationModel->isDeletable());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('CUSTOM_VIEWS', CustomView_Record_Model::getAllByGroup($relatedModuleName));
        $viewer->assign('LOCKED_VIEWS', json_encode(Vtiger_Module_Model::getLockedFilters()));
        $viewer->assign('VIEW', $request->get('view'));

        return $viewer->view('EmailRelatedList.tpl', $moduleName, 'true');
    }
}
