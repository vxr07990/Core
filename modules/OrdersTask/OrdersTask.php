<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
class OrdersTask extends CRMEntity
{
    public $db, $log; // Used in class functions of CRMEntity

    public $table_name = 'vtiger_orderstask';
    public $table_index= 'orderstaskid';
    public $column_fields = array();

    /** Indicator if this is a custom module or standard module */
    public $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_orderstaskcf', 'orderstaskid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_orderstask', 'vtiger_orderstaskcf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_orderstask'   => 'orderstaskid',
        'vtiger_orderstaskcf' => 'orderstaskid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Local Operations Task Name'=> Array('orderstask', 'orderstaskname'),
        'Start Date'=> Array('orderstask', 'start_date'),
        'End Date'=> Array('orderstask', 'end_date'),
        'Type'=>Array('orderstask','orderstasktype'),
        'Progress'=>Array('orderstask','orderstaskprogress'),
        'Assigned To' => Array('crmentity','smownerid')

    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'Local Operations Task Name'=> 'orderstaskname',
        'Start Date'=>'start_date',
        'End Date'=> 'end_date',
        'Type'=>'orderstasktype',
        'Progress'=>'orderstaskprogress',
        'Assigned To' => 'assigned_user_id'
    );

    // Make the field link to detail view from list view (Fieldname)
    public $list_link_field = 'orderstaskname';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Local Operations Task Name'=> Array('orderstask', 'orderstaskname'),
        'Start Date'=> Array('orderstask', 'start_date'),
        'Type'=>Array('orderstask','orderstasktype'),
        'Assigned To' => Array('crmentity','smownerid')
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'Local Operations Task Name'=> 'orderstaskname',
        'Start Date'=>'start_date',
        'Type'=>'orderstasktype',
        'Assigned To' => 'assigned_user_id'
    );

    // For Popup window record selection
    public $popup_fields = array('orderstaskname');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    public $sortby_fields = array();

    // For Alphabetical search
    public $def_basicsearch_col = 'orderstaskname';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'orderstaskname';

    // Required Information for enabling Import feature
    public $required_fields = array('orderstaskname'=>1);

    // Callback function list during Importing
    public $special_functions = array('set_import_assigned_user');

    public $default_order_by = 'orderstaskname';
    public $default_sort_order='ASC';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('createdtime', 'modifiedtime', 'orderstaskname', 'ordersid', 'assigned_user_id');

    public function __construct()
    {
        global $log, $currentModule;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = PearDatabase::getInstance();
        $this->log = $log;
    }

    public function save_module($module)
    {
        $db = PearDatabase::getInstance();
        $request = new Vtiger_Request($_REQUEST, $_REQUEST);

		
        if (isset($_REQUEST['assigned_employee'])) {
            $assignedEmployee = $request->get('assigned_employee');

            if (is_array($assignedEmployee)) {
                $fieldValue = implode(' |##| ', $assignedEmployee);
                $db->pquery('UPDATE vtiger_orderstask SET assigned_employee=? WHERE orderstaskid=?', array($fieldValue, $this->id));
            }
        }

        if (isset($_REQUEST['assigned_vehicles'])) {
            $assignedVehicles = $request->get('assigned_vehicles');

            if (is_array($assignedVehicles)) {
                $fieldValue = implode(' |##| ', $assignedVehicles);
                $db->pquery('UPDATE vtiger_orderstask SET assigned_vehicles=? WHERE orderstaskid=?', array($fieldValue, $this->id));
            }
        }

        if (isset($_REQUEST['assigned_vendor'])) {
            $assignedVendors = $request->get('assigned_vendor');

            if (is_array($assignedVendors)) {
                $fieldValue = implode(' |##| ', $assignedVendors);
                $db->pquery('UPDATE vtiger_orderstask SET assigned_vendor=? WHERE orderstaskid=?', array($fieldValue, $this->id));
            }
        }

        $OrdersTaskAddresses = Vtiger_Module_Model::getInstance('OrdersTaskAddresses');
        if($OrdersTaskAddresses && $OrdersTaskAddresses->isActive()){
            $OrdersTaskAddresses->saveAddresses($_REQUEST,$this->id);
        }
    }

    public function saveentity($module, $fileid = '') {
        $db = &PearDatabase::getInstance();
        $this->column_fields['orderstask_account'] = null;


        if( $this->column_fields['dispatch_status'] == 'Accepted' && $this->column_fields['service_date_from'] != '' && !$this->column_fields['date_spread'] && $this->column_fields['disp_assigneddate'] == ''){
			$this->column_fields['disp_assigneddate'] = $this->column_fields['service_date_from'];
		}
		


        if($this->column_fields['ordersid'])
        {
            $res = $db->pquery('SELECT orders_account FROM vtiger_orders INNER JOIN vtiger_orderstask USING(ordersid) WHERE orderstaskid=?',
                        [$this->id]);
            if($res && $row = $res->fetchRow())
            {
                $this->column_fields['orderstask_account'] = $row['orders_account'];
            }

            if(empty($this->column_fields['participating_agent'])) {
                $validAgents = ParticipatingAgents_Module_Model::getParticipantAgentsPicklistValues($this->column_fields['ordersid']);
                foreach($validAgents as $agent) {
                    $agentModel = Vtiger_Record_Model::getInstanceById($agent['value'], 'Agents');
                    if($agentModel->get('agentmanager_id') == $this->column_fields['agentid']) {
                        $this->column_fields['participating_agent'] = $agent['value'];
                        break;
                    }
                }
            }
        }

        parent::saveentity($module, $fileid);

    }

    /**
     * Return query to use based on given modulename, fieldname
     * Useful to handle specific case handling for Popup
     */
    public function getQueryByModuleField($module, $fieldname, $srcrecord)
    {
        // $srcrecord could be empty
    }

    /**
     * Get list view query (send more WHERE clause condition if required)
     */
    public function getListQuery($module, $where='')
    {
        $query = "SELECT vtiger_crmentity.*, $this->table_name.*";

        // Keep track of tables joined to avoid duplicates
        $joinedTables = array();

        // Select Custom Field Table Columns if present
        if (!empty($this->customFieldTable)) {
            $query .= ", " . $this->customFieldTable[0] . ".* ";
        }

        $query .= " FROM $this->table_name";

        $query .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

        $joinedTables[] = $this->table_name;
        $joinedTables[] = 'vtiger_crmentity';

        // Consider custom table join as well.
        if (!empty($this->customFieldTable)) {
            $query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
                      " = $this->table_name.$this->table_index";
            $joinedTables[] = $this->customFieldTable[0];
        }
        $query .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";
        $query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

        $joinedTables[] = 'vtiger_users';
        $joinedTables[] = 'vtiger_groups';

        $linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
                " INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
                " WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($module));
        $linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

        for ($i=0; $i<$linkedFieldsCount; $i++) {
            $related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
            $fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
            $columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

            $other =  CRMEntity::getInstance($related_module);
            vtlib_setup_modulevars($related_module, $other);

            if (!in_array($other->table_name, $joinedTables)) {
                $query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
                $joinedTables[] = $other->table_name;
            }
        }

        global $current_user;
        $query .= $this->getNonAdminAccessControlQuery($module, $current_user);
        $query .= "	WHERE vtiger_crmentity.deleted = 0 ".$usewhere;
        return $query;
    }

    /**
     * Apply security restriction (sharing privilege) query part for List view.
     */
    public function getListViewSecurityParameter($module)
    {
        global $current_user;
        require ('include/utils/LoadUserPrivileges.php');
        require ('include/utils/LoadUserSharingPrivileges.php');

        $sec_query = '';
        $tabid = getTabid($module);

        if ($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1
            && $defaultOrgSharingPermission[$tabid] == 3) {
            $sec_query .= " AND (vtiger_crmentity.smownerid in($current_user->id) OR vtiger_crmentity.smownerid IN
                    (
                        SELECT vtiger_user2role.userid FROM vtiger_user2role
                        INNER JOIN vtiger_users ON vtiger_users.id=vtiger_user2role.userid
                        INNER JOIN vtiger_role ON vtiger_role.roleid=vtiger_user2role.roleid
                        WHERE vtiger_role.parentrole LIKE '".$current_user_parent_role_seq."::%'
                    )
                    OR vtiger_crmentity.smownerid IN
                    (
                        SELECT shareduserid FROM vtiger_tmp_read_user_sharing_per
                        WHERE userid=".$current_user->id." AND tabid=".$tabid."
                    )
                    OR
                        (";

                    // Build the query based on the group association of current user.
                    if (sizeof($current_user_groups) > 0) {
                        $sec_query .= " vtiger_groups.groupid IN (". implode(",", $current_user_groups) .") OR ";
                    }
            $sec_query .= " vtiger_groups.groupid IN
                        (
                            SELECT vtiger_tmp_read_group_sharing_per.sharedgroupid
                            FROM vtiger_tmp_read_group_sharing_per
                            WHERE userid=".$current_user->id." and tabid=".$tabid."
                        )";
            $sec_query .= ")
                )";
        }
        return $sec_query;
    }

    /**
     * Create query to export the records.
     */
    public function create_export_query($where)
    {
        global $current_user;

        include("include/utils/ExportUtils.php");

        //To get the Permitted fields query and the permitted fields list
        $sql = getPermittedFieldsQuery('OrdersTask', "detail_view");

        $fields_list = getFieldsListFromQuery($sql);

        $query = "SELECT $fields_list, vtiger_users.user_name AS user_name
					FROM vtiger_crmentity INNER JOIN $this->table_name ON vtiger_crmentity.crmid=$this->table_name.$this->table_index";

        if (!empty($this->customFieldTable)) {
            $query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
                      " = $this->table_name.$this->table_index";
        }

        $query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
        $query .= " LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id and vtiger_users.status='Active'";

        $linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
                " INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
                " WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($thismodule));
        $linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

        for ($i=0; $i<$linkedFieldsCount; $i++) {
            $related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
            $fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
            $columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

            $other = CRMEntity::getInstance($related_module);
            vtlib_setup_modulevars($related_module, $other);

            $query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = $this->table_name.$columnname";
        }

        $query .= $this->getNonAdminAccessControlQuery($thismodule, $current_user);
        $where_auto = " vtiger_crmentity.deleted=0";

        if ($where != '') {
            $query .= " WHERE ($where) AND $where_auto";
        } else {
            $query .= " WHERE $where_auto";
        }

        return $query;
    }

    /**
     * Transform the value while exporting
     */
    public function transform_export_value($key, $value)
    {
        return parent::transform_export_value($key, $value);
    }

    /**
     * Function which will give the basic query to find duplicates
     */
    public function getDuplicatesQuery($module, $table_cols, $field_values, $ui_type_arr, $select_cols='')
    {
        $select_clause = "SELECT ". $this->table_name .".".$this->table_index ." AS recordid, vtiger_users_last_import.deleted,".$table_cols;

        // Select Custom Field Table Columns if present
        if (isset($this->customFieldTable)) {
            $query .= ", " . $this->customFieldTable[0] . ".* ";
        }

        $from_clause = " FROM $this->table_name";

        $from_clause .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

        // Consider custom table join as well.
        if (isset($this->customFieldTable)) {
            $from_clause .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
                      " = $this->table_name.$this->table_index";
        }
        $from_clause .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

        $where_clause = "	WHERE vtiger_crmentity.deleted = 0";
        $where_clause .= $this->getListViewSecurityParameter($module);

        if (isset($select_cols) && trim($select_cols) != '') {
            $sub_query = "SELECT $select_cols FROM  $this->table_name AS t " .
                " INNER JOIN vtiger_crmentity AS crm ON crm.crmid = t.".$this->table_index;
            // Consider custom table join as well.
            if (isset($this->customFieldTable)) {
                $sub_query .= " LEFT JOIN ".$this->customFieldTable[0]." tcf ON tcf.".$this->customFieldTable[1]." = t.$this->table_index";
            }
            $sub_query .= " WHERE crm.deleted=0 GROUP BY $select_cols HAVING COUNT(*)>1";
        } else {
            $sub_query = "SELECT $table_cols $from_clause $where_clause GROUP BY $table_cols HAVING COUNT(*)>1";
        }


        $query = $select_clause . $from_clause .
                    " LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=" . $this->table_name .".".$this->table_index .
                    " INNER JOIN (" . $sub_query . ") AS temp ON ".get_on_clause($field_values, $ui_type_arr, $module) .
                    $where_clause .
                    " ORDER BY $table_cols,". $this->table_name .".".$this->table_index ." ASC";

        return $query;
    }

    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
     */
    public function vtlib_handler($modulename, $event_type)
    {
        global $adb;
        if ($event_type == 'module.postinstall') {
            $ordersTaskResult = $adb->pquery('SELECT tabid FROM vtiger_tab WHERE name=?', array('OrdersTask'));
            $orderstaskTabid = $adb->query_result($ordersTaskResult, 0, 'tabid');

            // Mark the module as Standard module
            $adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($modulename));

            if (getTabid('CustomerPortal')) {
                $checkAlreadyExists = $adb->pquery('SELECT 1 FROM vtiger_customerportal_tabs WHERE tabid=?', array($orderstaskTabid));
                if ($checkAlreadyExists && $adb->num_rows($checkAlreadyExists) < 1) {
                    $maxSequenceQuery = $adb->query("SELECT max(sequence) as maxsequence FROM vtiger_customerportal_tabs");
                    $maxSequence = $adb->query_result($maxSequenceQuery, 0, 'maxsequence');
                    $nextSequence = $maxSequence+1;
                    $adb->query("INSERT INTO vtiger_customerportal_tabs(tabid,visible,sequence) VALUES ($orderstaskTabid,1,$nextSequence)");
                    $adb->query("INSERT INTO vtiger_customerportal_prefs(tabid,prefkey,prefvalue) VALUES ($orderstaskTabid,'showrelatedinfo',1)");
                }
            }

            $modcommentsModuleInstance = Vtiger_Module::getInstance('ModComments');
            if ($modcommentsModuleInstance && file_exists('modules/ModComments/ModComments.php')) {
                include_once 'modules/ModComments/ModComments.php';
                if (class_exists('ModComments')) {
                    ModComments::addWidgetTo(array('OrdersTask'));
                }
            }

            $result = $adb->pquery("SELECT 1 FROM vtiger_modentity_num WHERE semodule = ? AND active = 1", array($modulename));
            if (!($adb->num_rows($result))) {
                //Initialize module sequence for the module
                $adb->pquery("INSERT INTO vtiger_modentity_num values(?,?,?,?,?,?)", array($adb->getUniqueId("vtiger_modentity_num"), $modulename, 'PT', 1, 1, 1));
            }
        } elseif ($event_type == 'module.disabled') {
            // TODO Handle actions when this module is disabled.
        } elseif ($event_type == 'module.enabled') {
            // TODO Handle actions when this module is enabled.
        } elseif ($event_type == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
        } elseif ($event_type == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } elseif ($event_type == 'module.postupdate') {
            $modcommentsModuleInstance = Vtiger_Module::getInstance('ModComments');
            if ($modcommentsModuleInstance && file_exists('modules/ModComments/ModComments.php')) {
                include_once 'modules/ModComments/ModComments.php';
                if (class_exists('ModComments')) {
                    ModComments::addWidgetTo(array('OrdersTask'));
                }
            }

            $result = $adb->pquery("SELECT 1 FROM vtiger_modentity_num WHERE semodule = ? AND active = 1", array($modulename));
            if (!($adb->num_rows($result))) {
                //Initialize module sequence for the module
                $adb->pquery("INSERT INTO vtiger_modentity_num values(?,?,?,?,?,?)", array($adb->getUniqueId("vtiger_modentity_num"), $modulename, 'PT', 1, 1, 1));
            }
        }
    }

    /**
     * Function to check the module active and user action permissions before showing as link in other modules
     * like in more actions of detail view(orderss).
     */
    public static function isLinkPermitted($linkData)
    {
        $moduleName = "OrdersTask";
        if (vtlib_isModuleActive($moduleName) && isPermitted($moduleName, 'EditView') == 'yes') {
            return true;
        }
        return false;
    }

    /**
     * Handle saving related module information.
     * NOTE: This function has been added to CRMEntity (base class).
     * You can override the behavior by re-defining it here.
     */
    // function save_related_module($module, $crmid, $with_module, $with_crmid) { }

    /**
     * Handle deleting related module information.
     * NOTE: This function has been added to CRMEntity (base class).
     * You can override the behavior by re-defining it here.
     */
    //function delete_related_module($module, $crmid, $with_module, $with_crmid) { }

    /**
     * Handle getting related list information.
     * NOTE: This function has been added to CRMEntity (base class).
     * You can override the behavior by re-defining it here.
     */
    //function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

    /**
     * Handle getting dependents list information.
     * NOTE: This function has been added to CRMEntity (base class).
     * You can override the behavior by re-defining it here.
     */
    //function get_dependents_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }
}
