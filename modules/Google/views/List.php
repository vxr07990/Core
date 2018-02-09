<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Google_List_View extends Vtiger_PopupAjax_View
{
    protected $noRecords = false;

    public function __construct()
    {
        $this->exposeMethod('Contacts');
        $this->exposeMethod('Calendar');
    }

    public function process(Vtiger_Request $request)
    {
        switch ($request->get('operation')) {
            case "sync":
                $this->renderSyncUI($request);
                break;
            case "removeSync":
                //if ($request->validateWriteAccess()) {
                    $this->deleteSync($request);
                //}
                break;
            default:
                $this->renderWidgetUI($request);
                break;
        }
    }

    public function renderWidgetUI(Vtiger_Request $request)
    {
        $sourceModule = $request->get('sourcemodule');
        $viewer       = $this->getViewer($request);
        $oauth2       = new Google_Oauth2_Connector($sourceModule);
        $firstime     = $oauth2->hasStoredToken();
        $viewer->assign('MODULE_NAME', $request->getModule());
        $viewer->assign('FIRSTTIME', $firstime);
        $viewer->assign('STATE', 'home');
        $viewer->assign('SYNCTIME', Google_Utils_Helper::getLastSyncTime($sourceModule));
        $viewer->assign('SOURCEMODULE', $request->get('sourcemodule'));
        $viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
        $viewer->view('Contents.tpl', $request->getModule());
    }

    public function renderSyncUI(Vtiger_Request $request)
    {
        $sourceModule = $request->get('sourcemodule');
        $viewer       = $this->getViewer($request);
        $viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
        $oauth2 = new Google_Oauth2_Connector($sourceModule);
        if ($request->has('oauth_verifier')) {
            try {
                $oauth2->getHttpClient($sourceModule);
            } catch (Exception $e) {
                $viewer->assign('DENY', true);
            }
            $viewer->assign('MODULE_NAME', $request->getModule());
            $viewer->assign('STATE', 'CLOSEWINDOW');
            $viewer->view('Contents.tpl', $request->getModule());
        } else {
            if (!empty($sourceModule)) {
                try {
                    $records = $this->invokeExposedMethod($sourceModule);
                    Google_Utils_Helper::updateSyncTime($sourceModule);
                } catch (Zend_Gdata_App_HttpException $e) {
                    $errorCode = $e->getResponse()->getStatus();
                    if ($errorCode == 401) {
                        $this->removeSynchronization($request);
                        $response = new Vtiger_Response();
                        $response->setError(401);
                        $response->emit();

                        return false;
                    }
                }
            }
            if (php_sapi_name() == 'cli') {
                $GLOBALS['record_sync'] = $records;
                return;
            }
            $firstime = $oauth2->hasStoredToken();
            $viewer->assign('MODULE_NAME', $request->getModule());
            $viewer->assign('FIRSTTIME', $firstime);
            $viewer->assign('RECORDS', $records);
            $viewer->assign('NORECORDS', $this->noRecords);
            $viewer->assign('SYNCTIME', Google_Utils_Helper::getLastSyncTime($sourceModule));
            $viewer->assign('STATE', $request->get('operation'));
            $viewer->assign('SOURCEMODULE', $request->get('sourcemodule'));
            if (!$firstime) {
                $viewer->view('Contents.tpl', $request->getModule());
            } else {
                echo $viewer->view('ContentDetails.tpl', $request->getModule(), true);
            }
        }
    }

    /**
     * Sync Contacts Records
     * @return <array> Count of Contacts Records
     */
    public function Contacts()
    {
        $user                          = Users_Record_Model::getCurrentUserModel();
        $controller                    = new Google_Contacts_Controller($user);
        $syncDirection                 = Google_Utils_Helper::getSyncDirectionForUser($user);

        while( $controller->targetConnector->moreRecordsExits() ||
               $controller->sourceConnector->moreRecordsExits() ) {

                   $records                       = $controller->synchronize();
                   $syncRecords                   = $this->getSyncRecordsCount($records);

               }

        return $syncRecords;
    }

    /**
     * Sync Calendar Records
     * @return <array> Count of Calendar Records
     */
    public function Calendar($userId = false)
    {
        if (!$userId) {
            $user = Users_Record_Model::getCurrentUserModel();
        } else {
            $user = new Users();
            $user = $user->retrieve_entity_info($userId, 'Users');
            $user = Users_Record_Model::getInstanceFromUserObject($user);
        }
        try {
            $controller = new Google_Calendar_Controller($user);
        } catch (Exception $ex) {
            if ($ex->getMessage() == 'Unable to refresh access token' && $ex->getCode() == 599) {
                //Access token with value of "invalid_grant" has been retrieved. Remove oauth2 data for user.
                $db = PearDatabase::getInstance();
                $sql = "DELETE FROM `vtiger_google_oauth2` WHERE userid=?";
                $db->pquery($sql, [$user->getId()]);
            }

            throw new Exception($ex->getMessage(), $ex->getCode());
        }
        //Initial call - limit of 100
        $records                       = $controller->synchronize();
        $syncRecords                   = $this->getSyncRecordsCount($records);
        //This continues while there are still records to sync
        while( $controller->targetConnector->moreRecordsExits() ||
               $controller->sourceConnector->moreRecordsExits() ) {

                   $records                       = $controller->synchronize();
                   $syncRecords                   = $this->getSyncRecordsCount($records);
                   $totalRecords                  = array_sum($syncRecords['vtiger']) + array_sum($syncRecords['google']);
                   if($totalRecords == 0){
                       break;
                   }
               }

        return $syncRecords;
    }

    /**
     * Removes Synchronization
     */
    public function removeSynchronization($request)
    {
        $sourceModule = $request->get('sourcemodule');
        $userModel    = Users_Record_Model::getCurrentUserModel();
        Google_Module_Model::removeSync($sourceModule, $userModel->getId());
    }

    public function deleteSync($request)
    {
        $sourceModule = $request->get('sourcemodule');
        $userModel    = Users_Record_Model::getCurrentUserModel();
        Google_Module_Model::deleteSync($sourceModule, $userModel->getId());
    }

    /**
     * Return the sync record added,updated and deleted count
     *
     * @param type $syncRecords
     *
     * @return array
     */
    public function getSyncRecordsCount($syncRecords)
    {
        $countRecords = ['vtiger' => ['update' => 0, 'create' => 0, 'delete' => 0], 'google' => ['update' => 0, 'create' => 0, 'delete' => 0]];
        foreach ($syncRecords as $key => $records) {
            if ($key == 'push') {
                $pushRecord = false;
                if (count($records) == 0) {
                    $pushRecord = true;
                }
                foreach ($records as $record) {
                    foreach ($record as $type => $data) {
                        if ($type == 'source') {
                            if ($data->getMode() == WSAPP_SyncRecordModel::WSAPP_UPDATE_MODE) {
                                $countRecords['vtiger']['update']++;
                            } elseif ($data->getMode() == WSAPP_SyncRecordModel::WSAPP_CREATE_MODE) {
                                $countRecords['vtiger']['create']++;
                            } elseif ($data->getMode() == WSAPP_SyncRecordModel::WSAPP_DELETE_MODE) {
                                $countRecords['vtiger']['delete']++;
                            }
                        }
                    }
                }
            } elseif ($key == 'pull') {
                $pullRecord = false;
                if (count($records) == 0) {
                    $pullRecord = true;
                }
                foreach ($records as $type => $record) {
                    foreach ($record as $type => $data) {
                        if ($type == 'target') {
                            if ($data->getMode() == WSAPP_SyncRecordModel::WSAPP_UPDATE_MODE) {
                                $countRecords['google']['update']++;
                            } elseif ($data->getMode() == WSAPP_SyncRecordModel::WSAPP_CREATE_MODE) {
                                $countRecords['google']['create']++;
                            } elseif ($data->getMode() == WSAPP_SyncRecordModel::WSAPP_DELETE_MODE) {
                                $countRecords['google']['delete']++;
                            }
                        }
                    }
                }
            }
        }
        if ($pullRecord && $pushRecord) {
            $this->noRecords = true;
        }

        return $countRecords;
    }

    /**
     * Function to get the list of Script models to be included
     *
     * @param Vtiger_Request $request
     *
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    public function getHeaderScripts(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();

        return $this->checkAndConvertJsScripts(["~libraries/bootstrap/js/bootstrap-popover.js", "modules.$moduleName.resources.List"]);
    }

    public function validateRequest(Vtiger_Request $request)
    {
        //don't do validation because there is a redirection from google
    }
}
