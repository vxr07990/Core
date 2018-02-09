<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_MoveEasyIntegration_Module_Model extends Settings_Vtiger_Module_Model
{

    /**
     * Function to get the module model
     * @return string
     */
    public static function getCleanInstance()
    {
        return new self;
    }

    /**
     * Function to get the ListView Component Name
     * @return string
     */
    public function getDefaultViewName()
    {
        return 'Index';
    }

    /**
     * Function to get the EditView Component Name
     * @return string
     */
    public function getEditViewName()
    {
        return 'Edit';
    }

    public function getParentName()
    {
        return parent::getParentName();
    }

    public function getModule($raw=true)
    {
        $moduleName = self::getModuleName();
        if (!$raw) {
            $parentModule = self::getParentName();
            if (!empty($parentModule)) {
                $moduleName = $parentModule.':'.$moduleName;
            }
        }
        return $moduleName;
    }

    public function getMenuItem()
    {
        $menuItem = Settings_Vtiger_MenuItem_Model::getInstance('MoveEasyIntegration');
        return $menuItem;
    }

    /**
    * Function to get the url for default view of the module
    * @return <string> - url
    */
    public function getDefaultUrl()
    {
        return 'index.php?module='.$this->getModuleName().'&parent=Settings&view='.$this->getDefaultViewName();
    }

    public function getDetailViewUrl()
    {
        return 'index.php?module='.$this->getModuleName().'&parent=Settings&view='.$this->getDefaultViewName();
    }


   /**
    * Function to get the url for Edit view of the module
    * @return <string> - url
    */
    public function getEditViewUrl()
    {
        return 'index.php?module='.$this->getModuleName().'&parent=Settings&view='.$this->getEditViewName();
    }
}
