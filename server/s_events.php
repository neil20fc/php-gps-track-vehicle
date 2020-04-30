<?
	// $loc - location data array
	// $ed - event data array
	// $ud - user data array
	// $od - object data array
	
	function check_events($loc, $loc_prev, $loc_events, $params_events, $service_events)
	{
		global $ms;
		
		$q = "SELECT gs_objects.*, gs_user_objects.*
				FROM gs_objects
				INNER JOIN gs_user_objects ON gs_objects.imei = gs_user_objects.imei
				WHERE gs_user_objects.imei='".$loc['imei']."'";
				
		$r = mysqli_query($ms, $q);
		
		while($od = mysqli_fetch_array($r))
		{
			// get user data
			$q2 = "SELECT * FROM `gs_users` WHERE `id`='".$od['user_id']."'";
			$r2 = mysqli_query($ms, $q2);
			$ud = mysqli_fetch_array($r2);
			
			// events loop
			$q2 = "SELECT * FROM `gs_user_events` WHERE `user_id`='".$od['user_id']."' AND UPPER(`imei`) LIKE '%".$loc['imei']."%'";
			$r2 = mysqli_query($ms, $q2);
			
			while($ed = mysqli_fetch_array($r2))
			{			
				if ($ed['active'] == 'true')
				{					
					// check for loc events
					if ($loc_events == true)
					{
						if ($ed['type'] == 'overspeed')
						{
							event_overspeed($ed,$ud,$od,$loc);
						}
						if ($ed['type'] == 'underspeed')
						{
							event_underspeed($ed,$ud,$od,$loc);
						}
						if ($ed['type'] == 'route_in')
						{
							event_route_in($ed,$ud,$od,$loc);
						}
						if ($ed['type'] == 'route_out')
						{
							event_route_out($ed,$ud,$od,$loc);
						}
						if ($ed['type'] == 'zone_in')
						{
							event_zone_in($ed,$ud,$od,$loc);
						}
						if ($ed['type'] == 'zone_out')
						{
							event_zone_out($ed,$ud,$od,$loc);
						}
					}
					
					// check for params events
					if ($params_events == true)
					{
						if ($ed['type'] == 'param')
						{
							event_param($ed,$ud,$od,$loc,$loc_prev);
						}
						
						if ($ed['type'] == 'sensor')
						{
							event_sensor($ed,$ud,$od,$loc,$loc_prev);
						}
						
						if ($ed['type'] == 'driverch')
						{
							event_driver_change($ed,$ud,$od,$loc,$loc_prev);
						}
						
						if ($ed['type'] == 'trailerch')
						{
							event_trailer_change($ed,$ud,$od,$loc,$loc_prev);
						}
					}
					
					// check for service events
					if ($service_events == true)
					{
						if (($ed['type'] == 'connyes') || ($ed['type'] == 'connno'))
						{
							event_connection($ed,$ud,$od,$loc);
						}
						
						if (($ed['type'] == 'gpsyes') || ($ed['type'] == 'gpsno'))
						{
							event_gps($ed,$ud,$od,$loc);
						}
						
						if (($ed['type'] == 'stopped') || ($ed['type'] == 'moving') || ($ed['type'] == 'engidle'))
						{
							event_stopped_moving_engidle($ed,$ud,$od,$loc);
						}
					}
					
					// check for GPS tracker events
					if (!isset($loc['event']))
					{
						continue;
					}
					
					if (($ed['type'] == 'sos') && ($loc['event'] == 'sos'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'bracon') && ($loc['event'] == 'bracon'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'bracoff') && ($loc['event'] == 'bracoff'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'dismount') && ($loc['event'] == 'dismount'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'door') && ($loc['event'] == 'door'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'mandown') && ($loc['event'] == 'mandown'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'shock') && ($loc['event'] == 'shock'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'tow') && ($loc['event'] == 'tow'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'haccel') && ($loc['event'] == 'haccel'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'hbrake') && ($loc['event'] == 'hbrake'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'hcorn') && ($loc['event'] == 'hcorn'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'pwrcut') && ($loc['event'] == 'pwrcut'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'gpsantcut') && ($loc['event'] == 'gpscut'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'lowdc') && ($loc['event'] == 'lowdc'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'lowbat') && ($loc['event'] == 'lowbat'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'jamming') && ($loc['event'] == 'jamming'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
					
					if (($ed['type'] == 'dtc') && (substr($loc['event'], 0, 3) == 'dtc'))
					{
						event_tracker($ed,$ud,$od,$loc);
					}
				}
			}
		}
	}
	
	function event_tracker($ed,$ud,$od,$loc)
	{
		$event_status = get_event_status($ed['event_id'], $loc['imei']);
		
		$ed['event_desc'] = $ed['name'];
		
		if ($ed['type'] == 'dtc')
		{
			$codes = str_replace("dtc:", "", $loc['event']);
			$codes = str_replace(",", ", ", $codes);
			$ed['event_desc'] .= ' ('.$codes.')';
		}
		
		event_notify($ed,$ud,$od,$loc);
	}
	
	function event_connection($ed,$ud,$od,$loc)
	{
		global $gsValues;
		
		if ($ed['type'] == 'connyes')
		{
			if(strtotime($loc['dt_server']) >= strtotime(gmdate("Y-m-d H:i:s")." - ".$gsValues['CONNECTION_TIMEOUT']." minutes"))
			{
				
				if (get_event_status($ed['event_id'], $loc['imei']) == -1)
				{
					set_event_status($ed['event_id'], $loc['imei'], '1');
					// set dt_tracker to dt_server to show exact time
					$loc['dt_tracker'] = $loc['dt_server'];
					// add event desc to event data array
					$ed['event_desc'] = $ed['name'];
					event_notify($ed,$ud,$od,$loc);	
				}			
			}
			else
			{
				if (get_event_status($ed['event_id'], $loc['imei']) != -1)
				{
					set_event_status($ed['event_id'], $loc['imei'], '-1');
				}
			}
		}
		
		if ($ed['type'] == 'connno')
		{
			if(strtotime($loc['dt_server']) < strtotime(gmdate("Y-m-d H:i:s")." - ".$ed['checked_value']." minutes"))
			{
				if (get_event_status($ed['event_id'], $loc['imei']) == -1)
				{
					set_event_status($ed['event_id'], $loc['imei'], '1');
					// set dt_tracker to dt_server to show exact time
					$loc['dt_tracker'] = $loc['dt_server'];
					// add event desc to event data array
					$ed['event_desc'] = $ed['name'];
					event_notify($ed,$ud,$od,$loc);	
				}			
			}
			else
			{
				if (get_event_status($ed['event_id'], $loc['imei']) != -1)
				{
					set_event_status($ed['event_id'], $loc['imei'], '-1');
				}
			}
		}
	}
	
	function event_gps($ed,$ud,$od,$loc)
	{
		if ($ed['type'] == 'gpsyes')
		{
			if ($loc['loc_valid'] == '1')
			{	
				if (get_event_status($ed['event_id'], $loc['imei']) == -1)
				{
					set_event_status($ed['event_id'], $loc['imei'], '1');
					// set dt_tracker to dt_server to show exact time
					$loc['dt_tracker'] = $loc['dt_server'];
					// add event desc to event data array
					$ed['event_desc'] = $ed['name'];
					event_notify($ed,$ud,$od,$loc);	
				}			
			}
			else
			{
				if (get_event_status($ed['event_id'], $loc['imei']) != -1)
				{
					set_event_status($ed['event_id'], $loc['imei'], '-1');
				}
			}
		}
		
		if ($ed['type'] == 'gpsno')
		{
			if (($loc['loc_valid'] == '0') && (strtotime($loc['dt_tracker']) < strtotime(gmdate("Y-m-d H:i:s")." - ".$ed['checked_value']." minutes")))
			{
				if (get_event_status($ed['event_id'], $loc['imei']) == -1)
				{
					set_event_status($ed['event_id'], $loc['imei'], '1');
					// set dt_tracker to dt_server to show exact time
					$loc['dt_tracker'] = $loc['dt_server'];
					// add event desc to event data array
					$ed['event_desc'] = $ed['name'];
					event_notify($ed,$ud,$od,$loc);	
				}			
			}
			else
			{
				if (get_event_status($ed['event_id'], $loc['imei']) != -1)
				{
					set_event_status($ed['event_id'], $loc['imei'], '-1');
				}
			}
		}
	}
	
	function event_stopped_moving_engidle($ed,$ud,$od,$loc)
	{
		$dt_last_stop = strtotime($loc['dt_last_stop']);
		$dt_last_idle = strtotime($loc['dt_last_idle']);
		$dt_last_move = strtotime($loc['dt_last_move']);
		
		if (($dt_last_stop > 0) || ($dt_last_move > 0))
		{
			if ($ed['type'] == 'stopped')
			{
				if (($dt_last_stop >= $dt_last_move) && (strtotime($loc['dt_last_stop']) < strtotime(gmdate("Y-m-d H:i:s")." - ".$ed['checked_value']." minutes")))
				{
					if (get_event_status($ed['event_id'], $loc['imei']) == -1)
					{
						set_event_status($ed['event_id'], $loc['imei'], '1');
						// set dt_tracker to dt_server to show exact time
						$loc['dt_tracker'] = $loc['dt_server'];
						// add event desc to event data array
						$ed['event_desc'] = $ed['name'];
						event_notify($ed,$ud,$od,$loc);	
					}	
				}
				else
				{
					if (get_event_status($ed['event_id'], $loc['imei']) != -1)
					{
						set_event_status($ed['event_id'], $loc['imei'], '-1');
					}
				}
			}
			
			if ($ed['type'] == 'moving')
			{
				if (($dt_last_stop < $dt_last_move) && (strtotime($loc['dt_last_move']) < strtotime(gmdate("Y-m-d H:i:s")." - ".$ed['checked_value']." minutes")))
				{
					if (get_event_status($ed['event_id'], $loc['imei']) == -1)
					{
						set_event_status($ed['event_id'], $loc['imei'], '1');
						// set dt_tracker to dt_server to show exact time
						$loc['dt_tracker'] = $loc['dt_server'];
						// add event desc to event data array
						$ed['event_desc'] = $ed['name'];
						event_notify($ed,$ud,$od,$loc);	
					}	
				}
				else
				{
					if (get_event_status($ed['event_id'], $loc['imei']) != -1)
					{
						set_event_status($ed['event_id'], $loc['imei'], '-1');
					}
				}
			}
			
			if ($ed['type'] == 'engidle')
			{
				if (($dt_last_stop <= $dt_last_idle) && ($dt_last_move <= $dt_last_idle) && (strtotime($loc['dt_last_idle']) < strtotime(gmdate("Y-m-d H:i:s")." - ".$ed['checked_value']." minutes")))
				{
					if (get_event_status($ed['event_id'], $loc['imei']) == -1)
					{
						set_event_status($ed['event_id'], $loc['imei'], '1');
						// set dt_tracker to dt_server to show exact time
						$loc['dt_tracker'] = $loc['dt_server'];
						// add event desc to event data array
						$ed['event_desc'] = $ed['name'];
						event_notify($ed,$ud,$od,$loc);	
					}	
				}
				else
				{
					if (get_event_status($ed['event_id'], $loc['imei']) != -1)
					{
						set_event_status($ed['event_id'], $loc['imei'], '-1');
					}
				}
			}
		}
	}
	
	function event_route_in($ed,$ud,$od,$loc)
	{
		global $ms;
		
		$event_status = get_event_status($ed['event_id'], $loc['imei']);
		
		// check if route still exists, to fix bug if user deletes zone
		$q = "SELECT * FROM `gs_user_routes` WHERE `route_id`='".$event_status."'";
		$r = mysqli_query($ms, $q);
		
		if (mysqli_num_rows($r) == 0)
		{
			set_event_status($ed['event_id'], $loc['imei'], '-1');
			
			$event_status = '-1';
		}
		
		// check event
		$q = "SELECT * FROM `gs_user_routes` WHERE `user_id`='".$ed['user_id']."' AND `route_id` IN (".$ed['routes'].")";
		$r = mysqli_query($ms, $q);
		
		while($route = mysqli_fetch_array($r))
		{	
			$dist = isPointOnLine($route['route_points'], $loc['lat'], $loc['lng']);
			
			// get user units and convert if needed
			$units = explode(",", $ud['units']);
			$dist = convDistanceUnits($dist, 'km', $units[0]);
			
			if ($dist <= $route['route_deviation'])
			{
				if ($event_status == -1)
				{
					set_event_status($ed['event_id'], $loc['imei'], $route['route_id']);
					// add event desc to event data array
					$ed['event_desc'] = $ed['name']. ' ('.$route['route_name'].')';
					event_notify($ed,$ud,$od,$loc);
				}			
			}
			else
			{
				if ($event_status == $route['route_id'])
				{
					set_event_status($ed['event_id'], $loc['imei'], '-1');
				}
			}
		}
	}
	
	function event_route_out($ed,$ud,$od,$loc)
	{
		global $ms;
		
		$event_status = get_event_status($ed['event_id'], $loc['imei']);
		
		// check if route still exists, to fix bug if user deletes zone
		$q = "SELECT * FROM `gs_user_routes` WHERE `route_id`='".$event_status."'";
		$r = mysqli_query($ms, $q);
		
		if (mysqli_num_rows($r) == 0)
		{
			set_event_status($ed['event_id'], $loc['imei'], '-1');
			
			$event_status = '-1';
		}
		
		// check event
		$q = "SELECT * FROM `gs_user_routes` WHERE `user_id`='".$ed['user_id']."' AND `route_id` IN (".$ed['routes'].")";
		$r = mysqli_query($ms, $q);
		
		while($route = mysqli_fetch_array($r))
		{			
			$dist = isPointOnLine($route['route_points'], $loc['lat'], $loc['lng']);
			
			// get user units and convert if needed
			$units = explode(",", $ud['units']);
			$dist = convDistanceUnits($dist, 'km', $units[0]);
			
			if ($dist < $route['route_deviation'])
			{
				if ($event_status == -1)
				{
					set_event_status($ed['event_id'], $loc['imei'], $route['route_id']);
				}	
			}
			else
			{
				if ($event_status == $route['route_id'])
				{
					set_event_status($ed['event_id'], $loc['imei'], '-1');
					// add event desc to event data array
					$ed['event_desc'] = $ed['name']. ' ('.$route['route_name'].')';
					event_notify($ed,$ud,$od,$loc);
				}	
			}
		}
	}
	
	function event_zone_in($ed,$ud,$od,$loc)
	{
		global $ms;
		
		$event_status = get_event_status($ed['event_id'], $loc['imei']);
		
		// check if zone still exists, to fix bug if user deletes zone
		$q = "SELECT * FROM `gs_user_zones` WHERE `zone_id`='".$event_status."'";
		$r = mysqli_query($ms, $q);
		
		if (mysqli_num_rows($r) == 0)
		{
			set_event_status($ed['event_id'], $loc['imei'], '-1');
			
			$event_status = '-1';
		}
		
		// check event
		$q = "SELECT * FROM `gs_user_zones` WHERE `user_id`='".$ed['user_id']."' AND `zone_id` IN (".$ed['zones'].")";
		$r = mysqli_query($ms, $q);
		
		if (!$r) { return;}
		
		while($zone = mysqli_fetch_array($r))
		{	
			$in_zone = isPointInPolygon($zone['zone_vertices'], $loc['lat'], $loc['lng']);
			
			if ($in_zone)
			{
				if ($event_status == -1)
				{
					set_event_status($ed['event_id'], $loc['imei'], $zone['zone_id']);
					// add event desc to event data array
					$ed['event_desc'] = $ed['name']. ' ('.$zone['zone_name'].')';
					event_notify($ed,$ud,$od,$loc);
				}			
			}
			else
			{
				if ($event_status == $zone['zone_id'])
				{
					set_event_status($ed['event_id'], $loc['imei'], '-1');
				}
			}
		}
	}
	
	function event_zone_out($ed,$ud,$od,$loc)
	{
		global $ms;
		
		$event_status = get_event_status($ed['event_id'], $loc['imei']);
		
		// check if zone still exists, to fix bug if user deletes zone
		$q = "SELECT * FROM `gs_user_zones` WHERE `zone_id`='".$event_status."'";
		$r = mysqli_query($ms, $q);
		
		if (mysqli_num_rows($r) == 0)
		{
			set_event_status($ed['event_id'], $loc['imei'], '-1');
			
			$event_status = '-1';
		}
		
		// check event
		$q = "SELECT * FROM `gs_user_zones` WHERE `user_id`='".$ed['user_id']."' AND `zone_id` IN (".$ed['zones'].")";
		$r = mysqli_query($ms, $q);
		
		if (!$r) { return;}
		
		while($zone = mysqli_fetch_array($r))
		{			
			$in_zone = isPointInPolygon($zone['zone_vertices'], $loc['lat'], $loc['lng']);
			
			if ($in_zone)
			{
				if ($event_status == -1)
				{
					set_event_status($ed['event_id'], $loc['imei'], $zone['zone_id']);
				}	
			}
			else
			{
				if ($event_status == $zone['zone_id'])
				{
					set_event_status($ed['event_id'], $loc['imei'], '-1');
					// add event desc to event data array
					$ed['event_desc'] = $ed['name']. ' ('.$zone['zone_name'].')';
					event_notify($ed,$ud,$od,$loc);
				}	
			}
		}
	}
	
	function event_param($ed,$ud,$od,$loc,$loc_prev)
	{
		$condition = false;
		$params = $loc['params'];
		$params_prev = $loc_prev['params'];
		$units = explode(",", $ud['units']);
		
		$pc = json_decode($ed['checked_value'], true);
		if ($pc == null)
		{
			return;
		}
		
		// check conditions
		for ($i=0; $i<count($pc); $i++)
		{
			$cn = false;
			
			if (($pc[$i]['cn'] == 'grp') || ($pc[$i]['cn'] == 'lwp'))
			{
				$pc[$i]['val'] = trim(str_replace("%", "", $pc[$i]['val']));
				
				if ($pc[$i]['src'] == 'speed')
				{
					$value = convSpeedUnits($loc['speed'], 'km', $units[0]);
					$value_prev = convSpeedUnits($loc_prev['speed'], 'km', $units[0]);
				}
				else
				{
					// check if param exits
					if ((!isset($params[$pc[$i]['src']])) || (!isset($params_prev[$pc[$i]['src']])))
					{
						$condition = false;
						break;
					}
					
					$value = $params[$pc[$i]['src']];
					$value_prev = $params_prev[$pc[$i]['src']];
				}
				
				if ($value_prev == 0)
				{
					$condition = false;
					break;
				}
				
				if (($pc[$i]['cn'] == 'grp') && ($value > $value_prev))
				{
					$percent_diff = ($value - $value_prev) / $value_prev * 100;
					$percent_diff = abs($percent_diff);
					if ($percent_diff > $pc[$i]['val']) $cn = true;					
				}
				
				if (($pc[$i]['cn'] == 'lwp') && ($value < $value_prev))
				{
					$percent_diff = ($value - $value_prev) / $value_prev * 100;
					$percent_diff = abs($percent_diff);
					if ($percent_diff > $pc[$i]['val']) $cn = true;
				}
			}
			else
			{
				if ($pc[$i]['src'] == 'speed')
				{
					$value = convSpeedUnits($loc['speed'], 'km', $units[0]);
				}
				else
				{
					// check if param exits
					if (!isset($params[$pc[$i]['src']]))
					{
						$condition = false;
						break;
					}
					
					$value = $params[$pc[$i]['src']];
				}
			
				if ($pc[$i]['cn'] == 'eq')
				{
					if ($value == $pc[$i]['val']) $cn = true;
				}
				
				if ($pc[$i]['cn'] == 'gr')
				{
					if ($value > $pc[$i]['val']) $cn = true;
				}
				
				if ($pc[$i]['cn'] == 'lw')
				{
					if ($value < $pc[$i]['val']) $cn = true;
				}
			}
			
			if ($cn == true)
			{
				$condition = true;
			}
			else
			{
				$condition = false;
				break;
			}
		}
		
		if ($condition)
		{
			if (get_event_status($ed['event_id'], $loc['imei']) == -1)
			{
				set_event_status($ed['event_id'], $loc['imei'], '1');
				// add event desc to event data array
				$ed['event_desc'] = $ed['name'];
				event_notify($ed,$ud,$od,$loc);
			}			
		}
		else
		{
			if (get_event_status($ed['event_id'], $loc['imei']) != -1)
			{
				set_event_status($ed['event_id'], $loc['imei'], '-1');
			}
		}
	}
	
	function event_sensor($ed,$ud,$od,$loc,$loc_prev)
	{
		$condition = false;
		$params = $loc['params'];
		$params_prev = $loc_prev['params'];
		$units = explode(",", $ud['units']);
		
		$sc = json_decode($ed['checked_value'], true);
		if ($sc == null)
		{
			return;
		}
		
		$sensors = getSensors($loc['imei']);
		
		if ($sensors == false)
		{
			return;
		}
		
		// check conditions
		for ($i=0; $i<count($sc); $i++)
		{			
			$cn = false;
			
			if (($sc[$i]['cn'] == 'grp') || ($sc[$i]['cn'] == 'lwp'))
			{
				$sc[$i]['val'] = trim(str_replace("%", "", $sc[$i]['val']));
				
				if ($sc[$i]['src'] == 'speed')
				{
					$value = convSpeedUnits($loc['speed'], 'km', $units[0]);
					$value_prev = convSpeedUnits($loc_prev['speed'], 'km', $units[0]);
				}
				else
				{
					$sensor = false;
					
					for ($j=0; $j<count($sensors); ++$j)
					{
						if ($sc[$i]['src'] == $sensors[$j]['name'])
						{
							$sensor = $sensors[$j];
						}
					}
					
					// check if sensor exits
					if (!$sensor)
					{
						$condition = false;
						break;
					}
					
					// check if param exits
					if ((!isset($params[$sensor['param']])) || (!isset($params_prev[$sensor['param']])))
					{
						$condition = false;
						break;
					}
					
					$sensor_value = getSensorValue($params, $sensor);
					$sensor_value_prev = getSensorValue($params_prev, $sensor);
									
					$value = $sensor_value['value'];
					$value_prev = $sensor_value_prev['value'];
				}
				
				if ($value_prev == 0)
				{
					$condition = false;
					break;
				}
				
				if (($sc[$i]['cn'] == 'grp') && ($value > $value_prev))
				{
					$percent_diff = ($value - $value_prev) / $value_prev * 100;
					$percent_diff = abs($percent_diff);
					if ($percent_diff > $sc[$i]['val']) $cn = true;					
				}
				
				if (($sc[$i]['cn'] == 'lwp') && ($value < $value_prev))
				{
					$percent_diff = ($value - $value_prev) / $value_prev * 100;
					$percent_diff = abs($percent_diff);
					if ($percent_diff > $sc[$i]['val']) $cn = true;
				}
			}
			else
			{
				if ($sc[$i]['src'] == 'speed')
				{
					$value = convSpeedUnits($loc['speed'], 'km', $units[0]);
				}
				else
				{
					$sensor = false;
					
					for ($j=0; $j<count($sensors); ++$j)
					{
						if ($sc[$i]['src'] == $sensors[$j]['name'])
						{
							$sensor = $sensors[$j];
						}
					}
					
					// check if sensor exits
					if (!$sensor)
					{
						$condition = false;
						break;
					}
					
					// check if param exits
					if (!isset($params[$sensor['param']]))
					{
						$condition = false;
						break;
					}
					
					$sensor_value = getSensorValue($params, $sensor);
									
					$value = $sensor_value['value'];
				}
				
				if ($sc[$i]['cn'] == 'eq')
				{
					if ($value == $sc[$i]['val']) $cn = true;
				}
				
				if ($sc[$i]['cn'] == 'gr')
				{
					if ($value > $sc[$i]['val']) $cn = true;
				}
				
				if ($sc[$i]['cn'] == 'lw')
				{
					if ($value < $sc[$i]['val']) $cn = true;
				}	
			}
			
			if ($cn == true)
			{
				$condition = true;
			}
			else
			{
				$condition = false;
				break;
			}
		}
		
		if ($condition)
		{
			if (get_event_status($ed['event_id'], $loc['imei']) == -1)
			{
				set_event_status($ed['event_id'], $loc['imei'], '1');
				// add event desc to event data array
				$ed['event_desc'] = $ed['name'];
				event_notify($ed,$ud,$od,$loc);
			}			
		}
		else
		{
			if (get_event_status($ed['event_id'], $loc['imei']) != -1)
			{
				set_event_status($ed['event_id'], $loc['imei'], '-1');
			}
		}
	}
	
	function event_driver_change($ed,$ud,$od,$loc,$loc_prev)
	{
		$imei = $loc['imei'];
		$params = $loc['params'];
		$params_prev = $loc_prev['params'];
		
		$group_array = array('da');
		
		for ($i=0; $i<count($group_array); ++$i)
		{
			$group = $group_array[$i];
			
			$sensor = getSensorFromType($imei, $group);
			
			if ($sensor != false)
			{
				$sensor_ = $sensor[0];
				
				$sensor_data = getSensorValue($params, $sensor_);
				$assign_id = $sensor_data['value'];
				
				$sensor_data_prev = getSensorValue($params_prev, $sensor_);
				$assign_id_prev = $sensor_data_prev['value'];
				
				if ((string)$assign_id != (string)$assign_id_prev)
				{
					// add event desc to event data array
					$ed['event_desc'] = $ed['name'];
					event_notify($ed,$ud,$od,$loc);
				}
			}
			
		}
	}
	
	function event_trailer_change($ed,$ud,$od,$loc,$loc_prev)
	{
		$imei = $loc['imei'];
		$params = $loc['params'];
		$params_prev = $loc_prev['params'];
		
		$group_array = array('ta');
		
		for ($i=0; $i<count($group_array); ++$i)
		{
			$group = $group_array[$i];
			
			$sensor = getSensorFromType($imei, $group);
			
			if ($sensor != false)
			{
				$sensor_ = $sensor[0];
				
				$sensor_data = getSensorValue($params, $sensor_);
				$assign_id = $sensor_data['value'];
				
				$sensor_data_prev = getSensorValue($params_prev, $sensor_);
				$assign_id_prev = $sensor_data_prev['value'];
				
				if ((string)$assign_id != (string)$assign_id_prev)
				{
					// add event desc to event data array
					$ed['event_desc'] = $ed['name'];
					event_notify($ed,$ud,$od,$loc);
				}
			}
			
		}
	}

	function event_overspeed($ed,$ud,$od,$loc)
	{
		$speed = $loc['speed'];
		
		// get user speed unit and convert if needed
		$units = explode(",", $ud['units']);
		$speed = convSpeedUnits($speed, 'km', $units[0]);
		
		if ($speed > $ed['checked_value'])
		{
			if (get_event_status($ed['event_id'], $loc['imei']) == -1)
			{
				set_event_status($ed['event_id'], $loc['imei'], '1');
				// add event desc to event data array
				$ed['event_desc'] = $ed['name'];;
				event_notify($ed,$ud,$od,$loc);
			}			
		}
		else
		{
			if (get_event_status($ed['event_id'], $loc['imei']) != -1)
			{
				set_event_status($ed['event_id'], $loc['imei'], '-1');
			}
		}
	}
	
	function event_underspeed($ed,$ud,$od,$loc)
	{
		$speed = $loc['speed'];
		
		// get user speed unit and convert if needed
		$units = explode(",", $ud['units']);
		$speed = convSpeedUnits($speed, 'km', $units[0]);
		
		if ($speed < $ed['checked_value'])
		{
			if (get_event_status($ed['event_id'], $loc['imei']) == -1)
			{
				set_event_status($ed['event_id'], $loc['imei'], '1');
				// add event desc to event data array
				$ed['event_desc'] = $ed['name'];;
				event_notify($ed,$ud,$od,$loc);
			}			
		}
		else
		{
			if (get_event_status($ed['event_id'], $loc['imei']) != -1)
			{
				set_event_status($ed['event_id'], $loc['imei'], '-1');
			}
		}
	}
	
	function event_notify($ed,$ud,$od,$loc)
	{
		global $ms, $gsValues;
		
		$imei = $loc['imei'];
		
		if (!checkObjectActive($imei))
		{
			return;
		}
		
		// get current date and time for week days and day time check
		$dt_check = convUserIDTimezone($ud['id'], date("Y-m-d H:i:s", strtotime($loc['dt_server'])));
		
		if (!check_event_week_days($dt_check, $ed['week_days']))
		{
			return;
		}
		
		if (!check_event_day_time($dt_check, $ed['day_time']))
		{
			return;
		}
		
		if(!check_event_route_trigger($ed, $ud, $loc))
		{
			return;
		}
		
		if(!check_event_zone_trigger($ed, $ud, $loc))
		{
			return;
		}
		
		// duration from last event
		if(!check_event_duration_from_last($ed, $imei))
		{
			
			return;
		}
		else
		{
			$q = "UPDATE `gs_user_events_status` SET `dt_server`='".gmdate("Y-m-d H:i:s")."' WHERE `event_id`='".$ed['event_id']."' AND `imei`='".$imei."'";	
			$r = mysqli_query($ms, $q);	
		}

		// insert event into list
		$q = "INSERT INTO `gs_user_last_events_data` (	user_id,
								type,
								event_desc,
								notify_system,
								notify_push,
								notify_arrow,
								notify_arrow_color,
								notify_ohc,
								notify_ohc_color,
								imei,
								name,
								dt_server,
								dt_tracker,
								lat,
								lng,
								altitude,
								angle,
								speed,
								params
								) VALUES (
								'".$ed['user_id']."',
								'".$ed['type']."',
								'".mysqli_real_escape_string($ms, $ed['event_desc'])."',
								'".$ed['notify_system']."',
								'".$ed['notify_push']."',
								'".$ed['notify_arrow']."',
								'".$ed['notify_arrow_color']."',
								'".$ed['notify_ohc']."',
								'".$ed['notify_ohc_color']."',
								'".$od['imei']."',
								'".mysqli_real_escape_string($ms, $od['name'])."',
								'".$loc['dt_server']."',
								'".$loc['dt_tracker']."',
								'".$loc['lat']."',
								'".$loc['lng']."',
								'".$loc['altitude']."',
								'".$loc['angle']."',
								'".$loc['speed']."',
								'".json_encode($loc['params'])."')";
		$r = mysqli_query($ms, $q);
		
		// insert event into list
		$q = "INSERT INTO `gs_user_events_data` (	user_id,
								type,
								event_desc,
								imei,
								name,
								dt_server,
								dt_tracker,
								lat,
								lng,
								altitude,
								angle,
								speed,
								params
								) VALUES (
								'".$ed['user_id']."',
								'".$ed['type']."',
								'".mysqli_real_escape_string($ms, $ed['event_desc'])."',
								'".$od['imei']."',
								'".mysqli_real_escape_string($ms, $od['name'])."',
								'".$loc['dt_server']."',
								'".$loc['dt_tracker']."',
								'".$loc['lat']."',
								'".$loc['lng']."',
								'".$loc['altitude']."',
								'".$loc['angle']."',
								'".$loc['speed']."',
								'".json_encode($loc['params'])."')";
		$r = mysqli_query($ms, $q);
		
		// send webhook
		if ($ed['webhook_send'] == 'true')
		{
			$url = $ed['webhook_url'];
			$url .= '?username='.urlencode($ud['username']);
			$url .= '&type='.urlencode($ed['type']);
			$url .= '&desc='.urlencode($ed['event_desc']);
			$url .= '&imei='.urlencode($od['imei']);
			$url .= '&name='.urlencode($od['name']);
			$url .= '&dt_server='.urlencode($loc['dt_server']);
			$url .= '&dt_tracker='.urlencode($loc['dt_tracker']);
			$url .= '&lat='.urlencode($loc['lat']);
			$url .= '&lng='.urlencode($loc['lng']);
			$url .= '&altitude='.urlencode($loc['altitude']);
			$url .= '&angle='.urlencode($loc['angle']);
			$url .= '&speed='.urlencode($loc['speed']);
			
			sendWebhookQueue($url);
		}
		
		// send cmd
		if ($ed['cmd_send'] == 'true')
		{			
			if ($ed['cmd_gateway'] == 'gprs')
			{				
				sendObjectGPRSCommand($ed['user_id'], $imei, mysqli_real_escape_string($ms, $ed['event_desc']), $ed['cmd_type'], mysqli_real_escape_string($ms, $ed['cmd_string']));
			}
			else if ($ed['cmd_gateway'] == 'sms')
			{
				sendObjectSMSCommand($ed['user_id'], $imei, mysqli_real_escape_string($ms, $ed['event_desc']), mysqli_real_escape_string($ms, $ed['cmd_string']));
			}
		}		
		
		// send push notification
		if ($ed['notify_push'] == 'true')
		{
			// account
			$result = sendPushQueue($ud['push_notify_identifier'], 'event', '');
			
			// sub accounts
			$q = "SELECT * FROM `gs_users` WHERE `manager_id`='".$ed['user_id']."' AND `privileges` LIKE ('%subuser%')";
			$r = mysqli_query($ms, $q);
			
			while ($row = mysqli_fetch_array($r))
			{
				$privileges = json_decode($row['privileges'],true);
				if (!isset($privileges["imei"]))
				{
					continue;
				}
				$imeis = explode(",", $privileges["imei"]);
				if (in_array($imei, $imeis))
				{
					$result = sendPushQueue($row['push_notify_identifier'], 'event', '');
				}
			}
		}
			
		// send email notification
		if (checkUserUsage($ed['user_id'], 'email'))
		{
			if (($ed['notify_email'] == 'true') && ($ed['notify_email_address'] != ''))
			{			
				$email = $ed['notify_email_address'];
				
				$template = event_notify_template('email',$ed,$ud,$od,$loc);
								
				$result = sendEmailQueue($email, $template['subject'], $template['message'], true);
				
				if ($result)
				{
					updateUserUsage($ed['user_id'], false, $result, false, false);	
				}				
			}	
		}
		
		// send SMS notification
		if (checkUserUsage($ed['user_id'], 'sms'))
		{
			if (($ed['notify_sms'] == 'true') && ($ed['notify_sms_number'] != ''))
			{
				$result = false;
				
				$number = $ed['notify_sms_number'];
				
				$template = event_notify_template('sms',$ed,$ud,$od,$loc);
				
				if ($ud['sms_gateway'] == 'true')
				{
					if ($ud['sms_gateway_type'] == 'http')
					{
						$result = sendSMSHTTPQueue($ud['sms_gateway_url'], '', $number, $template['message']);	
					}
					else if ($ud['sms_gateway_type'] == 'app')
					{
						$result = sendSMSAPP($ud['sms_gateway_identifier'], '', $number, $template['message']);
					}
				}
				else
				{
					if (($ud['sms_gateway_server'] == 'true') && ($gsValues['SMS_GATEWAY'] == 'true'))
					{
						if ($gsValues['SMS_GATEWAY_TYPE'] == 'http')
						{
							$result = sendSMSHTTPQueue($gsValues['SMS_GATEWAY_URL'], $gsValues['SMS_GATEWAY_NUMBER_FILTER'], $number, $template['message']);	
						}
						else if ($gsValues['SMS_GATEWAY_TYPE'] == 'app')
						{
							$result = sendSMSAPP($gsValues['SMS_GATEWAY_IDENTIFIER'], $gsValues['SMS_GATEWAY_NUMBER_FILTER'], $number, $template['message']);
						}
					}
				}
				
				if ($result)
				{
					//update user usage
					updateUserUsage($ed['user_id'], false, false, $result, false);
				}
			}	
		}
	}
	
	function event_notify_template($type,$ed,$ud,$od,$loc)
	{
		global $ms, $la;
		
		// load language
		loadLanguage($ud["language"], $ud["units"]);
		
		// get template
		$template = array();
		$template['subject'] = '';
		$template['message'] = '';
		
		if ($type == 'email')
		{
			$template = getDefaultTemplate('event_email', $ud["language"]);
		}
		else if ($type == 'sms')
		{
			$template = getDefaultTemplate('event_sms', $ud["language"]);
		}
		
		if ($ed[$type.'_template_id'] != 0)
		{
			$q = "SELECT * FROM `gs_user_templates` WHERE `template_id`='".$ed[$type.'_template_id']."'";
			$r = mysqli_query($ms, $q);
			$row = mysqli_fetch_array($r);
			
			if ($row)
			{
				if ($row['subject'] != '')
				{
					$template['subject'] = $row['subject'];
				}
				
				if ($row['message'] != '')
				{
					$template['message'] = $row['message'];
				}
			}
		}
		
		// modify template variables
		$g_map = 'http://maps.google.com/maps?q='.$loc['lat'].','.$loc['lng'].'&t=m';
		
		// add timezone to dt_tracker and dt_server
		$dt_server = convUserIDTimezone($ud['id'], $loc['dt_server']);
		$dt_tracker = convUserIDTimezone($ud['id'], $loc['dt_tracker']);
		
		$units = explode(",", $ud['units']);
		
		$speed = $loc['speed'];		
		$speed = convSpeedUnits($speed, 'km', $units[0]);
		$speed = $speed.' '.$la["UNIT_SPEED"];
		
		$driver = getObjectDriver($ud['id'], $od['imei'], $loc['params']);
		
		$trailer = getObjectTrailer($ud['id'], $od['imei'], $loc['params']);
		
		// check if there is address variable
		if ((strpos($template['subject'], "%ADDRESS%") !== "") || (strpos($template['message'], "%ADDRESS%") !== ""))
		{
			$address = geocoderGetAddress($loc["lat"], $loc["lng"]);
		}
		
		// check odometer variable
		if ((strpos($template['subject'], "%ODOMETER%") !== "") || (strpos($template['message'], "%ODOMETER%") !== ""))
		{
			$odometer = getObjectOdometer($od['imei']);
			$odometer = floor(convDistanceUnits($odometer, 'km', $units[0]));	
		}
		
		// check engine hours variable
		if ((strpos($template['subject'], "%ENG_HOURS%") !== "") || (strpos($template['message'], "%ENG_HOURS%") !== ""))
		{
			$eng_hours = getObjectEngineHours($od['imei'], true);
		}
		
		foreach ($template as $key => $value)
		{
			$value = str_replace("%NAME%", $od["name"], $value);
			$value = str_replace("%IMEI%", $od["imei"], $value);
			$value = str_replace("%EVENT%", $ed['event_desc'], $value);
			$value = str_replace("%LAT%", $loc["lat"], $value);
			$value = str_replace("%LNG%", $loc["lng"], $value);
			$value = str_replace("%ADDRESS%", $address, $value);			
			$value = str_replace("%SPEED%", $speed, $value);
			$value = str_replace("%ALT%", $loc["altitude"], $value);
			$value = str_replace("%ANGLE%", $loc["angle"], $value);
			$value = str_replace("%DT_POS%", $dt_tracker, $value);
			$value = str_replace("%DT_SER%", $dt_server, $value);
			$value = str_replace("%G_MAP%", $g_map, $value);
			$value = str_replace("%TR_MODEL%", $od["model"], $value);
			$value = str_replace("%PL_NUM%", $od["plate_number"], $value);
			$value = str_replace("%DRIVER%", $driver['driver_name'], $value);
			$value = str_replace("%TRAILER%", $trailer['trailer_name'], $value);
			$value = str_replace("%ODOMETER%", $odometer, $value);
			$value = str_replace("%ENG_HOURS%", $eng_hours, $value);
			
			$template[$key] = $value;
		}
		
		return $template;
	}
	
	function get_event_status($event_id, $imei)
	{
		global $ms;
		
		$result = '-1';
		
		$q = "SELECT * FROM `gs_user_events_status` WHERE `event_id`='".$event_id."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		if ($row)
		{	
			$result = $row['event_status'];
		}
		else
		{
			$q = "INSERT INTO `gs_user_events_status` (`event_id`,`imei`,`event_status`) VALUES ('".$event_id."','".$imei."','-1')";
			$r = mysqli_query($ms, $q);
		}
		
		return $result;	
	}
	
	function set_event_status($event_id, $imei, $value)
	{
		global $ms;
		
		$q = "UPDATE `gs_user_events_status` SET `event_status`='".$value."' WHERE `event_id`='".$event_id."' AND `imei`='".$imei."'";	
		$r = mysqli_query($ms, $q);
	}
	
	function check_event_duration_from_last($ed, $imei)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_user_events_status` WHERE `event_id`='".$ed['event_id']."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		if ($row)
		{
			if($ed['duration_from_last_event'] == 'true')
			{				
				if(strtotime($row['dt_server']) >= strtotime(gmdate("Y-m-d H:i:s")." - ".$ed['duration_from_last_event_minutes']." minutes"))
				{
					return false;
				}
			}
		}
		
		return true;
	}
	
	function check_event_week_days($dt_check, $week_days)
	{
		$day_of_week = gmdate('w', strtotime($dt_check));
		$week_days = explode(',', $week_days);
		
		if ($week_days[$day_of_week] == 'true')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function check_event_day_time($dt_check, $day_time)
	{
		$day_of_week = gmdate('w', strtotime($dt_check));
		$day_time = json_decode($day_time, true);
		
		if ($day_time != null)
		{
			if ($day_time['dt'] == true)
			{
				if (($day_time['sun'] == true) && ($day_of_week == 0))
				{
					$from = $day_time['sun_from'];
					$to = $day_time['sun_to'];
				}
				else if (($day_time['mon'] == true) && ($day_of_week == 1))
				{
					$from = $day_time['mon_from'];
					$to = $day_time['mon_to'];
				}
				else if (($day_time['tue'] == true) && ($day_of_week == 2))
				{
					$from = $day_time['tue_from'];
					$to = $day_time['tue_to'];
				}
				else if (($day_time['wed'] == true) && ($day_of_week == 3))
				{
					$from = $day_time['wed_from'];
					$to = $day_time['wed_to'];
				}
				else if (($day_time['thu'] == true) && ($day_of_week == 4))
				{
					$from = $day_time['thu_from'];
					$to = $day_time['thu_to'];
				}
				else if (($day_time['fri'] == true) && ($day_of_week == 5))
				{
					$from = $day_time['fri_from'];
					$to = $day_time['fri_to'];
				}
				else if (($day_time['sat'] == true) && ($day_of_week == 6))
				{
					$from = $day_time['sat_from'];
					$to = $day_time['sat_to'];
				}
				else
				{
					return false;
				}
				
				if (isset($from) && isset($to))
				{
					$dt_check = strtotime(date("H:i", strtotime($dt_check)));
					$from = strtotime($from);		
					$to = strtotime($to);
					
					// add one day offset
					if ($from > $to)
					{
						$to = $to + 86400;
					}
					
					if (($from < $dt_check) && ($to > $dt_check))
					{
						return true;
					}
					else
					{
						return false;
					}
				}
				else
				{
					return true;
				}
			}
			else
			{
				return true;
			}
		}
		else
		{
			return true;
		}
	}
	
	function check_event_route_trigger($ed, $ud, $loc)
	{
		global $ms;
		
		$user_id = $ed['user_id'];
		$route_trigger = $ed['route_trigger'];
		$routes = $ed['routes'];
		$lat = $loc['lat'];
		$lng = $loc['lng'];
		
		if (($route_trigger == '') || ($route_trigger == 'off'))
		{
			return true;
		}
		
		if ($route_trigger == 'in')
		{
			$q = "SELECT * FROM `gs_user_routes` WHERE `user_id`='".$user_id."' AND `route_id` IN (".$routes.")";
			$r = mysqli_query($ms, $q);
			
			if (!$r) {return false;}
			
			while($route = mysqli_fetch_array($r))
			{
				$dist = isPointOnLine($route['route_points'], $loc['lat'], $loc['lng']);
			
				// get user units and convert if needed
				$units = explode(",", $ud['units']);
				$dist = convDistanceUnits($dist, 'km', $units[0]);
				
				if ($dist <= $route['route_deviation'])
				{
					return true;		
				}
			}
		}
		
		if ($route_trigger == 'out')
		{
			$q = "SELECT * FROM `gs_user_routes` WHERE `user_id`='".$user_id."' AND `route_id` IN (".$routes.")";
			$r = mysqli_query($ms, $q);
			
			if (!$r) {return false;}
			
			$in_routes = false;
			
			while($route = mysqli_fetch_array($r))
			{
				$dist = isPointOnLine($route['route_points'], $loc['lat'], $loc['lng']);
				
				// get user units and convert if needed
				$units = explode(",", $ud['units']);
				$dist = convDistanceUnits($dist, 'km', $units[0]);
				
				if ($dist <= $route['route_deviation'])
				{
					$in_routes = true;
					break;
				}
			}
			
			if ($in_routes == false)
			{
				return true;
			}
		}
		
		return false;
	}
	
	function check_event_zone_trigger($ed, $ud, $loc)
	{
		global $ms;
		
		$user_id = $ed['user_id'];
		$zone_trigger = $ed['zone_trigger'];
		$zones = $ed['zones'];
		$lat = $loc['lat'];
		$lng = $loc['lng'];
		
		if (($zone_trigger == '') || ($zone_trigger == 'off'))
		{
			return true;
		}
		
		if ($zone_trigger == 'in')
		{
			$q = "SELECT * FROM `gs_user_zones` WHERE `user_id`='".$user_id."' AND `zone_id` IN (".$zones.")";
			$r = mysqli_query($ms, $q);
			
			if (!$r) {return false;}
			
			while($zone = mysqli_fetch_array($r))
			{
				$in_zone = isPointInPolygon($zone['zone_vertices'], $lat, $lng);
				
				if ($in_zone)
				{				
					return true;
				}
			}
		}
		
		if ($zone_trigger == 'out')
		{
			$q = "SELECT * FROM `gs_user_zones` WHERE `user_id`='".$user_id."' AND `zone_id` IN (".$zones.")";
			$r = mysqli_query($ms, $q);
			
			if (!$r) {return false;}
			
			$in_zones = false;
			
			while($zone = mysqli_fetch_array($r))
			{	
				$in_zone = isPointInPolygon($zone['zone_vertices'], $lat, $lng);
				
				if ($in_zone)
				{
					$in_zones = true;
					break;
				}
			}
			
			if ($in_zones == false)
			{
				return true;
			}
		}
		
		return false;
	}
?>