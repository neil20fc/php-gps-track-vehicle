<? 
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	checkUserSession();
	
	loadLanguage($_SESSION["language"], $_SESSION["units"]);
	
	// check privileges
	if ($_SESSION["privileges"] == 'subuser')
	{
		$user_id = $_SESSION["manager_id"];
	}
	else
	{
		$user_id = $_SESSION["user_id"];
	}
	
	if(@$_POST['cmd'] == 'add_object')
	{
		if (($_SESSION["manager_id"] == 0) && ($_SESSION["obj_add"] != 'false'))
		{
			$name = $_POST["name"];
			$imei = strtoupper($_POST["imei"]);
			
			if (checkObjectLimitSystem())
			{
				echo 'ERROR_OBJECT_LIMIT';
				die;
			}
			
			if(checkObjectExistsUser($imei))
			{
				echo 'ERROR_IMEI_EXISTS';
				die;
			}
			
			if($_SESSION["obj_add"] == 'true')
			{
				if(checkObjectLimitUser($user_id))
				{
					echo 'ERROR_OBJECT_LIMIT';
					die;
				}
				
				if ($_SESSION["obj_days"] == 'true')
				{
					$object_expire = 'true';
					$object_expire_dt = $_SESSION["obj_days_dt"];
				}
				else
				{
					$object_expire = 'false';
					$object_expire_dt = '';
				}
			}
			else if ($_SESSION["obj_add"] == "trial")
			{
				$object_expire = 'true';
				$object_expire_dt = gmdate("Y-m-d", strtotime(gmdate("Y-m-d").' + '.$gsValues['OBJ_DAYS_TRIAL'].' days'));
			}
			
			addObjectSystem($name, $imei, 'true', $object_expire, $object_expire_dt, $_SESSION["manager_id"]);
			
			addObjectUser($user_id, $imei, 0, 0, 0);
			
			createObjectDataTable($imei);
			
			echo 'OK';
		}
		die;
	}
	
	if(@$_POST['cmd'] == 'duplicate_object')
	{
		if (($_SESSION["manager_id"] == 0) && ($_SESSION["obj_add"] != 'false'))
		{
			$duplicate_imei = strtoupper($_POST["duplicate_imei"]);
			$name = $_POST["name"];
			$imei = strtoupper($_POST["imei"]);
			
			if (checkObjectLimitSystem())
			{
				echo 'ERROR_OBJECT_LIMIT';
				die;
			}
			
			if(checkObjectExistsUser($imei))
			{
				echo 'ERROR_IMEI_EXISTS';
				die;
			}
			
			if($_SESSION["obj_add"] == 'true')
			{
				if(checkObjectLimitUser($user_id))
				{
					echo 'ERROR_OBJECT_LIMIT';
					die;
				}
				
				if ($_SESSION["obj_days"] == 'true')
				{
					$object_expire = 'true';
					$object_expire_dt = $_SESSION["obj_days_dt"];
				}
				else
				{
					$object_expire = 'false';
					$object_expire_dt = '';
				}
			}
			else if ($_SESSION["obj_add"] == "trial")
			{
				$object_expire = 'true';
				$object_expire_dt = gmdate("Y-m-d", strtotime(gmdate("Y-m-d").' + '.$gsValues['OBJ_DAYS_TRIAL'].' days'));
			}
			
			duplicateObjectSystem($duplicate_imei, $imei, $object_expire, $object_expire_dt, $_SESSION["manager_id"], $name);
			
			$q = "SELECT * FROM `gs_user_objects` WHERE `imei`='".$duplicate_imei."' AND `user_id`='".$user_id."'";
			$r = mysqli_query($ms, $q);
			$row = mysqli_fetch_array($r);
			
			addObjectUser($user_id, $imei, $row['group_id'], $row['driver_id'], $row['trailer_id']);
			
			createObjectDataTable($imei);
			
			//write log
			writeLog('object_op', 'Add object: successful. IMEI: '.$imei);
			
			echo 'OK';
		}
		die;	
	}
	
	if(@$_POST['cmd'] == 'edit_object')
	{
		if($_SESSION["obj_edit"] == 'true')
		{
			$group_id = $_POST["group_id"];
			$driver_id = $_POST["driver_id"];
			$trailer_id = $_POST["trailer_id"];
			$name = $_POST["name"];
			$imei = $_POST["imei"];
			$device = $_POST["device"];
			$sim_number = $_POST["sim_number"];
			$model = $_POST["model"];
			$vin = $_POST["vin"];
			$plate_number = $_POST["plate_number"];
			$icon = $_POST["icon"];
			$map_arrows = $_POST["map_arrows"];
			$map_icon = $_POST["map_icon"];
			$tail_color = $_POST["tail_color"];
			$tail_points = $_POST["tail_points"];
			$fcr = $_POST["fcr"];
			$time_adj = $_POST["time_adj"];
			$accuracy = $_POST["accuracy"];		
			
			$q = "UPDATE `gs_user_objects` SET 	`group_id`='".$group_id."',
								`driver_id`='".$driver_id."',
								`trailer_id`='".$trailer_id."'
								WHERE `user_id`='".$user_id."' AND `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
			
			$q = "UPDATE `gs_objects` SET 	`name`='".$name."',
							`icon`='".$icon."',
							`map_icon`='".$map_icon."',
							`map_arrows`='".$map_arrows."',
							`tail_color`='".$tail_color."',
							`tail_points`='".$tail_points."',
							`device`='".$device."',
							`sim_number`='".$sim_number."',
							`model`='".$model."',
							`vin`='".$vin."',
							`plate_number`='".$plate_number."',
							`fcr`='".$fcr."',
							`accuracy`='".$accuracy."'						
							WHERE `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
			
			// set time adjustment
			$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
			$row = mysqli_fetch_array($r);
			
			if($time_adj != $row["time_adj"])
			{
				$q = "UPDATE `gs_objects` SET  `dt_server`='0000-00-00 00:00:00',
								`dt_tracker`='0000-00-00 00:00:00',
								`lat`='0',
								`lng`='0',
								`altitude`='0',
								`angle`='0',
								`speed`='0',
								`loc_valid`='0',
								`params`='',
								`time_adj`='".$time_adj."'
								WHERE `imei`='".$imei."'";
				$r = mysqli_query($ms, $q);
			}
			
			// set odometer and engine hours type
			$odometer_type = $_POST["odometer_type"];
			$engine_hours_type = $_POST["engine_hours_type"];
			
			$q = "UPDATE `gs_objects` SET `odometer_type`='".$odometer_type."', `engine_hours_type`='".$engine_hours_type."' WHERE `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
			
			// get odometer and engine_hours and check if saving is needed
			$odometer = $_POST["odometer"];
			
			if ($odometer != 'false')
			{
				// save in km
				$odometer = floor(convDistanceUnits($odometer, $_SESSION["unit_distance"], 'km'));
				
				$q = "UPDATE `gs_objects` SET `odometer`='".$odometer."' WHERE `imei`='".$imei."'";
				$r = mysqli_query($ms, $q);
			}
			
			$engine_hours = $_POST["engine_hours"];
			
			if ($engine_hours != 'false')
			{
				$engine_hours = $engine_hours * 60 * 60;
				
				$q = "UPDATE `gs_objects` SET `engine_hours`='".$engine_hours."' WHERE `imei`='".$imei."'";
				$r = mysqli_query($ms, $q);
			}
			
			echo 'OK';	
		}

		die;
	}
	
	if(@$_POST['cmd'] == 'clear_history_object')
	{
		if($_SESSION["obj_history_clear"] == 'true')
		{
			$imei = $_POST['imei'];
			
			$q = "SELECT * FROM `gs_user_objects` WHERE `imei`='".$imei."' AND `user_id`='".$user_id."'";
			$r = mysqli_query($ms, $q);
			$row = mysqli_fetch_array($r);
			
			if($row)
			{
				clearObjectHistory($imei);
			}
			
			echo 'OK';	
		}
		
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_object')
	{
		$imei = $_POST["imei"];
		
		if (($_SESSION["billing"] == true) && ($gsValues['BILLING_RECOVER_PLAN'] == 'true') && ($_SESSION["obj_add"] == 'trial'))
		{
			moveObjectPlanToBilling($user_id, $imei);
		}
		
		delObjectUser($user_id, $imei);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'clear_history_selected_objects')
	{
		if($_SESSION["obj_history_clear"] == 'true')
		{
			$items = $_POST["items"];
					
			for ($i = 0; $i < count($items); ++$i)
			{
				$item = $items[$i];
						
				clearObjectHistory($item);
			}
			
			echo 'OK';
			die;	
		}
	}
	
	if(@$_POST['cmd'] == 'delete_selected_objects')
	{
		$items = $_POST["items"];
		
		for ($i = 0; $i < count($items); ++$i)
		{
			$item = $items[$i];
			
			if (($_SESSION["billing"] == true) && ($gsValues['BILLING_RECOVER_PLAN'] == 'true') && ($_SESSION["obj_add"] == 'trial'))
			{
				moveObjectPlanToBilling($user_id, $item);
			}
			
			delObjectUser($user_id, $item);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'load_object_data')
	{
		// check privileges		
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT gs_objects.*, gs_user_objects.*
				FROM gs_objects
				INNER JOIN gs_user_objects ON gs_objects.imei = gs_user_objects.imei
				WHERE gs_user_objects.user_id='".$user_id."'
				AND gs_objects.imei IN (".$_SESSION["privileges_imei"].")";
		}
		else
		{
			$q = "SELECT gs_objects.*, gs_user_objects.*
				FROM gs_objects
				INNER JOIN gs_user_objects ON gs_objects.imei = gs_user_objects.imei
				WHERE gs_user_objects.user_id='".$user_id."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		$result = array();
		
		while($row = mysqli_fetch_array($r))
		{			
			$imei = $row['imei'];
			
			// get object accuracy
			$accuracy = getObjectAccuracy($imei);
			
			// get object sensor list
			$sensors = getObjectSensors($imei);
			
			// get object service list
			$service = getObjectService($imei);
			
			// get object custom fields list
			$custom_fields = getObjectCustomFields($imei);
			
			// set default fcr if not set in DB
			$fcr = getObjectFCR($imei);
			
			// set default odometer and engine hours type if not set in DB
			if ($row['odometer_type'] == '')
			{
				$row['odometer_type'] = 'gps';
			}
			
			if ($row['engine_hours_type'] == '')
			{
				$row['engine_hours_type'] = 'acc';
			}
			
			// odometer and engine hours
			$row['odometer'] = floor(convDistanceUnits($row['odometer'], 'km', $_SESSION["unit_distance"]));
			
			$row['engine_hours'] = floor($row['engine_hours'] / 60 / 60);
			
			// map arrows
			$default = array(	'arrow_no_connection' => 'arrow_red',
						'arrow_stopped' => 'arrow_red',
						'arrow_moving' => 'arrow_green',
						'arrow_engine_idle' => 'off'
						);
			
			if (($row['map_arrows'] == '') || (json_decode($row['map_arrows'],true) == null))
			{
				$map_arrows = $default;
			}
			else
			{
				$map_arrows = json_decode($row['map_arrows'],true);
				
				if (!isset($map_arrows["arrow_no_connection"])) { $map_arrows["arrow_no_connection"] = $default["arrow_no_connection"]; }
				if (!isset($map_arrows["arrow_stopped"])) { $map_arrows["arrow_stopped"] = $default["arrow_stopped"]; }
				if (!isset($map_arrows["arrow_moving"])) { $map_arrows["arrow_moving"] = $default["arrow_moving"]; }
				if (!isset($map_arrows["arrow_engine_idle"])) { $map_arrows["arrow_engine_idle"] = $default["arrow_engine_idle"]; }
			}		
			
			$result[$imei] = array( 'protocol' => $row['protocol'],
					        'group_id' => $row['group_id'],
						'driver_id' => $row['driver_id'],
						'trailer_id' => $row['trailer_id'],
						'name' => $row['name'],
						'icon' => $row['icon'],
						'map_arrows' => $map_arrows,
						'map_icon' => $row['map_icon'],
						'tail_color' => $row['tail_color'],
						'tail_points' => $row['tail_points'],
						'device' => $row['device'], 
						'sim_number' => $row['sim_number'],
						'model' => $row['model'],
						'vin' => $row['vin'],
						'plate_number' => $row['plate_number'],
						'odometer_type' => $row['odometer_type'],
						'engine_hours_type' => $row['engine_hours_type'],
						'odometer' => $row['odometer'],
						'engine_hours' => $row['engine_hours'],
						'fcr' => $fcr,
						'time_adj' => $row['time_adj'],
						'accuracy' => $accuracy,						
						'sensors' => $sensors,
						'service' => $service,
						'custom_fields' => $custom_fields,
						'params' => getParamsArray($row['params']),
						'active' => $row['active'],
						'object_expire' => $row['object_expire'],
						'object_expire_dt' => $row['object_expire_dt']
						);
		}
		
		echo json_encode($result);
		die;
	}
	
	if(@$_GET['cmd'] == 'load_object_info_list')
	{
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		$imei = $_GET['imei'];
		
		if(!$sidx) $sidx =1;
		
		// get records number
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		$row = mysqli_fetch_array($r);
		
		$row['dt_server'] = convUserTimezone($row['dt_server']);
		$row['dt_tracker'] = convUserTimezone($row['dt_tracker']);
		
		$row['lat'] = sprintf('%0.6f', $row['lat']);
		$row['lng'] = sprintf('%0.6f', $row['lng']);
		
		$row['altitude'] = convAltitudeUnits($row['altitude'], 'km', $_SESSION["unit_distance"]).' '.$la["UNIT_HEIGHT"];
		$row['speed'] = convSpeedUnits($row['speed'], 'km', $_SESSION["unit_distance"]).' '.$la["UNIT_SPEED"];
		
		if ($row['params'] == '')
		{
			$row['params'] = '';
		}
		else
		{
			$row['params'] = json_decode($row['params'],true);
			
			$arr_params = array();
			
			foreach ($row['params'] as $key => $value)
			{
				array_push($arr_params, $key.'='.$value);
			}
			
			$row['params'] = implode(', ', $arr_params);
		}
		
		$list_array = array(	$la['ALTITUDE'] => $row['altitude'],
					$la['ANGLE'] => $row['angle'].' &deg;',
					$la['LATITUDE'] => $row['lat'].' &deg;',
					$la['LONGITUDE'] => $row['lng'].' &deg;',
					$la['PARAMETERS'] => $row['params'],
					$la['PROTOCOL'] => $row['protocol'],
					$la['SPEED'] => $row['speed'],
					$la['TIME_POSITION'] => $row['dt_tracker'],
					$la['TIME_SERVER'] => $row['dt_server']
					);
		
		ksort($list_array);
		
		$response = new stdClass();
		$response->page = 1;
		//$response->total = $count;
		$response->records = $count;
		
		$i=0;
		foreach ($list_array as $key => $value)
		{
			$response->rows[$i]['cell']=array($key, $value);
			$i++;
		}

		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
	
	if(@$_GET['cmd'] == 'load_object_list')
	{
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		//$search = caseToUpper(@$_GET['s']); // get search
		
		if(!$sidx) $sidx =1;
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		if ($count > 0)
		{
			$total_pages = ceil($count/$limit);
		}
		else
		{
			$total_pages = 1;
		}
		
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei` IN (".getUserObjectIMEIs($user_id).") ORDER BY $sidx $sord LIMIT $start, $limit";
		$r = mysqli_query($ms, $q);
		
		$response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $count;
		
		if ($r)
		{
			$i=0;
			while($row = mysqli_fetch_array($r))
			{
				$imei = $row['imei'];
				
				$object_expire_dt = '';
				
				if ($row['active'] == 'true')
				{
					$active = '<img src="theme/images/tick-green.svg" />';
					
					if ($row['object_expire'] == 'true')
					{
						$object_expire_dt = $row['object_expire_dt'];
					}
				}
				else
				{
					$active = '<img src="theme/images/remove-red.svg" style="width:12px;" />';
					
					if ($row['object_expire'] == 'true')
					{					
						if ($_SESSION["billing"] == true)
						{
							$object_expire_dt = '<a href="#" onclick="billingOpen();">'.$la['ACTIVATE'].'</a>';	
						}
						else
						{
							$object_expire_dt = $row['object_expire_dt'];
						}
					}	
				}
				
				// set modify buttons
				$modify = '<a href="#" onclick="settingsObjectEdit(\''.$imei.'\');" title="'.$la['EDIT'].'"><img src="theme/images/edit.svg" /></a>';
				$modify .= '<a href="#" onclick="settingsObjectDuplicate(\''.$imei.'\');" title="'.$la['DUPLICATE'].'"><img src="theme/images/copy.svg" /></a>';
				$modify .= '<a href="#" onclick="settingsObjectClearHistory(\''.$imei.'\');" title="'.$la['CLEAR_HISTORY'].'"><img src="theme/images/erase.svg" /></a>';
				$modify .= '<a href="#" onclick="settingsObjectDelete(\''.$imei.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				
				// set row
				$response->rows[$i]['id']=$imei;
				$response->rows[$i]['cell']=array($row['name'],$imei,$active,$object_expire_dt,$modify);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_custom_icon')
	{
		$file = $_POST['file'];
		$path = $gsValues['PATH_ROOT'];
		
		$icon_file = $path.'/'.$file;
		if(is_file($icon_file))
		{
			@unlink($icon_file);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_all_custom_icons')
	{
		$filter = $_SESSION['user_id'].'_';
		
		$path = $gsValues['PATH_ROOT'].'data/user/objects';
		$dh = opendir($path);
	    
		$result = array();
		    
		while (($file = readdir($dh)) !== false)
		{
			if ($file != '.' && $file != '..' && $file != 'Thumbs.db')
			{
				if (0 === strpos($file, $filter))
				{
					$icon_file = $path.'/'.$file;
					if(is_file($icon_file))
					{
						@unlink($icon_file);
					}
				}
			}
		}
		
		closedir($dh);
		
		echo 'OK';
		die;
	}
?>