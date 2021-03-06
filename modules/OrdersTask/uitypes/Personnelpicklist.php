<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class OrdersTask_Personnelpicklist_UIType extends Vtiger_Base_UIType
{

    /**
     * Function to get the Template name for the current UI Type object
     * @return <String> - Template Name
     */
    public function getTemplateName()
    {
        return 'uitypes/Personnelpicklist.tpl';
    }

    /**
     * Function to get the Display Value, for the current field type with given DB Insert Value
     * @param <Object> $value
     * @return <Object>
     */
    public function getReferenceModule()
    {
        return Vtiger_Module_Model::getInstance('EmployeeRoles');
    }

    /**
     * Function to get the display value in detail view
     * @param <Integer> crmid of record
     * @return <String>
     */
    public function getDisplayValue($value)
    {
        $result = '';
        if (!empty($value)) {
            $referenceModule = $this->getReferenceModule($value);
            if ($referenceModule) {
                $referenceModuleName = $referenceModule->get('name');
                $entityNames = getEntityName($referenceModuleName, array($value));
                $result = "<a href='index.php?module=$referenceModuleName&view=".$referenceModule->getDetailViewName()."&record=$value'
                        title='".vtranslate($referenceModuleName, $referenceModuleName)."'>$entityNames[$value]</a>";
                if ($value == -1) {
                    $result ='Any Personnel Type';
                }
            }
        }
        return $result;
    }

    public function getDBInsertValue($value)
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }
        return $value;
    }

    /**
     * Function to get the display value in edit view
     * @param reference record id
     * @return link
     */
    public function getEditViewDisplayValue($value)
    {
        $referenceModule = $this->getReferenceModule($value);
        if ($referenceModule) {
            $referenceModuleName = $referenceModule->get('name');
            $entityNames = getEntityName($referenceModuleName, array($value));
            return $entityNames[$value];
        }
        return '';
    }

    public function getListSearchTemplateName()
    {
        return 'uitypes/PersonnalpicklistSearchView.tpl';
    }
}
