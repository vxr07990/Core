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
{assign var=PICKLIST_VALUES value=$FIELD_MODEL->getUITypeModel()->getAppliedServices($EFFECTIVE_DATE_ID)}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{assign var="FIELD_VALUE_LIST" value=explode(' |##| ',$FIELD_MODEL->get('fieldvalue'))}
<input type="hidden" name="{$FIELD_MODEL->getFieldName()}" value="" />
{assign var=VIEW_NAME value={getPurifiedSmartyParameters('view')}}
<select id="{$MODULE}_{$VIEW_NAME}_fieldName_{$FIELD_MODEL->get('name')}" multiple class="select2" name="{$FIELD_MODEL->getFieldName()}[]" data-fieldinfo='{$FIELD_INFO}' {if $FIELD_MODEL->isMandatory() eq true} data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} {/if} style="width: 60%">
    {foreach item=PICKLIST_VALUE key=PICKLIST_NAME  from=$PICKLIST_VALUES}
        <option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if in_array(Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME), $FIELD_VALUE_LIST)} selected {/if}>{$PICKLIST_VALUE}</option>
    {/foreach}
</select>
{/strip}
