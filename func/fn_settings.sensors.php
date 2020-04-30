<? 
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	checkUserSession();
	
	loadLanguage($_SESSION["language"], $_SESSION["units"]);
	
	if(@$_POST['cmd'] == 'clear_detected_sensor_cache')
	{
		$imei = $_POST["imei"];
		
		$q = "UPDATE `gs_objects` SET `params`='' WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_object_sensor')
	{
		$sensor_id = $_POST["sensor_id"];
		$imei = $_POST["imei"];
		
		$q = "DELETE FROM `gs_object_sensors` WHERE `sensor_id`='".$sensor_id."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_object_sensors')
	{
		$items = $_POST["items"];
		$imei = $_POST["imei"];
		
		for ($i = 0; $i < count($items); ++$i)
		{
			$item = $items[$i];
			
			$q = "DELETE FROM `gs_object_sensors` WHERE `sensor_id`='".$item."' AND `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'save_object_sensor')
	{
		$sensor_id = $_POST["sensor_id"];
		$imei = $_POST["imei"];
		$name = $_POST["name"];
		$type = $_POST["type"];
		$param = $_POST["param"];
		$data_list = $_POST["data_list"];
		$popup = $_POST["popup"];
		$result_type = $_POST["result_type"];
		$text_1 = $_POST["text_1"];
		$text_0 = $_POST["text_0"];
		$units = $_POST["units"];
		$lv = $_POST["lv"];
		$hv = $_POST["hv"];
		$acc_ignore = $_POST["acc_ignore"];
		$formula = $_POST["formula"];
		$calibration = $_POST["calibration"];
		$dictionary = $_POST["dictionary"];
		
		if ($sensor_id == 'false')
		{
			$q = "INSERT INTO `gs_object_sensors` 		(`imei`,
									`name`,
									`type`,
									`param`,
									`data_list`,
									`popup`,
									`result_type`,
									`text_1`,
									`text_0`,
									`units`,
									`lv`,
									`hv`,
									`acc_ignore`,
									`formula`,
									`calibration`,
									`dictionary`)
									VALUES
									('".$imei."',
									'".$name."',
									'".$type."',
									'".$param."',
									'".$data_list."',
									'".$popup."',
									'".$result_type."',
									'".$text_1."',
									'".$text_0."',
									'".$units."',
									'".$lv."',
									'".$hv."',
									'".$acc_ignore."',
									'".$formula."',
									'".$calibration."',
									'".$dictionary."')";
		}
		else
		{
			$q = "UPDATE `gs_object_sensors` SET 	`name`='".$name."',
								`type`='".$type."',
								`param`='".$param."',
								`data_list`='".$data_list."',
								`popup`='".$popup."',
								`result_type`='".$result_type."',
								`text_1`='".$text_1."',
								`text_0`='".$text_0."',
								`units`='".$units."',
								`lv`='".$lv."',
								`hv`='".$hv."',
								`acc_ignore`='".$acc_ignore."',
								`formula`='".$formula."',
								`calibration`='".$calibration."',
								`dictionary`='".$dictionary."'
								WHERE `sensor_id`='".$sensor_id."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_GET['cmd'] == 'load_object_sensor_list')
	{ 
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		$imei = $_GET['imei'];
		
		if(!$sidx) $sidx =1;
		
		// get records number
		$q = "SELECT * FROM `gs_object_sensors` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		$q = "SELECT * FROM `gs_object_sensors` WHERE `imei`='".$imei."' ORDER BY $sidx $sord";
		$r = mysqli_query($ms, $q);
		
		$response = new stdClass();
		$response->page = 1;
		//$response->total = $count;
		$response->records = $count;
		
		if ($r)
		{
			$i=0;
			while($row = mysqli_fetch_array($r)) {
				$sensor_id = $row["sensor_id"];
				$name = $row['name'];
				$type = $row['type'];
				$param = $row['param'];
				// change type
				if ($type == "batt") $type = $la['BATTERY'];
				if ($type == "di") $type = $la['DIGITAL_INPUT'];
				if ($type == "do") $type = $la['DIGITAL_OUTPUT'];
				if ($type == "da") $type = $la['DRIVER_ASSIGN'];
				if ($type == "engh") $type = $la['ENGINE_HOURS'];
				if ($type == "fuel") $type = $la['FUEL_LEVEL'];
				if ($type == "fuelsumup") $type = $la['FUEL_LEVEL_SUM_UP'];
				if ($type == "fuelcons") $type = $la['FUEL_CONSUMPTION'];
				if ($type == "gsm") $type = $la['GSM_LEVEL'];
				if ($type == "gps") $type = $la['GPS_LEVEL'];
				if ($type == "acc") $type = $la['IGNITION_ACC'];
				if ($type == "odo") $type = $la['ODOMETER'];
				if ($type == "pa") $type = $la['PASSENGER_ASSIGN'];
				if ($type == "temp") $type = $la['TEMPERATURE'];
				if ($type == "ta") $type = $la['TRAILER_ASSIGN'];
				if ($type == "cust") $type = $la['CUSTOM'];
				// set modify buttons
				$modify = '<a href="#" onclick="settingsObjectSensorProperties(\''.$sensor_id.'\');" title="'.$la['EDIT'].'"><img src="theme/images/edit.svg" />';
				$modify .= '</a><a href="#" onclick="settingsObjectSensorDelete(\''.$sensor_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				// set row
				$response->rows[$i]['id']=$sensor_id;
				$response->rows[$i]['cell']=array($name,$type,$param,$modify);
				$i++;
			}	
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
?>