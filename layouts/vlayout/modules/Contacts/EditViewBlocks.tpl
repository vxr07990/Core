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


<div class='container-fluid editViewContainer'>
	<form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
		{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
		{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
			<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
		{/if}
		{assign var=QUALIFIED_MODULE_NAME value={$MODULE}}
		{assign var=IS_PARENT_EXISTS value=strpos($MODULE,":")}
		{if $IS_PARENT_EXISTS}
			{assign var=SPLITTED_MODULE value=":"|explode:$MODULE}
			<input type="hidden" name="module" value="{$SPLITTED_MODULE[1]}" />
			<input type="hidden" name="parent" value="{$SPLITTED_MODULE[0]}" />
		{else}
			<input type="hidden" name="module" value="{$MODULE}" />
		{/if}
		<input type="hidden" name="action" value="Save" />
		<input type="hidden" name="record" value="{$RECORD_ID}" />
		<input type="hidden" name="defaultCallDuration" value="{$USER_MODEL->get('callduration')}" />
		<input type="hidden" name="defaultOtherEventDuration" value="{$USER_MODEL->get('othereventduration')}" />
		{if $IS_RELATION_OPERATION }
			<input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}" />
			<input type="hidden" name="sourceRecord" value="{$SOURCE_RECORD}" />
			<input type="hidden" name="relationOperation" value="{$IS_RELATION_OPERATION}" />
		{/if}
		<div class="contentHeader row-fluid">
		{assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
		{if $RECORD_ID neq ''}
			<h3 class="span8 textOverflowEllipsis" title="{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} {$RECORD_STRUCTURE_MODEL->getRecordName()}">{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} - {$RECORD_STRUCTURE_MODEL->getRecordName()}</h3>
		{else}
			<h3 class="span8 textOverflowEllipsis">{vtranslate('LBL_CREATING_NEW', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)}</h3>
		{/if}
			<span class="pull-right">
				<button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
				<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
			</span>
		</div>

                <input type="hidden" id="hiddenFields" name="hiddenFields" value="{$HIDDEN_FIELDS}" />

		{foreach key=BLOCK_NAME item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
			{if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
			<table name="{$BLOCK_NAME}" class="table table-bordered blockContainer showInlineTable equalSplit{if is_array($HIDDEN_BLOCKS)}{if in_array($BLOCK_NAME, $HIDDEN_BLOCKS)} hide{/if}{/if}">
                <thead>
			<tr>
				<th class="blockHeader" colspan="4">{vtranslate($BLOCK_NAME, $MODULE)}</th>
			</tr>
                </thead>
                <tbody>
			<tr>
			{assign var=COUNTER value=0}
			{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
				{if $FIELD_NAME eq 'primary_phone_ext'}
					{continue}
				{/if}
				{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
				{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
					{if $COUNTER eq '1'}
                                    <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                                </tr>
                                <tr>
						{assign var=COUNTER value=0}
					{/if}
				{/if}
                {if $BLOCK_NAME neq 'LBL_CUSTOM_INFORMATION'}
                    {if $COUNTER eq 2}
                                </tr>
                                <tr>
                        {assign var=COUNTER value=1}
                    {else}
                        {assign var=COUNTER value=$COUNTER+1}
                    {/if}
                {/if}
				<td class="fieldLabel {$WIDTHTYPE} {if $BLOCK_NAME eq 'LBL_CUSTOM_INFORMATION' AND $FIELD_NAME neq 'contact_type' AND $TYPE_FIELD_MAP[$FIELD_NAME] neq $CONTACT_TYPE}hide{/if}">
					{if $isReferenceField neq "reference"}<label class="muted pull-right marginRight10px">{/if}
						{if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
						{if $isReferenceField eq "reference"}
							{assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
							{assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
							{if $REFERENCE_LIST_COUNT > 1}
								{assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
								{assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
								{if !empty($REFERENCED_MODULE_STRUCT)}
									{assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
								{/if}
								<span class="pull-right">
									{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
                                    <select id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown" class="chzn-select referenceModulesList streched" style="width:160px;">
										<optgroup>
											{foreach key=index item=value from=$REFERENCE_LIST}
												<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>
											{/foreach}
										</optgroup>
									</select>
								</span>
							{else}
								<label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>
							{/if}
						{elseif $FIELD_MODEL->get('uitype') eq "83"}
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER MODULE=$MODULE}
						{else}
							{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
						{/if}
					{if $isReferenceField neq "reference"}</label>{/if}
				</td>
				{if $FIELD_MODEL->get('uitype') neq "83"}
					<td class="fieldValue {$WIDTHTYPE} {if $BLOCK_NAME eq 'LBL_CUSTOM_INFORMATION' AND $FIELD_NAME neq 'contact_type' AND $TYPE_FIELD_MAP[$FIELD_NAME] neq $CONTACT_TYPE}hide{/if}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
						<div class="row-fluid">
							<span class="span10">
								{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
								{if $FIELD_NAME eq 'phone' && 'primary_phone_ext'|array_key_exists:$BLOCK_FIELDS}
									<span id='primaryPhoneSpan' {if $BLOCK_FIELDS['primary_phone_type']->get('fieldvalue') neq 'Work'}class='hide'{/if}>
									&nbsp; Ext:&nbsp;
									{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($BLOCK_FIELDS['primary_phone_ext']->getFieldInfo()))}
									{assign var="SPECIAL_VALIDATOR" value=$BLOCK_FIELDS['primary_phone_ext']->getValidator()}
									{assign var="FIELD_LABEL" value=$BLOCK_FIELDS['primary_phone_ext']->get('name')}
									<input id="{$MODULE}_editView_fieldName_{$FIELD_LABEL}" type="text" class="input-large {if $BLOCK_FIELDS['primary_phone_ext']->isNameField()}nameField{/if}" data-validation-engine="validate[{if $BLOCK_FIELDS['primary_phone_ext']->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" name="{$BLOCK_FIELDS['primary_phone_ext']->getFieldName()}" value="{$BLOCK_FIELDS['primary_phone_ext']->get('fieldvalue')}"
									{if $BLOCK_FIELDS['primary_phone_ext']->get('uitype') eq '3' || $BLOCK_FIELDS['primary_phone_ext']->get('uitype') eq '4'|| $BLOCK_FIELDS['primary_phone_ext']->isReadOnly()} readonly {/if} data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
									<style>
										[name=primary_phone_ext]{
											width: 50px;
										}
									</style>
									&nbsp;
									</span>
								{/if}
							</span>
						</div>
					</td>
				{/if}
				{if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
					<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
				{/if}
				{if $MODULE eq 'Events' && $BLOCK_LABEL eq 'LBL_EVENT_INFORMATION' && $smarty.foreach.blockfields.last }
					{include file=vtemplate_path('uitypes/FollowUp.tpl',$MODULE) COUNTER=$COUNTER}
				{/if}
			{/foreach}
		{* adding additional column for odd number of fields in a block *}
		{if $BLOCK_FIELDS|@end eq true and $BLOCK_FIELDS|@count neq 1 and $COUNTER eq 1}
			<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
		{/if}
			</tr>
</tbody>
			</table>
			<br>
		{/foreach}
{/strip}
