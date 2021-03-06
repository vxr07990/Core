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
<div class='modelContainer'>
	<div class="modal-header">
		<button data-dismiss="modal" class="close" title="{vtranslate('LBL_CLOSE')}">x</button>
		<h3>{vtranslate('LBL_DELETE_PICKLIST_ITEMS', $QUALIFIED_MODULE)}</h3>
	</div>
	<form id="deleteItemForm" class="form-horizontal" method="post" action="index.php">
		<input type="hidden" name="module" value="{$MODULE}" />
		{*<input type="hidden" name="parent" value="Settings" />*}
		<input type="hidden" id="delete_lead_manager" name="delete_lead_manager" value="" />
		<input type="hidden" name="source_module" value="{$SOURCE_MODULE}" />
		<input type="hidden" name="agentid" value="{$AGENT_MANAGER_ID}" />
		<input type="hidden" name="action" value="SaveAjax" />
		<input type="hidden" name="delete_value" value="{$FIELD_VALUE}" />
        <input type="hidden" name="fieldid" value="{$FIELD_ID}" />

		<input type="hidden" name="mode" value="remove" />
		<input type="hidden" name="picklistName" value="{$FIELD_MODEL->get('name')}" />
		<div class="modal-body tabbable" style="height:300px">
			<div class="control-group">
				<div class="control-label">{vtranslate('LBL_ITEMS_TO_DELETE',$QUALIFIED_MODULE)}</div>
				<div class="controls">
				{$FIELD_VALUE_HTML}

				</div><br>
				<div class="control-label">{vtranslate('LBL_REPLACE_IT_WITH',$QUALIFIED_MODULE)}</div>
				<div class="controls">
					<select id="replaceValue" name="replace_value" class="chzn-select" data-validation-engine="validate[required]">
						{foreach from=$PICKLIST_VALUES_REPLACEMENT key=PICKLIST_VALUE_KEY item=PICKLIST_VALUE}
							{if !(in_array($PICKLIST_VALUE, $FIELD_VALUES))}
								<option value="{$PICKLIST_VALUE_KEY}">{vtranslate($PICKLIST_VALUE,$SOURCE_MODULE)}</option>
							{/if}
						{/foreach}
					</select>
				</div>
			</div>
		</div>
		<div class="modal-footer">
		<div class=" pull-right cancelLinkContainer">
			<a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
		</div>
		<button class="btn btn-danger" type="submit" name="saveButton"><strong>{vtranslate('LBL_DELETE', $MODULE)}</strong></button>
	</div>
	</form>
</div>
{/strip}
