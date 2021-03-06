<?php

class Orders_SaveStopField_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $field = $request->get('field');
        $value = $request->get('value');
        $record = $request->get('record');
        $id = $request->get('stopid');
        $field = 'stop_'.$field;
        $sql = "UPDATE `vtiger_extrastops` SET ".$field." = ? WHERE stop_order = ? AND stopid = ?";
        $result = $db->pquery($sql, array($value, $record, $id));
        $response = new Vtiger_Response();
        $response->setResult(true);
        $response->emit();
    }
}
