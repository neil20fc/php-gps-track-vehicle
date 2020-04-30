<?
	set_time_limit(0);
	
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	include ('../tools/email.php');
	include ('../tools/sms.php');
	checkUserSession();
	checkUserCPanelPrivileges();
	
	loadLanguage($_SESSION["language"], $_SESSION["units"]);
        
        if(@$_GET['cmd'] == 'load_object_list')
	{ 
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		$search = caseToUpper(@$_GET['s']); // get search
		$manager_id = @$_GET['manager_id'];
		
		if(!$sidx) $sidx = 1;
		
		// check if admin or manager
		if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin'))
		{
			if ($manager_id == 0)
			{
				$q = "SELECT * FROM `gs_objects` WHERE UPPER(`imei`) LIKE '%$search%' OR UPPER(`name`) LIKE '%$search%' OR UPPER(`protocol`) LIKE '%$search%' OR UPPER(`sim_number`) LIKE '%$search%'";
			}
			else
			{
				$q = "SELECT * FROM `gs_objects` WHERE `manager_id`='".$manager_id."' AND (UPPER(`imei`) LIKE '%$search%' OR UPPER(`name`) LIKE '%$search%' OR UPPER(`protocol`) LIKE '%$search%' OR UPPER(`sim_number`) LIKE '%$search%')";
			}
		}
		else
		{
			$q = "SELECT * FROM `gs_objects` WHERE `manager_id`='".$_SESSION["cpanel_manager_id"]."' AND (UPPER(`imei`) LIKE '%$search%' OR UPPER(`name`) LIKE '%$search%' OR UPPER(`protocol`) LIKE '%$search%' OR UPPER(`sim_number`) LIKE '%$search%')";
		}
		
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		if ($count > 0) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 1;
		}
		
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
		
		$q .= " ORDER BY $sidx $sord LIMIT $start, $limit";
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
				
				if ($row['active'] == 'true')
				{
					$active = '<a href="#" onclick="objectDeactivate(\''.$imei.'\');" title="'.$la['DEACTIVATE'].'"><img src="theme/images/tick-green.svg" /></a>';
				}
				else
				{
					$active = '<a href="#" onclick="objectActivate(\''.$imei.'\');" title="'.$la['ACTIVATE'].'"><img src="theme/images/remove-red.svg" style="width:12px;" />';
				}
				
				$expires_on = '';
				
				if ($row['object_expire'] == 'true')
				{
					if (strtotime($row['object_expire_dt']) > 0)
					{
						$expires_on = $row['object_expire_dt'];
					}
				}
				
				$last_connection = $row['dt_server'];
				$dt_now = gmdate("Y-m-d H:i:s");
				
				$dt_difference = strtotime($dt_now) - strtotime($last_connection);
				if($dt_difference < $gsValues['CONNECTION_TIMEOUT'] * 60)
				{
					$loc_valid = $row['loc_valid'];
					
					if ($loc_valid == 1)
					{
						$status = '<img src="theme/images/connection-gsm-gps.svg" />';
					}
					else
					{
						$status = '<img src="theme/images/connection-gsm.svg" />';
					}
				}
				else
				{
					$status = '<img src="theme/images/connection-no.svg" />';
				}
				
				$last_connection = convUserTimezone($last_connection);
				
				$protocol = $row['protocol'];
				$net_protocol = strtoupper($row['net_protocol']);
				$port = $row['port'];
				
				$used_in = '';
				
				$q2 = "SELECT * FROM `gs_user_objects` WHERE `imei`='".$imei."' ORDER BY `user_id` ASC";
				$r2 = mysqli_query($ms, $q2);
				
				if (mysqli_num_rows($r2) > 0)
				{
					while($row2 = mysqli_fetch_array($r2))
					{
						$user = getUserData($row2['user_id']);
						
						if ($_SESSION["cpanel_privileges"] == 'super_admin')
						{
							$used_in .= '<a href="#" onclick="userEdit(\''.$user['user_id'].'\');">'.$user['username'].'</a>, ';
						}
						else if ($_SESSION["cpanel_privileges"] == 'admin')
						{
							if ($user['privileges'] == 'super_admin')
							{
								$used_in .= $user['username'].', ';
							}
							else if (($user['privileges'] == 'admin') && ($user['user_id'] != $_SESSION["cpanel_user_id"]))
							{
								$used_in .= $user['username'].', ';
							}
							else
							{
								$used_in .= '<a href="#" onclick="userEdit(\''.$user['user_id'].'\');">'.$user['username'].'</a>, ';	
							}
						}
						else
						{
							if ($user['manager_id'] == $_SESSION["cpanel_manager_id"])
							{
								$used_in .= '<a href="#" onclick="userEdit(\''.$user['user_id'].'\');">'.$user['username'].'</a>, ';
							}
						}
					}
					$used_in = rtrim($used_in, ', ');
				}
				
				// set modify buttons
				$modify = '<a href="#" onclick="objectEdit(\''.$imei.'\');" title="'.$la['EDIT'].'"><img src="theme/images/edit.svg" /></a>';
				$modify .= '<a href="#" onclick="objectClearHistory(\''.$imei.'\');" title="'.$la['CLEAR_HISTORY'].'"><img src="theme/images/erase.svg" /></a>';
				$modify .= '<a href="#" onclick="objectDelete(\''.$imei.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				
				// set row
				$response->rows[$i]['id']=$imei;
				$response->rows[$i]['cell']=array($row['name'],$row['imei'],$active,$expires_on,$row['sim_number'],$last_connection,$protocol,$net_protocol,$port,$status,$used_in,$modify);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
	
	if(@$_GET['cmd'] == 'load_unused_object_list')
	{ 
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		$search = caseToUpper(@$_GET['s']); // get search
		
		if(!$sidx) $sidx = 1;
		
		$q = "SELECT * FROM `gs_objects_unused` WHERE UPPER(`imei`) LIKE '%$search%' OR UPPER(`protocol`) LIKE '%$search%'";
		
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		if ($count > 0) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 1;
		}
		
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
		
		$q .= " ORDER BY $sidx $sord LIMIT $start, $limit";
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
				
				$last_connection = $row['dt_server'];
				$last_connection = convUserTimezone($last_connection);
				
				$protocol = $row['protocol'];
				$net_protocol = strtoupper($row['net_protocol']);
				$port = $row['port'];
				$count = $row['count'];
				
				// set modify buttons
				$modify = '<a href="#" onclick="unusedObjectDelete(\''.$imei.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				
				// set row
				$response->rows[$i]['id']=$imei;
				$response->rows[$i]['cell']=array($row['imei'],$last_connection,$protocol,$net_protocol,$port,$count,$modify);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
        
	if(@$_GET['cmd'] == 'load_object_search_list')
	{
		$result = array();
		
		$search = caseToUpper(@$_GET['search']);
		$manager_id = @$_GET['manager_id'];
		
		// check if admin or manager
		if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin'))
		{
			if ($manager_id == 0)
			{
				$q = "SELECT * FROM `gs_objects` WHERE UPPER(`imei`) LIKE '%$search%' OR UPPER(`name`) LIKE '%$search%'";
			}
			else
			{
				$q = "SELECT * FROM `gs_objects` WHERE `manager_id`='".$manager_id."' AND (UPPER(`imei`) LIKE '%$search%' OR UPPER(`name`) LIKE '%$search%')";
			}
		}
		else
		{
			$q = "SELECT * FROM `gs_objects` WHERE `manager_id`='".$_SESSION["cpanel_manager_id"]."' AND (UPPER(`imei`) LIKE '%$search%' OR UPPER(`name`) LIKE '%$search%')";
		}
		
		$q .= " ORDER BY name ASC";
		
		$r = mysqli_query($ms, $q);
		
		while($row = mysqli_fetch_array($r))
		{
			$data['value'] = $row['imei'];
			$data['text'] = stripslashes($row['name']);
			$result[] = $data;	
		}
		
		header('Content-type: application/json');
		echo json_encode($result);
		die;
	}
        
        if(@$_POST['cmd'] == 'add_object')
	{
		$active = $_POST['active'];
		$object_expire = $_POST['object_expire'];
		$object_expire_dt = $_POST['object_expire_dt'];
		$name = $_POST['name'];
		$imei = strtoupper($_POST['imei']);
		$model = $_POST['model'];
		$vin = $_POST['vin'];
		$plate_number = $_POST['plate_number'];
		$device = $_POST['device'];
		$sim_number = $_POST['sim_number'];
		$manager_id = $_POST['manager_id'];
		$user_ids = $_POST['user_ids'];
		
		$user_ids_ = json_decode(stripslashes($user_ids),true);
		
		if ($imei != "")
		{
			if (checkObjectLimitSystem())
			{
				echo 'ERROR_SYSTEM_OBJECT_LIMIT';
				die;
			}
			
			if (!checkObjectExistsSystem($imei))
			{
				// check if admin or manager
				if ($_SESSION["cpanel_privileges"] == 'manager')
				{
					$manager_id = $_SESSION["cpanel_manager_id"];
					
					// check object limit
					$q = "SELECT * FROM `gs_objects` WHERE `manager_id`='".$manager_id."'";
					$r = mysqli_query($ms, $q);
					$num = mysqli_num_rows($r);
					
					if ($_SESSION["obj_add"] == 'true')
					{
						if ($_SESSION["obj_limit"] == 'true')
						{
							if($num >= $_SESSION["obj_limit_num"])
							{
								echo 'ERROR_OBJECT_LIMIT';
								die;
							}
						}
						
						if ($_SESSION["obj_days"] == 'true')
						{
							if (($object_expire == 'false') || ($object_expire_dt == ''))
							{
								echo 'ERROR_EXPIRATION_DATE_NOT_SET';
								die;
							}
							
							if (strtotime($_SESSION["obj_days_dt"]) < strtotime($object_expire_dt))
							{
								echo 'ERROR_EXPIRATION_DATE_TOO_LATE';
								die;
							}
						}
					}
					else
					{
						echo 'ERROR_NO_PRIVILEGES';
						die;
					}
				}
				
				addObjectSystemExtended($name, $imei, $model, $vin, $plate_number, $device, $sim_number, $active, $object_expire, $object_expire_dt, $manager_id);
				
				createObjectDataTable($imei);
				
				for($i=0; $i<count($user_ids_); $i++)
				{
					$user_id = $user_ids_[$i];
									
					addObjectUser($user_id, $imei, 0, 0, 0);
				}
				
				echo 'OK';
			}
			else
			{
				echo 'ERROR_IMEI_EXISTS';
			}
		}
		die;
	}
        
        if(@$_POST['cmd'] == 'load_object_data')
	{
		$imei = $_POST['imei'];
		
		checkCPanelToObjectPrivileges($imei);
		
		// get users where object is available
		$users = array();
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `imei`='".$imei."' ORDER BY `user_id` ASC";
		$r = mysqli_query($ms, $q);
		
		while($row = mysqli_fetch_array($r))
		{
			$q2 = "SELECT * FROM `gs_users` WHERE `id`='".$row['user_id']."'";
			$r2 = mysqli_query($ms, $q2);
			$row2 = mysqli_fetch_array($r2);
			
			$data['value'] = $row['user_id'];
			$data['text'] = stripslashes($row2['username']);
			$users[] = $data;
		}
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		$result = array('active' => $row["active"],
				'object_expire' => $row["object_expire"],
				'object_expire_dt' => $row["object_expire_dt"],
				'name' => $row["name"],
				'imei' => $row["imei"],
				'model' => $row["model"],
				'vin' => $row["vin"],
				'plate_number' => $row["plate_number"],
				'device' => $row["device"],
				'sim_number' => $row["sim_number"],
				'manager_id' => $row["manager_id"],
				'users' => $users
				);
		echo json_encode($result);
		die;
	}
        
        if(@$_POST['cmd'] == 'edit_object')
	{
		$active = $_POST['active'];
		$object_expire = $_POST['object_expire'];
		$object_expire_dt = $_POST['object_expire_dt'];
		$name = $_POST['name'];
		$imei = strtoupper($_POST['imei']);
		$new_imei = strtoupper($_POST['new_imei']);
		$model = $_POST['model'];
		$vin = $_POST['vin'];
		$plate_number = $_POST['plate_number'];
		$device = $_POST['device'];
		$sim_number = $_POST['sim_number'];
		$manager_id = $_POST['manager_id'];
		$user_ids = $_POST['user_ids'];
		
		checkCPanelToObjectPrivileges($imei);
		
		// change imei
		if ($new_imei != '')
		{			
			if (changeObjectIMEI($imei, $new_imei))
			{
				$imei = $new_imei;	
			}
			else
			{
				echo 'ERROR_IMEI_EXISTS';
				die;
			}
		}
		
		if ($_SESSION["cpanel_privileges"] == 'manager')
		{
			$manager_id = $_SESSION["cpanel_manager_id"];
			
			if ($_SESSION["obj_days"] == 'true')
			{
				if (($object_expire == 'false') || ($object_expire_dt == ''))
				{
					echo 'ERROR_EXPIRATION_DATE_NOT_SET';
					die;
				}
				
				if (strtotime($_SESSION["obj_days_dt"]) < strtotime($object_expire_dt))
				{
					echo 'ERROR_EXPIRATION_DATE_TOO_LATE';
					die;
				}	
			}
		}
		
		$q = "UPDATE `gs_objects` SET 	`name`='".$name."',
						`model`='".$model."',
						`vin`='".$vin."',
						`plate_number`='".$plate_number."',
						`device`='".$device."',
						`sim_number`='".$sim_number."',
						`active`='".$active."',
						`object_expire`='".$object_expire."',
						`object_expire_dt`='".$object_expire_dt."',
						`manager_id`='".$manager_id."' WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		// get object group, driver and trailer settings (we do not want to to lose them)
		$gs_user_objects = array();
				
		$q = "SELECT * FROM `gs_user_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		while($row = mysqli_fetch_array($r))
		{
			$gs_user_objects[] = $row;
		}
		
		// delete object from all users 
		$q = "DELETE FROM `gs_user_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		// add object to all users
		$user_ids_ = json_decode(stripslashes($user_ids),true);
		
		for($i=0; $i<count($user_ids_); $i++)
		{
			$user_id = $user_ids_[$i];
			
			$group_id = 0;
			$driver_id = 0;
			$trailer_id = 0;
			
			for($j=0; $j<count($gs_user_objects); $j++)
			{
				if ($gs_user_objects[$j]['user_id'] == $user_id)
				{
					$group_id = $gs_user_objects[$j]['group_id'];
					$driver_id = $gs_user_objects[$j]['driver_id'];
					$trailer_id = $gs_user_objects[$j]['trailer_id'];
				}
			}
							
			addObjectUser($user_id, $imei, $group_id, $driver_id, $trailer_id);
		}
		
		echo 'OK';
		die;
	}
        
        if(@$_POST['cmd'] == 'clear_history_object')
	{
		$imei = $_POST['imei'];
		
		checkCPanelToObjectPrivileges($imei);
		
		clearObjectHistory($imei);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'clear_history_selected_objects')
	{
		$imeis = $_POST["imeis"];
				
		for ($i = 0; $i < count($imeis); ++$i)
		{
			$imei = $imeis[$i];
			
			checkCPanelToObjectPrivileges($imei);
		
			clearObjectHistory($imei);
		}
		
		echo 'OK';
		die;
	}
        
        if(@$_POST['cmd'] == 'delete_object')
	{
		$imei = $_POST['imei'];
		
		checkCPanelToObjectPrivileges($imei);
		
		delObjectSystem($imei);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'activate_object')
	{
		$imei = $_POST["imei"];
				
		checkCPanelToObjectPrivileges($imei);
		
		$q = "UPDATE `gs_objects` SET `active`='true' WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'deactivate_object')
	{
		$imei = $_POST["imei"];
				
		checkCPanelToObjectPrivileges($imei);
		
		$q = "UPDATE `gs_objects` SET `active`='false' WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'activate_selected_objects')
	{
		$imeis = $_POST["imeis"];
				
		for ($i = 0; $i < count($imeis); ++$i)
		{
			$imei = $imeis[$i];
			
			checkCPanelToObjectPrivileges($imei);
		
			$q = "UPDATE `gs_objects` SET `active`='true' WHERE `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'deactivate_selected_objects')
	{
		$imeis = $_POST["imeis"];
				
		for ($i = 0; $i < count($imeis); ++$i)
		{
			$imei = $imeis[$i];
			
			checkCPanelToObjectPrivileges($imei);
		
			$q = "UPDATE `gs_objects` SET `active`='false' WHERE `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_objects')
	{
		$imeis = $_POST["imeis"];
				
		for ($i = 0; $i < count($imeis); ++$i)
		{
			$imei = $imeis[$i];
			
			checkCPanelToObjectPrivileges($imei);
		
			delObjectSystem($imei);
		}
		
		echo 'OK';
		die;
	}
        
        if(@$_POST['cmd'] == 'delete_unused_object')
	{
		$imei = $_POST['imei'];
		
		$q = "DELETE FROM `gs_objects_unused` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_unused_objects')
	{
		$imeis = $_POST["imeis"];
				
		for ($i = 0; $i < count($imeis); ++$i)
		{
			$imei = $imeis[$i];
			
			$q = "DELETE FROM `gs_objects_unused` WHERE `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
?>