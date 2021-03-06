/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_Popup_Js("AgentCompensationGroup_Popup_Js", {}, {
    /**
     * Function to get complete params
     */
    getCompleteParams: function () {
        var params = {};
        params['view'] = this.getView();
        params['src_module'] = this.getSourceModule();
        params['src_record'] = this.getSourceRecord();
        params['src_field'] = this.getSourceField();
        params['search_key'] = this.getSearchKey();
        params['search_value'] = this.getSearchValue();
        params['orderby'] = this.getOrderBy();
        params['sortorder'] = this.getSortOrder();
        params['page'] = this.getPageNumber();
        params['related_parent_module'] = this.getRelatedParentModule();
        params['related_parent_id'] = this.getRelatedParentRecord();
        params['module'] = this.getSearchedModule();

        if (jQuery('#agentId') != undefined) {
            params['agentId'] = jQuery('#agentId').val();
        }

        if (this.isMultiSelectMode()) {
            params['multi_select'] = true;
        }
        return params;
    },
});
