<?php

class Actuals_Detail_View extends Estimates_Detail_View
{
    public function process(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $recordId = $request->get('record');
        //logic to include Addresss List
        $addressListModule = Vtiger_Module_Model::getInstance('AddressList');
        if ($addressListModule && $addressListModule->isActive()) {
            $addressListModule->assignValueForAddressList($viewer,$recordId);
        }
        return parent::process($request); // TODO: Change the autogenerated stub
    }

    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        //Added to remove the module specific js, as they depend on inventory files
        $moduleBaseTariff = 'modules.' . $moduleName . '.resources.BaseTariff';
        $modulePopUpFile  = 'modules.'.$moduleName.'.resources.Popup';
        $moduleEditFile   = 'modules.'.$moduleName.'.resources.Edit';
        $moduleDetailFile = 'modules.'.$moduleName.'.resources.Detail';
        unset($headerScriptInstances[$moduleBaseTariff]);
        unset($headerScriptInstances[$modulePopUpFile]);
        unset($headerScriptInstances[$moduleEditFile]);
        unset($headerScriptInstances[$moduleDetailFile]);
        $jsFileNames   = [
            'modules.Inventory.resources.Popup',
            'modules.Inventory.resources.Detail',
            'modules.Inventory.resources.Edit',
            'modules.Quotes.resources.Detail',
            'modules.Quotes.resources.Edit',
            'modules.Estimates.resources.Detail',
            'modules.Estimates.resources.Edit',
            'modules.Estimates.resources.Common',
            'modules.Estimates.resources.BaseTariff',
        ];
        $jsFileNames[] = $moduleEditFile;
        $jsFileNames[] = $modulePopUpFile;
        $jsFileNames[] = "modules.$moduleName.resources.BaseTariff";
        if (getenv('INSTANCE_NAME') == 'sirva') {
            $jsFileNames[] = "modules.$moduleName.resources.TPGTariff";
            $jsFileNames[] = "modules.$moduleName.resources.BaseSIRVA";
        }
        $jsFileNames[]         = "modules.$moduleName.resources.Detail";
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }
}
