<?
	//$_POST['net_protocol'] - tcp or udp
	//$_POST['protocol'] - device protocol, like coban, teltonika, xexun
	//$_POST['ip'] - IP address of GPS device
	//$_POST['port'] - PORT of GPS device
	//$_POST['imei'] - device 15 char ID
	//$_POST['dt_server'] - 0 UTC date and time in "YYYY-MM-DD HH-MM-SS" format
	//$_POST['dt_tracker'] - 0 UTC date and time in "YYYY-MM-DD HH-MM-SS" format
	//$_POST['lat'] - latitude with +/-
	//$_POST['lng'] - longitude with +/-
	//$_POST['altitude'] - in meters
	//$_POST['angle'] - in degree
	//$_POST['speed'] - in km/h
	//$_POST['loc_valid'] - 1 means valid location, 0 means not valid location
	//$_POST['params'] - stores array of params like acc, di, do, ai...
	//$_POST['event'] - possible events: sos, bracon, bracoff, dismount, mandown, shock, tow, pwrcut, gpscut, jamming, lowdc, lowbat, haccel, hbrake, hcorn
	
	include ('s_init.php');
	include ('s_events.php');
	include ('../func/fn_common.php');
	include ('../tools/gc_func.php');

	function insert_db_loc($loc)
	{
		global $ms;
		
		// format data
		$loc['imei'] = strtoupper(trim($loc['imei']));
		$loc['lat'] = (double)sprintf('%0.6f', $loc['lat']);
		$loc['lng'] = (double)sprintf('%0.6f', $loc['lng']);
		$loc['altitude'] = floor($loc['altitude']);
		$loc['angle'] = floor($loc['angle']);
		$loc['speed'] = floor($loc['speed']);
		$loc['protocol'] = strtolower($loc['protocol']);
		$loc['net_protocol'] = strtolower($loc['net_protocol']);
		
		// check for wrong IMEI
		if (!ctype_alnum($loc['imei']))
		{
			return false;
		}
		
		// check for wrong speed
		if ($loc['speed'] > 1000)
		{
			return false;
		}
		
		// check if object exists in system
		if (!checkObjectExistsSystem($loc['imei']))
		{
			insert_db_unused($loc);
			return false;
		}

		// apply GPS Roll Over fix		
		if (substr($loc['dt_tracker'], 0, 4) > (gmdate('Y') + 10))
		{
			if (substr($loc['dt_tracker'], 5, 5) == gmdate('m-d'))
			{
				$loc['dt_tracker'] = gmdate('Y').substr($loc['dt_tracker'], 4, 15);	
			}
			else
			{
				$loc['dt_tracker'] = gmdate("Y-m-d H:i:s");
			}
		}
		
		// check if dt_tracker is one day too far - skip coordinate		      
		if (strtotime($loc['dt_tracker']) >= strtotime(gmdate("Y-m-d H:i:s").' +1 days'))
		{
			return false;
		}
		
		// check if dt_tracker is at least one hour too far - set 0 UTC time
		if (strtotime($loc['dt_tracker']) >= strtotime(gmdate("Y-m-d H:i:s").' +1 hours'))
		{
			$loc['dt_tracker'] = gmdate("Y-m-d H:i:s");
		}
		
		// adjust GPS time
		$loc['dt_tracker'] = adjustObjectTime($loc['imei'], $loc['dt_tracker']);
		
		// get previous known location
		$loc_prev = get_gs_objects_data($loc['imei']);
		
		// merge params only if dt_tracker is newer
		if (strtotime($loc['dt_tracker']) >= strtotime($loc_prev['dt_tracker']))
		{
			$loc['params'] = mergeParams($loc_prev['params'], $loc['params']);
			
			// check if there is any sensor values to ignore due to ignition off
			$ignore_sensors = array();
			$acc_sensor = getSensorFromType($loc['imei'], 'acc');
			
			if ($acc_sensor)
			{
				$sensors = getObjectSensors($loc['imei']);
				
				foreach ($sensors as $key=>$value)
				{
					if ($value['acc_ignore'] == 'true')
					{
						$ignore_sensors[] = $value;
					}
				}
				
				foreach ($ignore_sensors as $key=>$value)
				{
					$sensor_data = getSensorValue($loc['params'], $acc_sensor[0]);					
						
					if ($sensor_data['value'] == 0)					
					{
						if (isset($loc_prev['params'][$value['param']]))
						{
							$loc['params'][$value['param']] = $loc_prev['params'][$value['param']];	
						}
						else
						{
							unset($loc['params'][$value['param']]);
						}						
					}					
				}		
			}
		}
		
		insert_db_objects($loc, $loc_prev);
		
		insert_db_status($loc, $loc_prev);
		
		insert_db_odo_engh($loc, $loc_prev);
		
		insert_db_ri($loc, $loc_prev);
		
		insert_db_dtc($loc);
		
		// check for duplicate locations
		if (loc_filter($loc, $loc_prev) == false)
		{
			insert_db_object_data($loc);
			
			if ($loc['loc_valid'] == 0)
			{
				if (($loc['lat'] == 0) || ($loc['lng'] == 0))
				{
					$loc['dt_tracker'] = $loc_prev['dt_tracker'];
					$loc['lat'] = $loc_prev['lat'];
					$loc['lng'] = $loc_prev['lng'];
					$loc['altitude'] = $loc_prev['altitude'];
					$loc['angle'] = $loc_prev['angle'];
					$loc['speed'] = $loc_prev['speed'];
				}
			}
			
			// check for local events if dt_tracker is newer, in other case only tracker events will be checked
			if (($loc['lat'] != 0) && ($loc['lng'] != 0))
			{
				// check for local events if dt_tracker is newer, in other case only tracker events will be checked
				if (strtotime($loc['dt_tracker']) >= strtotime($loc_prev['dt_tracker']))
				{
					check_events($loc, $loc_prev, true, true, false);
				}
				else
				{
					check_events($loc, false, false, false, false);
				}
			}
		}
	}
	
	function insert_db_noloc($loc)
	{
		global $ms;
		
		// format data
		$loc['imei'] = strtoupper(trim($loc['imei']));
		$loc['protocol'] = strtolower($loc['protocol']);
		$loc['net_protocol'] = strtolower($loc['net_protocol']);
		
		// check for wrong IMEI
		if (!ctype_alnum($loc['imei']))
		{
			return false;
		}
		
		// get previous known location
		$loc_prev = get_gs_objects_data($loc['imei']);
		
		if ($loc_prev != false)
		{
			// add previous known location
			$loc['dt_tracker'] = $loc_prev['dt_tracker'];
			$loc['lat'] = $loc_prev['lat'];
			$loc['lng'] = $loc_prev['lng'];
			$loc['altitude'] = $loc_prev['altitude'];
			$loc['angle'] = $loc_prev['angle'];
			
			// check speed for reset
			$loc['speed'] = $loc_prev['speed'];
			if ($loc['speed'] > 0)
			{
				$dt_difference = strtotime(gmdate("Y-m-d H:i:s")) - strtotime($loc['dt_tracker']);
				if($dt_difference >= 300)
				{
					$loc['speed'] = 0;
				}	
			}
			
			$loc['loc_valid'] = $loc_prev['loc_valid'];
			$loc['params'] = mergeParams($loc_prev['params'], $loc['params']);
			
			$q = "UPDATE gs_objects SET 	`protocol`='".$loc['protocol']."',
							`net_protocol`='".$loc['net_protocol']."',
							`ip`='".$loc['ip']."',
							`port`='".$loc['port']."',
							`dt_server`='".$loc['dt_server']."',
							`speed`='".$loc['speed']."',
							`params`='".json_encode($loc['params'])."'
							WHERE imei='".$loc['imei']."'";
							
			$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
			
			// check if location exists
			if (($loc['lat'] != 0) && ($loc['lng'] != 0))
			{
				insert_db_status($loc, $loc_prev);
				
				insert_db_ri($loc, $loc_prev);
				
				insert_db_dtc($loc);
				
				check_events($loc, $loc_prev, false, true, false);
			}
		}
	}
	
	function insert_db_imgloc($loc)
	{
		global $ms, $gsValues;
		
		// format data
		$loc['imei'] = strtoupper(trim($loc['imei']));
		$loc['lat'] = (double)sprintf('%0.6f', $loc['lat']);
		$loc['lng'] = (double)sprintf('%0.6f', $loc['lng']);
		$loc['altitude'] = floor($loc['altitude']);
		$loc['angle'] = floor($loc['angle']);
		$loc['speed'] = floor($loc['speed']);
		$loc['protocol'] = strtolower($loc['protocol']);
		$loc['net_protocol'] = strtolower($loc['net_protocol']);
		
		// check for wrong IMEI
		if (!ctype_alnum($loc['imei']))
		{
			return false;
		}
		
		// check if object exists in system
		if (!checkObjectExistsSystem($loc['imei']))
		{
			return false;
		}
		
		if (($loc['lat'] == 0) || ($loc['lng'] == 0))
		{
			// get previous known location
			$loc_prev = get_gs_objects_data($loc['imei']);
		
			//$loc['dt_tracker'] = $loc_prev['dt_tracker'];
			$loc['lat'] = $loc_prev['lat'];
			$loc['lng'] = $loc_prev['lng'];
			$loc['altitude'] = $loc_prev['altitude'];
			$loc['angle'] = $loc_prev['angle'];
			$loc['speed'] = $loc_prev['speed'];
		}
		
		$img_file = $loc['imei'].'_'.$loc['dt_server'].'.jpg';
		$img_file = str_replace('-', '', $img_file);
		$img_file = str_replace(':', '', $img_file);
		$img_file = str_replace(' ', '_', $img_file);
		
		// save to database
		$q = "INSERT INTO gs_object_img (img_file,
						imei,
						dt_server,
						dt_tracker,
						lat,
						lng,
						altitude,
						angle,
						speed,
						params
						) VALUES (
						'".$img_file."',
						'".$loc['imei']."',
						'".$loc['dt_server']."',
						'".$loc['dt_tracker']."',
						'".$loc['lat']."',
						'".$loc['lng']."',
						'".$loc['altitude']."',
						'".$loc['angle']."',
						'".$loc['speed']."',
						'".json_encode($loc['params'])."')";
				    
		$r = mysqli_query($ms, $q);
		
		 // save file
		$img_path = $gsValues['PATH_ROOT'].'/data/img/';
		$img_path = $img_path.basename($img_file);
		
		if (!isFilePathValid($img_path))
		{
			die;
		}
		
		$postdata = hex2bin($loc["img"]);
				
		if(substr($postdata,0,3) == "\xFF\xD8\xFF")
		{
			$fp = fopen($img_path,"w");
			fwrite($fp,$postdata);
			fclose($fp);
		}
	}
	
	function insert_db_unused($loc)
	{
		global $ms;
		
		$q = "INSERT INTO `gs_objects_unused` (imei, protocol, net_protocol, ip, port, dt_server, count)
						VALUES ('".$loc['imei']."', '".$loc['protocol']."', '".$loc['net_protocol']."', '".$loc['ip']."', '".$loc['port']."', '".$loc['dt_server']."', '1')
						ON DUPLICATE KEY UPDATE protocol = '".$loc['protocol']."', net_protocol = '".$loc['net_protocol']."', ip = '".$loc['ip']."', port = '".$loc['port']."', dt_server = '".$loc['dt_server']."', count = count + 1";
		$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
	}
	
	function insert_db_objects($loc, $loc_prev)
	{
		global $ms;
		
		if (strtotime($loc['dt_tracker']) >= strtotime($loc_prev['dt_tracker']))
		{
			if ($loc['loc_valid'] == 1)
			{
				// calculate angle
				if ($loc['angle'] == 0)
				{
					$loc['angle'] = getAngle($loc_prev['lat'], $loc_prev['lng'], $loc['lat'], $loc['lng']);	
				}
				
				$q = "UPDATE gs_objects SET	`protocol`='".$loc['protocol']."',
								`net_protocol`='".$loc['net_protocol']."',
								`ip`='".$loc['ip']."',
								`port`='".$loc['port']."',
								`dt_server`='".$loc['dt_server']."',
								`dt_tracker`='".$loc['dt_tracker']."',
								`lat`='".$loc['lat']."',
								`lng`='".$loc['lng']."',
								`altitude`='".$loc['altitude']."',
								`angle`='".$loc['angle']."',
								`speed`='".$loc['speed']."',
								`loc_valid`='1',
								`params`='".json_encode($loc['params'])."'
								WHERE imei='".$loc['imei']."'";
								
				$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
			}
			else
			{
				$loc['speed'] = 0;
				
				$q = "UPDATE gs_objects SET 	`protocol`='".$loc['protocol']."',
								`net_protocol`='".$loc['net_protocol']."',
								`ip`='".$loc['ip']."',
								`port`='".$loc['port']."',
								`dt_server`='".$loc['dt_server']."',
								`speed`='".$loc['speed']."',
								`loc_valid`='0',
								`params`='".json_encode($loc['params'])."'
								WHERE imei='".$loc['imei']."'";
								
				$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
			}
		}
		else
		{
			$q = "UPDATE gs_objects SET 	`protocol`='".$loc['protocol']."',
							`net_protocol`='".$loc['net_protocol']."',
							`ip`='".$loc['ip']."',
							`port`='".$loc['port']."',
							`dt_server`='".$loc['dt_server']."'
							WHERE imei='".$loc['imei']."'";
							
			$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
		}
	}
	
	function insert_db_object_data($loc)
	{
		global $ms;
		
		if (($loc['lat'] != 0) && ($loc['lng'] != 0))
		{
			$q = "INSERT INTO gs_object_data_".$loc['imei']."(	dt_server,
										dt_tracker,
										lat,
										lng,
										altitude,
										angle,
										speed,
										params
										) VALUES (
										'".$loc['dt_server']."',
										'".$loc['dt_tracker']."',
										'".$loc['lat']."',
										'".$loc['lng']."',
										'".$loc['altitude']."',
										'".$loc['angle']."',
										'".$loc['speed']."',
										'".json_encode($loc['params'])."')";
										
			$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
		}
	}
	
	function insert_db_status($loc, $loc_prev)
	{
		global $ms;
		
		if (strtotime($loc['dt_tracker']) >= strtotime($loc_prev['dt_tracker']))
		{
			$imei = $loc['imei'];
			$params = $loc['params'];
			
			$dt_last_stop = strtotime($loc_prev['dt_last_stop']);
			$dt_last_idle = strtotime($loc_prev['dt_last_idle']);
			$dt_last_move = strtotime($loc_prev['dt_last_move']);
			
			if ($loc['loc_valid'] == 1)
			{
				// status stop
				if ((($dt_last_stop <= 0) || ($dt_last_stop < $dt_last_move)) && ($loc['speed'] == 0))
				{
					$q = "UPDATE gs_objects SET `dt_last_stop`='".$loc['dt_server']."' WHERE imei='".$imei."'";			
					$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
					
					$dt_last_stop = strtotime($loc['dt_server']);
				}
				
				// status moving
				if (($dt_last_stop >= $dt_last_move) && ($loc['speed'] > 0))
				{
					$q = "UPDATE gs_objects SET `dt_last_move`='".$loc['dt_server']."' WHERE imei='".$imei."'";			
					$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
					
					$dt_last_move = strtotime($loc['dt_server']);
				}
			}
			else
			{
				// status stop
				if ((($dt_last_stop <= 0) || ($dt_last_stop < $dt_last_move)) && ($loc['speed'] == 0))
				{
					$q = "UPDATE gs_objects SET `dt_last_stop`='".$loc['dt_server']."' WHERE imei='".$imei."'";			
					$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
					
					$dt_last_stop = strtotime($loc['dt_server']);
				}
			}
			
			// status idle
			if ($dt_last_stop >= $dt_last_move)
			{
				$sensor = getSensorFromType($imei, 'acc');
				$acc = $sensor[0]['param'];
				
				if (isset($params[$acc]))
				{
					if (($params[$acc] == 1) && ($dt_last_idle <= 0))
					{
						$q = "UPDATE gs_objects SET `dt_last_idle`='".$loc['dt_server']."' WHERE imei='".$imei."'";
						$r = mysqli_query($ms, $q) or die(mysqli_error($ms));	
					}
					else if (($params[$acc] == 0) && ($dt_last_idle > 0))
					{
						$q = "UPDATE gs_objects SET `dt_last_idle`='0000-00-00 00:00:00' WHERE imei='".$imei."'";
						$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
					}
				}
			}
			else
			{
				if ($dt_last_idle > 0)
				{
					$q = "UPDATE gs_objects SET `dt_last_idle`='0000-00-00 00:00:00' WHERE imei='".$imei."'";
					$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
				}
			}
		}
	}
	
	function insert_db_odo_engh($loc, $loc_prev)
	{
		global $ms;
		
		$imei = $loc['imei'];
		$params = $loc['params'];
		$params_prev = $loc_prev['params'];
		
		// odo gps
		if ($loc_prev['odometer_type'] == 'gps')
		{
			if (strtotime($loc['dt_tracker']) >= strtotime($loc_prev['dt_tracker']))
			{
				if (($loc_prev['lat'] != 0) && ($loc_prev['lng'] != 0) && ($loc['speed'] > 3))
				{
					$odometer = getLengthBetweenCoordinates($loc_prev['lat'], $loc_prev['lng'], $loc['lat'], $loc['lng']);
					
					$q = 'UPDATE gs_objects SET `odometer` = odometer + '.$odometer.' WHERE imei="'.$imei.'"';
					$r = mysqli_query($ms, $q);
				}	
			}
		}
		
		// odo sen
		if ($loc_prev['odometer_type'] == 'sen')
		{
			$sensor = getSensorFromType($imei, 'odo');
			
			if ($sensor != false)
			{
				$sensor_ = $sensor[0];
				
				$odo = getSensorValue($params, $sensor_);
				
				$result_type = $sensor_['result_type'];
				
				if ($result_type == 'abs')
				{
					if (strtotime($loc['dt_tracker']) >= strtotime($loc_prev['dt_tracker']))
					{	
						$q = 'UPDATE gs_objects SET `odometer` = '.$odo['value'].' WHERE imei="'.$imei.'"';
						$r = mysqli_query($ms, $q);
					}
				}
				
				if ($result_type == 'rel')
				{
					$q = 'UPDATE gs_objects SET `odometer` = odometer + '.$odo['value'].' WHERE imei="'.$imei.'"';
					$r = mysqli_query($ms, $q);
				}
			}
		}
		
		// engh acc
		if ($loc_prev['engine_hours_type'] == 'acc')
		{
			if (strtotime($loc['dt_tracker']) >= strtotime($loc_prev['dt_tracker']))
			{
				if ((strtotime($loc['dt_tracker']) > 0) && (strtotime($loc_prev['dt_tracker']) > 0))
				{
					$engine_hours = 0;
					
					// get ACC sensor
					$sensor = getSensorFromType($imei, 'acc');
					$acc = $sensor[0]['param'];
					
					// calculate engine hours from ACC
					$dt_tracker = $loc['dt_tracker'];
					$dt_tracker_prev = $loc_prev['dt_tracker'];
					
					if (isset($params_prev[$acc]) && isset($params[$acc]))
					{
						if (($params_prev[$acc] == '1') && ($params[$acc] == '1'))
						{
							$engine_hours = strtotime($dt_tracker)-strtotime($dt_tracker_prev);
							
							$q = 'UPDATE gs_objects SET `engine_hours` = engine_hours + '.$engine_hours.' WHERE imei="'.$imei.'"';
							$r = mysqli_query($ms, $q);
						}
					}	
				}
			}
		}
		
		// eng sen
		if ($loc_prev['engine_hours_type'] == 'sen')
		{
			$sensor = getSensorFromType($imei, 'engh');
			
			if ($sensor != false)
			{
				$sensor_ = $sensor[0];
				
				$engh = getSensorValue($params, $sensor_);
								
				$result_type = $sensor_['result_type'];
				
				if ($result_type == 'abs')
				{
					if (strtotime($loc['dt_tracker']) >= strtotime($loc_prev['dt_tracker']))
					{	
						$q = 'UPDATE gs_objects SET `engine_hours` = '.$engh['value'].' WHERE imei="'.$imei.'"';
						$r = mysqli_query($ms, $q);
					}
				}
				
				if ($result_type == 'rel')
				{
					$q = 'UPDATE gs_objects SET `engine_hours` = engine_hours + '.$engh['value'].' WHERE imei="'.$imei.'"';
					$r = mysqli_query($ms, $q);
				}
			}
		}
	}
	
	function insert_db_ri($loc, $loc_prev)
	{
		global $ms;
		
		// logbook
		if (strtotime($loc['dt_tracker']) >= strtotime($loc_prev['dt_tracker']))
		{
			$imei = $loc['imei'];
			$params = $loc['params'];
			$params_prev = $loc_prev['params'];
			
			$group_array = array('da', 'pa', 'ta');
			
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
						insert_db_ri_data($loc['dt_server'], $loc['dt_tracker'], $imei, $group, $assign_id, $loc['lat'], $loc['lng']);
					}
				}
				
			}
		}
	}
	
	function insert_db_ri_data($dt_server, $dt_tracker, $imei, $group, $assign_id, $lat, $lng)
	{
		global $ms;
		
		$address = geocoderGetAddress($lat, $lng);
		
		$q = 'INSERT INTO gs_rilogbook_data  (	`dt_server`,
							`dt_tracker`,
							`imei`,
							`group`,
							`assign_id`,
							`lat`,
							`lng`,
							`address`
							) VALUES (
							"'.$dt_server.'",
							"'.$dt_tracker.'",
							"'.$imei.'",
							"'.$group.'",
							"'.$assign_id.'",
							"'.$lat.'",
							"'.$lng.'",
							"'.mysqli_real_escape_string($ms, $address).'")';
							
		$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
	}
	
	function insert_db_dtc($loc)
	{
		global $ms;
		
		if (isset($loc['event']))
		{	
			if (substr($loc['event'], 0, 3) == 'dtc')
			{
				$dtcs = str_replace("dtc:", "", $loc['event']);
				
				$dtcs = explode(',', $dtcs);
				
				for ($i = 0; $i < count($dtcs); ++$i)
				{
					if ($dtcs[$i] != '')
					{
						insert_db_dtc_data($loc['dt_server'], $loc['dt_tracker'], $loc['imei'], strtoupper($dtcs[$i]), $loc['lat'], $loc['lng']);	
					}
				}
			}
		}
	}
	
	function insert_db_dtc_data($dt_server, $dt_tracker, $imei, $code, $lat, $lng)
	{
		global $ms;
		
		// check for duplicates during past 24 hours
		$q = "SELECT * FROM `gs_dtc_data` WHERE `imei`='".$imei."' AND `code`='".$code."' AND dt_server > DATE_SUB(UTC_DATE(), INTERVAL 1 DAY)";
		$r = mysqli_query($ms, $q);
		
		$num = mysqli_num_rows($r);
		
		if ($num == 0)
		{
			$address = geocoderGetAddress($lat, $lng);
					
			$q = 'INSERT INTO gs_dtc_data  (`dt_server`,
							`dt_tracker`,
							`imei`,
							`code`,
							`lat`,
							`lng`,
							`address`
							) VALUES (
							"'.$dt_server.'",
							"'.$dt_tracker.'",
							"'.$imei.'",
							"'.$code.'",
							"'.$lat.'",
							"'.$lng.'",
							"'.mysqli_real_escape_string($ms, $address).'")';
								
			$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
		}
	}
	
	function get_gs_objects_data($imei)
	{
		global $ms;
		
		$q = "SELECT * FROM gs_objects WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q) or die(mysqli_error($ms));
		$row = mysqli_fetch_array($r);
		
		if ($row)
		{
			$row['params'] = json_decode($row['params'],true);
			
			return $row;
		}
		else
		{
			return false;
		}
	}
	
	function loc_filter($loc, $loc_prev)
	{
		global $ms, $gsValues;
		
		if ($gsValues['LOCATION_FILTER'] == false)
		{
			return false;
		}
		
		if (isset($loc['lat']) && isset($loc['lng']) && isset($loc['params']))
		{
			if (($loc['event'] == '') && ($loc_prev['params'] == $loc['params']))
			{
				$dt_difference = abs(strtotime($loc['dt_server']) - strtotime($loc_prev['dt_server']));
				
				if($dt_difference < 120)
				{
					// skip same location
					if (($loc_prev['lat'] == $loc['lat']) && ($loc_prev['lng'] == $loc['lng']) && ($loc_prev['speed'] == $loc['speed']))
					{
						return true;
					}
					
					// skip drift
					$distance = getLengthBetweenCoordinates($loc_prev['lat'], $loc_prev['lng'], $loc['lat'], $loc['lng']);
					if (($dt_difference < 30) && ($distance < 0.01) && ($loc['speed'] < 3) && ($loc_prev['speed'] == 0))
					{
						return true;
					}
				}
			}
		}
		
		return false;
	}
?>