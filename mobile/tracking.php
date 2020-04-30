<?	
	session_start();
	include ('../init.php');
	include ('../func/fn_common.php');
	checkUserSession();
        
	setUserSessionSettings($_SESSION["user_id"]);
	loadLanguage($_SESSION['language'], $_SESSION["units"]);
	
	// get mobile app cookie
	if (isset($_COOKIE['app']))
        {
                $_SESSION['app'] = $_COOKIE['app'];
        }
	else
	{
		$_SESSION['app'] = 'false';
	}
	
	// push notifications cookie
	$expire = time() + 2592000;
	setcookie('push_notify_identifier', $_SESSION['push_notify_identifier'], $expire, '/');
        setcookie('push_notify_mobile', $_SESSION['push_notify_mobile'], $expire, '/');
	setcookie('push_notify_mobile_interval', $_SESSION['push_notify_mobile_interval'], $expire, '/');		
?>

<!DOCTYPE html>
<html lang="en">
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><? echo $gsValues['NAME'].' '.$gsValues['VERSION']; ?></title>
	
	<?
		if (file_exists('../favicon.png'))
		{
			echo '<link rel="shortcut icon" href="'.$gsValues['URL_ROOT'].'/favicon.png" type="image/x-icon">';
		}
		else
		{
			echo '<link rel="shortcut icon" href="'.$gsValues['URL_ROOT'].'/favicon.ico" type="image/x-icon">';
		}	
	?>
	
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	
	<link type="text/css" href="../theme/leaflet/leaflet.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	<link type="text/css" href="../theme/leaflet/markercluster.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
    
        <link type="text/css" href="theme/bootstrap.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="stylesheet">
	<link type="text/css" href="theme/datetimepicker.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="stylesheet">
	<link type="text/css" href="theme/style.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="stylesheet">
	<link type="text/css" href="theme/style.custom.php?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	
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
	
	<script type="text/javascript" src="../js/leaflet/leaflet.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="../js/es6-promise.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script>ES6Promise.polyfill();</script>
	
	<script type="text/javascript" src="../js/leaflet/tile/google.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="../js/leaflet/tile/bing.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="../js/leaflet/tile/yandex.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="../js/leaflet/leaflet.markercluster.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="../js/leaflet/marker.rotate.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	
	<script type="text/javascript" src="../js/jquery-2.1.4.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="../js/jquery-migrate-1.2.1.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	
	<script type="text/javascript" src="../js/jquery.flot.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="../js/jquery.flot.navigate.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="../js/jquery.flot.time.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="../js/jquery.flot.resize.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	
	<script type="text/javascript" src="../js/moment.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
                <script type="text/javascript" src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
                <script type="text/javascript" src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <![endif]-->
        
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script type="text/javascript" src="js/bootstrap.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/bootbox.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="js/datetimepicker.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	
	<script type="text/javascript" src="../js/gs.config.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="../js/gs.common.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="js/gs.connect.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	
	<?
	// check if spare parts files exist, if not, use joined file
        if(file_exists('js/src/gs.tracking.js'))
	{
	?>
		<script type="text/javascript" src="js/src/gs.datalist.js"></script>
		<script type="text/javascript" src="js/src/gs.cmd.js"></script>
		<script type="text/javascript" src="js/src/gs.events.js"></script>
		<script type="text/javascript" src="js/src/gs.gui.js"></script>
		<script type="text/javascript" src="js/src/gs.places.markers.js"></script>
		<script type="text/javascript" src="js/src/gs.places.routes.js"></script>
		<script type="text/javascript" src="js/src/gs.places.zones.js"></script>
		<script type="text/javascript" src="js/src/gs.history.js"></script>
		<script type="text/javascript" src="js/src/gs.misc.js"></script>
		<script type="text/javascript" src="js/src/gs.notify.js"></script>
		<script type="text/javascript" src="js/src/gs.settings.js"></script>
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
        <nav class="navbar navbar-default navbar-fixed-top">
		<div class="container-fluid">
			<a href="#" class="show-menu icon-only pull-left" onclick="switchPage('menu');">
				<i class="glyphicon glyphicon-menu-hamburger"></i>
			</a>
			
			<div class="navbar-header pull-right">
				<select id="map_layer" class="navbar-btn form-control" onChange="switchMapLayer($(this).val());"></select>
			</div>
			
			<div class="navbar-header pull-right">
				<select id="event_list_page" class="navbar-btn form-control" style="display: none;" onChange="eventsLoadList();">
					<option value="1">1</option>
				</select>
			</div>
			
			<div class="navbar-header pull-right">
				<select id="marker_list_page" class="navbar-btn form-control" style="display: none;" onChange="placesMarkerLoadList();">
					<option value="1">1</option>
				</select>
			</div>
			
			<div class="navbar-header pull-right">
				<select id="route_list_page" class="navbar-btn form-control" style="display: none;" onChange="placesRouteLoadList();">
					<option value="1">1</option>
				</select>
			</div>
			
			<div class="navbar-header pull-right">
				<select id="zone_list_page" class="navbar-btn form-control" style="display: none;" onChange="placesZoneLoadList();">
					<option value="1">1</option>
				</select>
			</div>
			
			<div class="navbar-header">
				<div class="navbar-brand">
					<div id="page_title">
						<? echo $la['MAP']; ?>
					</div>
				</div>
			</div>	
		</div>
        </nav>
	
	<div id="loading_panel">
		<div class="table">
			<div class="table-cell center-middle">
				<div class="loader">
					<span></span><span></span><span></span><span></span><span></span><span></span><span></span>
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
	
	<div id="dt_picker"></div>
	
	<div id="page_map" class="page-map">
		<div id="map"></div>
		
		<div id="bottom_panel">
			<div class="datalist-object-name">
				<span id="bottom_panel_datalist_object_name"></span>
				<a href="#" onclick="datalistBottomHidePanel();"><span class="datalist-object-name-close-icon icon-close"></span></a>
			</div>
			<div id="bottom_panel_datalist" class="datalist">
				<div id="bottom_panel_datalist_object_data_list" class="datalist-item-list"></div>	
			</div>			
		</div>
		
		<div id="graph_panel" class="graph-panel">
			<div class="graph-controls">
				<div class="graph-controls-left">
					<select class="form-control" style="min-width:100px;" id="graph_panel_data_source" onchange="historyRouteChangeGraphSource();"></select>
				</div>
				<div class="graph-controls-right">
					<a href="#" onclick="graphPanLeft();" title="<? echo $la['PAN_LEFT'];?>">
						<img src="../theme/images/arrow-left.svg" width="16px" border="0"/>
					</a>
					
					<a href="#" onclick="graphPanRight();" title="<? echo $la['PAN_RIGHT'];?>">
						<img src="../theme/images/arrow-right.svg" width="16px" border="0"/>
					</a>
					  
					<a href="#" onclick="graphZoomIn();" title="<? echo $la['ZOOM_IN'];?>">
						<img src="../theme/images/plus.svg" width="16px" border="0"/>
					</a>
					
					<a href="#" onclick="graphZoomOut();" title="<? echo $la['ZOOM_OUT'];?>">
						<img src="../theme/images/minus.svg" width="16px" border="0"/>
					</a>
				</div>
			</div>
			
			<span id="graph_panel_label" class="graph-panel-label"></span>
						
			<div id="graph_panel_plot" class="graph-panel-plot"></div>
		</div>
		
		<div id="details_panel" class="details-panel">
			<div id="details_panel_detail_list" class="panel panel-default"></div>
			<div id="details_panel_detail_ext_list" class="list-group"></div>
		</div>
		
		<div id="history_playback">
			<div class="container-fluid">
				<div class="row vertical-align">
					<a onclick="historyRoutePlay();">
						<i class="glyphicon glyphicon-play"></i>
					</a>
					<a onclick="historyRoutePause();">
						<i class="glyphicon glyphicon-pause"></i>
					</a>
					<a onclick="historyRouteStop();">
						<i class="glyphicon glyphicon-stop"></i>
					</a>
					
					<select id="history_playback_play_speed" class="form-control">
						<option value=1>x1</option>
						<option value=2>x2</option>
						<option value=3>x3</option>
						<option value=4>x4</option>
						<option value=5>x5</option>
						<option value=6>x6</option>
					</select>	
				</div>
			</div>
		</div>
		
		<nav id="history_navbar" class="navbar navbar-default navbar-fixed-bottom">
			<div class="container-fluid">
				<div class="row vertical-align">
					<div id="history_navbar_map" class="width33">
						<a href="#" class="btn btn-default dropdown-toggle" onclick="hideHistoryPanels();">
							<i class="glyphicon glyphicon-globe"></i>
							<? echo $la['MAP']; ?>
						</a>
					</div>
					<div id="history_navbar_graph" class="width33">
						<a href="#" class="btn btn-default dropdown-toggle" onclick="showHistoryGraphPanel();">
							<i class="glyphicon glyphicon-signal"></i>
							<? echo $la['GRAPH']; ?>
						</a>
					</div>
					<div id="history_navbar_details" class="width33">
						<a href="#" class="btn btn-default dropdown-toggle" onclick="showHistoryDetailsPanel();">
							<i class="glyphicon glyphicon-road"></i>
							<? echo $la['ROUTE']; ?>
						</a>
					</div>
					<div class="width33">
						<a href="#" class="btn btn-default dropdown-toggle" onclick="historyHideRoute();">
							<i class="glyphicon glyphicon-remove"></i>
							<? echo $la['HIDE']; ?>
						</a>
					</div>
				</div>
			</div>
		</nav>
	</div>
	
	<? include ("inc/inc_page.menu.php"); ?>
	<? include ("inc/inc_page.objects.php"); ?>
	<? include ("inc/inc_page.events.php"); ?>
	<? include ("inc/inc_page.places.php"); ?>
	<? include ("inc/inc_page.history.php"); ?>	
	<? include ("inc/inc_page.cmd.php"); ?>
	<? include ("inc/inc_page.settings.php"); ?>
</body>
</html>