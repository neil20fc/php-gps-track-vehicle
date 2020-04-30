<?
	session_start();
	include ('init.php');
	include ('func/fn_common.php');	
	checkUserSession();
	
	setUserSessionSettings($_SESSION["user_id"]);
	loadLanguage($_SESSION['language'], $_SESSION["units"]);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><? echo $gsValues['NAME'].' '.$gsValues['VERSION']; ?></title>
	
	<?
		if (file_exists('favicon.png'))
		{
			echo '<link rel="shortcut icon" href="'.$gsValues['URL_ROOT'].'/favicon.png" type="image/x-icon">';
		}
		else
		{
			echo '<link rel="shortcut icon" href="'.$gsValues['URL_ROOT'].'/favicon.ico" type="image/x-icon">';
		}	
	?>
		
        <link type="text/css" href="theme/jquery-ui.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
        <link type="text/css" href="theme/jquery.qtip.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
        <link type="text/css" href="theme/ui.jqgrid.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
        <link type="text/css" href="theme/jquery.pnotify.default.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	<link type="text/css" href="theme/jquery.multiple.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	
	<link type="text/css" href="theme/style.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	<link type="text/css" href="theme/style.custom.php?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	
	<link type="text/css" href="theme/leaflet/leaflet.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	<link type="text/css" href="theme/leaflet/markercluster.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	<link type="text/css" href="theme/leaflet/leaflet-routing-machine.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	
	<?
	if ($gsValues['MAP_GOOGLE'] == 'true')
	{
		if ($gsValues['MAP_GOOGLE_KEY'] == '')
		{
			echo '<script src="'.$gsValues['HTTP_MODE'].'://maps.google.com/maps/api/js"></script>';
		}
		else
		{
			echo '<script src="'.$gsValues['HTTP_MODE'].'://maps.google.com/maps/api/js?key='.$gsValues['MAP_GOOGLE_KEY'].'"></script>';
		}
	}
	?>
	
	<?
	if ($gsValues['MAP_YANDEX'] == 'true')
	{
		if ($gsValues['MAP_YANDEX_KEY'] == '')
		{
			echo '<script src="'.$gsValues['HTTP_MODE'].'://api-maps.yandex.ru/2.1/?lang=ru-RU"></script>';
		}
		else
		{
			echo '<script src="'.$gsValues['HTTP_MODE'].'://api-maps.yandex.ru/2.1/?apikey='.$gsValues['MAP_YANDEX_KEY'].'&lang=ru-RU"></script>';
		}
	}
	?>
	
        <script type="text/javascript" src="js/leaflet/leaflet.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/es6-promise.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script>ES6Promise.polyfill();</script>
	
	<script type="text/javascript" src="js/md5.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/csv2json.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/xml2json.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/sprintf.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	
	<script type="text/javascript" src="js/leaflet/tile/google.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/leaflet/tile/bing.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/leaflet/tile/yandex.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/leaflet/leaflet.editable.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/leaflet/leaflet.markercluster.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/leaflet/leaflet.polylinedecorator.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/leaflet/leaflet.routingmachine.js?v='.$gsValues['VERSION_ID'].'"></script>
	<script type="text/javascript" src="js/leaflet/marker.rotate.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/leaflet/path.drag.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	
	<script type="text/javascript" src="js/jquery-2.1.4.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/jquery-migrate-1.2.1.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="js/jquery-ui.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="js/jquery.qtip.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="js/jquery.jqGrid.locale.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="js/jquery.jqGrid.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="js/jquery.pnotify.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/jquery.generatefile.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/jquery.blockUI.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/jquery.multiple.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>

        <script type="text/javascript" src="js/jquery.flot.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/jquery.flot.crosshair.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="js/jquery.flot.navigate.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="js/jquery.flot.selection.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/jquery.flot.time.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="js/jquery.flot.resize.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	
	<script type="text/javascript" src="js/jscolor.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	
	<script type="text/javascript" src="js/moment.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>	
	
	<script type="text/javascript" src="js/push.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>

	<script type="text/javascript" src="js/gs.config.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/gs.common.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/gs.connect.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        
        <?
	// check if spare parts files exist, if not, use joined file
        if(file_exists('js/src/gs.tracking.js'))
	{
	?>
		<script type="text/javascript" src="js/src/gs.datalist.js"></script>
		<script type="text/javascript" src="js/src/gs.billing.js"></script>
		<script type="text/javascript" src="js/src/gs.chat.js"></script>
		<script type="text/javascript" src="js/src/gs.cmd.js"></script>
		<script type="text/javascript" src="js/src/gs.dtc.js"></script>
		<script type="text/javascript" src="js/src/gs.maintenance.js"></script>
		<script type="text/javascript" src="js/src/gs.events.js"></script>
		<script type="text/javascript" src="js/src/gs.gui.js"></script>
		<script type="text/javascript" src="js/src/gs.history.inpexp.js"></script>
		<script type="text/javascript" src="js/src/gs.history.js"></script>
		<script type="text/javascript" src="js/src/gs.img.js"></script>
		<script type="text/javascript" src="js/src/gs.misc.js"></script>
		<script type="text/javascript" src="js/src/gs.notify.js"></script>
		<script type="text/javascript" src="js/src/gs.places.inpexp.js"></script>
		<script type="text/javascript" src="js/src/gs.places.js"></script>
		<script type="text/javascript" src="js/src/gs.places.markers.js"></script>
		<script type="text/javascript" src="js/src/gs.places.routes.js"></script>
		<script type="text/javascript" src="js/src/gs.places.zones.js"></script>
		<script type="text/javascript" src="js/src/gs.reports.js"></script>
		<script type="text/javascript" src="js/src/gs.tasks.js"></script>
		<script type="text/javascript" src="js/src/gs.rilogbook.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.customfields.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.drivers.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.events.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.groups.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.objects.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.passengers.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.sensors.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.service.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.subaccounts.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.templates.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.trailers.js"></script>
		<script type="text/javascript" src="js/src/gs.tracking.js"></script>
        <?
	}
	else
	{
	?>
        	<script type="text/javascript" src="js/gs.main.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <? 
	}
	?>
    </head>
    
    <body onload="load()" onUnload="unload()">
	<input id="load_file" type="file" style="display: none;" onchange=""/>
	
	<div id="loading_panel">
		<div class="table">
			<div class="table-cell center-middle">
				<div id="loading_panel_text">
					<div class="row">
						<img class="logo" src="<? echo $gsValues['URL_ROOT'].'/img/'.$gsValues['LOGO']; ?>" />	
					</div>
					<div class="row">
						<div class="loader">
							<span></span><span></span><span></span><span></span><span></span><span></span><span></span>
						</div>
					</div>
				</div>
			</div>    
		</div>
	</div>
	
	<div id="loading_data_panel" style="display: none;">
		<div class="table">
			<div class="table-cell center-middle">
				<div class="loader">
					<span></span><span></span><span></span><span></span><span></span><span></span><span></span>
				</div>
			</div>
		</div>
	</div>
	
	<div id="blocking_panel">
		<div class="table">
			<div class="table-cell center-middle">
				<div id="blocking_panel_text">
					<div class="row">
						<img class="logo" src="<? echo $gsValues['URL_ROOT'].'/img/'.$gsValues['LOGO']; ?>" />
					</div>
					<? echo '<a href="'.$gsValues['URL_LOGIN'].'">'.$la['SESSION_HAS_EXPIRED'].'</a>'; ?>
				</div>
			</div>
		</div>
	</div>
	
	<div id="content" style="visibility: hidden;">
		<? include ("inc/inc_panels.php"); ?>
		<? include ("inc/inc_menus.php"); ?>
		<? include ("inc/inc_dashboard.php"); ?>
		<? include ("inc/inc_dialogs.main.php"); ?>
		<? include ("inc/inc_dialogs.places.php"); ?>
		<? include ("inc/inc_dialogs.reports.php"); ?>
		<? include ("inc/inc_dialogs.tasks.php"); ?>
		<? include ("inc/inc_dialogs.rilogbook.php"); ?>
		<? include ("inc/inc_dialogs.dtc.php"); ?>
		<? include ("inc/inc_dialogs.maintenance.php"); ?>
		<? include ("inc/inc_dialogs.cmd.php"); ?>
		<? include ("inc/inc_dialogs.img.php"); ?>
		<? include ("inc/inc_dialogs.chat.php"); ?>
		<? include ("inc/inc_dialogs.settings.php"); ?>	
	</div>        
    </body>
</html>