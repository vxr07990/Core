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
<input type="hidden" id="disabledGoogleModules" value="{getenv('GOOGLE_ADDRESS_DISABLE')}">
{include file="EditViewBlocks.tpl"|@vtemplate_path:$MODULE}
{*{include file='EditViewTariffItems.tpl'|@vtemplate_path:$MODULE}*}
{include file='EditViewTariffMisc.tpl'|@vtemplate_path:$MODULE}
{include file="EditViewBlocksNotes.tpl"|@vtemplate_path:$MODULE}
{include file="EditViewActions.tpl"|@vtemplate_path:$MODULE}
