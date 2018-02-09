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
<div class='modelContainer modal basicCreateView'>
	<div class="modal-header">
		<button data-dismiss="modal" class="close" title="{vtranslate('LBL_CLOSE')}">x</button>
		<h3>{vtranslate('LBL_ADD_ITEM_TO', $QUALIFIED_MODULE)}&nbsp;{vtranslate($SELECTED_PICKLIST_FIELDMODEL->get('label'),$SELECTED_MODULE_NAME)}</h3>
	</div>
	<form name="addItemForm" class="form-horizontal" method="post" action="index.php">
		<input type="hidden" name="module" value="PicklistCustomizer" />
		<input type="hidden" id="id_lead_manager" name="id_lead_manager" value="" />
		{*<input type="hidden" name="parent" value="Settings" />*}
		<input type="hidden" name="agentid" value="{$AGENT_MANAGER_ID}" />
		<input type="hidden" name="source_module" value="{$SELECTED_MODULE_NAME}" />
		<input type="hidden" name="action" value="SaveAjax" />
		<input type="hidden" name="mode" value="add" />
        <input type="hidden" name="fieldid" value="{$FIELD_ID}" />

		<input type="hidden" name="picklistName" value="{$SELECTED_PICKLIST_FIELDMODEL->get('name')}" />
		<input type="hidden" name="pickListValues" value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($SELECTED_PICKLISTFIELD_ALL_VALUES))}' />
		<div class="modal-body tabbable">
			<div class="control-group">
				<div class="control-label"><span class="redColor">*</span>{vtranslate('LBL_ITEM_VALUE',$QUALIFIED_MODULE)}</div>
				<div class="controls"><input type="text" data-prompt-position="topLeft:70" data-validation-engine="validate[required, funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-validator={Zend_Json::encode([['name'=>'FieldLabel']])} name="newValue"></div>
			</div>
		</div>
		{include file='ModalFooter.tpl'|@vtemplate_path:$qualifiedName}
	</form>
</div>
{/strip}
