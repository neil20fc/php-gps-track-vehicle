<? 
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	include ('../tools/sms.php');
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
	
	if(@$_GET['cmd'] == 'load_cmd_gprs_exec_list')
	{
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx = 1;
		
		// get records number
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT * FROM `gs_object_cmd_exec` WHERE `imei` IN (".$_SESSION["privileges_imei"].") AND `gateway`='gprs'";
		}
		else
		{
			$q = "SELECT * FROM `gs_object_cmd_exec` WHERE `imei` IN (".getUserObjectIMEIs($user_id).") AND `gateway`='gprs'";
		}
		
		$r = mysqli_query($ms, $q);
		
		if (!$r){die;}
		
		$count = mysqli_num_rows($r);
		
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT * FROM `gs_object_cmd_exec` WHERE `imei` IN (".$_SESSION["privileges_imei"].") AND `gateway`='gprs' ORDER BY $sidx $sord";
		}
		else
		{
			$q = "SELECT * FROM `gs_object_cmd_exec` WHERE `imei` IN (".getUserObjectIMEIs($user_id).") AND `gateway`='gprs' ORDER BY $sidx $sord";
		}
		
		$r = mysqli_query($ms, $q);
		
		$response = new stdClass();
		$response->page = 1;
		//$response->total = $count;
		$response->records = $count;
		
		if ($r)
		{
			$i=0;
			while($row = mysqli_fetch_array($r))
			{
				$cmd_id = $row['cmd_id'];
				$time = convUserTimezone($row['dt_cmd']);
				$object = getObjectName($row['imei']);
				
				$name = $row['name'];
				$type = strtoupper($row['type']);
				$cmd = $row['cmd'];
				
				if ($row['status'] == 0)
				{
					$status = '<span class="spinner" style="height: 3px;"></span>';
				}
				else if ($row['status'] == 1)
				{
					$status = '<img src="theme/images/tick-green.svg" />';
				}
				
				$re_hex = $row['re_hex'];
				
				// set modify buttons
				$modify = '<a href="#" onclick="cmdGPRSExecDelete(\''.$cmd_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				// set row
				$response->rows[$i]['id']=$cmd_id;
				$response->rows[$i]['cell']=array($time,$object,$name,$cmd,$status,$modify,$re_hex);
				$i++;
			}	
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
	
	if(@$_GET['cmd'] == 'load_cmd_sms_exec_list')
	{
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx = 1;
		
		// get records number
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT * FROM `gs_object_cmd_exec` WHERE `imei` IN (".$_SESSION["privileges_imei"].") AND `gateway`='sms'";
		}
		else
		{
			$q = "SELECT * FROM `gs_object_cmd_exec` WHERE `imei` IN (".getUserObjectIMEIs($user_id).") AND `gateway`='sms'";
		}
		
		$r = mysqli_query($ms, $q);
		
		if (!$r){die;}
		
		$count = mysqli_num_rows($r);
		
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT * FROM `gs_object_cmd_exec` WHERE `imei` IN (".$_SESSION["privileges_imei"].") AND `gateway`='sms' ORDER BY $sidx $sord";
		}
		else
		{
			$q = "SELECT * FROM `gs_object_cmd_exec` WHERE `imei` IN (".getUserObjectIMEIs($user_id).") AND `gateway`='sms' ORDER BY $sidx $sord";
		}
		
		$r = mysqli_query($ms, $q);
		
		$response = new stdClass();
		$response->page = 1;
		//$response->total = $count;
		$response->records = $count;
		
		if ($r)
		{
			$i=0;
			while($row = mysqli_fetch_array($r))
			{
				$cmd_id = $row['cmd_id'];
				$time = convUserTimezone($row['dt_cmd']);
				$object = getObjectName($row['imei']);
				
				$name = $row['name'];
				$type = strtoupper($row['type']);
				$cmd = $row['cmd'];
				
				if ($row['status'] == 0)
				{
					$status = '<span class="spinner" style="height: 3px;"></span>';
				}
				else if ($row['status'] == 1)
				{
					$status = '<img src="theme/images/tick-green.svg" />';
				}
				
				$re_hex = $row['re_hex'];
				
				// set modify buttons
				$modify = '<a href="#" onclick="cmdSMSExecDelete(\''.$cmd_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				// set row
				$response->rows[$i]['id']=$cmd_id;
				$response->rows[$i]['cell']=array($time,$object,$name,$cmd,$status,$modify,$re_hex);
				$i++;
			}	
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
	
	if(@$_POST['cmd'] == 'exec_cmd_gprs')
	{
		$imei = $_POST["imei"];
		$name = $_POST["name"];
		$type = $_POST["type"];
		$cmd_ = $_POST["cmd_"];
		
		$imeis = explode(',', $imei);
		
		for ($i = 0; $i < count($imeis); ++$i)
		{
			sendObjectGPRSCommand($user_id, $imeis[$i], $name, $type, $cmd_);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'exec_cmd_sms')
	{
		$result = false;
		
		$imei = $_POST["imei"];
		$name = $_POST["name"];
		$cmd_ = $_POST["cmd_"];
		
		$imeis = explode(',', $imei);
		
		for ($i = 0; $i < count($imeis); ++$i)
		{
			$result = sendObjectSMSCommand($user_id, $imeis[$i], $name, $cmd_);
			
			if ($result == false)
			{
				break;
			}
		}
		
		if ($result == false)
		{
			echo 'ERROR_NOT_SENT';
			die;
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_cmd_exec')
	{
		$cmd_id = $_POST["cmd_id"];
		
		$q = "DELETE FROM `gs_object_cmd_exec` WHERE `cmd_id`='".$cmd_id."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_cmd_execs')
	{
		$items = $_POST["items"];
		
		for ($i = 0; $i < count($items); ++$i)
		{
			$item = $items[$i];
			
			$q = "DELETE FROM `gs_object_cmd_exec` WHERE `cmd_id`='".$item."' AND `user_id`='".$user_id."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_GET['cmd'] == 'load_cmd_schedule_list')
	{			
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx =1;
		
		// get records number
		$q = "SELECT * FROM `gs_user_cmd_schedule` WHERE `user_id`='".$user_id."'";
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
		
		$q = "SELECT * FROM `gs_user_cmd_schedule` WHERE `user_id`='".$user_id."' ORDER BY $sidx $sord LIMIT $start, $limit";
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
				$cmd_id = $row['cmd_id'];
				$name = $row['name'];
				
				if ($row['exact_time'] == 'true')
				{
					$schedule = $la['EXACT_TIME'];
				}
				else
				{
					$schedule = $la['RECURRING'];
				}
				
				$gateway = strtoupper($row['gateway']);
				$type = strtoupper($row['type']);
				$cmd = $row['cmd'];
				
				if ($row['active'] == 'true')
				{
					$active = '<img src="theme/images/tick-green.svg" />';
				}
				else
				{
					$active = '<img src="theme/images/remove-red.svg" style="width:12px;" />';
				}
				
				// set modify buttons
				$modify = '<a href="#" onclick="cmdScheduleProperties(\''.$cmd_id.'\');" title="'.$la['EDIT'].'"><img src="theme/images/edit.svg" />';
				$modify .= '<a href="#" onclick="cmdScheduleDelete(\''.$cmd_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				// set row
				$response->rows[$i]['id']=$cmd_id;
				$response->rows[$i]['cell']=array($name,$active,$schedule,$gateway,$type,$cmd,$modify);
				$i++;
			}	
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
	
	if(@$_POST['cmd'] == 'load_cmd_schedule')
	{
		$result = array();
		
		$cmd_id = $_POST['cmd_id'];
		
		$q = "SELECT * FROM `gs_user_cmd_schedule` WHERE `cmd_id`='".$cmd_id."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
			
		$day_time = json_decode($row['day_time'], true);
		
		$result = array('name' => $row['name'],
				'active' => $row['active'],
				'exact_time' => $row['exact_time'],
				'exact_time_dt' => $row['exact_time_dt'],
				'day_time' => $day_time,
				'protocol' => $row['protocol'],
				'imei' => $row['imei'],
				'gateway' => $row['gateway'],
				'type' => $row['type'],
				'cmd' => $row['cmd']);
		
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'save_cmd_schedule')
	{
		$cmd_id = $_POST["cmd_id"];
		$name = $_POST["name"];
		$active = $_POST["active"];
		$exact_time = $_POST["exact_time"];
		$exact_time_dt = $_POST["exact_time_dt"];
		$day_time = $_POST["day_time"];
		$protocol = $_POST["protocol"];
		$imei = $_POST["imei"];
		$gateway = $_POST["gateway"];
		$type = $_POST["type"];
		$cmd_ = $_POST["cmd_"];
		
		if ($cmd_id == 'false')
		{
			$q = "INSERT INTO `gs_user_cmd_schedule`(`user_id`,
								`name`,
								`active`,
								`exact_time`,
								`exact_time_dt`,
								`day_time`,
								`protocol`,
								`imei`,
								`gateway`,
								`type`,
								`cmd`)
								VALUES
								('".$user_id."',
								'".$name."',
								'".$active."',
								'".$exact_time."',
								'".$exact_time_dt."',
								'".$day_time."',
								'".$protocol."',
								'".$imei."',
								'".$gateway."',
								'".$type."',
								'".$cmd_."')";	
		}
		else
		{
			$q = "UPDATE `gs_user_cmd_schedule` SET 	`name`='".$name."',
									`active`='".$active."',
									`exact_time`='".$exact_time."',
									`exact_time_dt`='".$exact_time_dt."',
									`day_time`='".$day_time."',
									`protocol`='".$protocol."',
									`imei`='".$imei."',
									`protocol`='".$protocol."',
									`gateway`='".$gateway."',
									`type`='".$type."',
									`cmd`='".$cmd_."',
									`dt_schedule_e`='',
									`dt_schedule_d`=''
									WHERE `cmd_id`='".$cmd_id."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_cmd_schedule')
	{
		$cmd_id = $_POST["cmd_id"];
		
		$q = "DELETE FROM `gs_user_cmd_schedule` WHERE `cmd_id`='".$cmd_id."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_cmd_schedules')
	{
		$items = $_POST["items"];
		
		for ($i = 0; $i < count($items); ++$i)
		{
			$item = $items[$i];
			
			$q = "DELETE FROM `gs_user_cmd_schedule` WHERE `cmd_id`='".$item."' AND `user_id`='".$user_id."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_GET['cmd'] == 'load_cmd_template_list')
	{			
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx =1;
		
		// get records number
		$q = "SELECT * FROM `gs_user_cmd` WHERE `user_id`='".$user_id."'";
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
		
		$q = "SELECT * FROM `gs_user_cmd` WHERE `user_id`='".$user_id."' ORDER BY $sidx $sord LIMIT $start, $limit";
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
				$cmd_id = $row['cmd_id'];
				$name = $row['name'];
				$protocol = $row['protocol'];
				$gateway = strtoupper($row['gateway']);
				$type = strtoupper($row['type']);
				$cmd = $row['cmd'];			
				
				// set modify buttons
				$modify = '<a href="#" onclick="cmdTemplateProperties(\''.$cmd_id.'\');" title="'.$la['EDIT'].'"><img src="theme/images/edit.svg" />';
				$modify .= '<a href="#" onclick="cmdTemplateDelete(\''.$cmd_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				// set row
				$response->rows[$i]['id']=$cmd_id;
				$response->rows[$i]['cell']=array($name,$protocol,$gateway,$type,$cmd,$modify);
				$i++;
			}	
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
	
	if(@$_POST['cmd'] == 'load_cmd_template_data')
	{
		$q = "SELECT * FROM `gs_user_cmd` WHERE `user_id`='".$user_id."' ORDER BY `cmd_id` ASC";
		$r = mysqli_query($ms, $q);
		
		$result = array();
		
		while($row=mysqli_fetch_array($r))
		{		
			$cmd_id = $row['cmd_id'];
			$result[$cmd_id] = array(	'name' => $row['name'],
							'protocol' => $row['protocol'],
							'gateway' => $row['gateway'],
							'type' => $row['type'],
							'cmd' => $row['cmd']
							);
		}
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'save_cmd_template')
	{
		$cmd_id = $_POST["cmd_id"];
		$name = $_POST["name"];
		$protocol = $_POST["protocol"];
		$gateway = $_POST["gateway"];
		$type = $_POST["type"];
		$cmd_ = $_POST["cmd_"];
		
		if ($cmd_id == 'false')
		{
			$q = "INSERT INTO `gs_user_cmd`(`user_id`,
							`name`,
							`protocol`,
							`gateway`,
							`type`,
							`cmd`)
							VALUES
							('".$user_id."',
							'".$name."',
							'".$protocol."',
							'".$gateway."',
							'".$type."',
							'".$cmd_."')";	
		}
		else
		{
			$q = "UPDATE `gs_user_cmd` SET 	`name`='".$name."',
							`protocol`='".$protocol."',
							`gateway`='".$gateway."',
							`type`='".$type."',
							`cmd`='".$cmd_."'
							WHERE `cmd_id`='".$cmd_id."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_cmd_template')
	{
		$cmd_id = $_POST["cmd_id"];
		
		$q = "DELETE FROM `gs_user_cmd` WHERE `cmd_id`='".$cmd_id."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_cmd_templates')
	{
		$items = $_POST["items"];
		
		for ($i = 0; $i < count($items); ++$i)
		{
			$item = $items[$i];
			
			$q = "DELETE FROM `gs_user_cmd` WHERE `cmd_id`='".$item."' AND `user_id`='".$user_id."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
?>