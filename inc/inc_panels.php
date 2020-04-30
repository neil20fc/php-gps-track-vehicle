<div id="map"></div>

<div class="map-layer-control">
	<div class="row4">
		<select id="map_layer" class="select" style="min-width: 100px;" onChange="switchMapLayer($(this).val());"></select>
	</div>
</div>

<div id="history_view_control" class="history-view-control">
	<a href="#" onclick="historyRouteRouteToggle();" title="<? echo $la['ENABLE_DISABLE_ROUTE'];?>">
		<span class="icon-route-route" id="history_view_control_route"></span>
	</a>
	<a href="#" onclick="historyRouteSnapToggle();" title="<? echo $la['ENABLE_DISABLE_SNAP'];?>">
		<span class="icon-route-snap disabled" id="history_view_control_snap"></span>
	</a>
	<a href="#" onclick="historyRouteArrowsToggle();" title="<? echo $la['ENABLE_DISABLE_ARROWS'];?>">
		<span class="icon-route-arrow disabled" id="history_view_control_arrows"></span>
	</a>
	<a href="#" onclick="historyRouteDataPointsToggle();" title="<? echo $la['ENABLE_DISABLE_DATA_POINTS'];?>">
		<span class="icon-route-data-point disabled" id="history_view_control_data_points"></span>
	</a>
	<a href="#" onclick="historyRouteStopsToggle();" title="<? echo $la['ENABLE_DISABLE_STOPS'];?>">
		<span class="icon-route-stop" id="history_view_control_stops"></span>
	</a>
	<a href="#" onclick="historyRouteEventsToggle();" title="<? echo $la['ENABLE_DISABLE_EVENTS'];?>">
		<span class="icon-route-event" id="history_view_control_events"></span>
	</a>
	<a href="#" onclick="historyHideRoute();" title="<? echo $la['HIDE'];?>">
		<span class="icon-close"></span>
	</a>
</div>

<div id="street_view_control" class="street-view-control">
	<? echo $la['STREET_VIEW']; ?>
</div>

<div id="top_panel">

<div style="float: left; width: 363px; height: 56px; background-color: #021929;">
	<center>
		<a href="#" onclick="$('#dialog_about').dialog('open');" title="<? echo $la['ABOUT']; ?>"><img style="width: 60%;height: 100%; margin-top: 0px;" src="<? echo $gsValues['URL_ROOT'].'/img/'.$gsValues['LOGO']; ?>" /></a>
	</center>
</div>
	<div class="tp-menu left-menu">
		

		<div class="point-btn">
			<a href="#" onclick="$('#dialog_show_point').dialog('open');" title="<? echo $la['SHOW_POINT']; ?>">
				<img src="img/panel/1b.svg" border="0"/>
			</a>
		</div>
		<div class="search-btn">
			<a href="#" onclick="$('#dialog_address_search').dialog('open');" title="<? echo $la['ADDRESS_SEARCH']; ?>">
				<img src="img/panel/2b.svg" border="0"/>
			</a>
		</div>
		
	</div>
    
	<div class="tp-menu right-menu">				
		<div class="select-language <? if ($_SESSION["cpanel_privileges"]){?>cp<? }?>">
			<select id="system_language" onChange="switchLanguageTracking();" class="select">
			<? echo getLanguageList(); ?>
			</select>
		</div>
		<div class="settings-btn">
			<a href="#" onclick="settingsOpen();" title="<? echo $la['SETTINGS']; ?>">
				<img src="img/panel/3b.svg" border="0"/>
			</a>
		</div>
		<? if ($_SESSION["privileges_reports"] == true){?>
		<div class="report-btn">
			<a href="#" onclick="reportsOpen();" title="<? echo $la['REPORTS']; ?>">
				<img src="img/panel/4b.svg" border="0"/>
			</a>
		</div>
		<? } ?>
		<? if ($_SESSION["privileges_object_control"] == true){?>
		<div class="cmd-btn">
			<a href="#" onclick="cmdOpen();" title="<? echo $la['OBJECT_CONTROL']; ?>">
				<img src="img/panel/8b.svg" border="0"/>
			</a>
		</div>
		<? } ?>
		<div class="user-btn">
			<a href="#" onclick="settingsOpenUser();" title="<? echo $la['MY_ACCOUNT']; ?>">
				<img src="img/panel/11b.svg" border="0"/>
				<span><? echo $_SESSION["username"];?></span>
			</a>
		</div>
		<? if ($_SESSION["cpanel_privileges"]){?>
		<div class="cpanel-btn">
			<a href="cpanel.php" title="<? echo $la['CONTROL_PANEL']; ?>">
				<img src="img/panel/12b.svg" border="0"/>
			</a>
		</div>
		<? }?>
		<div class="mobile-btn">
			<a href="mobile/tracking.php" title="<? echo $la['MOBILE_VERSION']; ?>">
				<img src="img/panel/13b.svg" border="0"/>
			</a>
		</div>
		<div class="logout-btn">
			<a href="#" onclick="connectLogout();" title="<? echo $la['LOGOUT']; ?>">
				<img src="img/panel/14b.svg" border="0"/>
			</a>
		</div>
	</div>
</div>

<div id="side_panel">
	<ul>           
		<li><a href="#side_panel_objects" onclick="datalistBottomSwitch('object');"><? echo $la['OBJECTS']; ?></a></li>
		<li><a href="#side_panel_events" onclick="datalistBottomSwitch('event');"><? echo $la['EVENTS']; ?></a></li>
		<li><a href="#side_panel_places" id="side_panel_places_tab"><? echo $la['PLACES']; ?></a></li>
		<li><a href="#side_panel_history" onclick="datalistBottomSwitch('route');"><? echo $la['HISTORY']; ?></a></li>
	</ul>
	      
	<div id="side_panel_objects">
		<div id="side_panel_objects_object_list">
			<table id="side_panel_objects_object_list_grid"></table>
		</div>
		<div id="side_panel_objects_dragbar">
		</div>
		<div id="side_panel_objects_object_data_list">
			<table id="side_panel_objects_object_datalist_grid"></table>
		</div>
	</div>
	
	<div id="side_panel_events">
		<div id="side_panel_events_event_list">
		       <table id="side_panel_events_event_list_grid"></table>
		       <div id="side_panel_events_event_list_grid_pager"></div>
	       </div>
	       <div id="side_panel_events_dragbar">
	       </div>
	       <div id="side_panel_events_event_data_list">
		       <table id="side_panel_events_event_datalist_grid"></table>
	       </div>
	</div>
    
	<div id="side_panel_places">
		<ul>
			<li><a href="#side_panel_places_markers" id="side_panel_places_markers_tab"><span><? echo $la['MARKERS']; ?> </span><span id="side_panel_places_markers_num"></span></a></li>
			<li><a href="#side_panel_places_routes" id="side_panel_places_routes_tab"><span><? echo $la['ROUTES']; ?> </span><span id="side_panel_places_routes_num"></span></a></li>
			<li><a href="#side_panel_places_zones" id="side_panel_places_zones_tab"><span><? echo $la['ZONES']; ?> </span><span id="side_panel_places_zones_num"></span></a></li>
		</ul>
		
		<div id="side_panel_places_markers">
			<div id="side_panel_places_marker_list">
				<table id="side_panel_places_marker_list_grid"></table>
				<div id="side_panel_places_marker_list_grid_pager"></div>
			</div>
		</div>
		
		<div id="side_panel_places_routes">
			<div id="side_panel_places_route_list">
				<table id="side_panel_places_route_list_grid"></table>
				<div id="side_panel_places_route_list_grid_pager"></div>
			</div>
		</div>
		
		<div id="side_panel_places_zones">
			<div id="side_panel_places_zone_list">
				<table id="side_panel_places_zone_list_grid"></table>
				<div id="side_panel_places_zone_list_grid_pager"></div>
			</div>
		</div>
	</div>
    
	<div id="side_panel_history">
		<div id="side_panel_history_parameters">
			<div class="row2">
			    <div class="width35"><? echo $la['OBJECT']; ?></div>
			    <div class="width65"><select id="side_panel_history_object_list" class="select-search width100"></select></div>
			</div>
			<div class="row2">
				<div class="width35"><? echo $la['FILTER'];?></div>
				<div class="width65">
				    <select id="side_panel_history_filter" class="select width100" onchange="switchDateFilter('history');">
					<option value="0" selected></option>
					<option value="1"><? echo $la['LAST_HOUR'];?></option>
					<option value="2"><? echo $la['TODAY'];?></option>
					<option value="3"><? echo $la['YESTERDAY'];?></option>
					<option value="4"><? echo $la['BEFORE_2_DAYS'];?></option>
					<option value="5"><? echo $la['BEFORE_3_DAYS'];?></option>
					<option value="6"><? echo $la['THIS_WEEK'];?></option>
					<option value="7"><? echo $la['LAST_WEEK'];?></option>
					<option value="8"><? echo $la['THIS_MONTH'];?></option>
					<option value="9"><? echo $la['LAST_MONTH'];?></option>
				    </select>
				</div>
			</div>
			<div class="row2">
				<div class="width35"><? echo $la['TIME_FROM']; ?></div>
				<div class="width31">
					<input readonly class="inputbox-calendar inputbox width100" id="side_panel_history_date_from" type="text" value=""/>
				</div>
				<div class="width2"></div>
				<div class="width15">
					<select id="side_panel_history_hour_from" class="select width100">
					<? include ("inc/inc_dt.hours.php"); ?>
					</select>
				</div>
				<div class="width2"></div>
				<div class="width15">
					<select id="side_panel_history_minute_from" class="select width100">
					<? include ("inc/inc_dt.minutes.php"); ?>
					</select>
				</div>
			</div>
			<div class="row2">
				<div class="width35"><? echo $la['TIME_TO']; ?></div>
				<div class="width31">
					<input readonly class="inputbox-calendar inputbox width100" id="side_panel_history_date_to" type="text" value=""/>
				</div>
				<div class="width2"></div>
				<div class="width15">
					<select id="side_panel_history_hour_to" class="select width100">
					<? include ("inc/inc_dt.hours.php"); ?>
					</select>
				</div>
				<div class="width2"></div>
				<div class="width15">
					<select id="side_panel_history_minute_to" class="select width100">
					<? include ("inc/inc_dt.minutes.php"); ?>
					</select>
				</div>
			</div>
			
			<div class="row3">
				<div class="width35"><? echo $la['STOPS']; ?></div>
				<div class="width31">
					<select id="side_panel_history_stop_duration" class="select width100">
						<option value="1">> 1 <? echo $la['UNIT_MIN']; ?></option>
						<option value="2">> 2 <? echo $la['UNIT_MIN']; ?></option>
						<option value="5">> 5 <? echo $la['UNIT_MIN']; ?></option>
						<option value="10">> 10 <? echo $la['UNIT_MIN']; ?></option>
						<option value="20">> 20 <? echo $la['UNIT_MIN']; ?></option>
						<option value="30">> 30 <? echo $la['UNIT_MIN']; ?></option>
						<option value="60">> 1 <? echo $la['UNIT_H']; ?></option>
						<option value="120">> 2 <? echo $la['UNIT_H']; ?></option>
						<option value="300">> 5 <? echo $la['UNIT_H']; ?></option>
					</select>
				</div>
			</div>
	    
			<div class="row3">
				<input style="width: 100px; margin-right: 3px;" class="button" type="button" value="<? echo $la['SHOW']; ?>" onclick="historyLoadRoute();"/>
				<input style="width: 100px; margin-right: 3px;" class="button" type="button" value="<? echo $la['HIDE']; ?>" onclick="historyHideRoute();"/>
				<input style="width: 134px;" id="side_panel_history_import_export_action_menu_button" class="button" type="button" value="<? echo $la['IMPORT_EXPORT']; ?>"/>
			</div>
		</div>
	
		<div id="side_panel_history_route">
			<table id="side_panel_history_route_detail_list_grid"></table>
		</div>
		
		<div id="side_panel_history_dragbar">
		</div>
		
		<div id="side_panel_history_route_data_list">
			<table id="side_panel_history_route_datalist_grid"></table>
		</div>
	</div>
</div>

<div id="bottom_panel">
	<div class="controls">
		<a href="#" onclick="hideBottomPanel();" title="<? echo $la['HIDE'];?>">
			<span class="icon-close"></span>
		</a>	
	</div>
	
	<div id="bottom_panel_tabs" style="height: 100%;">
		<ul>
			<li id="bottom_panel_datalist_tab"><a href="#bottom_panel_datalist"><? echo $la['DATA']; ?></a></li>
			<li><a href="#bottom_panel_graph"><? echo $la['GRAPH']; ?></a></li>
			<li><a href="#bottom_panel_msg"><? echo $la['MESSAGES']; ?></a></li>
		</ul>
		
		<div id="bottom_panel_datalist" class="datalist">
			<div id="bottom_panel_datalist_object_data_list" class="datalist-item-list">
				<div class="data-item-text"><? echo $la['NO_OBJECT_SELECTED']; ?></div>
			</div>
			<div id="bottom_panel_datalist_event_data_list" class="datalist-item-list" style="display: none;">
				<div class="data-item-text"><? echo $la['NO_EVENT_SELECTED']; ?></div>
			</div>
			<div id="bottom_panel_datalist_route_data_list" class="datalist-item-list" style="display: none;">
				<div class="data-item-text"><? echo $la['NO_HISTORY_LOADED']; ?></div>
			</div>
		</div>
		
		<div id="bottom_panel_graph">			
			<div class="graph-controls">
				<div class="graph-controls-left">
					<select id="bottom_panel_graph_data_source" class="select" style="width:120px;" onchange="historyRouteChangeGraphSource();"></select>					
					<a href="#" onclick="historyRoutePlay();">
						<div class="panel-button" title="<? echo $la['PLAY'];?>">
							<img src="theme/images/play.svg" width="12px" border="0"/>
						</div>
					</a>				    
					<a href="#" onclick="historyRoutePause();">
						<div class="panel-button" title="<? echo $la['PAUSE'];?>">
							<img src="theme/images/pause.svg" width="12px" border="0"/>
						</div>
					</a>				    
					<a href="#" onclick="historyRouteStop();">
						<div class="panel-button" title="<? echo $la['STOP'];?>">
							<img src="theme/images/stop.svg" width="12px" border="0"/>
						</div>
					</a>					
					<select id="bottom_panel_graph_play_speed" class="select" style="width:50px;">
						<option value=1>x1</option>
						<option value=2>x2</option>
						<option value=3>x3</option>
						<option value=4>x4</option>
						<option value=5>x5</option>
						<option value=6>x6</option>
					</select>
				</div>
				<div class="graph-controls-right">
					<div id="bottom_panel_graph_label" class="graph-label"></div>
					
					<a href="#" onclick="graphPanLeft();">
						<div class="panel-button" title="<? echo $la['PAN_LEFT'];?>">
							<img src="theme/images/arrow-left.svg" width="12px" border="0"/>
						</div>
					</a>
					
					<a href="#" onclick="graphPanRight();">
						<div class="panel-button" title="<? echo $la['PAN_RIGHT'];?>">
							<img src="theme/images/arrow-right.svg" width="12px" border="0"/>
						</div>
					</a>
					  
					<a href="#" onclick="graphZoomIn();">
						<div class="panel-button" title="<? echo $la['ZOOM_IN'];?>">
							<img src="theme/images/plus.svg" width="12px" border="0"/>
						</div>
					</a>
					
					<a href="#" onclick="graphZoomOut();">
						<div class="panel-button" title="<? echo $la['ZOOM_OUT'];?>">
							<img src="theme/images/minus.svg" width="12px" border="0"/>
						</div>
					</a>
				</div>
			</div>
			
			<div id="bottom_panel_graph_plot"></div>
		</div>
		
		<div id="bottom_panel_msg">
			<table id="bottom_panel_msg_list_grid"></table>
			<div id="bottom_panel_msg_list_grid_pager"></div>
		</div>
	</div>
</div>

<a href="#" onclick="showHideLeftPanel();">
	<div id="side_panel_dragbar">    
	</div>
</a>

<a href="#" onclick="showBottomPanel();">
	<div id="bottom_panel_dragbar">    
	</div>
</a>