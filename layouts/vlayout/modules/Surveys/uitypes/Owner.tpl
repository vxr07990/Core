{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
{strip}
{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{if $FIELD_MODEL->get('uitype') eq '53'}
	{assign var=ALL_ACTIVEUSER_LIST value=$USER_MODEL->getAccessibleUsers()}
	{assign var=ALL_ACTIVEGROUP_LIST value=$USER_MODEL->getAccessibleGroups()}
	{assign var=ASSIGNED_USER_ID value=$FIELD_MODEL->get('name')}
    {assign var=CURRENT_USER_ID value=$USER_MODEL->get('id')}

	{assign var=ACCESSIBLE_USER_LIST value=$USER_MODEL->getAccessibleUsersForModule($MODULE)}
	{assign var=ACCESSIBLE_GROUP_LIST value=$USER_MODEL->getAccessibleGroupForModule($MODULE)}

	{assign var=FIELD_VALUE value=$FIELD_MODEL->get('fieldvalue')}

	{if !array_key_exists($FIELD_VALUE, $ACCESSIBLE_USER_LIST)}
        {*assign var=SAVED_OWNER_RECORD value=Users_Record_Model::getInstanceById($FIELD_VALUE,'Users')*}
        {*$ALL_ACTIVEUSER_LIST[$FIELD_VALUE] = $SAVED_OWNER_RECORD->getDisplayName()*}
        {$ALL_ACTIVEUSER_LIST[$FIELD_VALUE] = Users_Record_Model::getDisplaynameById($FIELD_VALUE)}
	{/if}

	{if $FIELD_VALUE eq ''}
		{assign var=FIELD_VALUE value=$CURRENT_USER_ID}
	{/if}
	<select {if $DEFAULT_CHZN eq 1}id="{$FIELD_MODEL->getFieldName()}"{/if} class="{if $DEFAULT_CHZN eq 0}chzn-select {/if}{$ASSIGNED_USER_ID}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-name="{$ASSIGNED_USER_ID}" name="{$ASSIGNED_USER_ID}" data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if}{if $FIELD_MODEL->get('disabled')}disabled{/if}>
        <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
        {if $FIELD_MODEL->get('name') != 'assigned_user_id' || !getenv('IGC_MOVEHQ')  || getenv('INSTANCE_NAME') == 'graebel'}
			<optgroup label="{vtranslate('LBL_USERS')}">
				{foreach key=OWNER_ID item=OWNER_NAME from=$ALL_ACTIVEUSER_LIST}
						<option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}' {if $FIELD_VALUE eq $OWNER_ID} selected {/if}
							{if array_key_exists($OWNER_ID, $ACCESSIBLE_USER_LIST)} data-recordaccess=true {else} data-recordaccess=false {/if}
							data-userId="{$CURRENT_USER_ID}">
						{$OWNER_NAME}
						</option>
				{/foreach}
			</optgroup>
			<optgroup label="{vtranslate('LBL_GROUPS')}">
				{foreach key=OWNER_ID item=OWNER_NAME from=$ALL_ACTIVEGROUP_LIST}
					<option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}' {if $FIELD_MODEL->get('fieldvalue') eq $OWNER_ID} selected {/if}
						{if array_key_exists($OWNER_ID, $ACCESSIBLE_GROUP_LIST)} data-recordaccess=true {else} data-recordaccess=false {/if} >
					{$OWNER_NAME}
					</option>
				{/foreach}
			</optgroup>
		{else}
			{assign var=EMPLOYEES_USERS_ID value=Surveys_Record_Model::getEmployeesUsersId()}
			{foreach key=OWNER_ID item=OWNER_NAME from=$ALL_ACTIVEUSER_LIST}
				{if in_array($OWNER_ID,$EMPLOYEES_USERS_ID)}
					<option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_ID}' {if $FIELD_MODEL->get('fieldvalue') eq $OWNER_ID} selected {/if}
							{if array_key_exists($OWNER_ID, $ACCESSIBLE_USER_LIST)} data-recordaccess=true {else} data-recordaccess=false {/if}
							data-userId="{$CURRENT_USER_ID}">
						{$OWNER_NAME}
					</option>
				{/if}
			{/foreach}
		{/if}
	</select>
{/if}
{* TODO - UI type 52 needs to be handled *}
{/strip}
