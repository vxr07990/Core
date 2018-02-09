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
<!DOCTYPE html>
<html>
	<head>
		{if getenv(SIRVA_APPDYNAMICS) == "1"}
		<script>window["adrum-start-time"] = new Date().getTime(); window["adrum-app-key"] ="{getenv(SIRVA_ADRUM_KEY)}";</script><script src="https://cdn.appdynamics.com/adrum/adrum-latest.js"></script>
		{/if}
		<title>
			{vtranslate($PAGETITLE, $MODULE_NAME)}
		</title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
        {*Stop caching, lowers speeds but prevents UI errors*}
        <meta name="robots" content="noindex" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="expires" content="0" />
		<meta http-equiv="pragma" content="no-cache" />
        {*End caching fix.*}
		<link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
        <link rel="manifest" href="manifest.json">
        <link rel="mask-icon" href="safari-pinned-tab.svg" color="#f84e57">
        <meta name="theme-color" content="#ffffff">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="libraries/jquery/chosen/chosen.css" type="text/css" media="all" />
		<link rel="stylesheet" href="libraries/jquery/jquery-ui/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" media="all" />

		<link rel="stylesheet" href="libraries/jquery/select2/select2.css" type="text/css" media="all" />
		<link rel="stylesheet" href="libraries/bootstrap/css/bootstrap.css" type="text/css" media="all" />
                <link rel="stylesheet" href="libraries/bootstrap/css/jqueryBxslider.css" type="text/css" media="all" />
		<link rel="stylesheet" href="resources/styles.css" type="text/css" media="all" />
		<link rel="stylesheet" href="libraries/jquery/posabsolute-jQuery-Validation-Engine/css/validationEngine.jquery.css" />

		<link rel="stylesheet" href="libraries/jquery/select2/select2.css" />

		<link rel="stylesheet" href="libraries/guidersjs/guiders-1.2.6.css"/>
		<link rel="stylesheet" href="libraries/jquery/pnotify/jquery.pnotify.default.css"/>
		<link rel="stylesheet" href="libraries/jquery/pnotify/use for pines style icons/jquery.pnotify.default.icons.css"/>
		<link rel="stylesheet" media="all" type="text/css" href="libraries/jquery/datepicker/css/datepicker.css" />
		{foreach key=index item=cssModel from=$STYLES}
                    <link rel="{$cssModel->getRel()}" href="{vresource_url($cssModel->getHref())}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
		{/foreach}

		{* For making pages - print friendly *}
		<style type="text/css">
		@media print {
		.noprint { display:none; }
		}
		</style>

		{* This is needed as in some of the tpl we are using jQuery.ready *}
		<script type="text/javascript" src="libraries/jquery/jquery.min.js"></script>
		<!--[if IE]>
		<script type="text/javascript" src="libraries/html5shim/html5.js"></script>
		<script type="text/javascript" src="libraries/html5shim/respond.js"></script>
		<![endif]-->
		{* ends *}

		{* ADD <script> INCLUDES in JSResources.tpl - for better performance *}
	</head>

	<body data-skinpath="{$SKIN_PATH}" data-language="{$LANGUAGE}">
		<div id="js_strings" class="hide noprint">{Zend_Json::encode($LANGUAGE_STRINGS)}</div>
		{assign var=CURRENT_USER_MODEL value=Users_Record_Model::getCurrentUserModel()}
		<input type="hidden" id="start_day" value="{$CURRENT_USER_MODEL->get('dayoftheweek')}" />
		<input type="hidden" id="row_type" value="{$CURRENT_USER_MODEL->get('rowheight')}" />
		<input type="hidden" id="current_user_id" value="{$CURRENT_USER_MODEL->get('id')}" />
		<input type="hidden" name="instance" value="{getenv('INSTANCE_NAME')}" />
		<input type="hidden" name="movehq" value="{getenv('IGC_MOVEHQ')}" />
		<div id="page">
			<!-- container which holds data temporarly for pjax calls -->
			<div id="pjaxContainer" class="hide noprint"></div>
{/strip}
