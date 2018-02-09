{strip}
<div class='container-fluid editViewContainer'>
        <input type="hidden" name="module" value="TimeCalculator">
        <input type="hidden" name="action" value="Save">
        <input type="hidden" name="RelatedSave" value="1">
        <input type="hidden" name="record" value="{$RECORD_ID}">
        <input type="hidden" name="agentid" value="{$AGENT_ID}">
        {foreach key=BLOCK_LABEL item=TIMECALCULATOR_BLOCK_FIELDS from=$TIMECALCULATOR_RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
            {if $TIMECALCULATOR_BLOCK_FIELDS|@count lte 0}{continue}{/if}
            <table class="table table-bordered blockContainer showInlineTable equalSplit{if is_array($HIDDEN_BLOCKS)}{if in_array($BLOCK_LABEL, $HIDDEN_BLOCKS)} hide{/if}{/if}">
                <thead>
                <tr>
                    <th class="blockHeader" colspan="4">{vtranslate($BLOCK_LABEL, $MODULE)}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    {assign var=COUNTER value=0}
                    {foreach key=FIELD_NAME item=FIELD_MODEL from=$TIMECALCULATOR_BLOCK_FIELDS name=blockfields}
                    {if $FIELD_NAME eq 'agentid'}
                        {continue}
                    {/if}
                    {assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
                    {if $isReferenceField eq 'reference' && count($FIELD_MODEL->getReferenceList()) < 1}{continue}{/if}
                    {if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
                    {if $COUNTER eq '1'}
                    <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                </tr>
                <tr>
                    {assign var=COUNTER value=0}
                    {/if}
                    {/if}
                    {if $COUNTER eq 2}
                </tr>
                <tr>
                    {assign var=COUNTER value=1}
                    {else}
                    {assign var=COUNTER value=$COUNTER+1}
                    {/if}
                    <td class="fieldLabel {$WIDTHTYPE}">
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
                        <td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                            <div class="row-fluid">
                                    <span class="span10">
                                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$TIMECALCULATOR_BLOCK_FIELDS}
                                    </span>
                            </div>
                        </td>
                    {/if}
                    {if $TIMECALCULATOR_BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
                        <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                    {/if}
                    {/foreach}
                    {* adding additional column for odd number of fields in a block *}
                    {if $TIMECALCULATOR_BLOCK_FIELDS|@end eq true and $TIMECALCULATOR_BLOCK_FIELDS|@count neq 1 and $COUNTER eq 1}
                        <td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
                    {/if}
                </tr>
                </tbody>
            </table>
            <br>

            {if $LONGCARRIES_MODULE_MODEL && $LONGCARRIES_MODULE_MODEL->isActive() && $BLOCK_LABEL eq 'LBL_TIMECALCULATOR_DETAILS'}
                {include file=vtemplate_path('LongCarriesEdit.tpl', 'LongCarries')}
            {/if}

            {if $FLIGHTS_MODULE_MODEL && $FLIGHTS_MODULE_MODEL->isActive() && $BLOCK_LABEL eq 'LBL_TIMECALCULATOR_DETAILS'}
                {include file=vtemplate_path('FlightsEdit.tpl', 'Flights')}
            {/if}

            {if $ELEVATORS_MODULE_MODEL && $ELEVATORS_MODULE_MODEL->isActive() && $BLOCK_LABEL eq 'LBL_TIMECALCULATOR_DETAILS'}
                {include file=vtemplate_path('ElevatorsEdit.tpl', 'Elevators')}
            {/if}
        {/foreach}
        <div class="row-fluid">
            <div class="pull-right">
                <button class="btn btn-success submitButton" type="button"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
            </div>
            <div class="clearfix"></div>
        </div>
        <br>
</div>
{/strip}