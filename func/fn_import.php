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
	
	if(@$_POST['format'] == 'evt')
        {
                $data = json_decode(stripslashes($_POST['data']),true);
                
                for ($i=0; $i<count($data['events']); ++$i)
                {
                        $type = mysqli_real_escape_string($ms, $data['events'][$i]['type']);
			$name = mysqli_real_escape_string($ms, $data['events'][$i]['name']);
			$active = mysqli_real_escape_string($ms, $data['events'][$i]['active']);
			$duration_from_last_event = mysqli_real_escape_string($ms, $data['events'][$i]['duration_from_last_event']);
			$duration_from_last_event_minutes = mysqli_real_escape_string($ms, $data['events'][$i]['duration_from_last_event_minutes']);
			$week_days = mysqli_real_escape_string($ms, $data['events'][$i]['week_days']);
			$day_time = mysqli_real_escape_string($ms, $data['events'][$i]['day_time']);
			
			$imei = mysqli_real_escape_string($ms, $data['events'][$i]['imei']);
			
			$imei_new = array();
			$imei_arr = explode(',', $imei);		
			for ($j = 0; $j < count($imei_arr); ++$j)
			{
				$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id."' AND `imei`='".$imei_arr[$j]."'";
				$r = mysqli_query($ms, $q);
				$row = mysqli_fetch_array($r);
				
				if ($row)
				{
					$imei_new[] = $imei_arr[$j];
				}
			}			
			$imei = implode(",", $imei_new);
		
			$checked_value = mysqli_real_escape_string($ms, $data['events'][$i]['checked_value']);
			$route_trigger = mysqli_real_escape_string($ms, $data['events'][$i]['route_trigger']);
			$zone_trigger = mysqli_real_escape_string($ms, $data['events'][$i]['zone_trigger']);
			
			$routes = mysqli_real_escape_string($ms, $data['events'][$i]['routes']);
			
			$routes_new = array();
			$routes_arr = explode(',', $routes);		
			for ($j = 0; $j < count($routes_arr); ++$j)
			{
				$q = "SELECT * FROM `gs_user_routes` WHERE `user_id`='".$user_id."' AND `route_id`='".$routes_arr[$j]."'";
				$r = mysqli_query($ms, $q);
				$row = mysqli_fetch_array($r);
				
				if ($row)
				{
					$routes_new[] = $routes_arr[$j];
				}
			}			
			$routes = implode(",", $routes_new);
			
			$zones = mysqli_real_escape_string($ms, $data['events'][$i]['zones']);
			
			$zones_new = array();
			$zones_arr = explode(',', $zones);		
			for ($j = 0; $j < count($zones_arr); ++$j)
			{
				$q = "SELECT * FROM `gs_user_zones` WHERE `user_id`='".$user_id."' AND `zone_id`='".$zones_arr[$j]."'";
				$r = mysqli_query($ms, $q);
				$row = mysqli_fetch_array($r);
				
				if ($row)
				{
					$zones_new[] = $zones_arr[$j];
				}
			}			
			$zones = implode(",", $zones_new);
			
			$notify_system = mysqli_real_escape_string($ms, $data['events'][$i]['notify_system']);
			$notify_push = mysqli_real_escape_string($ms, $data['events'][$i]['notify_push']);
			$notify_email = mysqli_real_escape_string($ms, $data['events'][$i]['notify_email']);
			$notify_email_address = mysqli_real_escape_string($ms, $data['events'][$i]['notify_email_address']);
			$notify_sms = mysqli_real_escape_string($ms, $data['events'][$i]['notify_sms']);
			$notify_sms_number = mysqli_real_escape_string($ms, $data['events'][$i]['notify_sms_number']);
			$notify_arrow = mysqli_real_escape_string($ms, $data['events'][$i]['notify_arrow']);
			$notify_arrow_color = mysqli_real_escape_string($ms, $data['events'][$i]['notify_arrow_color']);
			$notify_ohc = mysqli_real_escape_string($ms, $data['events'][$i]['notify_ohc']);
			$notify_ohc_color = mysqli_real_escape_string($ms, $data['events'][$i]['notify_ohc_color']);
			
			$email_template_id = mysqli_real_escape_string($ms, $data['events'][$i]['email_template_id']);
			
			$q = "SELECT * FROM `gs_user_templates` WHERE `template_id`='".$email_template_id."' AND `user_id`='".$user_id."' LIMIT 1";
			$r = mysqli_query($ms, $q);
			$num = mysqli_num_rows($r);
			
			if ($num == 0) { $email_template_id = 0; }
			
			$sms_template_id = mysqli_real_escape_string($ms, $data['events'][$i]['sms_template_id']);
			
			$q = "SELECT * FROM `gs_user_templates` WHERE `template_id`='".$sms_template_id."' AND `user_id`='".$user_id."' LIMIT 1";
			$r = mysqli_query($ms, $q);
			$num = mysqli_num_rows($r);
			
			if ($num == 0) { $sms_template_id = 0; }
			
			$webhook_send = mysqli_real_escape_string($ms, $data['events'][$i]['webhook_send']);
			$webhook_url = mysqli_real_escape_string($ms, $data['events'][$i]['webhook_url']);
			$cmd_send = mysqli_real_escape_string($ms, $data['events'][$i]['cmd_send']);
			$cmd_gateway = mysqli_real_escape_string($ms, $data['events'][$i]['cmd_gateway']);
			$cmd_type = mysqli_real_escape_string($ms, $data['events'][$i]['cmd_type']);
			$cmd_string = mysqli_real_escape_string($ms, $data['events'][$i]['cmd_string']);

			$q = "INSERT INTO `gs_user_events` (	`user_id`,
								`type`,
								`name`,
								`active`,
								`duration_from_last_event`,
								`duration_from_last_event_minutes`,
								`week_days`,
								`day_time`,
								`imei`,
								`checked_value`,
								`route_trigger`,
								`zone_trigger`,
								`routes`,
								`zones`,
								`notify_system`,
								`notify_push`,
								`notify_email`,
								`notify_email_address`,
								`notify_sms`,
								`notify_sms_number`,
								`email_template_id`,
								`sms_template_id`,
								`notify_arrow`,
								`notify_arrow_color`,
								`notify_ohc`,
								`notify_ohc_color`,
								`webhook_send`,
								`webhook_url`,
								`cmd_send`,
								`cmd_gateway`,
								`cmd_type`,
								`cmd_string`
								) VALUES (
								'".$user_id."',
								'".$type."',
								'".$name."',
								'".$active."',
								'".$duration_from_last_event."',
								'".$duration_from_last_event_minutes."',
								'".$week_days."',
								'".$day_time."',
								'".$imei."',
								'".$checked_value."',
								'".$route_trigger."',
								'".$zone_trigger."',
								'".$routes."',
								'".$zones."',
								'".$notify_system."',
								'".$notify_push."',
								'".$notify_email."',
								'".$notify_email_address."',
								'".$notify_sms."',												
								'".$notify_sms_number."',
								'".$email_template_id."',
								'".$sms_template_id."',
								'".$notify_arrow."',
								'".$notify_arrow_color."',
								'".$notify_ohc."',
								'".$notify_ohc_color."',
								'".$webhook_send."',
								'".$webhook_url."',
								'".$cmd_send."',
								'".$cmd_gateway."',
								'".$cmd_type."',
								'".$cmd_string."')";
                        $r = mysqli_query($ms, $q);
                }
                
                echo 'OK';
                die;
        }
	
	if(@$_POST['format'] == 'cte')
        {
                $user_id = $_SESSION["user_id"];
		
		$data = json_decode(stripslashes($_POST['data']),true);
		
		for ($i=0; $i<count($data['templates']); ++$i)
		{
			$name = mysqli_real_escape_string($ms, $data['templates'][$i]['name']);
			$protocol = mysqli_real_escape_string($ms, $data['templates'][$i]['protocol']);
			$gateway = mysqli_real_escape_string($ms, $data['templates'][$i]['gateway']);
			$type = mysqli_real_escape_string($ms, $data['templates'][$i]['type']);
			$cmd = mysqli_real_escape_string($ms, $data['templates'][$i]['cmd']);
			
			$q = 'INSERT INTO `gs_user_cmd` (	`user_id`,
								`name`,
								`protocol`,
								`gateway`,
								`type`,
								`cmd`)
							VALUES ("'.$user_id.'",
								"'.$name.'",
								"'.$protocol.'",
								"'.$gateway.'",
								"'.$type.'",
								"'.$cmd.'")';
			$r = mysqli_query($ms, $q);
		}  
                
                echo 'OK';
                die;
        }
	
	if(@$_POST['format'] == 'tem')
        {
                $user_id = $_SESSION["user_id"];
		
		$data = json_decode(stripslashes($_POST['data']),true);
		
		for ($i=0; $i<count($data['templates']); ++$i)
		{
			$name = mysqli_real_escape_string($ms, $data['templates'][$i]['name']);
			$desc = mysqli_real_escape_string($ms, $data['templates'][$i]['desc']);
			$message = mysqli_real_escape_string($ms, $data['templates'][$i]['message']);
			
			$q = 'INSERT INTO `gs_user_templates` (	`user_id`,
								`name`,
								`desc`,
								`message`)
							VALUES ("'.$user_id.'",
								"'.$name.'",
								"'.$desc.'",
								"'.$message.'")';
			$r = mysqli_query($ms, $q);
		}  
                
                echo 'OK';
                die;
        }
	
	if(@$_POST['format'] == 'otr')
        {
                $user_id = $_SESSION["user_id"];
		
		$data = json_decode(stripslashes($_POST['data']),true);
		
		for ($i=0; $i<count($data['trailers']); ++$i)
		{
			$trailer_name = mysqli_real_escape_string($ms, $data['trailers'][$i]['trailer_name']);
			$trailer_assign_id = mysqli_real_escape_string($ms, $data['trailers'][$i]['trailer_assign_id']);
			$trailer_model = mysqli_real_escape_string($ms, $data['trailers'][$i]['trailer_model']);
			$trailer_vin = mysqli_real_escape_string($ms, $data['trailers'][$i]['trailer_vin']);
			$trailer_plate_number = mysqli_real_escape_string($ms, $data['trailers'][$i]['trailer_plate_number']);
			$trailer_desc = mysqli_real_escape_string($ms, $data['trailers'][$i]['trailer_desc']);
			
			$q = 'INSERT INTO `gs_user_object_trailers` (	`user_id`,
									`trailer_name`,
									`trailer_assign_id`,
									`trailer_model`,
									`trailer_vin`,
									`trailer_plate_number`,
									`trailer_desc`)
							    VALUES ("'.$user_id.'",
								    "'.$trailer_name.'",
								    "'.$trailer_assign_id.'",
								    "'.$trailer_model.'",
								    "'.$trailer_vin.'",
								    "'.$trailer_plate_number.'",
								    "'.$trailer_desc.'")';
			$r = mysqli_query($ms, $q);
		}  
                
                echo 'OK';
                die;
        }
	
	if(@$_POST['format'] == 'opa')
        {
                $user_id = $_SESSION["user_id"];
		
		$data = json_decode(stripslashes($_POST['data']),true);
		
		for ($i=0; $i<count($data['passengers']); ++$i)
		{
			$passenger_name = mysqli_real_escape_string($ms, $data['passengers'][$i]['passenger_name']);
			$passenger_assign_id = mysqli_real_escape_string($ms, $data['passengers'][$i]['passenger_assign_id']);
			$passenger_idn = mysqli_real_escape_string($ms, $data['passengers'][$i]['passenger_idn']);
			$passenger_address = mysqli_real_escape_string($ms, $data['passengers'][$i]['passenger_address']);
			$passenger_phone = mysqli_real_escape_string($ms, $data['passengers'][$i]['passenger_phone']);
			$passenger_email = mysqli_real_escape_string($ms, $data['passengers'][$i]['passenger_email']);
			$passenger_desc = mysqli_real_escape_string($ms, $data['passengers'][$i]['passenger_desc']);
			
			$q = 'INSERT INTO `gs_user_object_passengers` (	`user_id`,
									`passenger_name`,
									`passenger_assign_id`,
									`passenger_idn`,
									`passenger_address`,
									`passenger_phone`,
									`passenger_email`,
									`passenger_desc`)
							    VALUES ("'.$user_id.'",
								    "'.$passenger_name.'",
								    "'.$passenger_assign_id.'",
								    "'.$passenger_idn.'",
								    "'.$passenger_address.'",
								    "'.$passenger_phone.'",
								    "'.$passenger_email.'",
								    "'.$passenger_desc.'")';
			$r = mysqli_query($ms, $q);
		}  
                
                echo 'OK';
                die;
        }
	
	if(@$_POST['format'] == 'odr')
        {
                $user_id = $_SESSION["user_id"];
		
		$data = json_decode(stripslashes($_POST['data']),true);
		
		for ($i=0; $i<count($data['drivers']); ++$i)
		{
			$driver_name = mysqli_real_escape_string($ms, $data['drivers'][$i]['driver_name']);
			$driver_assign_id = mysqli_real_escape_string($ms, $data['drivers'][$i]['driver_assign_id']);
			$driver_idn = mysqli_real_escape_string($ms, $data['drivers'][$i]['driver_idn']);
			$driver_address = mysqli_real_escape_string($ms, $data['drivers'][$i]['driver_address']);
			$driver_phone = mysqli_real_escape_string($ms, $data['drivers'][$i]['driver_phone']);
			$driver_email = mysqli_real_escape_string($ms, $data['drivers'][$i]['driver_email']);
			$driver_desc = mysqli_real_escape_string($ms, $data['drivers'][$i]['driver_desc']);
			$driver_img_file = mysqli_real_escape_string($ms, $data['drivers'][$i]['driver_img_file']);
			
			$q = 'INSERT INTO `gs_user_object_drivers` (	`user_id`,
									`driver_name`,
									`driver_assign_id`,
									`driver_idn`,
									`driver_address`,
									`driver_phone`,
									`driver_email`,
									`driver_desc`,
									`driver_img_file`)
							    VALUES ("'.$user_id.'",
								    "'.$driver_name.'",
								    "'.$driver_assign_id.'",
								    "'.$driver_idn.'",
								    "'.$driver_address.'",
								    "'.$driver_phone.'",
								    "'.$driver_email.'",
								    "'.$driver_desc.'",
								    "'.$driver_img_file.'")';
			$r = mysqli_query($ms, $q);
		}  
                
                echo 'OK';
                die;
        }
	
	if(@$_POST['format'] == 'ogr')
        {
                $user_id = $_SESSION["user_id"];
		
		$data = json_decode(stripslashes($_POST['data']),true);
		
		for ($i=0; $i<count($data['groups']); ++$i)
		{
			$group_name = mysqli_real_escape_string($ms, $data['groups'][$i]['group_name']);
			$group_desc = mysqli_real_escape_string($ms, $data['groups'][$i]['group_desc']);
			
			$q = 'INSERT INTO `gs_user_object_groups` (	`user_id`,
									`group_name`,
									`group_desc`)
							    VALUES ("'.$user_id.'",
								    "'.$group_name.'",
								    "'.$group_desc.'")';
			$r = mysqli_query($ms, $q);
		}  
                
                echo 'OK';
                die;
        }
	
	if(@$_POST['format'] == 'pgr')
        {
                $user_id = $_SESSION["user_id"];
		
		$data = json_decode(stripslashes($_POST['data']),true);
		
		for ($i=0; $i<count($data['groups']); ++$i)
		{
			$group_name = mysqli_real_escape_string($ms, $data['groups'][$i]['group_name']);
			$group_desc = mysqli_real_escape_string($ms, $data['groups'][$i]['group_desc']);
			
			$q = 'INSERT INTO `gs_user_places_groups` (	`user_id`,
									`group_name`,
									`group_desc`)
							    VALUES ("'.$user_id.'",
								    "'.$group_name.'",
								    "'.$group_desc.'")';
			$r = mysqli_query($ms, $q);
		}  
                
                echo 'OK';
                die;
        }
	
	if(@$_POST['format'] == 'sen')
        {
                $imei = $_POST["imei"];
                
                if (!checkUserToObjectPrivileges($user_id, $imei))
		{
			die;
		}
                
                $data = json_decode(stripslashes($_POST['data']),true);
                
                for ($i=0; $i<count($data['sensors']); ++$i)
                {
                        $name = mysqli_real_escape_string($ms, $data['sensors'][$i]['name']);
                        $type = mysqli_real_escape_string($ms, $data['sensors'][$i]['type']);
                        $param = mysqli_real_escape_string($ms, $data['sensors'][$i]['param']);
			$data_list = mysqli_real_escape_string($ms, $data['sensors'][$i]['data_list']);
                        $popup = mysqli_real_escape_string($ms, $data['sensors'][$i]['popup']);
                        $result_type = mysqli_real_escape_string($ms, $data['sensors'][$i]['result_type']);
                        $text_1 = mysqli_real_escape_string($ms, $data['sensors'][$i]['text_1']);
                        $text_0 = mysqli_real_escape_string($ms, $data['sensors'][$i]['text_0']);
                        $units = mysqli_real_escape_string($ms, $data['sensors'][$i]['units']);
                        $lv = mysqli_real_escape_string($ms, $data['sensors'][$i]['lv']);
                        $hv = mysqli_real_escape_string($ms, $data['sensors'][$i]['hv']);
			if (isset($data['sensors'][$i]['acc_ignore']))
			{
				$acc_ignore = mysqli_real_escape_string($ms, $data['sensors'][$i]['acc_ignore']);	
			}
			else
			{
				$acc_ignore = 'false';
			}			
                        $formula = mysqli_real_escape_string($ms, $data['sensors'][$i]['formula']);
                        $calibration = mysqli_real_escape_string($ms, $data['sensors'][$i]['calibration']);
			$dictionary = mysqli_real_escape_string($ms, $data['sensors'][$i]['dictionary']);
			
			if ($type == 'acc')
			{
				if (getSensorFromType($imei, $type) != false)
				{
					continue;
				}
			}
			
			if ($type == 'fuelsumup')
			{
				if (getSensorFromType($imei, $type) != false)
				{
					continue;
				}
			}
			
			if ($type == 'fuelcons')
			{
				if (getSensorFromType($imei, $type) != false)
				{
					continue;
				}
			}
			
			if ($type == 'engh')
			{
				if (getSensorFromType($imei, $type) != false)
				{
					continue;
				}
			}
			
			if ($type == 'odo')
			{
				if (getSensorFromType($imei, $type) != false)
				{
					continue;
				}
			}
			
			if ($type == 'da')
			{
				if (getSensorFromType($imei, $type) != false)
				{
					continue;
				}
			}
			
			if ($type == 'pa')
			{
				if (getSensorFromType($imei, $type) != false)
				{
					continue;
				}
			}
			
			if ($type == 'ta')
			{
				if (getSensorFromType($imei, $type) != false)
				{
					continue;
				}
			}
                        
                        $q = "INSERT INTO `gs_object_sensors`  (`imei`,
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
                                                VALUES ('".$imei."',
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
                        $r = mysqli_query($ms, $q);
                }
                
                echo 'OK';
                die;
        }
        
        if(@$_POST['format'] == 'ser')
        {
                $imei = $_POST["imei"];
                
                if (!checkUserToObjectPrivileges($user_id, $imei))
		{
			die;
		}
                
                $data = json_decode(stripslashes($_POST['data']),true);
                
                for ($i=0; $i<count($data['services']); ++$i)
                {
                        $name = mysqli_real_escape_string($ms, $data['services'][$i]['name']);
			$data_list = mysqli_real_escape_string($ms, $data['services'][$i]['data_list']);
			$popup = mysqli_real_escape_string($ms, $data['services'][$i]['popup']);
                        $odo = mysqli_real_escape_string($ms, $data['services'][$i]['odo']);
                        $odo_interval = mysqli_real_escape_string($ms, $data['services'][$i]['odo_interval']);
                        $odo_last = mysqli_real_escape_string($ms, $data['services'][$i]['odo_last']);
                        $engh = mysqli_real_escape_string($ms, $data['services'][$i]['engh']);
                        $engh_interval = mysqli_real_escape_string($ms, $data['services'][$i]['engh_interval']);
                        $engh_last = mysqli_real_escape_string($ms, $data['services'][$i]['engh_last']);
                        $days = mysqli_real_escape_string($ms, $data['services'][$i]['days']);
                        $days_interval =mysqli_real_escape_string($ms, $data['services'][$i]['days_interval']);
                        $days_last = mysqli_real_escape_string($ms, $data['services'][$i]['days_last']);
                        $odo_left = mysqli_real_escape_string($ms, $data['services'][$i]['odo_left']);
                        $odo_left_num = mysqli_real_escape_string($ms, $data['services'][$i]['odo_left_num']);
                        $engh_left = mysqli_real_escape_string($ms, $data['services'][$i]['engh_left']);
                        $engh_left_num = mysqli_real_escape_string($ms, $data['services'][$i]['engh_left_num']);
                        $days_left = mysqli_real_escape_string($ms, $data['services'][$i]['days_left']);
                        $days_left_num = mysqli_real_escape_string($ms, $data['services'][$i]['days_left_num']);
                        $update_last = mysqli_real_escape_string($ms, $data['services'][$i]['update_last']);
                        
                        $q = 'INSERT INTO `gs_object_services`  (`imei`,
                                                                `name`,
								`data_list`,
								`popup`,
                                                                `odo`,
                                                                `odo_interval`,
                                                                `odo_last`,
                                                                `engh`,
                                                                `engh_interval`,                                                                
                                                                `engh_last`,
                                                                `days`,
                                                                `days_interval`,
                                                                `days_last`,
                                                                `odo_left`,
                                                                `odo_left_num`,
                                                                `engh_left`,
                                                                `engh_left_num`,
                                                                `days_left`,
                                                                `days_left_num`,
                                                                `update_last`)
                                                VALUES ("'.$imei.'",
                                                        "'.$name.'",
							"'.$data_list.'",
							"'.$popup.'",
                                                        "'.$odo.'",
                                                        "'.$odo_interval.'",
                                                        "'.$odo_last.'",
                                                        "'.$engh.'",
                                                        "'.$engh_interval.'",
                                                        "'.$engh_last.'",
                                                        "'.$days.'",
                                                        "'.$days_interval.'",
                                                        "'.$days_last.'",
                                                        "'.$odo_left.'",
                                                        "'.$odo_left_num.'",
                                                        "'.$engh_left.'",
                                                        "'.$engh_left_num.'",
                                                        "'.$days_left.'",
                                                        "'.$days_left_num.'",
                                                        "'.$update_last.'")';
                        $r = mysqli_query($ms, $q);
                }
                
                echo 'OK';
                die;
        }
	
	if(@$_POST['format'] == 'cfl')
        {
                $imei = $_POST["imei"];
                
                if (!checkUserToObjectPrivileges($user_id, $imei))
		{
			die;
		}
                
                $data = json_decode(stripslashes($_POST['data']),true);
                
                for ($i=0; $i<count($data['fields']); ++$i)
                {
                        $name = mysqli_real_escape_string($ms, $data['fields'][$i]['name']);
                        $value = mysqli_real_escape_string($ms, $data['fields'][$i]['value']);
			$data_list = mysqli_real_escape_string($ms, $data['fields'][$i]['data_list']);
			$popup = mysqli_real_escape_string($ms, $data['fields'][$i]['popup']);
                        
                        $q = 'INSERT INTO `gs_object_custom_fields`  (`imei`,
									`name`,
									`value`,
									`data_list`,
									`popup`)
							VALUES ("'.$imei.'",
								"'.$name.'",
								"'.$value.'",
								"'.$data_list.'",
								"'.$popup.'")';
                        $r = mysqli_query($ms, $q);
                }
                
                echo 'OK';
                die;
        }
        
        if(@$_POST['format'] == 'plc')
        {
                $data = json_decode(stripslashes($_POST['data']),true);
                
                $user_id = $_SESSION["user_id"];
                
                // check marker limits
                if ($_POST['markers'] == 'true')
                {
                        $count = getUserNumberOfMarkers($user_id);
                        $count += count($data['markers']);
                        
                        if ($_SESSION["places_markers"] != '')
                        {
                                if ($count > $_SESSION["places_markers"])
                                {
                                        echo 'ERROR_MARKER_LIMIT';
                                        die;
                                }
                        }
                        else
                        {
                                if ($count > $gsValues['PLACES_MARKERS'])
                                {
                                        echo 'ERROR_MARKER_LIMIT';
                                        die;
                                }
                        }  
                }
                
                // check route limits
                if ($_POST['routes'] == 'true')
                {
                        $count = getUserNumberOfRoutes($user_id);
                        $count += count($data['routes']);
                        
                        if ($_SESSION["places_routes"] != '')
                        {
                                if ($count > $_SESSION["places_routes"])
                                {
                                        echo 'ERROR_ROUTE_LIMIT';
                                        die;
                                }
                        }
                        else
                        {
                                if ($count > $gsValues['PLACES_ROUTES'])
                                {
                                        echo 'ERROR_ROUTE_LIMIT';
                                        die;
                                }
                        }
                }
                
                // check zone limits
                if ($_POST['zones'] == 'true')
                {
                        $count = getUserNumberOfZones($user_id);
                        $count += count($data['zones']);
                        
                        if ($_SESSION["places_zones"] != '')
                        {
                                if ($count > $_SESSION["places_zones"])
                                {
                                        echo 'ERROR_ZONE_LIMIT';
                                        die;
                                }
                        }
                        else
                        {
                                if ($count > $gsValues['PLACES_ZONES'])
                                {
                                        echo 'ERROR_ZONE_LIMIT';
                                        die;
                                }
                        }
                }
                
                if ($_POST['markers'] == 'true')
                {                        
                        for ($i=0; $i<count($data['markers']); ++$i)
                        {
                                $marker_name = mysqli_real_escape_string($ms, $data['markers'][$i]['name']);
                                $marker_desc = mysqli_real_escape_string($ms, $data['markers'][$i]['desc']);
                                $marker_icon = mysqli_real_escape_string($ms, $data['markers'][$i]['icon']);
                                $marker_visible = mysqli_real_escape_string($ms, $data['markers'][$i]['visible']);
                                $marker_lat = mysqli_real_escape_string($ms, $data['markers'][$i]['lat']);
                                $marker_lng = mysqli_real_escape_string($ms, $data['markers'][$i]['lng']);
                                
				 $q = 'INSERT INTO `gs_user_markers` (`user_id`,
                                                                        `marker_name`,
                                                                        `marker_desc`,
                                                                        `marker_icon`,
                                                                        `marker_visible`,
                                                                        `marker_lat`,
                                                                        `marker_lng`)
                                                        VALUES ("'.$user_id.'",
                                                                "'.$marker_name.'",
                                                                "'.$marker_desc.'",
                                                                "'.$marker_icon.'",
                                                                "'.$marker_visible.'",
                                                                "'.$marker_lat.'",
                                                                "'.$marker_lng.'")';
                                $r = mysqli_query($ms, $q);
                        }  
                }
                
                if ($_POST['routes'] == 'true')
                {  
                        for ($i=0; $i<count($data['routes']); ++$i)
                        {
                                $route_name = mysqli_real_escape_string($ms, $data['routes'][$i]['name']);
                                $route_color = mysqli_real_escape_string($ms, $data['routes'][$i]['color']);
                                $route_visible = mysqli_real_escape_string($ms, $data['routes'][$i]['visible']);
                                $route_name_visible = mysqli_real_escape_string($ms, $data['routes'][$i]['name_visible']);
                                $route_deviation = mysqli_real_escape_string($ms, $data['routes'][$i]['deviation']);
                                $route_points = mysqli_real_escape_string($ms, $data['routes'][$i]['points']);
                                
                                $q = 'INSERT INTO `gs_user_routes` (`user_id`,
                                                                    `route_name`,
                                                                    `route_color`,
                                                                    `route_visible`,
                                                                    `route_name_visible`,
                                                                    `route_deviation`,
                                                                    `route_points`)
                                                        VALUES ("'.$user_id.'",
                                                                "'.$route_name.'",
                                                                "'.$route_color.'",
                                                                "'.$route_visible.'",
                                                                "'.$route_name_visible.'",
                                                                "'.$route_deviation.'",
                                                                "'.$route_points.'")';
                                $r = mysqli_query($ms, $q);
                        }  
                }
                
                if ($_POST['zones'] == 'true')
                {  
                        for ($i=0; $i<count($data['zones']); ++$i)
                        {
				$zone_name = mysqli_real_escape_string($ms, $data['zones'][$i]['name']);
				$zone_color = mysqli_real_escape_string($ms, $data['zones'][$i]['color']);
				$zone_visible = mysqli_real_escape_string($ms, $data['zones'][$i]['visible']);
				$zone_name_visible = mysqli_real_escape_string($ms, $data['zones'][$i]['name_visible']);
				
				if (isset($data['zones'][$i]['area']))
				{
					$area = $data['zones'][$i]['area'];
				}
				else
				{
					$area = 0;
				}
				
				$zone_vertices = $data['zones'][$i]['vertices'];
				
				$q = 'INSERT INTO `gs_user_zones` (`user_id`,
								    `zone_name`,
								    `zone_color`,
								    `zone_visible`,
								    `zone_name_visible`,
								    `zone_area`,
								    `zone_vertices`)
							VALUES ("'.$user_id.'",
								"'.$zone_name.'",
								"'.$zone_color.'",
								"'.$zone_visible.'",
								"'.$zone_name_visible.'",
								"'.$area.'",
								"'.$zone_vertices.'")';
				$r = mysqli_query($ms, $q);
                        }  
                }
                
                echo 'OK';
                die;
        }
?>