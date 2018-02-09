<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class OrdersTask_Assignedvendors_UIType extends Vtiger_Base_UIType
{

    /**
     * Function to get the Template name for the current UI Type object
     * @return <String> - Template Name
     */
    public function getTemplateName()
    {
        return 'uitypes/AssignedVendors.tpl';
    }

    /**
     * Function to get the Display Value, for the current field type with given DB Insert Value
     * @param <Object> $value
     * @return <Object>
     */
    public function getDisplayValue($value)
    {
        if (is_array($value)) {
            $value = implode(' |##| ', $value);
        }
        $vendorsIds = explode(' |##| ', $value);
        $displayValue = '';

        foreach ($vendorsIds as $vendorsId) {
            if (!$vendorsId) {
                continue;
            }
            try {
                $vendorRecordModel = Vtiger_Record_Model::getInstanceById($vendorsId, 'Vendors');
                $displayValue .= $vendorRecordModel->get('vendorname').' ('.$vendorRecordModel->get('vendor_no').')';
            } catch(Exception $e){
                $displayValue .= '(deleted)';
            }

            if ($vendorsId != end($vendorsIds)) {
                $displayValue .= ', ';
            }
        }
        return $displayValue;
    }

    public function getDBInsertValue($value)
    {
        if (is_array($value)) {
            $value = implode(' |##| ', $value);
        }
        return $value;
    }
}
