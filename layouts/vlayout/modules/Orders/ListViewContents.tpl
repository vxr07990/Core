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
<input type="hidden" id="view" value="{$VIEW}" />
<input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
<input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
<input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
<input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
<input type="hidden" id="alphabetSearchKey" value= "{$MODULE_MODEL->getAlphabetSearchField()}" />
<input type="hidden" id="Operator" value="{$OPERATOR}" />
<input type="hidden" id="alphabetValue" value="{$ALPHABET_VALUE}" />
<input type="hidden" id="totalCount" value="{$LISTVIEW_COUNT}" />
<input type='hidden' value="{$PAGE_NUMBER}" id='pageNumber'>
<input type='hidden' value="{$PAGING_MODEL->getPageLimit()}" id='pageLimit'>
<input type="hidden" value="{$LISTVIEW_ENTRIES_COUNT}" id="noOfEntries">

{assign var = ALPHABETS_LABEL value = vtranslate('LBL_ALPHABETS', 'Vtiger')}
{assign var = ALPHABETS value = ','|explode:$ALPHABETS_LABEL}
{if $LDDList neq 'true'}
<div class="alphabetSorting noprint">
	<table width="100%" class="table-bordered" style="border: 1px solid #ddd;table-layout: fixed">
		<tbody>
			<tr>
			{foreach item=ALPHABET from=$ALPHABETS}
				<td class="alphabetSearch textAlignCenter cursorPointer {if $ALPHABET_VALUE eq $ALPHABET} highlightBackgroundColor {/if}" style="padding : 0px !important"><a id="{$ALPHABET}" href="#">{$ALPHABET}</a></td>
			{/foreach}
			</tr>
		</tbody>
	</table>
</div>
{/if} 
<div id="selectAllMsgDiv" class="alert-block msgDiv noprint">
	<strong><a id="selectAllMsg">{vtranslate('LBL_SELECT_ALL',$MODULE)}&nbsp;{vtranslate($MODULE ,$MODULE)}&nbsp;(<span id="totalRecordsCount"></span>)</a></strong>
</div>
<div id="deSelectAllMsgDiv" class="alert-block msgDiv noprint">
	<strong><a id="deSelectAllMsg">{vtranslate('LBL_DESELECT_ALL_RECORDS',$MODULE)}</a></strong>
</div>
<div class="contents-topscroll noprint">
	<div class="topscroll-div">
		&nbsp;
	 </div>
</div>
<div class="listViewEntriesDiv contents-bottomscroll">
	<div class="bottomscroll-div">
	<input type="hidden" value="{$ORDER_BY}" id="orderBy">
	<input type="hidden" value="{$SORT_ORDER}" id="sortOrder">
	<span class="listViewLoadingImageBlock hide modal noprint" id="loadingListViewModal">
		<img class="listViewLoadingImage" src="{vimage_path('loading.gif')}" alt="no-image" title="{vtranslate('LBL_LOADING', $MODULE)}"/>
		<p class="listViewLoadingMsg">{vtranslate('LBL_LOADING_LISTVIEW_CONTENTS', $MODULE)}........</p>
	</span>
	{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
	<table class="table table-bordered listViewEntriesTable">
		<thead>
			<tr class="listViewHeaders">
				<th width="5%">
					<input type="checkbox" id="listViewEntriesMainCheckBox" />
				</th>
				{foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}


				<th nowrap {if $LISTVIEW_HEADER@last && $LDDList neq 'true' && !$EXTRA_HEADERS} colspan="2" {/if}>
					<a href="javascript:void(0);" class="listViewHeaderValues" data-nextsortorderval="{if $COLUMN_NAME eq $LISTVIEW_HEADER->get('column')}{$NEXT_SORT_ORDER}{else}ASC{/if}" data-columnname="{$LISTVIEW_HEADER->get('column')}">
                    {vtranslate($LISTVIEW_HEADER->get('label'), getTabModuleName($LISTVIEW_HEADER->getModuleId()))}
                    &nbsp;&nbsp;{if $COLUMN_NAME eq $LISTVIEW_HEADER->get('column')}<img class="{$SORT_IMAGE} icon-white">{/if}</a>
				</th>
                {if $LISTVIEW_HEADER@last}
					{foreach item=LISTVIEW_HEADER_EXTRA from=$EXTRA_HEADERS}
					<th nowrap>
						{vtranslate($LISTVIEW_HEADER_EXTRA.label, $MODULE)}
					</th>
					{/foreach}
					<th nowrap>
					</th>
				{/if}

                                {if $LDDList eq 'true' && $LISTVIEW_HEADER@last}
                                    <th nowrap >Shuttle Notification</th>
{*                                    <th nowrap>On Hold</th>
*}                                    <th nowrap colspan="3" >Agent Pickup</th>
                                {/if}
                                
				{/foreach}
			</tr>
		</thead>
        {if $MODULE_MODEL->isQuickSearchEnabled()}
        <tr>
            <td></td>
			{foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
             <td>
                 {assign var=FIELD_UI_TYPE_MODEL value=$LISTVIEW_HEADER->getUITypeModel()}
                {include file=vtemplate_path($FIELD_UI_TYPE_MODEL->getListSearchTemplateName(),$MODULE_NAME)
                    FIELD_MODEL= $LISTVIEW_HEADER SEARCH_INFO=$SEARCH_DETAILS[$LISTVIEW_HEADER->getName()] USER_MODEL=$CURRENT_USER_MODEL}
             </td>
                {if $LISTVIEW_HEADER@last}
				{foreach item=LISTVIEW_HEADER_EXTRA from=$EXTRA_HEADERS}
				<td>
				</td>
				{/foreach}
				{/if}
			{/foreach}
                        {if $LDDList eq 'true'} 
                            <td>&nbsp;</td>
                            <td colspan="2">&nbsp; </td>
                        
                        {/if}
                        
			<td>
				<button class="btn" data-trigger="listSearch">{vtranslate('LBL_SEARCH', $MODULE )}</button>
			</td>
        </tr>
        {/if}
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview key=LISTVIEW_KEY}
		<tr {if $LDDList eq 'true'} style="background-color:{$LISTVIEW_ENTRY->getBackgroundListViewColor($LISTVIEW_ENTRY->getId())}" {/if} class="listViewEntries" data-id='{$LISTVIEW_ENTRY->getId()}' data-recordUrl='{$LISTVIEW_ENTRY->getDetailViewUrl()}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
            <td  width="5%" class="{$WIDTHTYPE}">
				<input type="checkbox" value="{$LISTVIEW_ENTRY->getId()}" class="listViewEntriesCheckBox"/>
			</td>
			{foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
			{assign var=LISTVIEW_HEADERNAME value=$LISTVIEW_HEADER->get('name')}
			<td class="listViewEntryValue {$WIDTHTYPE}" data-field-type="{if $LDDList eq 'true' && $LISTVIEW_HEADER->getFieldDataType() eq 'ordersautoseq'}reference{else}{$LISTVIEW_HEADER->getFieldDataType()}{/if}" nowrap>
				{if ($LISTVIEW_HEADER->isNameField() eq true or $LISTVIEW_HEADER->get('uitype') eq '4') and $MODULE_MODEL->isListViewNameFieldNavigationEnabled() eq true }
					<a href="{$LISTVIEW_ENTRY->getDetailViewUrl()}">{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}</a>
				{else if $LISTVIEW_HEADER->get('uitype') eq '72'}
					{assign var=CURRENCY_SYMBOL_PLACEMENT value={$CURRENT_USER_MODEL->get('currency_symbol_placement')}}
					{if $CURRENCY_SYMBOL_PLACEMENT eq '1.0$'}
						{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}{$LISTVIEW_ENTRY->get('currencySymbol')}
					{else}
						{$LISTVIEW_ENTRY->get('currencySymbol')}{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
					{/if}
				{else if $LISTVIEW_HEADERNAME eq 'origin_zone' || $LISTVIEW_HEADERNAME eq 'empty_zone'}
					{assign var=RECORD_MODEL value=Vtiger_Record_Model::getInstanceById($LISTVIEW_ENTRY->getId())}
					{$RECORD_MODEL->getZoneAdminDisplayValue($LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME))}
				{else}
                    {if $LISTVIEW_HEADER->getFieldDataType() eq 'double'}
                        {decimalFormat($LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME))}
                    {else}
                        {$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
                    {/if}
				{/if}
				{if $LISTVIEW_HEADER@last}
					{foreach item=LISTVIEW_HEADER_EXTRA from=$EXTRA_HEADERS}
						</td>
						<td>
						{$LISTVIEW_ENTRY->get($LISTVIEW_HEADER_EXTRA.label)}
					{/foreach}
				{/if}

				{if $LISTVIEW_HEADER@last}
				</td>
                                {if $LDDList eq 'true'}
                                    <td><span data-id="{$LISTVIEW_ENTRY->getId()}"  class="shuttle-status">{$LISTVIEW_ENTRY->getShuttleStatus($LISTVIEW_ENTRY->getId())}</span></td>
{*                                    <td><input data-id="{$LISTVIEW_ENTRY->getId()}" type="checkbox"  {if $LISTVIEW_ENTRY->getOnHoldStatus($LISTVIEW_ENTRY->getId()) eq true} checked {/if}  class="on-hold"></td>
*}                                    <td style="min-width: 80px;"><input data-id="{$LISTVIEW_ENTRY->getId()}" type="checkbox" {if $LISTVIEW_ENTRY->getAPUCheck($LISTVIEW_ENTRY->getId()) eq true} checked {/if} class="apu"></td>
                                    <td><div class="input-append row-fluid apu-dates" style="  min-width: 150px;"><div class="span12 row-fluid date"><input data-id="{$LISTVIEW_ENTRY->getId()}" id="" type="text" class="dateFieldApu orderstask_apudate" disabled name="orderstask_apudate" data-date-format="{$CURRENT_USER_MODEL->get('date_format')}" type="text" value="" style="width:100px;"/><span class="add-on"><i class="icon-calendar"></i></span></div></div></td>
                                {/if}
                                
                                <td nowrap class="{$WIDTHTYPE}">
				<div class="actions pull-right">
					<span class="actionImages">
                                            
                                            {if $LDDList eq 'true'}
                                            	<i style="margin-right: 5px;" id="{$LISTVIEW_ENTRY->getId()}" onclick="" class="overflow icon-share"></i>&nbsp;
                                            {/if}
						<a href="{$LISTVIEW_ENTRY->getFullDetailViewUrl()}"><i title="{vtranslate('LBL_SHOW_COMPLETE_DETAILS', $MODULE)}" class="icon-th-list alignMiddle"></i></a>&nbsp;
						{if $IS_MODULE_EDITABLE}{* && $CURRENT_USER_MODEL->getExtraPermission($LISTVIEW_KEY)*}
							<a href='{$LISTVIEW_ENTRY->getEditViewUrl()}'><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>&nbsp;
						{/if}
						{if $IS_MODULE_DELETABLE}{* && $CURRENT_USER_MODEL->getExtraPermission($LISTVIEW_KEY)*}
							<a class="deleteRecordButton"><i title="{vtranslate('LBL_DELETE', $MODULE)}" class="icon-trash alignMiddle"></i></a>
						{/if}
					</span>
				</div></td>
				{/if}
			</td>
			{/foreach}
		</tr>
		{/foreach}
	</table>

<!--added this div for Temporarily -->
{if $LISTVIEW_ENTRIES_COUNT eq '0'}
	<table class="emptyRecordsDiv">
		<tbody>
			<tr>
				<td>
					{assign var=SINGLE_MODULE value="SINGLE_$MODULE"}
					{vtranslate('LBL_NO')} {vtranslate($MODULE, $MODULE)} {vtranslate('LBL_FOUND')}.{if $IS_MODULE_EDITABLE} {vtranslate('LBL_CREATE')} <a href="{$MODULE_MODEL->getCreateRecordUrl()}">{vtranslate($SINGLE_MODULE, $MODULE)}</a>{/if}
				</td>
			</tr>
		</tbody>
	</table>
{/if}
</div>
</div>
{/strip}
