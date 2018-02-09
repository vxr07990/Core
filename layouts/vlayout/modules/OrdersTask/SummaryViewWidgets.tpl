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
    {foreach item=DETAIL_VIEW_WIDGET from=$DETAILVIEW_LINKS['DETAILVIEWWIDGET']}
        {if ($DETAIL_VIEW_WIDGET->getLabel() eq 'Documents') }
            {assign var=DOCUMENT_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
        {elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'ModComments')}
            {assign var=COMMENTS_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
        {elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'LBL_UPDATES')}
            {assign var=UPDATES_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
        {elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'Resources')}
            {assign var=RESOURCES_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
        {/if}
    {/foreach}


<div class="row-fluid">
	<div class="span7">
		{* Module Summary View *}
			<div class="summaryView row-fluid">
				{$MODULE_SUMMARY}
			</div>
		{* Module Summary View Ends Here *}

		{* Summary View comments Widget*}
		{if $COMMENTS_WIDGET_MODEL}
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_comments" data-url="{$COMMENTS_WIDGET_MODEL->getUrl()}" data-name="{$COMMENTS_WIDGET_MODEL->getLabel()}">
					<div class="widget_header row-fluid">
						<span class="span9"><h4>{vtranslate($COMMENTS_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4></span>

						<span class="span3">
							{if $COMMENTS_WIDGET_MODEL->get('action')}
								<button class="btn pull-right addButton createRecord" type="button" data-url="{$COMMENTS_WIDGET_MODEL->get('actionURL')}">
									<strong>{vtranslate('LBL_ADD',$MODULE_NAME)}</strong>
								</button>
							{/if}
						</span>
						<input type="hidden" name="relatedModule" value="{$COMMENTS_WIDGET_MODEL->get('linkName')}" />
					</div>
					<div class="widget_contents">
					</div>
				</div>
			</div>
		{/if}
		{* Summary View Comments Widget Ends Here *}

        </div>
	<div class='span5' style="overflow: hidden">

		
                
                {* Summary View Loading Widgte*}
		{if $RESOURCES_WIDGET_MODEL}
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_tasks" data-url="{$RESOURCES_WIDGET_MODEL->getUrl()}" data-name="{$RESOURCES_WIDGET_MODEL->getLabel()}">
					<div class="widget_header row-fluid">
						<span class="span9">
							<div class="row-fluid">
								<span class="span4 margin0px"><h4>{vtranslate($RESOURCES_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4></span>
								<span class="span7">
									
								</span>
							</div>
						</span>
						<span class="span3">
							
								<button class="btn pull-right addButton addResource" id="Loading" type="button" data-url="{$RESOURCES_WIDGET_MODEL->get('actionURL')}" data-parent-related-field="projectid">
									<strong>{vtranslate('LBL_ADD',$MODULE_NAME)}</strong>
								</button>
							
						</span>
						<input type="hidden" name="relatedModule" value="{$RESOURCES_WIDGET_MODEL->get('linkName')}" />
					</div>
					<div class="widget_contents">
					</div>
				</div>
			</div>
		{/if}
		{* Summary View Loading Widget Ends Here *}
                
                
        
                
		{* Summary View Document Widget*}
		{if $DOCUMENT_WIDGET_MODEL}
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_documents" data-url="{$DOCUMENT_WIDGET_MODEL->getUrl()}" data-name="{$DOCUMENT_WIDGET_MODEL->getLabel()}">
					<div class="widget_header row-fluid">
						<span class="span9"><h4>{vtranslate($DOCUMENT_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4></span>
						<span class="span3">
							{if $DOCUMENT_WIDGET_MODEL->get('action')}
								<button class="btn pull-right addButton createRecord" type="button" data-url="{$DOCUMENT_WIDGET_MODEL->get('actionURL')}">
									<strong>{vtranslate('LBL_ADD',$MODULE_NAME)}</strong>
								</button>
							{/if}
						</span>
						<input type="hidden" name="relatedModule" value="{$DOCUMENT_WIDGET_MODEL->get('linkName')}" />
					</div>
					<div class="widget_contents">
					</div>
				</div>
			</div>
		{/if}
		{* Summary View Document Widget Ends Here*}

		{* Summary View Updates Widget *}
		{if $UPDATES_WIDGET_MODEL}
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_updates" data-url="{$UPDATES_WIDGET_MODEL->getUrl()}" data-name="{$UPDATES_WIDGET_MODEL->getLabel()}">
					<div class="widget_header row-fluid">
						<span class="span9"><h4>{vtranslate($UPDATES_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4></span>

						<span class="span3">
							{if $UPDATES_WIDGET_MODEL->get('action')}
								<button class="btn pull-right addButton createRecord" type="button" data-url="{$UPDATES_WIDGET_MODEL->get('actionURL')}">
									<strong>{vtranslate('LBL_ADD',$MODULE_NAME)}</strong>
								</button>
							{/if}
						</span>
						<input type="hidden" name="relatedModule" value="{$UPDATES_WIDGET_MODEL->get('linkName')}" />
					</div>
					<div class="widget_contents">
					</div>
				</div>
			</div>
		{/if}
		{* Summary View Updates Widget Ends Here*}
	</div>
</div>
{/strip}