<?
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	checkUserSession();
	
	if (!isset($_GET['imei']))
	{
		die;
	}
	
	// check privileges
	if ($_SESSION["privileges"] == 'subuser')
	{
		$user_id = $_SESSION["manager_id"];
	}
	else
	{
		$user_id = $_SESSION["user_id"];
	}
	
	$imei = $_GET['imei'];
	$map_layer = $_GET['map_layer'];
	
	if(!checkUserToObjectPrivileges($user_id, $imei))
	{
		die;
	}
	
	loadLanguage($_SESSION["language"], $_SESSION["units"]);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><? echo $la['FOLLOW'].' ('.getObjectName($imei).')'; ?></title>
	
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
        
        <link type="text/css" href="../theme/jquery-ui.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
        <link type="text/css" href="../theme/ui.jqgrid.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	<link type="text/css" href="../theme/style.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	<link type="text/css" href="../theme/style.custom.php?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	<link type="text/css" href="../theme/jquery.multiple.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	
	<link type="text/css" href="../theme/leaflet/leaflet.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />	
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
	<script type="text/javascript" src="../js/leaflet/marker.rotate.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	
	<script type="text/javascript" src="../js/jquery-2.1.4.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="../js/jquery-migrate-1.2.1.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="../js/jquery.jqGrid.locale.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        <script type="text/javascript" src="../js/jquery.jqGrid.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>	
	<script type="text/javascript" src="../js/jquery.multiple.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	
	<script type="text/javascript" src="../js/moment.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>

	<script type="text/javascript" src="../js/gs.config.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	<script type="text/javascript" src="../js/gs.common.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
        
        <script>
                // vars
		var la = [];
                var map;
                var mapLayers = new Array();
		var mapMarkerIcons = new Array();
		var mapPopup;
                var timer_objectFollow;
                var objectsData = new Array();
                var settingsUserData = new Array();
                var settingsObjectData = new Array();
		
		// data list
		gsValues['datalist_groups_colapsed'] = new Array();
                
                function load()
                {
			loadLanguage(function(response){
			loadSettings('server', function(response){
			loadSettings('user', function(response){
			loadSettings('objects', function(response){
			
			load2();
			
			});});});});
                }
		
		function load2()
                {
			initMap();
			initGui();
                        initGrids();
                        
                        objectFollow('<? echo $imei; ?>');
			
                        document.getElementById("loading_panel").style.display = "none";
                }
                
                function unload()
                {
                        
                }
                
                function objectFollow(imei)
                {
                        clearTimeout(timer_objectFollow);
                        
                        var data = {
                                cmd: 'load_object_data',
                                imei: imei
                        };
                        
                        $.ajax({
                                type: "POST",
                                url: "../func/fn_objects.php",
                                data: data,
                                dataType: 'json',
                                cache: false,
                                error: function(statusCode, errorThrown) {
                                        // shedule next object reload
                                        timer_objectFollow = setTimeout("objectFollow('"+imei+"');", gsValues['map_refresh'] * 1000);
                                },
                                success: function(result)
                                {
                                        // convert tracking route to normal format
                                        for (var imei in result)
                                        {
                                                result[imei] = transformToObjectData(result[imei]);
                                        }
                                        
                                        if (Object.keys(objectsData).length != Object.keys(result).length)
                                        {
                                                objectsData = result;
                                        }
                                        else
                                        {
                                                for (var imei in result)
                                                {
                                                        objectsData[imei]['conn_valid'] = result[imei]['conn_valid'];
                                                        objectsData[imei]['loc_valid'] = result[imei]['loc_valid'];
                                                        objectsData[imei]['odometer'] = result[imei]['odometer'];
							objectsData[imei]['status'] = result[imei]['status'];
							objectsData[imei]['status_string'] = result[imei]['status_string'];
                                                        objectsData[imei]['engine_hours'] = result[imei]['engine_hours'];
                                                        objectsData[imei]['service'] = result[imei]['service'];
							
                                                        if (objectsData[imei]['data'] == '')
                                                        {
                                                                objectsData[imei]['data'] = result[imei]['data'];
                                                        }
                                                        else
                                                        {
                                                                if (objectsData[imei]['data'].length >= settingsObjectData[imei]['tail_points'])
                                                                {
                                                                        objectsData[imei]['data'].pop(); 
                                                                }
                                                                objectsData[imei]['data'].unshift(result[imei]['data'][0]);
                                                        }
                                                }
                                        }
                                        
                                        objectRemoveFromMap();
                                        if (settingsObjectData[imei].active == "true")
                                        {
                                                objectAddToMap(imei);
                                        }
					
					if (document.getElementById("follow").checked == true)
					{
						var lat = objectsData[imei]['data'][0]['lat'];
						var lng = objectsData[imei]['data'][0]['lng'];
						
						map.panTo({lat: lat, lng: lng});
					}
                                        
                                        // shedule next object reload
                                        timer_objectFollow = setTimeout("objectFollow('"+imei+"');", gsValues['map_refresh'] * 1000);
                                }
                        });  
                }
                
                function objectAddToMap(imei)
                {
                        // get data
                        var name = settingsObjectData[imei]['name'];
                        
                        if (objectsData[imei]['data'] != '')
                        {
                                var lat = objectsData[imei]['data'][0]['lat'];
                                var lng = objectsData[imei]['data'][0]['lng'];
                                var altitude = objectsData[imei]['data'][0]['altitude'];
                                var angle = objectsData[imei]['data'][0]['angle'];
                                var speed = objectsData[imei]['data'][0]['speed'];
                                var dt_tracker = objectsData[imei]['data'][0]['dt_tracker'];
				var params = objectsData[imei]['data'][0]['params'];
				
				var extra_data = objectsData[imei]['data'][0];
				showExtraData(imei, extra_data);	
                        }
                        else
                        {
                                var lat = 0;
                                var lng = 0;
                                var speed = 0;
				var params = false;
                        }
			
			// get icon zoom level
			var zoom = settingsUserData['map_is'];
                        
                        // rotate marker only if icon is arrow
                        var iconAngle = angle;
                        if (settingsObjectData[imei]['map_icon'] != 'arrow')
                        {
                                iconAngle = 0;
                        }
                        
			//marker
			var status = objectsData[imei]['status'];
                        var icon = getMarkerIcon(imei, speed, status, false);
                        var marker = L.marker([lat, lng], {icon: icon, iconAngle: iconAngle});
			
			// label
			var label = name + " (" + speed + " " + la["UNIT_SPEED"] +")";
			marker.bindTooltip(label, {permanent: true, offset: [20*zoom,0], direction: 'right'}).openTooltip();
			
			// set click event
			marker.on('click', function(e) {				
				if (objectsData[imei]['data'] != '')
				{
					geocoderGetAddress(lat, lng, function(response)
					{
						var address = response;
						var position = urlPosition(lat, lng);
						
						var text_sensors = '';
						var text_fields = '';
						var text_services = '';
						
						// sensors
						var sortedSensors = new Array();
						for (var key in settingsObjectData[imei]['sensors'])
						{
							sortedSensors.push(settingsObjectData[imei]['sensors'][key]);
						}
						
						var sensors = sortArrayByElement(sortedSensors, 'name');
						
						for (var key in sensors)
						{
							var sensor = sensors[key];
							if (sensor.popup == 'true')
							{
								if (sensor.type == 'fuelsumup')
								{
									var sensor_data = getSensorValueFuelLevelSumUp(imei, params, sensor);
									text_sensors += '<tr><td><strong>' + sensor.name + ':</strong></td><td>' + sensor_data.value_full + '</td></tr>';
								}
								else
								{
									var sensor_data = getSensorValue(params, sensor);
									text_sensors += '<tr><td><strong>' + sensor.name + ':</strong></td><td>' + sensor_data.value_full + '</td></tr>';
								}
							}
						}
						
						// custom fields
						var sortedFields = new Array();
						for (var key in settingsObjectData[imei]['custom_fields'])
						{
							sortedFields.push(settingsObjectData[imei]['custom_fields'][key]);
						}
						
						var fields = sortArrayByElement(sortedFields, 'name');
						
						for (var key in fields)
						{
							var field = fields[key];
							if (field.popup == 'true')
							{
								text_fields += '<tr><td><strong>' + field.name + ':</strong></td><td>' + field.value + '</td></tr>';
							}
						}
						
						// service
						var sortedService = new Array();
						for (var key in objectsData[imei]['service'])
						{
							sortedService.push(objectsData[imei]['service'][key]);
						}
		
						var service = sortArrayByElement(sortedService, 'name');
						
						for (var key in service)
						{
							if (service[key].popup == 'true')
							{
								text_services += '<tr><td><strong>' + service[key].name + ':</strong></td><td>' + service[key].status + '</td></tr>';
							}
						}
								
						var text = '<table>\
							<tr><td><strong>' + la['OBJECT'] + ':</strong></td><td>' + name + '</td></tr>\
							<tr><td><strong>' + la['ADDRESS'] + ':</strong></td><td>' + address + '</td></tr>\
							<tr><td><strong>' + la['POSITION'] + ':</strong></td><td>' + position + '</td></tr>\
							<tr><td><strong>' + la['ALTITUDE'] + ':</strong></td><td>' + altitude + ' ' + la["UNIT_HEIGHT"] + '</td></tr>\
							<tr><td><strong>' + la['ANGLE'] + ':</strong></td><td>' + angle + ' &deg;</td></tr>\
							<tr><td><strong>' + la['SPEED'] + ':</strong></td><td>' + speed + ' ' + la["UNIT_SPEED"] + '</td></tr>\
							<tr><td><strong>' + la['TIME'] + ':</strong></td><td>' + dt_tracker + '</td></tr>';
							
						var odo = getObjectOdometer(imei, false);
						if (odo != -1)
						{
							text += '<tr><td><strong>' + la['ODOMETER'] + ':</strong></td><td>' + odo + ' ' + la["UNIT_DISTANCE"] + '</td></tr>';
						}
						
						var engh = getObjectEngineHours(imei, false);
						if (engh != -1)
						{
							text += '<tr><td><strong>' + la['ENGINE_HOURS'] + ':</strong></td><td>' + engh + '</td></tr>';
						}
							
						var text_detailed = text + text_fields + text_sensors + text_services;
						
						text += '</table>';
						text_detailed += '</table>';
							
						addPopupToMap(lat, lng, [0, -14*zoom], text, text_detailed);
					});
				}
			});
                        
                        marker.on('add', function(e) {
                                objectAddTailToMap(imei);
                        });
                        
			marker.on('remove', function(e) {
				if (objectsData[imei] != undefined)
				{
					if (objectsData[imei].layers.tail)
					{
						mapLayers['realtime'].removeLayer(objectsData[imei].layers.tail);
					}	
				}
			});
			
                        mapLayers['realtime'].addLayer(marker);
			//mapLayers['realtime'].addLayer(label);
                        
                        // store layer
                        objectsData[imei].layers.marker = marker;
			//objectsData[imei].layers.label = label;
                }
                
                function objectRemoveFromMap()
                {
                        mapLayers['realtime'].clearLayers();
                }
		
		function objectAddTailToMap(imei)
		{
			if (settingsObjectData[imei]['tail_points'] > 0)
			{
				if (objectsData[imei].layers.tail)
				{
					mapLayers['realtime'].removeLayer(objectsData[imei].layers.tail);	
				}
				
				var line_points = new Array();
				var i;
				
				for (i=0;i<objectsData[imei]['data'].length;i++)
				{
					var lat = objectsData[imei]['data'][i]['lat'];
					var lng = objectsData[imei]['data'][i]['lng'];
					
					line_points.push(L.latLng(lat, lng));
				}
				
				// draw tail polyline
				var tail = L.polyline(line_points, {color: settingsObjectData[imei]['tail_color'], opacity: 0.8, weight: 3});
				
				mapLayers['realtime'].addLayer(tail);
				
				// store layer
				objectsData[imei].layers.tail = tail;
			}
		}
		
		function showExtraData(imei, data)
		{
			var list_data = [];
			var list_id = $("#side_panel_follow_datalist_grid");
			var list_str = 'side_panel_follow_datalist_grid';		
			
			var datalist_items = settingsUserData['datalist_items'].split(",");
			
			// groups colapsed
			for (var i=0;i<5;i++)
			{
				if (document.getElementById(list_str+'ghead_0_'+i) != null)
				{
					if ($('#'+list_str+'ghead_0_'+i).find('span').hasClass('ui-icon-circlesmall-minus'))
					{
						gsValues['datalist_groups_colapsed'][i] = false;
					}
					else
					{
						gsValues['datalist_groups_colapsed'][i] = true;
					}	
				}
			}
				
			// store scroll
			var scrollPosition = list_id.closest(".ui-jqgrid-bdiv").scrollTop();
	
			list_id.clearGridData(true);
			
			// exit function if no object data
			if (data == '') return;
			
			var dt_server = data['dt_server'];
			var dt_tracker = data['dt_tracker'];
			var lat = data['lat'];
			var lng = data['lng'];
			var altitude = data['altitude'];
			var angle = data['angle'];
			var speed = data['speed'];
			var params = data['params'];
			
			if (datalist_items.indexOf('odometer') !== -1 )
                        {
				var odo = getObjectOdometer(imei, false);
				if (odo != -1)
				{
					list_data.push({group_name: la['GENERAL'], data: la['ODOMETER'], value: odo + ' ' + la["UNIT_DISTANCE"]});
				}
			}

			if (datalist_items.indexOf('engine_hours') !== -1 )
                        {
				var engh = getObjectEngineHours(imei, false);
				if (engh != -1)
				{
					list_data.push({group_name: la['GENERAL'], data: la['ENGINE_HOURS'], value: engh});
				}
			}
			
			if (datalist_items.indexOf('status') !== -1 )
                        {
				var status_string = objectsData[imei]['status_string'];
				if (status_string != '')
				{
					list_data.push({group_name: la['GENERAL'], data: la['STATUS'], value: status_string});       
				}
			}
			
			if (datalist_items.indexOf('time_position') !== -1 )
                        {
				list_data.push({group_name: la['LOCATION'], data: la['TIME_POSITION'], value: dt_tracker});
			}
			
			if (datalist_items.indexOf('time_server') !== -1 )
                        {
				list_data.push({group_name: la['LOCATION'], data: la['TIME_SERVER'], value: dt_server});
			}
		 
			if (datalist_items.indexOf('model') !== -1 )
			{
				var model = settingsObjectData[imei]['model']; // get model
				if (model != "")
				{
					list_data.push({group_name: la['GENERAL'], data: la['MODEL'], value: model});
				}
			}
			
			if (datalist_items.indexOf('vin') !== -1 )
			{
				var vin = settingsObjectData[imei]['vin']; // get VIN
				if (vin != "")
				{
					list_data.push({group_name: la['GENERAL'], data: la['VIN'], value: vin});
				}
			}
			
			if (datalist_items.indexOf('plate_number') !== -1 )
			{
				var plate_number = settingsObjectData[imei]['plate_number']; // get plate_number
				if (plate_number != "")
				{
					list_data.push({group_name: la['GENERAL'], data: la['PLATE'], value: plate_number});
				}
			}
			
			if (datalist_items.indexOf('sim_number') !== -1 )
			{
				var sim_number = settingsObjectData[imei]['sim_number']; // get sim_number
				if (sim_number != "")
				{
					list_data.push({group_name: la['GENERAL'], data: la['SIM_CARD_NUMBER'], value: sim_number});
				}
				}
			
			// get address
			if (datalist_items.indexOf('address') !== -1 )
                        {
				if (gsValues['address_display_object_data_list'] == true)
				{
					geocoderGetAddress(lat, lng, function(response)
					{
						document.getElementById(list_str+"_address").innerHTML = response;
						document.getElementById(list_str+"_address").title = response;
						objectsData[imei]['address'] = response;
					});
				
					var address = '<span id="'+list_str+'_address">'+objectsData[imei]['address']+'</span>';
					list_data.push({group_name: la['LOCATION'], data: la['ADDRESS'], value: address});
				}
			}
	
			
			if (datalist_items.indexOf('position') !== -1 )
			{
				var position = urlPosition(lat, lng);	
				list_data.push({group_name: la['LOCATION'], data: la['POSITION'], value: position});
			}
			
			if (datalist_items.indexOf('speed') !== -1 )
			{
				list_data.push({group_name: la['LOCATION'], data: la['SPEED'], value: speed + ' ' + la["UNIT_SPEED"]});
			}
			
			if (datalist_items.indexOf('altitude') !== -1 )
			{
				list_data.push({group_name: la['LOCATION'], data: la['ALTITUDE'], value: altitude + ' ' + la["UNIT_HEIGHT"]});
			}
			if (datalist_items.indexOf('angle') !== -1 )
			{
				list_data.push({group_name: la['LOCATION'], data: la['ANGLE'], value: angle + ' &deg;'});
			}
			
			// add sensors to object data list
			var sensors = settingsObjectData[imei]['sensors'];
			for (var key in sensors)
			{
				var sensor = sensors[key];
				
				if (sensor.data_list == 'true')
				{
					if (sensor.type == 'fuelsumup')
					{
						var sensor_data = getSensorValueFuelLevelSumUp(imei, params, sensor);
						list_data.push({group_name: la['SENSORS'], data: sensor.name, value: sensor_data.value_full});
					}
					else
					{
						var sensor_data = getSensorValue(params, sensor);
						list_data.push({group_name: la['SENSORS'], data: sensor.name, value: sensor_data.value_full});	
					}
				}
			}
			
			// add custom fields
			var fields = settingsObjectData[imei]['custom_fields'];
			for (var key in fields)
			{
				var field = fields[key];
				
				if (field.data_list == 'true')
				{
					list_data.push({group_name: la['GENERAL'], data: field.name, value: field.value});
				}
			}
			
			// add service
			var service = objectsData[imei]['service'];
			for (var key in service)
			{
				if (service[key].data_list == 'true')
				{
					list_data.push({group_name: la['SERVICE'], data: service[key].name, value: service[key].status});
				}
			}
			
			for(var i=0;i<list_data.length;i++)
			{
				list_id.jqGrid('addRowData',i,list_data[i]);
			}
			list_id.setGridParam({sortname:'data', sortorder: 'asc'}).trigger('reloadGrid');
			
			//groups colapsed
			for (var i=0;i<gsValues['datalist_groups_colapsed'].length;i++)
			{
				if (document.getElementById(list_str+'ghead_0_'+i) != null)
				{
					if (gsValues['datalist_groups_colapsed'][i] == true)
					{
						list_id.jqGrid('groupingToggle',list_str+'ghead_0_'+i);
					}
				}
			}
			
			// restore scroll
			list_id.closest(".ui-jqgrid-bdiv").scrollTop(scrollPosition);
		}
                
		function showHideInfo()
		{
			var map_left = "280px";
			
			if ($(window).width()< 640)
			{
				var map_left = "0px";
			}
			
			if (document.getElementById("info").checked == true) {
				document.getElementById("side_panel_follow").style.display = "block";
				document.getElementById("map_follow").style.left = map_left;
				
				setTimeout( function() { map.invalidateSize(true);}, 200);
			} else {
				document.getElementById("side_panel_follow").style.display = "none";
				document.getElementById("map_follow").style.left = "0px";
				
				setTimeout( function() { map.invalidateSize(true);}, 200);
			}
		}
		
		function initMap()
		{
	                map = L.map('map_follow', {minZoom: gsValues['map_min_zoom'], maxZoom: gsValues['map_max_zoom'], editable: true, zoomControl: false});
                        
                        // add map layers
                        initSelectList('map_layer_list');
			
			// define map layers
			defineMapLayers();
                        
                        // define layers	
                        mapLayers['realtime'] = L.layerGroup();
                        mapLayers['realtime'].addTo(map);
			
			// add map controls
			map.addControl(L.control.zoom({zoomInText: '', zoomOutText: '', zoomInTitle: la['ZOOM_IN'], zoomOutTitle: la['ZOOM_OUT']}));
                        
			// set map type
			var map_layer = '<? echo $map_layer; ?>';
			switchMapLayer(map_layer);
			
                        map.setView([0, 0], 15);
		}
		
		function initGui()
		{
			$(window).bind('resize', function()
			{
				showHideInfo();
			}).trigger('resize');
			
			// map marker icons
			var zoom = settingsUserData['map_is'];
			
			var icon_size_x = 28 * zoom;
			var icon_size_y = 28 * zoom;
			var icon_anc_x = 14 * zoom;
			var icon_anc_y = 14 * zoom;
			
			mapMarkerIcons['arrow_black'] = L.icon({
				iconUrl: '../img/markers/arrow-black.svg',
				iconSize:     [icon_size_x, icon_size_y], // size of the icon
				iconAnchor:   [icon_anc_x, icon_anc_y], // point of the icon which will correspond to marker's location
				popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
			});
			    
			mapMarkerIcons['arrow_blue'] = L.icon({
				iconUrl: '../img/markers/arrow-blue.svg',
				iconSize:     [icon_size_x, icon_size_y], // size of the icon
				iconAnchor:   [icon_anc_x, icon_anc_y], // point of the icon which will correspond to marker's location
				popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
			});
			    
			mapMarkerIcons['arrow_green'] = L.icon({
				iconUrl: '../img/markers/arrow-green.svg',
				iconSize:     [icon_size_x, icon_size_y], // size of the icon
				iconAnchor:   [icon_anc_x, icon_anc_y], // point of the icon which will correspond to marker's location
				popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
			});
			    
			mapMarkerIcons['arrow_grey'] = L.icon({
				iconUrl: '../img/markers/arrow-grey.svg',
				iconSize:     [icon_size_x, icon_size_y], // size of the icon
				iconAnchor:   [icon_anc_x, icon_anc_y], // point of the icon which will correspond to marker's location
				popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
			});
			    
			mapMarkerIcons['arrow_orange'] = L.icon({
				iconUrl: '../img/markers/arrow-orange.svg',
					iconSize:     [icon_size_x, icon_size_y], // size of the icon
				iconAnchor:   [icon_anc_x, icon_anc_y], // point of the icon which will correspond to marker's location
				popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
			});
			    
			mapMarkerIcons['arrow_purple'] = L.icon({
				iconUrl: '../img/markers/arrow-purple.svg',
				iconSize:     [icon_size_x, icon_size_y], // size of the icon
				iconAnchor:   [icon_anc_x, icon_anc_y], // point of the icon which will correspond to marker's location
				popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
			});
			    
			mapMarkerIcons['arrow_red'] = L.icon({
				iconUrl: '../img/markers/arrow-red.svg',
				iconSize:     [icon_size_x, icon_size_y], // size of the icon
				iconAnchor:   [icon_anc_x, icon_anc_y], // point of the icon which will correspond to marker's location
				popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
			});
			    
			mapMarkerIcons['arrow_yellow'] = L.icon({
				iconUrl: '../img/markers/arrow-yellow.svg',
				iconSize:     [icon_size_x, icon_size_y], // size of the icon
				iconAnchor:   [icon_anc_x, icon_anc_y], // point of the icon which will correspond to marker's location
				popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
			});
			
			// selects
			$(".select").multipleSelect({single: true});
		}
		
		function initGrids()
		{
			var groupText = '<div style="float: right;"><span>{0}</span></div>';
			
			// define left panel object data list grid
			$("#side_panel_follow_datalist_grid").jqGrid({
				datatype: 'local',
				colNames:['', la['DATA'], la['VALUE']],
				colModel:[
					{name:'group_name',index:'group_name'},
					{name:'data',index:'data',width:90,sortable:false},
					{name:'value',index:'value',width:163,sortable:false}
				],
				width: '280',
				height: '100',
				rowNum: 100,
				grouping: true,
				groupingView:{
					groupField: ['group_name'],
					groupColumnShow: [false],
					groupText: [groupText],
					groupCollapse: false,
					groupOrder: ['asc'],
					//groupSummary: [true],
					groupDataSorted: [true]
				},
				shrinkToFit: false
			});
			
			$(window).bind('resize', function()
			{
				if ($(window).width()< 640)
				{
					$("#side_panel_follow_datalist_grid").setGridHeight($(window).height() - 105);
				}
				else
				{
					$("#side_panel_follow_datalist_grid").setGridHeight($(window).height() - 30);
				}
			}).trigger('resize');
		}
		
                function initSelectList(list)
                {
                        switch (list)
                        {
                                case "map_layer_list":
                                        var select = document.getElementById('map_layer');
                                        select.options.length = 0; // clear out existing items
                                        
					if (gsValues['map_osm'])
					{
						select.options.add(new Option('OSM Map', 'osm'));
					}
					
					if (gsValues['map_bing'])
					{
						select.options.add(new Option('Bing Road', 'broad'));
						select.options.add(new Option('Bing Aerial', 'baer'));
						select.options.add(new Option('Bing Hybrid', 'bhyb'));	
					}
					
					if (gsValues['map_google'])
					{
						select.options.add(new Option('Google Streets', 'gmap'));
						select.options.add(new Option('Google Satellite', 'gsat'));
						select.options.add(new Option('Google Hybrid', 'ghyb'));
						select.options.add(new Option('Google Terrain', 'gter'));
					}
					
					if (gsValues['map_mapbox'])
					{
						select.options.add(new Option('Mapbox Streets', 'mbmap'));
						select.options.add(new Option('Mapbox Satellite', 'mbsat'));
					}
					
					if (gsValues['map_yandex'])
					{
						select.options.add(new Option('Yandex', 'yandex'));	
					}
					
					for (var i=0;i<gsValues['map_custom'].length;i++)
					{
						var layer_id = gsValues['map_custom'][i].layer_id;
						var name = gsValues['map_custom'][i].name;
						
						select.options.add(new Option(name, layer_id));	
					}
                                break;
                        }
		}
		
		function loadObjectMapMarkerIcons()
		{
			var icon_array = new Array();
			for (var key in settingsObjectData)
			{
				var imei = settingsObjectData[key];
				icon_array.push(imei.icon);	
			}
			
			icon_array = uniqueArray(icon_array);
			
			for (i=0;i<icon_array.length;i++)
			{
				var name = icon_array[i];
				var file = '../'+icon_array[i]
				
				var zoom = settingsUserData['map_is'];
				
				mapMarkerIcons[name] = L.icon({
					iconUrl: file,
					iconSize:     [28*zoom, 28*zoom], // size of the icon
					iconAnchor:   [14*zoom, 14*zoom], // point of the icon which will correspond to marker's location
					popupAnchor:  [0, 0] // point from which the popup should open relative to the iconAnchor
				});				
			}
		}
		
		
		function addPopupToMap(lat, lng, offset, text, text_detailed)
		{
			if (text_detailed != '')
			{
				if (text != text_detailed)
				{
					if (gsValues['map_popup_detailed'] == true)
					{
						var style_short = 'style="display:none;"';
						var style_detailed = '';
					}
					else
					{
						var style_short = '';
						var style_detailed = 'style="display:none;"';
					}
					
					text = '<div id="popup_short" '+style_short+'>' + text;
					text += '<div style="width:100%; text-align: right;"><a href="#" class="" onClick="switchPopupDetailed(true)">'+la['DETAILED']+'</a></div>';
					text += '</div>'
					
					text += '<div id="popup_detailed" '+style_detailed+'>' + text_detailed;
					text += '<div style="width:100%; text-align: right;"><a href="#" class="" onClick="switchPopupDetailed(false)">'+la['SHORT']+'</a></div>';
					text += '</div>';
				}
			}
			
			mapPopup = L.popup({offset: offset}).setLatLng([lat, lng]).setContent(text).openOn(map);
		}
		
		function switchPopupDetailed(value)
		{
			switch (value)
			{
				case false:
					document.getElementById('popup_short').style.display = '';
					document.getElementById('popup_detailed').style.display = 'none';
					
					gsValues['map_popup_detailed'] = false;
					
					break;
				case true:
					document.getElementById('popup_short').style.display = 'none';
					document.getElementById('popup_detailed').style.display = '';
					
					gsValues['map_popup_detailed'] = true;
					
					break;
			}
		}
		
                function loadSettings(type, response)
                {
                        switch (type)
                        {
				case "server":
					var data = {
						cmd: 'load_server_data'
					};
					
					$.ajax({
						type: "POST",
						url: "fn_settings.php",
						data: data,
						dataType: 'json',
						cache: false,
						success: function(result)
						{
							gsValues['map_custom'] = result['map_custom'];
							gsValues['map_osm'] = strToBoolean(result['map_osm']);
							gsValues['map_bing'] = strToBoolean(result['map_bing']);
							gsValues['map_google'] = strToBoolean(result['map_google']);
							gsValues['map_google_traffic'] = strToBoolean(result['map_google_traffic']);
							gsValues['map_mapbox'] = strToBoolean(result['map_mapbox']);
							gsValues['map_yandex'] = strToBoolean(result['map_yandex']);
							gsValues['map_bing_key'] = result['map_bing_key'];
							gsValues['map_mapbox_key'] = result['map_mapbox_key'];
							gsValues['map_lat'] = result['map_lat'];
							gsValues['map_lng'] = result['map_lng'];
							gsValues['map_zoom'] = result['map_zoom'];
							gsValues['map_layer'] = result['map_layer'];
							gsValues['address_display_object_data_list'] = strToBoolean(result['address_display_object_data_list']);
							gsValues['address_display_event_data_list'] = strToBoolean(result['address_display_event_data_list']);
							gsValues['address_display_history_route_data_list'] = strToBoolean(result['address_display_history_route_data_list']);
							
							response(true);
						}
					});
					break;
                                case "user":
                                        var data = {
                                                cmd: 'load_user_data'
                                        };
					
                                        $.ajax({
                                                type: "POST",
                                                url: "fn_settings.php",
                                                data: data,
                                                dataType: 'json',
                                                cache: false,
                                                success: function(result)
                                                {
                                                        settingsUserData = result;
							
							response(true);
                                                }
                                        });
                                        break;
                                case "objects":
                                        var data = {
                                                cmd: 'load_object_data'
                                        };
                                        
                                        $.ajax({
                                                type: "POST",
                                                url: "fn_settings.objects.php",
                                                data: data,
                                                dataType: 'json',
                                                cache: false,
                                                success: function(result)
                                                {
                                                        settingsObjectData = result;
							
							loadObjectMapMarkerIcons();
							
							response(true);
                                                }
                                        });
                                        break;
                        }
                }
        </script>
    </head>
    
    <body onload="load()" onUnload="unload()">
	<div id="loading_panel">
		<div class="table">
			<div class="table-cell center-middle">
				<div class="loader">
					<span></span><span></span><span></span><span></span><span></span><span></span><span></span>
				</div>
			</div>
		</div>
	</div>

        <div id="map_follow"></div>
        <div class="object-follow-control">
		<div class="row4">
			<div class="margin-right-3"><input id="info" type="checkbox" class="checkbox" onclick="showHideInfo();"/></div>
			<div class="margin-right-3"><? echo $la['INFO']; ?></div>
			<div class="margin-right-3"><input id="follow" type="checkbox" class="checkbox" checked/></div>
			<div class="margin-right-3"><? echo $la['FOLLOW']; ?></div>
			<div class="margin-left-3"><select id="map_layer" class="select" onChange="switchMapLayer($(this).val());"></select></div>
		</div>
	</div>
	<div id="side_panel_follow">
		<div id="side_panel_follow_data_list">
			<table id="side_panel_follow_datalist_grid"></table>
		</div>
	</div>
    </body>
</html>