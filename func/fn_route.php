<?
	// describe route array data
	// $route[0] - dt_tracker
	// $route[1] - lat
	// $route[2] - lng
	// $route[3] - altitude
	// $route[4] - angle
	// $route[5] - speed
	// $route[6] - params

	function getRouteRaw($imei, $accuracy, $dtf, $dtt)
	{
		global $ms;
		
		$route = array();
		$params_prev = array();
		
		$q = "SELECT DISTINCT	dt_tracker,
					lat,
					lng,
					altitude,
					angle,
					speed,
					params
					FROM `gs_object_data_".$imei."` WHERE dt_tracker BETWEEN '".$dtf."' AND '".$dtt."' ORDER BY dt_tracker ASC";
					
		$r = mysqli_query($ms, $q);
		
		// check if there is any sensor values to ignore due to ignition off
		$ignore_sensors = array();
		$acc_sensor = getSensorFromType($imei, 'acc');
		
		if ($acc_sensor)
		{
			$sensors = getObjectSensors($imei);
			
			foreach ($sensors as $key=>$value)
			{
				if ($value['acc_ignore'] == 'true')
				{
					$ignore_sensors[] = $value;
				}
			}
		}
				
		while($route_data=mysqli_fetch_array($r))
		{
			$dt_tracker = convUserTimezone($route_data['dt_tracker']);
			$lat = $route_data['lat'];
			$lng = $route_data['lng'];
			$altitude = $route_data['altitude'];
			$angle = $route_data['angle'];
			$speed = $route_data['speed'];
			
			$params = json_decode($route_data['params'],true);
			
			// check if there is any sensor values to ignore due to ignition off
			if ($acc_sensor)
			{
				foreach ($ignore_sensors as $key=>$value)
				{
					$sensor_data = getSensorValue($params, $acc_sensor[0]);					
						
					if ($sensor_data['value'] == 0)
					{
						if (isset($params_prev[$value['param']]))
						{
							$params[$value['param']] = $params_prev[$value['param']];	
						}
					}					
				}		
			}
			
			$params = mergeParams($params_prev, $params);
			
			if (!isset($_SESSION["unit_distance"]))
			{
				$_SESSION["unit_distance"] = 'km';
			}
			
			$speed = convSpeedUnits($speed, 'km', $_SESSION["unit_distance"]);
			$altitude = convAltitudeUnits($altitude, 'km', $_SESSION["unit_distance"]);
			
			if (isset($params['gpslev']) && ($accuracy['use_gpslev'] == true))
			{
				$gpslev = $params['gpslev'];
				$min_gpslev = $accuracy['min_gpslev'];
			}
			else
			{
				$gpslev = 0;
				$min_gpslev = 0;
			}
			
			if (isset($params['hdop']) && ($accuracy['use_hdop'] == true))
			{
				$hdop = $params['hdop'];
				$max_hdop = $accuracy['max_hdop'];
			}
			else
			{
				$hdop = 0;
				$max_hdop = 0;
			}
						
			if (($gpslev >= $min_gpslev) && ($hdop <= $max_hdop))
			{
				
				if (($lat != 0) && ($lng != 0))
				{
					$route[] = array(	$dt_tracker,
								$lat,
								$lng,
								$altitude,
								$angle,
								$speed,
								$params);
				}
			}
			
			// store prev params
			$params_prev = $params;
		}
		
		// get fuel sensors
		$fuel_sensors = getSensorFromType($imei, 'fuel');
		
		// fuel smoothening
		if ($fuel_sensors != false)
		{				
			for ($i=0; $i<count($fuel_sensors); ++$i)
			{
				$param = $fuel_sensors[$i]['param'];
				
				for ($j=0; $j<count($route); ++$j)
				{					
					$prev = isset($route[$j-1]) ? getParamValue($route[$j-1][6], $param) : false;
					$curr = isset($route[$j]) ? getParamValue($route[$j][6], $param) : false;
					$next = isset($route[$j+1]) ? getParamValue($route[$j+1][6], $param) : false;
					
					if (($prev > $curr) && ($curr < $next))
					{
						if (isset($route[$j]))
						{
							if (isset($route[$j][6][$param]))
							{
								$route[$j][6][$param] = $next;
							}
						}
					}
				}
			}	
		}
		

		return $route;
	}
	
	function getRouteEvents($user_id, $imei, $dtf, $dtt)
	{
		global $ms;
		
		$events = array();
			
		$q = "SELECT * FROM `gs_user_events_data` WHERE `user_id`='".$user_id."' AND `imei`='".$imei."' AND dt_tracker BETWEEN '".$dtf."' AND '".$dtt."' ORDER BY dt_tracker ASC";
		$r = mysqli_query($ms, $q);
		
		while($event_data=mysqli_fetch_array($r))
		{
			$event_data['speed'] = convSpeedUnits($event_data['speed'], 'km', $_SESSION["unit_distance"]);
			$event_data['altitude'] = convAltitudeUnits($event_data['altitude'], 'km', $_SESSION["unit_distance"]);
			
			$event_data['params'] = json_decode($event_data['params'],true);
			
			$events[] = array(	$event_data['event_desc'],
						convUserTimezone($event_data['dt_tracker']),
						$event_data['lat'],
						$event_data['lng'],
						$event_data['altitude'],
						$event_data['angle'],
						$event_data['speed'],
						$event_data['params']
						);
		}
		
		return $events;
	}
	
	function getRoute($user_id, $imei, $dtf, $dtt, $min_stop_duration, $filter)
	{		
		$accuracy = getObjectAccuracy($imei);
		
		$result = array();
		$result['route'] = array();
		$result['stops'] = array();
		$result['drives'] = array();
		$result['events'] = array();
		
		if (checkObjectActive($imei) != true)
		{
			return $result;
		}
		
		$route = getRouteRaw($imei, $accuracy, $dtf, $dtt);
		
		if (count($route) > 0)
		{
			// get object fuel rates
			$fcr = getObjectFCR($imei);
			
			// get ACC sensor
			$sensor = getSensorFromType($imei, 'acc');
			$acc = $sensor[0]['param'];
			
			// filter jumping cordinates
			if ($filter == true)
			{
				$route = removeRouteJunkPoints($route, $accuracy);
			}
			$result['route'] = $route;
			
			// get fuel sensors
			$fuel_sensors = getSensorFromType($imei, 'fuel');
			$fuelcons_sensors = getSensorFromType($imei, 'fuelcons');
			
			// create stops
			if ($accuracy['stops'] == 'gpsacc')
			{
				$result['stops'] = getRouteStopsGPSACC($route, $accuracy, $fcr, $fuel_sensors, $fuelcons_sensors, $min_stop_duration, $acc);	
			}
			else if ($accuracy['stops'] == 'acc')
			{
				$result['stops'] = getRouteStopsACC($route, $accuracy, $fcr, $fuel_sensors, $fuelcons_sensors, $min_stop_duration, $acc);
			}
			else
			{
				$result['stops'] = getRouteStopsGPS($route, $accuracy, $fcr, $fuel_sensors, $fuelcons_sensors, $min_stop_duration, $acc);
			}
			
			// create drives
			$result['drives'] = getRouteDrives($route, $accuracy, $result['stops'], $fcr, $fuel_sensors, $fuelcons_sensors, $acc);
			
			// load events
			$result['events'] = getRouteEvents($user_id, $imei, $dtf, $dtt);
			
			// count route length
			$result['route_length'] = 0;
			for ($i=0; $i<count($result['drives']); ++$i)
			{
				$result['route_length'] += $result['drives'][$i][7];
			}
			
			// count top speed				
			$result['top_speed'] = 0;
			for ($i=0; $i<count($result['drives']); ++$i)
			{
				if ($result['top_speed'] < $result['drives'][$i][8])
				{
					$result['top_speed'] = $result['drives'][$i][8];
				}
			}
			
			//// count avg speed
			//$result['avg_speed'] = 0;
			//for ($i=0; $i<count($result['drives']); ++$i)
			//{
			//	$result['avg_speed'] += $result['drives'][$i][9];
			//}
			//
			//if (count($result['drives']) > 0)
			//{
			//	$result['avg_speed'] = floor($result['avg_speed'] / count($result['drives']));
			//}
				
			// count fuel consumption
			$result['fuel_consumption'] = 0;
			for ($i=0; $i<count($result['stops']); ++$i)
			{
				$result['fuel_consumption'] += $result['stops'][$i][9];
			}
			for ($i=0; $i<count($result['drives']); ++$i)
			{
				$result['fuel_consumption'] += $result['drives'][$i][10];
			}
			
			// count fuel cost
			$result['fuel_cost'] = 0;
			for ($i=0; $i<count($result['stops']); ++$i)
			{
				$result['fuel_cost'] += $result['stops'][$i][10];
			}
			for ($i=0; $i<count($result['drives']); ++$i)
			{
				$result['fuel_cost'] += $result['drives'][$i][11];
			}
			
			// count stops duration
			$result['stops_duration_time'] = 0;
			for ($i=0; $i<count($result['stops']); ++$i)
			{
				$diff = strtotime($result['stops'][$i][7])-strtotime($result['stops'][$i][6]);
				$result['stops_duration_time'] += $diff;
			}
			$result['stops_duration'] = getTimeDetails($result['stops_duration_time'], true);
			
			// count drives duration and engine work
			$result['drives_duration_time'] = 0;
			for ($i=0; $i<count($result['drives']); ++$i)
			{
				$diff = strtotime($result['drives'][$i][5])-strtotime($result['drives'][$i][4]);
				$result['drives_duration_time'] += $diff;
			}
			$result['drives_duration'] = getTimeDetails($result['drives_duration_time'], true);
			
			
			// count avg speed			
			$result['avg_speed'] = getRouteAvgSpeed($result['route_length'], $result['drives_duration_time']);
			
			// prepare full engine work and idle info
			$result['engine_work_time'] = 0;
			$result['engine_idle_time'] = 0;
			
			for ($i=0; $i<count($result['drives']); ++$i)
			{
				$result['engine_work_time'] += $result['drives'][$i][12];
				$result['drives'][$i][12] = getTimeDetails($result['drives'][$i][12], true);
			}
			
			for ($i=0; $i<count($result['stops']); ++$i)
			{
				$result['engine_idle_time'] += $result['stops'][$i][11];
				$result['stops'][$i][11] = getTimeDetails($result['stops'][$i][11], true);	
			}
			
			// set total engine work and idle
			$result['engine_work_time'] += $result['engine_idle_time'];
			$result['engine_work'] = getTimeDetails($result['engine_work_time'], true);
			$result['engine_idle'] = getTimeDetails($result['engine_idle_time'], true);
			
			// fuel consumption per 100 km and mpg
			if (($result['fuel_consumption'] > 0) && ($result['route_length'] > 0))
			{
				$fuel_consumption_per_100km = ($result['fuel_consumption'] / $result['route_length']) * 100;				
				$fuel_consumption_per_100km = round($fuel_consumption_per_100km * 100) / 100;
				
				$fuel_consumption_mpg = ($result['route_length'] / $result['fuel_consumption']);
				$fuel_consumption_mpg = round($fuel_consumption_mpg * 100) / 100;
			}
			else
			{
				$fuel_consumption_per_100km = 0;
				$fuel_consumption_mpg = 0;
			}
			
			// 100km
			// check for false consumption
			for ($i=0; $i<count($result['drives']); ++$i)
			{
				if (($result['drives'][$i][13] > ($fuel_consumption_per_100km * 1.5)) || ($result['drives'][$i][13] < ($fuel_consumption_per_100km * 0.5)))
				{
					$result['drives'][$i][13] = 0;
				}
			}
			
			// mpg
			// check for false consumption
			for ($i=0; $i<count($result['drives']); ++$i)
			{				
				if (($result['drives'][$i][14] > ($fuel_consumption_mpg * 1.5)) || ($result['drives'][$i][14] < ($fuel_consumption_mpg * 0.5)))
				{
					$result['drives'][$i][14] = 0;
				}
			}
			
			$result['fuel_consumption_per_100km'] = $fuel_consumption_per_100km;
			$result['fuel_consumption_mpg'] = $fuel_consumption_mpg;
		}
		
		return $result;
	}
	
	function getRouteOverspeeds($route, $speed_limit)
	{
		$overspeeds = array();
		$overspeed = 0;
		$top_speed = 0;
		$avg_speed = 0;
		$avg_speed_c = 0;
		
		for ($i=0; $i<count($route); ++$i)
		{
			$speed = $route[$i][5];
			
			if ($speed > $speed_limit)
			{	
				if($overspeed == 0)
				{
					$overspeed_start = $route[$i][0];
					$overspeed = 1;
				}
				
				if ($speed >= $top_speed)
				{
					$top_speed = $speed;
					$overspeed_lat = $route[$i][1];
					$overspeed_lng = $route[$i][2];
				}
				
				$avg_speed += $speed;
				$avg_speed_c++;
			}
			else
			{
				if ($overspeed == 1)
				{
					$overspeed_end = $route[$i][0];
					$overspeed_duration = getTimeDifferenceDetails($overspeed_start, $overspeed_end);
					
					$overspeeds[] = array(	$overspeed_start,
								$overspeed_end,
								$overspeed_duration,
								$top_speed,
								floor($avg_speed / $avg_speed_c),
								$overspeed_lat,
								$overspeed_lng
								);
					
					$top_speed = 0;
					$avg_speed = 0;
					$avg_speed_c = 0;
					$overspeed = 0;
				}
			}
		}
		
		return $overspeeds;
	}
	
	function getRouteUnderspeeds($route, $speed_limit)
	{
		$underpeeds = array();
		$underpeed = 0;
		$top_speed = 0;
		$avg_speed = 0;
		$avg_speed_c = 0;
		
		for ($i=0; $i<count($route); ++$i)
		{
			$speed = $route[$i][5];
			
			if ($speed < $speed_limit)
			{	
				if($underpeed == 0)
				{
					$underpeed_start = $route[$i][0];
					$underpeed = 1;
				}
				
				if ($speed >= $top_speed)
				{
					$top_speed = $speed;
					$underpeed_lat = $route[$i][1];
					$underpeed_lng = $route[$i][2];
				}
				
				$avg_speed += $speed;
				$avg_speed_c++;
			}
			else
			{
				if ($underpeed == 1)
				{
					$underpeed_end = $route[$i][0];
					$underpeed_duration = getTimeDifferenceDetails($underpeed_start, $underpeed_end);
					
					$underpeeds[] = array(	$underpeed_start,
								$underpeed_end,
								$underpeed_duration,
								$top_speed,
								floor($avg_speed / $avg_speed_c),
								$underpeed_lat,
								$underpeed_lng
								);
									
					$top_speed = 0;
					$avg_speed = 0;
					$avg_speed_c = 0;
					$underpeed = 0;
				}
			}
		}
		
		return $underpeeds;
	}
	
	function getRouteStopsGPSACC($route, $accuracy, $fcr, $fuel_sensors, $fuelcons_sensors, $min_stop_duration, $acc)
	{
		$stops = array();
		$stoped = 0;
		
		$min_moving_speed = $accuracy['min_moving_speed'];
		
		for ($i=0; $i<count($route); ++$i)
		{
			$params = $route[$i][6];
			
			if (!isset($params[$acc]))
			{
				$params[$acc] = '';
			}
			
			$stop_speed = $route[$i][5];
			
			if ((($stop_speed <= $min_moving_speed) && ($i < count($route)-1)) || (($params[$acc] == '0') && ($i < count($route)-1)))
			{	
				if($stoped == 0)
				{
					$id_start = $i;
					
					$stop_start = $route[$i][0];
					$stop_lat = $route[$i][1];
					$stop_lng = $route[$i][2];
					$stop_altitude = $route[$i][3];
					$stop_angle = $route[$i][4];
					$stop_params = $route[$i][6];
					
					$stoped = 1;
				}
			}
			else
			{
				if ($stoped == 1)
				{
					$id_end = $i;
					
					$stop_end = $route[$i][0];
					$stop_duration = getTimeDifferenceDetails($stop_start, $stop_end);
					$stop_fuel_consumption = getRouteFuelConsumption($route, $id_start, $id_end, $fcr, $fuel_sensors, $fuelcons_sensors);
					$stop_fuel_cost = getRouteFuelCost($stop_fuel_consumption, $fcr);
					$stop_engine_hours = getRouteEngineHours($route, $id_start, $id_end, $acc);
					
					$time_diff = strtotime($stop_end)-strtotime($stop_start);
					
					if ($time_diff > ($min_stop_duration * 60))
					{
						$stops[] = array(	$id_start,
									$id_end,
									$stop_lat,
									$stop_lng,
									$stop_altitude,
									$stop_angle,
									$stop_start,
									$stop_end,
									$stop_duration,
									$stop_fuel_consumption,
									$stop_fuel_cost,
									$stop_engine_hours,
									$stop_params,
									);
					}
					$stoped = 0;
				}
			}
		}
		return $stops;
	}
	
	function getRouteStopsACC($route, $accuracy, $fcr, $fuel_sensors, $fuelcons_sensors, $min_stop_duration, $acc)
	{
		$stops = array();
		$stoped = 0;
		
		for ($i=0; $i<count($route); ++$i)
		{
			$params = $route[$i][6];
			
			if (!isset($params[$acc]))
			{
				$params[$acc] = '';
			}
			
			if (($params[$acc] == '0') && ($i < count($route)-1))
			{
				
				
				if($stoped == 0)
				{
					$id_start = $i;
					
					$stop_start = $route[$i][0];
					$stop_lat = $route[$i][1];
					$stop_lng = $route[$i][2];
					$stop_altitude = $route[$i][3];
					$stop_angle = $route[$i][4];
					$stop_params = $route[$i][6];
					
					$stoped = 1;
				}
			}
			else
			{
				if ($stoped == 1)
				{
					$id_end = $i;
					
					$stop_end = $route[$i][0];
					$stop_duration = getTimeDifferenceDetails($stop_start, $stop_end);
					$stop_fuel_consumption = getRouteFuelConsumption($route, $id_start, $id_end, $fcr, $fuel_sensors, $fuelcons_sensors);
					$stop_fuel_cost = getRouteFuelCost($stop_fuel_consumption, $fcr);
					//$stop_engine_hours = getRouteEngineHours($route, $id_start, $id_end, $acc);
					$stop_engine_hours = 0; // because Stop is detected by ACC
					
					$time_diff = strtotime($stop_end)-strtotime($stop_start);
					
					if ($time_diff > ($min_stop_duration * 60))
					{
						$stops[] = array(	$id_start,
									$id_end,
									$stop_lat,
									$stop_lng,
									$stop_altitude,
									$stop_angle,
									$stop_start,
									$stop_end,
									$stop_duration,
									$stop_fuel_consumption,
									$stop_fuel_cost,
									$stop_engine_hours,
									$stop_params
									);
					}
					$stoped = 0;
				}
			}
		}
		return $stops;
	}

	function getRouteStopsGPS($route, $accuracy, $fcr, $fuel_sensors, $fuelcons_sensors, $min_stop_duration, $acc)
	{
		$stops = array();
		$stoped = 0;
		
		$min_moving_speed = $accuracy['min_moving_speed'];
		
		for ($i=0; $i<count($route); ++$i)
		{
			$stop_speed = $route[$i][5];
			
			if (($stop_speed <= $min_moving_speed) && ($i < count($route)-1))
			{	
				if($stoped == 0)
				{
					$id_start = $i;
					
					$stop_start = $route[$i][0];
					$stop_lat = $route[$i][1];
					$stop_lng = $route[$i][2];
					$stop_altitude = $route[$i][3];
					$stop_angle = $route[$i][4];
					$params = $route[$i][6];
					
					$stoped = 1;
				}
			}
			else
			{
				if ($stoped == 1)
				{
					$id_end = $i;
					
					$stop_end = $route[$i][0];
					$stop_duration = getTimeDifferenceDetails($stop_start, $stop_end);
					$stop_fuel_consumption = getRouteFuelConsumption($route, $id_start, $id_end, $fcr, $fuel_sensors, $fuelcons_sensors);
					$stop_fuel_cost = getRouteFuelCost($stop_fuel_consumption, $fcr);
					$stop_engine_hours = getRouteEngineHours($route, $id_start, $id_end, $acc);
					
					$time_diff = strtotime($stop_end)-strtotime($stop_start);
					
					if ($time_diff > ($min_stop_duration * 60))
					{
						$stops[] = array(	$id_start,
									$id_end,
									$stop_lat,
									$stop_lng,
									$stop_altitude,
									$stop_angle,
									$stop_start,
									$stop_end,
									$stop_duration,
									$stop_fuel_consumption,
									$stop_fuel_cost,
									$stop_engine_hours,
									$params);
					}
					$stoped = 0;
				}
			}
		}
		return $stops;
	}
	
	function getRouteDrives($route, $accuracy, $stops, $fcr, $fuel_sensors, $fuelcons_sensors, $acc)
	{
		$drives = array();
		
		if (count($stops) == 0)
		{
			// moving between start and end marker if no stops
			$id_start_s = 0;
			$id_start = 0;
			$id_end = count($route)-1;
			
			$dt_start_s = $route[$id_start_s][0];
			$dt_start = $route[$id_start][0];
			$dt_end = $route[$id_end][0];
			
			if ($dt_start != $dt_end)
			{
				$moving_duration = getTimeDifferenceDetails($dt_start, $dt_end);
				$route_length = getRouteLength($route, $id_start_s, $id_end);
				$top_speed = getRouteTopSpeed($route, $id_start_s, $id_end);
				$avg_speed = getRouteAvgSpeed($route_length, strtotime($dt_end)-strtotime($dt_start));
				
				if ($avg_speed > $top_speed)
				{
					$avg_speed = $top_speed;
				}
				
				$fuel_consumption = getRouteFuelConsumption($route, $id_start, $id_end, $fcr, $fuel_sensors, $fuelcons_sensors);
				$fuel_cost = getRouteFuelCost($fuel_consumption, $fcr);
				$engine_work = getRouteEngineHours($route, $id_start, $id_end, $acc);
				
				if (($fuel_consumption > 0) && ($route_length > 0))
				{
					$fuel_consumption_per_100km = ($fuel_consumption / $route_length) * 100;
					$fuel_consumption_mpg = ($route_length / $fuel_consumption);
					$fuel_consumption_per_100km = round($fuel_consumption_per_100km * 100) / 100;
					$fuel_consumption_mpg = round($fuel_consumption_mpg * 100) / 100;
				}
				else
				{
					$fuel_consumption_per_100km = 0;
					$fuel_consumption_mpg = 0;
				}
				
				$drives_start_end = array(	$id_start_s,
								$id_start,
								$id_end,
								$dt_start_s,
								$dt_start,
								$dt_end,
								$moving_duration,
								$route_length,
								$top_speed,
								$avg_speed,
								$fuel_consumption,
								$fuel_cost,
								$engine_work,
								$fuel_consumption_per_100km,
								$fuel_consumption_mpg);
			}
		}
		else
		{
			// moving between start and first stop
			$id_start_s = 0;
			$id_start = 0;
			$id_end = $stops[0][0];
			
			if ($id_end != 0)
			{
				$dt_start_s = $route[$id_start_s][0];
				$dt_start = $route[$id_start][0];
				$dt_end = $route[$id_end][0];
				
				if ($dt_start != $dt_end)
				{
					$moving_duration = getTimeDifferenceDetails($dt_start, $dt_end);
					$route_length = getRouteLength($route, $id_start_s, $id_end);
					$top_speed = getRouteTopSpeed($route, $id_start_s, $id_end);
					$avg_speed = getRouteAvgSpeed($route_length, strtotime($dt_end)-strtotime($dt_start));
					
					if ($avg_speed > $top_speed)
					{
						$avg_speed = $top_speed;
					}
					
					$fuel_consumption = getRouteFuelConsumption($route, $id_start, $id_end, $fcr, $fuel_sensors, $fuelcons_sensors);
					$fuel_cost = getRouteFuelCost($fuel_consumption, $fcr);
					$engine_work = getRouteEngineHours($route, $id_start, $id_end, $acc);
					
					if (($fuel_consumption > 0) && ($route_length > 0))
					{
						$fuel_consumption_per_100km = ($fuel_consumption / $route_length) * 100;
						$fuel_consumption_mpg = ($route_length / $fuel_consumption);
						$fuel_consumption_per_100km = round($fuel_consumption_per_100km * 100) / 100;
						$fuel_consumption_mpg = round($fuel_consumption_mpg * 100) / 100;
					}
					else
					{
						$fuel_consumption_per_100km = 0;
						$fuel_consumption_mpg = 0;
					}
					
					$drives_start = array(	$id_start_s,
								$id_start,
								$id_end,
								$dt_start_s,
								$dt_start,
								$dt_end,
								$moving_duration,
								$route_length,
								$top_speed,
								$avg_speed,
								$fuel_consumption,
								$fuel_cost,
								$engine_work,
								$fuel_consumption_per_100km,
								$fuel_consumption_mpg);
				}
			}
			
			// moving between end and last stop								
			$id_start_s = $stops[count($stops)-1][0];
			$id_start = $stops[count($stops)-1][1];
			$id_end = count($route)-1;
			
			if ($id_start != $id_end)
			{
				$dt_start_s = $route[$id_start_s][0];
				$dt_start = $route[$id_start][0];
				$dt_end = $route[$id_end][0];
				
				if ($dt_start != $dt_end)
				{
					$moving_duration = getTimeDifferenceDetails($dt_start, $dt_end);
					$route_length = getRouteLength($route, $id_start_s, $id_end);
					$top_speed = getRouteTopSpeed($route, $id_start_s, $id_end);
					$avg_speed = getRouteAvgSpeed($route_length, strtotime($dt_end)-strtotime($dt_start));
					
					if ($avg_speed > $top_speed)
					{
						$avg_speed = $top_speed;
					}
				
					$fuel_consumption = getRouteFuelConsumption($route, $id_start, $id_end, $fcr, $fuel_sensors, $fuelcons_sensors);
					$fuel_cost = getRouteFuelCost($fuel_consumption, $fcr);
					$engine_work = getRouteEngineHours($route, $id_start, $id_end, $acc);
					
					if (($fuel_consumption > 0) && ($route_length > 0))
					{
						$fuel_consumption_per_100km = ($fuel_consumption / $route_length) * 100;
						$fuel_consumption_mpg = ($route_length / $fuel_consumption);
						$fuel_consumption_per_100km = round($fuel_consumption_per_100km * 100) / 100;
						$fuel_consumption_mpg = round($fuel_consumption_mpg * 100) / 100;
					}
					else
					{
						$fuel_consumption_per_100km = 0;
						$fuel_consumption_mpg = 0;
					}
					
					$drives_end = array(	$id_start_s,
								$id_start,
								$id_end,
								$dt_start_s,
								$dt_start,
								$dt_end,
								$moving_duration,
								$route_length,
								$top_speed,
								$avg_speed,
								$fuel_consumption,
								$fuel_cost,
								$engine_work,
								$fuel_consumption_per_100km,
								$fuel_consumption_mpg);
				}
			}	
		}
		
		// moving between stops
		for ($i=0; $i<count($stops)-1; ++$i)
		{
			$id_start_s = $stops[$i][0];
			$id_start = $stops[$i][1];
			$id_end = $stops[$i+1][0];
			
			$dt_start_s = $route[$id_start_s][0];
			$dt_start = $route[$id_start][0];
			$dt_end = $route[$id_end][0];
			
			if ($dt_start != $dt_end)
			{
				$moving_duration = getTimeDifferenceDetails($dt_start, $dt_end);
				$route_length = getRouteLength($route, $id_start_s, $id_end);
				$top_speed = getRouteTopSpeed($route, $id_start_s, $id_end);
				$avg_speed = getRouteAvgSpeed($route_length, strtotime($dt_end)-strtotime($dt_start));
				
				if ($avg_speed > $top_speed)
				{
					$avg_speed = $top_speed;
				}
				
				$fuel_consumption = getRouteFuelConsumption($route, $id_start, $id_end, $fcr, $fuel_sensors, $fuelcons_sensors);
				$fuel_cost = getRouteFuelCost($fuel_consumption, $fcr);
				$engine_work = getRouteEngineHours($route, $id_start, $id_end, $acc);
				
				if (($fuel_consumption > 0) && ($route_length > 0))
				{
					$fuel_consumption_per_100km = ($fuel_consumption / $route_length) * 100;
					$fuel_consumption_mpg = ($route_length / $fuel_consumption);
					$fuel_consumption_per_100km = round($fuel_consumption_per_100km * 100) / 100;
					$fuel_consumption_mpg = round($fuel_consumption_mpg * 100) / 100;
				}
				else
				{
					$fuel_consumption_per_100km = 0;
					$fuel_consumption_mpg = 0;
				}
				
				$drives_stops[] = array(	$id_start_s,
								$id_start,
								$id_end,
								$dt_start_s,
								$dt_start,
								$dt_end,
								$moving_duration,
								$route_length,
								$top_speed,
								$avg_speed,
								$fuel_consumption,
								$fuel_cost,
								$engine_work,
								$fuel_consumption_per_100km,
								$fuel_consumption_mpg);
			}
		}
		
		if(isset($drives_start_end))
		{
			$drives[] = $drives_start_end;
		}
		else
		{
			if(isset($drives_start))
			{
				$drives[] = $drives_start;
			}
			
			if(isset($drives_stops))
			{
				$drives = array_merge($drives, $drives_stops);
			}
			
			if(isset($drives_end))
			{
				$drives[] = $drives_end;
			}
		}
		
		return $drives;
	}
	
	function getRouteFuelCost($fuel_consumption, $fcr)
	{
		$fuel_cost = 0;
		
		if ($fcr == '')
		{
			return $fuel_cost;
		}
		
		$fuel_cost = $fuel_consumption * $fcr['cost'];
		
		return round($fuel_cost * 100) / 100;
	}
	
	function getRouteFuelConsumption($route, $id_start, $id_end, $fcr, $fuel_sensors, $fuelcons_sensors)
	{
		$fuel_consumtion = 0;
		
		if ($fcr == '')
		{
			return $fuel_consumtion;
		}
		
		$source = $fcr['source'];
		$measurement = $fcr['measurement'];
		$cost = $fcr['cost'];
		$summer = $fcr['summer'];
		$winter = $fcr['winter'];
		$winter_start = $fcr['winter_start'];
		$winter_end= $fcr['winter_end'];
				
		if ($source == 'rates') 
		{
			if (($summer > 0) && ($winter > 0))
			{
				for ($i=$id_start; $i<$id_end; ++$i)
				{
					$lat1 = $route[$i][1];
					$lng1 = $route[$i][2];
					$lat2 = $route[$i+1][1];
					$lng2 = $route[$i+1][2];
					$length = getLengthBetweenCoordinates($lat1, $lng1, $lat2, $lng2);
					
					if ($measurement == 'mpg')
					{
						$length = convDistanceUnits($length, 'km', 'mi');
					}
					
					$f_date = strtotime($route[$i][0]);
					$f_date1 = strtotime(gmdate("Y").'-'.$winter_start);
					$f_date2 = strtotime(gmdate("Y").'-'.$winter_end);
					
					if ($f_date1 >= $f_date2)
					{
						$f_date2 = strtotime((gmdate("Y") + 1).'-'.$winter_end);
					}
					
					if (($f_date >= $f_date1) && ($f_date <= $f_date2 ))
					{
						$fuel_consumtion += $length / $winter;
					}
					else
					{
						$fuel_consumtion += $length / $summer;
					}	
				}			
			}	
		}
		else if (($source == 'fuel') && ($fuel_sensors != false))
		{
			$params1 = $route[$id_start][6];
			$params2 = $route[$id_end][6];
			
			// loop per fuel sensors
			for ($j=0; $j<count($fuel_sensors); ++$j)
			{
				$before = getSensorValue($params1, $fuel_sensors[$j]);
				$after = getSensorValue($params2, $fuel_sensors[$j]);
				
				if (($after['value'] > 0) && ($before['value'] > 0))
				{
					$diff = $after['value'] - $before['value'];
					
					if ($diff < 0)
					{
						$fuel_consumtion += $diff;
					}	
				}
			}
				
			$fuel_consumtion = abs($fuel_consumtion);
		}
		else if (($source == 'fuelcons') && ($fuelcons_sensors != false))
		{
			if ($fuelcons_sensors[0]['result_type'] == 'abs')
			{
				$fuel_start = getSensorValue($route[$id_start][6], $fuelcons_sensors[0]);
				$fuel_end = getSensorValue($route[$id_end][6], $fuelcons_sensors[0]);
				
				if (($fuel_end['value'] > 0) && ($fuel_start['value'] > 0))
				{
					$fuel_consumtion = $fuel_end['value'] - $fuel_start['value'];
					
					if ($fuel_consumtion < 0)
					{
						$fuel_consumtion = 0;
					}
				}
			}
			
			if ($fuelcons_sensors[0]['result_type'] == 'rel')
			{
				for ($i=$id_start; $i<$id_end; ++$i)
				{
					$params = $route[$i][6];
					
					$cons = getSensorValue($params, $fuelcons_sensors[0]);
					
					$fuel_consumtion += abs($cons['value']);
				}	
			}
		}
		
		return round($fuel_consumtion * 100) / 100;
	}
	
	function getRouteFuelFillings($route, $accuracy, $fuel_sensors)
	{
		$result = array();
		$result['fillings'] = array();
		
		if ($fuel_sensors == false)
		{
			return $result;
		}
		
		$min_fuel_speed = $accuracy['min_fuel_speed'];
		$diff_ff = $accuracy['min_ff'];
		
		$filling = 0;
		
		$total_filled = 0;
			
		for ($j=0; $j<count($fuel_sensors); ++$j)
		{
			for ($i=0; $i<count($route)-1; ++$i)
			{
				$lat = $route[$i][1];				
				$lng = $route[$i][2];
				
				$speed1 = $route[$i][5];
				$speed2 = $route[$i+1][5];
				
				$params1 = $route[$i][6];
				$params2 = $route[$i+1][6];
					
				$fuel_current = getSensorValue($params1, $fuel_sensors[$j]); // fuel level
				$fuel_next = getSensorValue($params2, $fuel_sensors[$j]); // fuel level in next point
				
				$diff = $fuel_next['value'] - $fuel_current['value']; // fuel filling
				
				if (($diff >= $diff_ff) && (($speed1 < $min_fuel_speed) || ($speed2 < $min_fuel_speed)) && ($i < count($route)-2))
				{
					if ($filling == 0)
					{
						$filling_dt = $route[$i][0];
						$filling_lat = $lat;
						$filling_lng = $lng;
						
						$filling_before = $fuel_current['value'];
						
						$filling = 1;
					}
				}
				else
				{
					if ($filling == 1)
					{
						$filling_after = $fuel_current['value'];
						
						$filling_filled = $filling_after - $filling_before;
						
						$total_filled += $filling_filled;
																
						$result['fillings'][] = array(	$filling_dt,
										$filling_lat,
										$filling_lng,
										$filling_before,
										$filling_after,
										$filling_filled,
										$fuel_sensors[$j]['name'],
										$fuel_sensors[$j]['units'],
										$params1);
					
						$filling = 0;
					}
				}
			}
		}
		
		usort($result['fillings'], function($a, $b) {
			return strtotime($a[0]) - strtotime($b[0]);
		});
		
		$result['total_filled'] = $total_filled.' '.$fuel_sensors[0]['units'];
		
		return $result;
	}
	
	function getRouteFuelThefts($route, $accuracy, $fuel_sensors)
	{
		$result = array();
		$result['thefts'] = array();
		
		if ($fuel_sensors == false)
		{
			return $result;
		}
		
		$min_fuel_speed = $accuracy['min_fuel_speed'];
		$diff_ft = $accuracy['min_ft'];
		
		$theft = 0;
		
		$total_stolen = 0;
		
		for ($j=0; $j<count($fuel_sensors); ++$j)
		{
			for ($i=0; $i<count($route)-1; ++$i)
			{
				$lat = $route[$i][1];
				$lng = $route[$i][2];
						
				$speed1 = $route[$i][5];
				$speed2 = $route[$i+1][5];
				
				$params1 = $route[$i][6];
				$params2 = $route[$i+1][6];
					
				$fuel_current = getSensorValue($params1, $fuel_sensors[$j]); // fuel level
				$fuel_next = getSensorValue($params2, $fuel_sensors[$j]); // fuel level in next point
				
				$diff = $fuel_current['value'] - $fuel_next['value']; // fuel theft
				
				if (($diff >= $diff_ft) && (($speed1 < $min_fuel_speed) || ($speed2 < $min_fuel_speed)) && ($i < count($route)-2))
				{
					if ($theft == 0)
					{
						$theft_dt = $route[$i][0];
						$theft_lat = $lat;
						$theft_lng = $lng;
						
						$theft_before = $fuel_current['value'];
						
						$theft = 1;
					}
				}
				else
				{
					if ($theft == 1)
					{
						$theft_after = $fuel_current['value'];
						
						$theft_theft = $theft_before - $theft_after;
						
						$total_stolen += $theft_theft;
																
						$result['thefts'][] = array(	$theft_dt,
										$theft_lat,
										$theft_lng,
										$theft_before,
										$theft_after,
										$theft_theft,
										$fuel_sensors[$j]['name'],
										$fuel_sensors[$j]['units'],
										$params1);
						
						$theft = 0;
					}
				}
			}
		}
		
		usort($result['thefts'], function($a, $b) {
			return strtotime($a[0]) - strtotime($b[0]);
		});
		
		$result['total_stolen'] = $total_stolen.' '.$fuel_sensors[0]['units'];
		
		return $result;
	}
	
	function getRouteLogicSensorInfo($route, $accuracy, $sensors)
	{
		$result = array();
		
		if ($sensors == false)
		{
			return $result;
		}
		
		for ($i=0; $i<count($sensors); ++$i)
		{
			$status = false;
			$activation_time = '';
			$deactivation_time = '';
			$activation_lat = '';
			$activation_lng = '';
			$deactivation_lat = '';
			$deactivation_lng = '';
			
			$sensor = $sensors[$i];
			$sensor_name = $sensor['name'];
			$sensor_param = $sensor['param'];			
			
			for ($j=0; $j<count($route); ++$j)
			{				
				$dt_tracker = $route[$j][0];
				$lat = $route[$j][1];
				$lng = $route[$j][2];
				$params = $route[$j][6];
				
				$param_value = getParamValue($params, $sensor_param);
				
				if ($status == false)
				{
					if ($param_value == 1)
					{
						$activation_time = $dt_tracker;
						$activation_lat = $lat;
						$activation_lng = $lng;
						$status = true;
					}
				}
				else
				{
					if ($param_value == 0)
					{
						$deactivation_time = $dt_tracker;
						$deactivation_lat = $lat;
						$deactivation_lng = $lng;
						
						$duration = getTimeDifferenceDetails($activation_time, $deactivation_time);
						
						$result[] = array($sensor_name,
							       $activation_time,
							       $deactivation_time,
							       $duration,
							       $activation_lat,
							       $activation_lng,
							       $deactivation_lat,
							       $deactivation_lng);
						
						$status = false;
						$activation_time = '';
						$deactivation_time = '';
						$activation_lat = '';
						$activation_lng = '';
						$deactivation_lat = '';
						$deactivation_lng = '';
					}
				}
			}
		}
		
		return $result;
	}
	
	function getRouteLength($route, $id_start, $id_end)
	{
		// check if not last point
		if (count($route) == $id_end)
		{
			$id_end -= 1;
		}
		
		$length = 0;
		
		for ($i=$id_start; $i<$id_end; ++$i)
		{
			$lat1 = $route[$i][1];
			$lng1 = $route[$i][2];
			$lat2 = $route[$i+1][1];
			$lng2 = $route[$i+1][2];
			$length += getLengthBetweenCoordinates($lat1, $lng1, $lat2, $lng2);
		}
		
		$length = convDistanceUnits($length, 'km', $_SESSION["unit_distance"]);
		
		return round($length * 100) / 100;
	}
	
	function getRouteTopSpeed($route, $id_start, $id_end)
	{
		$top_speed = 0;
		for ($i=$id_start; $i<$id_end; ++$i)
		{
			if ($top_speed < $route[$i][5])
			{
				$top_speed = $route[$i][5];
			}
		}
		
		return $top_speed;
	}
	
	function getRouteAvgSpeed($length, $duration)
	{
		if (($length > 0) && ($duration > 0))
		{
			$length = convDistanceUnits($length, $_SESSION["unit_distance"], 'km');
					
			$avg_speed = $length / ($duration / 60 / 60);
	
			$avg_speed = convSpeedUnits($avg_speed, 'km', $_SESSION["unit_distance"]);
			
			return floor($avg_speed);
		}
		else
		{
			return 0;
		}
	}
	
	function getRouteEngineHours($route, $id_start, $id_end, $acc)
	{		
		// check if not last point
		if (count($route) == $id_end)
		{
			$id_end -= 1;
		}
		
		$engine_hours = 0;
		
		for ($i=$id_start; $i<$id_end; ++$i)
		{
			$dt_tracker1 = $route[$i][0];
			$params1 = $route[$i][6];
			$dt_tracker2 = $route[$i+1][0];
			$params2 = $route[$i+1][6];
			
			if (isset($params1[$acc]) && isset($params2[$acc]))
			{
				if (($params1[$acc] == '1') && ($params2[$acc] == '1'))
				{
					$engine_hours += strtotime($dt_tracker2)-strtotime($dt_tracker1);
				}
			}
		}
		
		return $engine_hours;
	}
	
	function removeRouteJunkPoints($route, $accuracy)
	{
		$temp = array();
		
		if (count($route) < 1)
		{
			return $temp;
		}
		
		$min_moving_speed = $accuracy['min_moving_speed'];
		$min_diff_points = $accuracy['min_diff_points'];		
		
		// filter drifting
		for ($i=0; $i<count($route)-1; ++$i)
		{
			$dt_tracker = $route[$i][0];
			
			$lat1 = $route[$i][1];
			$lng1 = $route[$i][2];
			$lat2 = $route[$i+1][1];
			$lng2 = $route[$i+1][2];
			
			$speed = $route[$i][5];
			
			$lat_diff = abs($lat1 - $lat2);
			$lng_diff = abs($lng1 - $lng2);
			
			if (($i == 0) || ($speed > $min_moving_speed) || ($lat_diff > $min_diff_points) && ($lng_diff > $min_diff_points))
			{
				$lat_temp = $lat2;
				$lng_temp = $lng2;
				
				$temp[] = $route[$i];
			}
			else
			{
				if (isset($lat_temp))
				{
					$route[$i][1] = $lat_temp;
					$route[$i][2] = $lng_temp;
				}
				$temp[] = $route[$i];
			}
			
		}
		$temp[] = $route[count($route)-1]; // add last point
		
		return $temp;
	}
?>