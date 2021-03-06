<!DOCTYPE html>
<html lang="{$config.lang|truncate:2:''}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>{$lang.page.upgrade}</title>

	<!--jQuery 库-->
	<script src="{$smarty.const.BG_URL_JS}jquery.min.js" type="text/javascript"></script>
	<link href="{$smarty.const.BG_URL_STATIC_INSTALL}{$config.ui}/css/install.css" type="text/css" rel="stylesheet">

	<!--bootstrap-->
	<link href="{$smarty.const.BG_URL_JS}bootstrap/css/bootstrap.min.css" type="text/css" rel="stylesheet">
	<script src="{$smarty.const.BG_URL_JS}bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

	<!--表单验证 js-->
	<link href="{$smarty.const.BG_URL_JS}baigoValidator/baigoValidator.css" type="text/css" rel="stylesheet">
	<script src="{$smarty.const.BG_URL_JS}baigoValidator/baigoValidator.js" type="text/javascript"></script>

	<!--表单 ajax 提交 js-->
	<link href="{$smarty.const.BG_URL_JS}baigoSubmit/baigoSubmit.css" type="text/css" rel="stylesheet">
	<script src="{$smarty.const.BG_URL_JS}baigoSubmit/baigoSubmit.js" type="text/javascript"></script>
</head>

<body>

	<div class="container global">

		<nav class="navbar navbar-inverse">
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="{$smarty.const.PRD_SSO_URL}" target="_blank">
						<img alt="baigo SSO" src="{$smarty.const.BG_URL_STATIC_ADMIN}{$config.ui}/image/admin_logo.png">
					</a>
				</div>
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle {if $tplData.errCount > 0}disabled{/if}" data-toggle="dropdown">
							{$lang.btn.jump}
							<span class="caret"></span>
						</a>
						{include "include/upgrade_menu.tpl" cfg=$cfg}
					</li>
				</ul>
			</div>
		</nav>

		<div class="panel panel-success">
			<div class="panel-heading">
				<h4>{$lang.page.upgrade} <span class="label label-success">{$cfg.sub_title}</span></h4>
			</div>

			<div class="panel-body">

				{if $smarty.const.PRD_SSO_PUB > $smarty.const.BG_INSTALL_PUB}
					<div class="alert alert-warning">
						<span class="glyphicon glyphicon-warning-sign"></span>
						{$lang.label.upgrade}
						<span class="label label-warning">{$smarty.const.BG_INSTALL_VER}</span>
						{$lang.label.to}
						<span class="label label-warning">{$smarty.const.PRD_SSO_VER}</span>
					</div>
				{/if}