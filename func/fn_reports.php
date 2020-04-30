<? 
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	include ('../tools/html2pdf.php');
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
	
	if(@$_POST['cmd'] == 'load_report_data')
	{
		$q = "SELECT * FROM `gs_user_reports` WHERE `user_id`='".$user_id."' ORDER BY `report_id` ASC";
		$r = mysqli_query($ms, $q);
		
		$result = array();
		
		while($row=mysqli_fetch_array($r))
		{
			$report_id = $row['report_id'];
			
			$other = '';
			
			if (($row['type'] == 'rag') || ($row['type'] == 'rag_driver'))
			{
				$default = array('low_score' => 0, 'high_score' => 5);
				
				if (($row['other'] == '') || (json_decode($row['other'],true) == null))
				{
					$other = $default;						     
				}
				else
				{
					$other = json_decode($row['other'],true);
					
					if (!isset($other["low_score"])) { $other["low_score"] = $default["low_score"]; }
					if (!isset($other["high_score"])) { $other["high_score"] = $default["high_score"]; }
				}
			}
			
			$result[$report_id] = array(	'name' => $row['name'],
							'type' => $row['type'],
							'format' => $row['format'],
							'show_coordinates' => $row['show_coordinates'],
							'show_addresses' => $row['show_addresses'],
							'zones_addresses' => $row['zones_addresses'],
							'stop_duration' => $row['stop_duration'],
							'speed_limit' => $row['speed_limit'],
							'imei' => $row['imei'],
							'zone_ids' => $row['zone_ids'],
							'sensor_names' => $row['sensor_names'],
							'data_items' => $row['data_items'],
							'other' => $other,
							'schedule_period' => $row['schedule_period'],
							'schedule_email_address' => $row['schedule_email_address']
							);
		}
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'save_report')
	{
		$report_id = $_POST["report_id"];
		$name = $_POST["name"];
		$type = $_POST["type"];
		$format = $_POST["format"];
		$show_coordinates = $_POST["show_coordinates"];
		$show_addresses = $_POST["show_addresses"];
		$zones_addresses = $_POST["zones_addresses"];
		$stop_duration = $_POST["stop_duration"];
		$speed_limit = $_POST["speed_limit"];
		$imei = $_POST["imei"];
		$zone_ids = $_POST["zone_ids"];
		$sensor_names = $_POST["sensor_names"];
		$data_items = $_POST["data_items"];
		$other = $_POST["other"];
		$schedule_period = $_POST["schedule_period"];
		$schedule_email_address = $_POST["schedule_email_address"];
		
		if ($report_id == 'false')
		{
			$q = "INSERT INTO `gs_user_reports`(	`user_id`,
								`name`,
								`type`,
								`format`,
								`show_coordinates`,
								`show_addresses`,
								`zones_addresses`,
								`stop_duration`,
								`speed_limit`,
								`imei`,
								`zone_ids`,
								`sensor_names`,
								`data_items`,
								`other`,
								`schedule_period`,
								`schedule_email_address`)
								VALUES
								('".$user_id."',
								'".$name."',
								'".$type."',
								'".$format."',
								'".$show_coordinates."',
								'".$show_addresses."',
								'".$zones_addresses."',
								'".$stop_duration."',
								'".$speed_limit."',
								'".$imei."',
								'".$zone_ids."',
								'".$sensor_names."',
								'".$data_items."',
								'".$other."',
								'".$schedule_period."',
								'".$schedule_email_address."')";	
		}
		else
		{
			$q = "UPDATE `gs_user_reports` SET 	`name`='".$name."',
								`type`='".$type."',
								`format`='".$format."',
								`show_coordinates`='".$show_coordinates."',
								`show_addresses`='".$show_addresses."',
								`zones_addresses`='".$zones_addresses."',
								`stop_duration`='".$stop_duration."',
								`speed_limit`='".$speed_limit."',
								`imei`='".$imei."',
								`zone_ids`='".$zone_ids."',
								`sensor_names`='".$sensor_names."',
								`data_items`='".$data_items."',
								`other`='".$other."',
								`schedule_period`='".$schedule_period."',
								`schedule_email_address`='".$schedule_email_address."'
								WHERE `report_id`='".$report_id."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_report')
	{
		$report_id = $_POST["report_id"];
		
		$q = "DELETE FROM `gs_user_reports` WHERE `report_id`='".$report_id."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_reports')
	{
		$items = $_POST["items"];
		
		for ($i = 0; $i < count($items); ++$i)
		{
			$item = $items[$i];
			
			$q = "DELETE FROM `gs_user_reports` WHERE `report_id`='".$item."' AND `user_id`='".$user_id."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_GET['cmd'] == 'load_report_list')
	{			
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx =1;
		
		// get records number
		$q = "SELECT * FROM `gs_user_reports` WHERE `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		$q = "SELECT * FROM `gs_user_reports` WHERE `user_id`='".$user_id."' ORDER BY $sidx $sord";
		$r = mysqli_query($ms, $q);
		
		$response = new stdClass();
		$response->page = 1;
		//$response->total = $count;
		$response->records = $count;
		
		if ($r)
		{
			$i=0;
			while($row = mysqli_fetch_array($r)) {
				$report_id = $row['report_id'];
				$name = $row['name'];
				
				if ($row['type'] == 'general')
				{
					$type = $la['GENERAL_INFO'];
				}
				else if ($row['type'] == 'general_merged')
				{
					$type = $la['GENERAL_INFO_MERGED'];
				}
				else if ($row['type'] == 'object_info')
				{
					$type = $la['OBJECT_INFO'];
				}
				else if ($row['type'] == 'current_position')
				{
					$type = $la['CURRENT_POSITION'];
				}
				else if ($row['type'] == 'current_position_off')
				{
					$type = $la['CURRENT_POSITION_OFFLINE'];
				}
				else if ($row['type'] == 'drives_stops')
				{
					$type = $la['DRIVES_AND_STOPS'];
				}
				else if ($row['type'] == 'drives_stops_logic')
				{
					$type = $la['DRIVES_AND_STOPS_WITH_LOGIC_SENSORS'];
				}
				else if ($row['type'] == 'travel_sheet')
				{
					$type = $la['TRAVEL_SHEET'];
				}
				else if ($row['type'] == 'mileage_daily')
				{
					$type = $la['MILEAGE_DAILY'];
				}
				else if ($row['type'] == 'overspeed')
				{
					$type = $la['OVERSPEEDS'];
				}
				else if ($row['type'] == 'underspeed')
				{
					$type = $la['UNDERSPEEDS'];
				}
				else if ($row['type'] == 'zone_in_out')
				{
					$type = $la['ZONE_IN_OUT'];
				}
				else if ($row['type'] == 'events')
				{
					$type = $la['EVENTS'];
				}
				else if ($row['type'] == 'service')
				{
					$type = $la['SERVICE'];
				}
				else if ($row['type'] == 'fuelfillings')
				{
					$type = $la['FUEL_FILLINGS'];
				}
				else if ($row['type'] == 'fuelthefts')
				{
					$type = $la['FUEL_THEFTS'];
				}
				else if ($row['type'] == 'logic_sensors')
				{
					$type = $la['LOGIC_SENSORS'];
				}
				else if ($row['type'] == 'rag')
				{
					$type = $la['DRIVER_BEHAVIOR_RAG_BY_OBJECT'];
				}
				else if ($row['type'] == 'rag_driver')
				{
					$type = $la['DRIVER_BEHAVIOR_RAG_BY_DRIVER'];
				}
				else if ($row['type'] == 'tasks')
				{
					$type = $la['TASKS'];
				}
				else if ($row['type'] == 'rilogbook')
				{
					$type = $la['RFID_AND_IBUTTON_LOGBOOK'];
				}
				else if ($row['type'] == 'dtc')
				{
					$type = $la['DIAGNOSTIC_TROUBLE_CODES'];
				}
				else if ($row['type'] == 'speed_graph')
				{
					$type = $la['SPEED'];
				}
				else if ($row['type'] == 'altitude_graph')
				{
					$type = $la['ALTITUDE'];
				}
				else if ($row['type'] == 'acc_graph')
				{
					$type = $la['IGNITION_GRAPH'];
				}
				else if ($row['type'] == 'fuellevel_graph')
				{
					$type = $la['FUEL_LEVEL_GRAPH'];
				}
				else if ($row['type'] == 'temperature_graph')
				{
					$type = $la['TEMPERATURE_GRAPH'];
				}
				else if ($row['type'] == 'sensor_graph')
				{
					$type = $la['SENSOR_GRAPH'];
				}
				else if ($row['type'] == 'routes')
				{
					$type = $la['ROUTES'];
				}
				else if ($row['type'] == 'image_gallery')
				{
					$type = $la['IMAGE_GALLERY'];
				}
				else
				{
					$type = '';
				}
				
				$format = strtoupper($row['format']);
				
				$objects = count(explode(",", $row['imei']));
				
				if ($row['zone_ids'] != '')
				{
					$zones = count(explode(",", $row['zone_ids']));
				}
				else
				{
					$zones = 0;
				}
				
				if ($row['sensor_names'] != '')
				{
					$sensors = count(explode(",", $row['sensor_names']));
				}
				else
				{
					$sensors = 0;
				}
							
				if ($row['schedule_period'] == 'dw')
				{
					$daily = '<img src="theme/images/tick-green.svg" />';
					$weekly = '<img src="theme/images/tick-green.svg" />';
				}
				else if ($row['schedule_period'] == 'd')
				{
					$daily = '<img src="theme/images/tick-green.svg" />';
					$weekly = '<img src="theme/images/remove-red.svg" style="width:12px;" />';
				}
				else if ($row['schedule_period'] == 'w')
				{
					$daily = '<img src="theme/images/remove-red.svg" style="width:12px;" />';
					$weekly = '<img src="theme/images/tick-green.svg" />';
				}
				else
				{
					$daily = '<img src="theme/images/remove-red.svg" style="width:12px;" />';
					$weekly = '<img src="theme/images/remove-red.svg" style="width:12px;" />';
				}
				
				// set modify buttons
				$modify = '<span id="report_action_menu_'.$report_id.'" tag="'.$report_id.'"><a href="#" title="'.$la['GENERATE'].'"><img src="theme/images/action4.svg" /></span>';
				$modify .= '<a href="#" onclick="reportProperties(\''.$report_id.'\');" title="'.$la['EDIT'].'"><img src="theme/images/edit.svg" />';
				$modify .= '<a href="#" onclick="reportsDelete(\''.$report_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				// set row
				$response->rows[$i]['id']=$report_id;
				$response->rows[$i]['cell']=array($name,$type,$format,$objects,$zones,$sensors,$daily,$weekly,$modify);
				$i++;
			}	
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_report_generated')
	{
		$report_id = $_POST["report_id"];
		
		$q = "SELECT * FROM `gs_user_reports_generated` WHERE `report_id`='".$report_id."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		$report_file = $gsValues['PATH_ROOT'].'data/user/reports/'.$row['report_file'];
		if(is_file($report_file))
		{
			@unlink($report_file);
		}
		
		$q = "DELETE FROM `gs_user_reports_generated` WHERE `report_id`='".$report_id."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_reports_generated')
	{
		$items = $_POST["items"];
		
		for ($i = 0; $i < count($items); ++$i)
		{
			$item = $items[$i];
			
			$q = "SELECT * FROM `gs_user_reports_generated` WHERE `report_id`='".$item."'";
			$r = mysqli_query($ms, $q);
			$row = mysqli_fetch_array($r);
			
			$report_file = $gsValues['PATH_ROOT'].'data/user/reports/'.$row['report_file'];
			if(is_file($report_file))
			{
				@unlink($report_file);
			}
			
			$q = "DELETE FROM `gs_user_reports_generated` WHERE `report_id`='".$item."' AND `user_id`='".$user_id."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'open_generated')
	{
		$report_id = $_POST["report_id"];
		
		$q = "SELECT * FROM `gs_user_reports_generated` WHERE `report_id`='".$report_id."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		$report_file = $gsValues['PATH_ROOT'].'data/user/reports/'.$row['report_file'];
		
		if(is_file($report_file))
		{
			$report = file_get_contents($report_file);
			
			if ($row['format'] == 'pdf')
			{
				$report = base64_decode(stripslashes($report));
				$report = html2pdf($report);
				$report = base64_encode($report);
			}	
		}
		else
		{
			$report = base64_encode($la['NOTHING_HAS_BEEN_FOUND_ON_YOUR_REQUEST']);
		}
				
		$result = array('format' => $row['format'], 'filename' => $row['filename'], 'content' => $report);
		
		echo json_encode($result);
		die;
	}
	
	if(@$_GET['cmd'] == 'load_reports_generated_list')
	{
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx =1;
		
		// get records number
		$q = "SELECT * FROM `gs_user_reports_generated` WHERE `user_id`='".$user_id."'";
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
		
		$q = "SELECT * FROM `gs_user_reports_generated` WHERE `user_id`='".$user_id."' ORDER BY $sidx $sord LIMIT $start, $limit";
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
				$report_id = $row['report_id'];
				$dt_report = convUserTimezone($row['dt_report']);
				$name = $row['name'];
				
				if ($row['type'] == 'general')
				{
					$type = $la['GENERAL_INFO'];
				}
				else if ($row['type'] == 'general_merged')
				{
					$type = $la['GENERAL_INFO_MERGED'];
				}
				else if ($row['type'] == 'object_info')
				{
					$type = $la['OBJECT_INFO'];
				}
				else if ($row['type'] == 'current_position')
				{
					$type = $la['CURRENT_POSITION'];
				}
				else if ($row['type'] == 'current_position_off')
				{
					$type = $la['CURRENT_POSITION_OFFLINE'];
				}
				else if ($row['type'] == 'drives_stops')
				{
					$type = $la['DRIVES_AND_STOPS'];
				}
				else if ($row['type'] == 'travel_sheet')
				{
					$type = $la['TRAVEL_SHEET'];
				}
				else if ($row['type'] == 'mileage_daily')
				{
					$type = $la['MILEAGE_DAILY'];
				}
				else if ($row['type'] == 'overspeed')
				{
					$type = $la['OVERSPEEDS'];
				}
				else if ($row['type'] == 'underspeed')
				{
					$type = $la['UNDERSPEEDS'];
				}
				else if ($row['type'] == 'zone_in_out')
				{
					$type = $la['ZONE_IN_OUT'];
				}
				else if ($row['type'] == 'events')
				{
					$type = $la['EVENTS'];
				}
				else if ($row['type'] == 'service')
				{
					$type = $la['SERVICE'];
				}
				else if ($row['type'] == 'fuelfillings')
				{
					$type = $la['FUEL_FILLINGS'];
				}
				else if ($row['type'] == 'fuelthefts')
				{
					$type = $la['FUEL_THEFTS'];
				}
				else if ($row['type'] == 'logic_sensors')
				{
					$type = $la['LOGIC_SENSORS'];
				}
				else if ($row['type'] == 'rag')
				{
					$type = $la['DRIVER_BEHAVIOR_RAG_BY_OBJECT'];
				}
				else if ($row['type'] == 'rag_driver')
				{
					$type = $la['DRIVER_BEHAVIOR_RAG_BY_DRIVER'];
				}
				else if ($row['type'] == 'tasks')
				{
					$type = $la['TASKS'];
				}
				else if ($row['type'] == 'rilogbook')
				{
					$type = $la['RFID_AND_IBUTTON_LOGBOOK'];
				}
				else if ($row['type'] == 'dtc')
				{
					$type = $la['DIAGNOSTIC_TROUBLE_CODES'];
				}
				else if ($row['type'] == 'speed_graph')
				{
					$type = $la['SPEED'];
				}
				else if ($row['type'] == 'altitude_graph')
				{
					$type = $la['ALTITUDE'];
				}
				else if ($row['type'] == 'acc_graph')
				{
					$type = $la['IGNITION_GRAPH'];
				}
				else if ($row['type'] == 'fuellevel_graph')
				{
					$type = $la['FUEL_LEVEL_GRAPH'];
				}
				else if ($row['type'] == 'temperature_graph')
				{
					$type = $la['TEMPERATURE_GRAPH'];
				}
				else if ($row['type'] == 'sensor_graph')
				{
					$type = $la['SENSOR_GRAPH'];
				}
				else if ($row['type'] == 'routes')
				{
					$type = $la['ROUTES'];
				}
				else if ($row['type'] == 'image_gallery')
				{
					$type = $la['IMAGE_GALLERY'];
				}
				else
				{
					$type = '';
				}
				
				$format = strtoupper($row['format']);
							
				if ($row['schedule'] == 'true')
				{
					$schedule = '<img src="theme/images/tick-green.svg" />';
				}
				else
				{
					$schedule = '<img src="theme/images/remove-red.svg" style="width:12px;" />';
				}
				
				// set modify buttons
				$modify = '<a href="#" onclick="reportsGeneratedOpen(\''.$report_id.'\');" title="'.$la['OPEN'].'"><img src="theme/images/file.svg" /></a>';
				$modify .= '<a href="#" onclick="reportsGeneratedDelete(\''.$report_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				// set row
				$response->rows[$i]['id']=$report_id;
				$response->rows[$i]['cell']=array($dt_report,$name,$type,$format,$row['objects'],$row['zones'],$row['sensors'],$schedule,$modify);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
?>