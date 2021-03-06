<?php
include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Retrieve.php';
include_once 'include/Webservices/Revise.php';

class WFLocations_Edit_View extends Vtiger_Edit_View
{
    public function process(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $singles = ['tag','create_multiple','wfslot_configuration', 'vault_capacity'];
        $viewer->assign('SINGLE_FIELDS',$singles);
        $record     = $request->get('record');
        if(!empty($record)){
            $viewer->assign('LOCK_TAG', $this->isTagEditable($record));
            $viewer->assign('BASE_TYPE', WFLocations_Edit_View::getBaseLocationType($record));
        } else {
            $viewer->assign('BASE_TYPE', false);
        }
        parent::process($request); // TODO: Change the autogenerated stub
    }
    private function isTagEditable($record) {
      $db = PearDatabase::getInstance();

      $result = $db->pquery('SELECT * FROM vtiger_inventoryhistory WHERE `location_from` = ?',[$record]);

      if($db->num_rows($result) > 0) {
        return true;
      } else {
        return false;
      }
    }



    public static function getBaseLocationType($recordId, $alreadyBase = false){
        if($alreadyBase){
            $baseLocation = Vtiger_Record_Model::getInstanceById($recordId);
        } else {
            $recordInstance = Vtiger_Record_Model::getInstanceById($recordId);
            $baseLocation = Vtiger_Record_Model::getInstanceById($recordInstance->get('wflocation_base'));
        }
        if(!$baseLocation){
            return false;
        }
        global $adb;
        $sql = "SELECT
                    `vtiger_wflocationtypes`.`wflocationtypes_type`
                FROM `vtiger_wflocationtypes`
                INNER JOIN `vtiger_crmentity`
                ON `vtiger_crmentity`.`crmid` = `vtiger_wflocationtypes`.`wflocationtypesid`
                WHERE `vtiger_crmentity`.`deleted` = 0
                AND `vtiger_wflocationtypes`.`wflocationtypesid` = ?";

        $dataResult = $adb->pquery($sql,[$baseLocation->get('wflocation_type')]);
        if ($adb->num_rows($dataResult)){
            $result = $adb->fetchByAssoc($dataResult);
            return $result['wflocationtypes_type'];
        }
    }
}
