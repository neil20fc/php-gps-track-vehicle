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
        
        if(@$_GET['cmd'] == 'load_user_list')
	{	
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		$search = caseToUpper(@$_GET['s']); // get search
		$manager_id = @$_GET['manager_id'];
		
		if(!$sidx) $sidx = 1;
		
		$q = "SELECT gs_users.*,
			(SELECT COUNT(*) FROM gs_users as b WHERE privileges LIKE '%subuser%' AND b.manager_id = gs_users.id) as subacc_cnt,
			(SELECT COUNT(*) FROM gs_user_objects WHERE gs_user_objects.user_id = gs_users.id) as obj_cnt
			FROM gs_users";
				
		// check if admin or manager
		if ($_SESSION["cpanel_privileges"] == 'super_admin')
		{
			if ($manager_id == 0)
			{				
				$q .= " WHERE `privileges` NOT LIKE ('%subuser%')
				AND (`id` LIKE '%$search%'
				OR UPPER(`privileges`) LIKE '%$search%'
				OR UPPER(`username`) LIKE '%$search%'
				OR UPPER(`email`) LIKE '%$search%')";
			}
			else
			{
				$q .= " WHERE `privileges` NOT LIKE ('%subuser%')
				AND `manager_id`='".$manager_id."'
				AND (`id` LIKE '%$search%'
				OR UPPER(`privileges`) LIKE '%$search%'
				OR UPPER(`username`) LIKE '%$search%'
				OR UPPER(`email`) LIKE '%$search%')";
			}
		}
		else if ($_SESSION["cpanel_privileges"] == 'admin')
		{
			if ($manager_id == 0)
			{				
				$q .= " WHERE `privileges` NOT LIKE ('%subuser%')
				AND  `privileges` NOT LIKE ('%super_admin%')
				AND  (`privileges` NOT LIKE ('%admin%') OR `id`='".$_SESSION["cpanel_user_id"]."' )
				AND (`id` LIKE '%$search%'
				OR UPPER(`privileges`) LIKE '%$search%'
				OR UPPER(`username`) LIKE '%$search%'
				OR UPPER(`email`) LIKE '%$search%')";
			}
			else
			{
				$q .= " WHERE `privileges` NOT LIKE ('%subuser%')
				AND `manager_id`='".$manager_id."'
				AND (`id` LIKE '%$search%'
				OR UPPER(`privileges`) LIKE '%$search%'
				OR UPPER(`username`) LIKE '%$search%'
				OR UPPER(`email`) LIKE '%$search%')";
			}
		}
		else
		{
			$q .= " WHERE `privileges` NOT LIKE ('%subuser%')
			AND `manager_id`='".$_SESSION["cpanel_manager_id"]."'
			AND (`id` LIKE '%$search%'
			OR UPPER(`privileges`) LIKE '%$search%'
			OR UPPER(`username`) LIKE '%$search%'
			OR UPPER(`email`) LIKE '%$search%')";
		}
		
		// search for comments
		if ($search == 'HAS COMMENT')
		{
			$q = substr($q, 0, -1);
			$q .= " OR comment != '')";
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
				if ($row['active'] == 'true')
				{
					$active = '<a href="#" onclick="userDeactivate(\''.$row['id'].'\');" title="'.$la['DEACTIVATE'].'"><img src="theme/images/tick-green.svg" /></a>';
				}
				else
				{
					$active = '<a href="#" onclick="userActivate(\''.$row['id'].'\');" title="'.$la['ACTIVATE'].'"><img src="theme/images/remove-red.svg" style="width:12px;" />';
				}
				
				$expires_on = '';
				
				if ($row['account_expire'] == 'true')
				{
					if (strtotime($row['account_expire_dt']) > 0)
					{
						$expires_on = $row['account_expire_dt'];
					}
				}
				
				$row['privileges'] = json_decode($row['privileges'],true);
				
				$privileges = '';
				if ($row['privileges']['type'] == 'super_admin') {$privileges = $la['SUPER_ADMINISTRATOR'];}
				if ($row['privileges']['type'] == 'admin') {$privileges = $la['ADMINISTRATOR'];}
				if ($row['privileges']['type'] == 'manager') {$privileges = $la['MANAGER'];}
				if ($row['privileges']['type'] == 'user') {$privileges = $la['USER'];}
				if ($row['privileges']['type'] == 'viewer') {$privileges = $la['VIEWER'];}

				if ($row['api'] == 'true')
				{
					$api = '<a href="#" onclick="userAPIDeactivate(\''.$row['id'].'\');" title="'.$la['DEACTIVATE'].'"><img src="theme/images/tick-green.svg" /></a>';
				}
				else
				{
					$api = '<a href="#" onclick="userAPIActivate(\''.$row['id'].'\');" title="'.$la['ACTIVATE'].'"><img src="theme/images/remove-red.svg" style="width:12px;" />';
				}
				
				$dt_reg = convUserTimezone($row['dt_reg']);
				$dt_login = convUserTimezone($row['dt_login']);
				
				$objects = $row['obj_cnt'];
				
				// get gps object number
				$q2 = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$row['id']."'";
				$r2 = mysqli_query($ms, $q2);
				
				// check if any object expire soon
				while($row2 = mysqli_fetch_array($r2))
				{
					$imei = $row2['imei'];
					$q3 = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
					$r3 = mysqli_query($ms, $q3);
					$row3 = mysqli_fetch_array($r3);
					
					if ($row3['object_expire'] == 'true')
					{
						$diff = strtotime($row3['object_expire_dt']) - strtotime(gmdate("Y-m-d"));
						$days = $diff / 86400;
						if ($days < $gsValues['NOTIFY_OBJ_EXPIRE_PERIOD'])
						{
							$objects = '<font color="red">'.$objects.'</font>';
							break;
						}	
					}
				}
				
				// set modify buttons
				$modify = '<a href="#" onclick="userEdit(\''.$row['id'].'\');" title="'.$la['EDIT'].'"><img src="theme/images/edit.svg" /></a>';
				// check if user is not admin or manager, if admin or manager do not show delete button;
				if ($_SESSION["user_id"] != $row['id'])
				{
					$modify .= '<a href="#" onclick="userDelete(\''.$row['id'].'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				}
				$modify .= '<a href="#" onclick="userLogin(\''.$row['id'].'\');" title="'.$la['LOGIN_AS_USER'].'"><img src="theme/images/key.svg" /></a>';
				// set row
				$response->rows[$i]['id']=$row['id'];
				$response->rows[$i]['cell']=array($row['id'],$row['username'],$row['email'],$active,$expires_on,$privileges,$api,$dt_reg,$dt_login,$row['ip'],$row['subacc_cnt'],$objects,$row['usage_email_daily_cnt'],$row['usage_sms_daily_cnt'],$row['usage_api_daily_cnt'],$modify);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
        
        if(@$_GET['cmd'] == 'load_user_search_list')
	{
		$result = array();
		
		$search = caseToUpper(@$_GET['search']);
		$manager_id = @$_GET['manager_id'];
		
		// check if admin or manager
		if ($_SESSION["cpanel_privileges"] == 'super_admin')
		{
			if ($manager_id == 0)
			{				
				$q = "SELECT * FROM `gs_users`
				WHERE `privileges` NOT LIKE ('%subuser%')
				AND (`id` LIKE '%$search%'
				OR UPPER(`username`) LIKE '%$search%'
				OR UPPER(`email`) LIKE '%$search%')";
			}
			else
			{
				$q = "SELECT * FROM `gs_users`
				WHERE `privileges` NOT LIKE ('%subuser%')
				AND `manager_id`='".$manager_id."'
				AND (`id` LIKE '%$search%'
				OR UPPER(`username`) LIKE '%$search%'
				OR UPPER(`email`) LIKE '%$search%')";
			}
		}
		else if ($_SESSION["cpanel_privileges"] == 'admin')
		{
			if ($manager_id == 0)
			{				
				$q = "SELECT * FROM `gs_users`
				WHERE `privileges` NOT LIKE ('%subuser%')
				AND  `privileges` NOT LIKE ('%super_admin%')
				AND (`id` LIKE '%$search%'
				OR UPPER(`username`) LIKE '%$search%'
				OR UPPER(`email`) LIKE '%$search%')";
			}
			else
			{
				$q = "SELECT * FROM `gs_users`
				WHERE `privileges` NOT LIKE ('%subuser%')
				AND  `privileges` NOT LIKE ('%super_admin%')
				AND  `privileges` NOT LIKE ('%admin%')
				AND `manager_id`='".$manager_id."'
				AND (`id` LIKE '%$search%'
				OR UPPER(`username`) LIKE '%$search%'
				OR UPPER(`email`) LIKE '%$search%')";
			}
		}
		else
		{
			$q = "SELECT * FROM `gs_users`
			WHERE `privileges` NOT LIKE ('%subuser%')
			AND `manager_id`='".$_SESSION["cpanel_manager_id"]."'
			AND (`id` LIKE '%$search%'
			OR UPPER(`username`) LIKE '%$search%'
			OR UPPER(`email`) LIKE '%$search%')";
		}
		
		$q .= " ORDER BY username ASC";
		
		$r = mysqli_query($ms, $q);
		
		while($row = mysqli_fetch_array($r))
		{
			$data['value'] = $row['id'];
			$data['text'] = htmlentities(stripslashes($row['username']));
			$result[] = $data;	
		}
		
		echo json_encode($result);
		die;
	}
        
        if(@$_POST['cmd'] == 'register_user')
	{
		$email = $_POST['email'];
		$send = $_POST['send'];
		
		if ($email != '')
		{
			$privileges = array();
			$privileges['type'] = 'user';
			$privileges['map_osm'] = stringToBool($gsValues['USER_MAP_OSM']);
			$privileges['map_bing'] = stringToBool($gsValues['USER_MAP_BING']);
			$privileges['map_google'] = stringToBool($gsValues['USER_MAP_GOOGLE']);
			$privileges['map_google_street_view'] = stringToBool($gsValues['USER_MAP_GOOGLE_STREET_VIEW']);
			$privileges['map_google_traffic'] = stringToBool($gsValues['USER_MAP_GOOGLE_TRAFFIC']);
			$privileges['map_mapbox'] = stringToBool($gsValues['USER_MAP_MAPBOX']);
			$privileges['map_yandex'] = stringToBool($gsValues['USER_MAP_YANDEX']);
			$privileges['history'] = stringToBool($gsValues['HISTORY']);
			$privileges['reports'] = stringToBool($gsValues['REPORTS']);
			$privileges['tasks'] = stringToBool($gsValues['TASKS']);
			$privileges['rilogbook'] = stringToBool($gsValues['RILOGBOOK']);
			$privileges['dtc'] = stringToBool($gsValues['DTC']);
			$privileges['maintenance'] = stringToBool($gsValues['MAINTENANCE']);
			$privileges['object_control'] = stringToBool($gsValues['OBJECT_CONTROL']);
			$privileges['image_gallery'] = stringToBool($gsValues['IMAGE_GALLERY']);
			$privileges['chat'] = stringToBool($gsValues['CHAT']);
			$privileges['subaccounts'] = stringToBool($gsValues['SUBACCOUNTS']);
			$privileges = json_encode($privileges);
				
			// check if admin or manager
			if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin'))
			{
				$manager_id = $_POST['manager_id'];
				
			}
			else
			{
				$manager_id = $_SESSION["cpanel_manager_id"];
			}
			
			$result = addUser($send, 'true', 'false', '', $privileges, $manager_id, $email, $email, '', $gsValues['OBJ_ADD'], $gsValues['OBJ_LIMIT'], $gsValues['OBJ_LIMIT_NUM'], $gsValues['OBJ_DAYS'], $gsValues['OBJ_DAYS_NUM'], $gsValues['OBJ_EDIT'], $gsValues['OBJ_DELETE'], $gsValues['OBJ_HISTORY_CLEAR']);

			echo $result;
		}
		die;
	}
        
        if(@$_POST['cmd'] == 'login_user')
	{
		$id = $_POST["id"];
		
		checkCPanelToUserPrivileges($id);
		
		setUserSession($id);
		
		echo 'OK';
		die;
	}
        
        if(@$_POST['cmd'] == 'load_user_data')
	{
		$id = $_POST['user_id'];
		
		checkCPanelToUserPrivileges($id);
		
		$q = "SELECT * FROM `gs_users` WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		if($row["api"] == "")
		{
			$row["api"] = 'false';
		}
		
		if($row["api_key"] == "")
		{
			$row["api_key"] = genUserAPIKey($row["email"]);
		}
		
		$privileges = array();
		$privileges = json_decode($row['privileges'], true);
		$privileges = checkUserPrivilegesArray($privileges);
		
		if($row["sms_gateway_server"] == "")
		{
			$row["sms_gateway_server"] = 'false';
		}
		
		$info = json_decode($row['info'], true);
		if ($info == null)
		{
			$info = array('name' => '',
				      'company' => '',
				      'address' => '',
				      'post_code' => '',
				      'city' => '',
				      'country' => '',
				      'phone1' => '',
				      'phone2' => '',
				      'email' => ''
				      );
		}
		
		if ($row["obj_history_clear"] == '')
		{
			$row["obj_history_clear"] = 'false';
		}
		
		$result = array('active' => $row["active"],
				'account_expire' => $row["account_expire"],
				'account_expire_dt' => $row["account_expire_dt"],
				'privileges' => $privileges,
				'manager_id' => $row["manager_id"],
				'manager_billing' => $row["manager_billing"],
				'username' => $row["username"],
				'email' => $row["email"],
				'api' => $row["api"],
				'api_key' => $row["api_key"],
				'info' => $info,
				'comment' => $row['comment'],
				'obj_add' => $row["obj_add"],
				'obj_limit' => $row["obj_limit"],
				'obj_limit_num' => $row["obj_limit_num"],
				'obj_days' => $row["obj_days"],
				'obj_days_dt' => $row["obj_days_dt"],
				'obj_edit' => $row["obj_edit"],
				'obj_delete' => $row["obj_delete"],
				'obj_history_clear' => $row["obj_history_clear"],
				'sms_gateway_server' => $row['sms_gateway_server'],
				'places_markers' => $row['places_markers'],
				'places_routes' => $row['places_routes'],
				'places_zones' => $row['places_zones'],
				'usage_email_daily' => $row['usage_email_daily'],
				'usage_sms_daily' => $row['usage_sms_daily'],
				'usage_api_daily' => $row['usage_api_daily']
				);
		echo json_encode($result);
		die;
	}
        
        if(@$_POST['cmd'] == 'edit_user')
	{
		$id = $_POST['id'];
		$active = $_POST['active'];
		$account_expire = $_POST['account_expire'];
		$account_expire_dt = $_POST['account_expire_dt'];
		$privileges = $_POST['privileges'];
		$manager_id = $_POST['manager_id'];
		$manager_billing = $_POST['manager_billing'];
		$username = strtolower($_POST['username']);
		$password = $_POST['password'];
		$email = strtolower($_POST['email']);
		$api = $_POST['api'];
		$api_key = $_POST['api_key'];
		$info = $_POST['info'];
		$comment = $_POST['comment'];
		$obj_add = $_POST['obj_add'];
		$obj_limit = $_POST['obj_limit'];
		$obj_limit_num = $_POST['obj_limit_num'];
		$obj_days = $_POST['obj_days'];
		$obj_days_dt = $_POST['obj_days_dt'];
		$obj_edit = $_POST['obj_edit'];
		$obj_delete = $_POST['obj_delete'];
		$obj_history_clear = $_POST['obj_history_clear'];
		$sms_gateway_server = $_POST['sms_gateway_server'];
		$places_markers = $_POST['places_markers'];
		$places_routes = $_POST['places_routes'];
		$places_zones = $_POST['places_zones'];
		$usage_email_daily = $_POST['usage_email_daily'];
		$usage_sms_daily = $_POST['usage_sms_daily'];
		$usage_api_daily = $_POST['usage_api_daily'];
		
		checkCPanelToUserPrivileges($id);
		
		// check if same username and email is not used by another user
		$q = "SELECT * FROM `gs_users` WHERE `username`='".$username."' AND `id`<>'".$id."' LIMIT 1";
		$r = mysqli_query($ms, $q);
		$num = mysqli_num_rows($r);
		
		if ($num != 0)
		{
			echo 'ERROR_USERNAME_EXISTS';
			die;
		}
		
		$q = "SELECT * FROM `gs_users` WHERE `email`='".$email."' AND `id`<>'".$id."' LIMIT 1";
		$r = mysqli_query($ms, $q);
		$num = mysqli_num_rows($r);
		
		if ($num != 0)
		{
			echo 'ERROR_EMAIL_EXISTS';
			die;
		}
		
		$privileges = json_decode(stripslashes($privileges), true);
		
		if ($_SESSION["cpanel_privileges"] == 'super_admin')
		{
			if ($privileges['type'] == 'manager')
			{
				$manager_id = $id;
			}
		}		
		else if ($_SESSION["cpanel_privileges"] == 'admin')
		{
			// prevents from setting higher privileges
			if ($privileges['type'] == 'super_admin')
			{
				die;
			}
			
			// prevents from settings other user as admin
			if (($privileges['type'] == 'admin') && ($id != $_SESSION["cpanel_user_id"]))
			{
				die;
			}
			
			if ($privileges['type'] == 'manager')
			{
				$manager_id = $id;
			}
		}
		else
		{
			// prevents from setting higher privileges
			if (($privileges['type'] == 'super_admin') || ($privileges['type'] == 'admin'))
			{
				die;
			}
			
			// prevents from settings other user as manager
			if (($privileges['type'] == 'manager') && ($id != $_SESSION["cpanel_user_id"]))
			{
				die;
			}
			
			// prevents from saving to another user
			if ($id == $_SESSION["cpanel_user_id"])
			{
				$privileges['type'] = $_SESSION["cpanel_privileges"];
				$obj_add = $_SESSION["obj_add"];
				$obj_limit = $_SESSION["obj_limit"];
				$obj_limit_num = $_SESSION["obj_limit_num"];
				$obj_days = $_SESSION["obj_days"];
				$obj_days_dt = $_SESSION["obj_days_dt"];
				$manager_billing = $_SESSION["manager_billing"];
			}
			else
			{
				$obj_add = 'false';
				$obj_limit = 'false';
				$obj_limit_num = '';
				$obj_days = 'false';
				$obj_days_dt = '';
			}
			
			$manager_id = $_SESSION["cpanel_manager_id"];
		}
		
		$privileges = json_encode($privileges);
		
		// set expiration, but prevent setting it on own account
		if ($id != $_SESSION["cpanel_user_id"])
		{
			$q = "UPDATE `gs_users` SET 	`active`='".$active."',
							`account_expire`='".$account_expire."',
							`account_expire_dt`='".$account_expire_dt."'
							WHERE `id`='".$id."'";
			$r = mysqli_query($ms, $q);	
		}
		
		// set values
		$q = "UPDATE `gs_users` SET 	`privileges`='".$privileges."',
						`manager_id`='".$manager_id."',
						`manager_billing`='".$manager_billing."',
						`username`='".$username."',
						`email`='".$email."',
						`api`='".$api."',
						`api_key`='".$api_key."',
						`info`='".$info."',
						`comment`='".$comment."',
						`obj_add`='".$obj_add."',
						`obj_limit`='".$obj_limit."',
						`obj_limit_num`='".$obj_limit_num."',
						`obj_days`='".$obj_days."',
						`obj_days_dt`='".$obj_days_dt."',
						`obj_edit`='".$obj_edit."',
						`obj_delete`='".$obj_delete."',
						`obj_history_clear`='".$obj_history_clear."',
						`sms_gateway_server`='".$sms_gateway_server."',
						`places_markers`='".$places_markers."',
						`places_routes`='".$places_routes."',
						`places_zones`='".$places_zones."',
						`usage_email_daily`='".$usage_email_daily."',
						`usage_sms_daily`='".$usage_sms_daily."',
						`usage_api_daily`='".$usage_api_daily."'
						WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		// change password
		if ($password != '')
		{
			$q = "UPDATE `gs_users` SET `password`='".md5($password)."' WHERE `id`='".$id."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
        
        if(@$_POST['cmd'] == 'delete_user')
	{
		$id = $_POST["id"];
		
		checkCPanelToUserPrivileges($id);
		
		delUser($id);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'activate_user')
	{
		$id = $_POST["id"];
				
		if ($_SESSION["user_id"] != $id)
		{
			checkCPanelToUserPrivileges($id);
			
			$q = "UPDATE `gs_users` SET `active`='true' WHERE `id`='".$id."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'deactivate_user')
	{
		$id = $_POST["id"];
				
		if ($_SESSION["user_id"] != $id)
		{
			checkCPanelToUserPrivileges($id);
			
			$q = "UPDATE `gs_users` SET `active`='false' WHERE `id`='".$id."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'activate_selected_users')
	{
		$ids = $_POST["ids"];
				
		for ($i = 0; $i < count($ids); ++$i)
		{
			$id = $ids[$i];
			
			if ($_SESSION["user_id"] != $id)
			{
				checkCPanelToUserPrivileges($id);
				
				$q = "UPDATE `gs_users` SET `active`='true' WHERE `id`='".$id."'";
				$r = mysqli_query($ms, $q);
			}
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'deactivate_selected_users')
	{
		$ids = $_POST["ids"];
				
		for ($i = 0; $i < count($ids); ++$i)
		{
			$id = $ids[$i];
			
			if ($_SESSION["user_id"] != $id)
			{
				checkCPanelToUserPrivileges($id);
				
				$q = "UPDATE `gs_users` SET `active`='false' WHERE `id`='".$id."'";
				$r = mysqli_query($ms, $q);
			}
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'activate_user_api')
	{
		$id = $_POST["id"];

		$q = "UPDATE `gs_users` SET `api`='true' WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'deactivate_user_api')
	{
		$id = $_POST["id"];
					
		$q = "UPDATE `gs_users` SET `api`='false' WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_users')
	{
		$ids = $_POST["ids"];
				
		for ($i = 0; $i < count($ids); ++$i)
		{
			$id = $ids[$i];
			
			if ($_SESSION["user_id"] != $id)
			{
				checkCPanelToUserPrivileges($id);
				
				delUser($id);
			}
		}
		
		echo 'OK';
		die;
	}
        
        if(@$_GET['cmd'] == 'load_user_object_list')
	{ 
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		//$search = caseToUpper(@$_GET['s']); // get search
		$user_id = $_GET['id'];
		
		if(!$sidx) $sidx =1;
		
		// check if admin or manager
		$q = "SELECT gs_objects.*, gs_user_objects.*
			FROM gs_objects
			INNER JOIN gs_user_objects ON gs_objects.imei = gs_user_objects.imei";
				
		if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin'))
		{
			$q .= " WHERE gs_user_objects.user_id='".$user_id."'";
		}
		else
		{
			$q .= " WHERE `manager_id`='".$_SESSION["cpanel_manager_id"]."' AND gs_user_objects.user_id='".$user_id."'";
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
		
		$q .= " ORDER BY gs_objects.$sidx $sord LIMIT $start, $limit";
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
				
				// set modify buttons
				$modify = '<a href="#" onclick="objectEdit(\''.$imei.'\');" title="'.$la['EDIT'].'"><img src="theme/images/edit.svg" /></a>';
				$modify .= '<a href="#" onclick="objectClearHistory(\''.$imei.'\');" title="'.$la['CLEAR_HISTORY'].'"><img src="theme/images/erase.svg" /></a>';
				$modify .= '<a href="#" onclick="userObjectDelete(\''.$imei.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				// set row
				$response->rows[$i]['id']=$imei;
				$response->rows[$i]['cell']=array($row['name'],$imei,$active,$expires_on,$last_connection,$status,$modify);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
        
        if(@$_POST['cmd'] == 'add_user_objects')
	{
		$user_id = $_POST['user_id'];
		$imeis = strtoupper($_POST['imeis']);
		
		$imeis_ = json_decode(stripslashes($imeis),true);
		
		for($i=0; $i<count($imeis_); $i++)
		{		    
			$imei = $imeis_[$i];
					
			// check if admin or manager
			if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin'))
			{
				$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
			}
			else
			{
				$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."' AND `manager_id`='".$_SESSION["cpanel_manager_id"]."'";
			}
			
			$r = mysqli_query($ms, $q);
			$num = mysqli_num_rows($r);
			
			if ($num >= 1)
			{					
				addObjectUser($user_id, $imei, 0, 0, 0);
			}
		}
		
		echo 'OK';
		die;
	}
        
        if(@$_POST['cmd'] == 'delete_user_object')
	{
		$user_id = $_POST['user_id'];
		$imei = $_POST['imei'];
		
		checkCPanelToObjectPrivileges($imei);
		
		delObjectUser($user_id, $imei);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'activate_selected_user_objects')
	{
		$user_id = $_POST['user_id'];
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
	
	if(@$_POST['cmd'] == 'deactivate_selected_user_objects')
	{
		$user_id = $_POST['user_id'];
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
	
	if(@$_POST['cmd'] == 'delete_selected_user_objects')
	{
		$user_id = $_POST['user_id'];
		$imeis = $_POST["imeis"];
				
		for ($i = 0; $i < count($imeis); ++$i)
		{
			$imei = $imeis[$i];
			
			checkCPanelToObjectPrivileges($imei);
		
			delObjectUser($user_id, $imei);
		}
		
		echo 'OK';
		die;
	}
        
        if(@$_GET['cmd'] == 'load_user_subaccount_list')
	{ 
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		//$search = caseToUpper(@$_GET['s']); // get search
		$user_id = $_GET['id'];
		
		if(!$sidx) $sidx =1;
		
		// get records number
		$q = "SELECT * FROM `gs_users` WHERE `privileges` LIKE '%subuser%' AND `manager_id`='".$user_id."'";
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
			while($row = mysqli_fetch_array($r)) {
				$id = $row['id'];
				
				if ($row['active'] == 'true')
				{
					$active = '<a href="#" onclick="userSubaccountDeactivate(\''.$row['id'].'\');" title="'.$la['DEACTIVATE'].'"><img src="theme/images/tick-green.svg" /></a>';
				}
				else
				{
					$active = '<a href="#" onclick="userSubaccountActivate(\''.$row['id'].'\');" title="'.$la['ACTIVATE'].'"><img src="theme/images/remove-red.svg" style="width:12px;" />';
				}
				
				$username = '<input id="dialog_user_edit_subaccount_list_grid_username_'.$id.'" value="'.$row['username'].'" type="text"/>';
				$email = '<input id="dialog_user_edit_subaccount_list_grid_email_'.$id.'" value="'.$row['email'].'" type="text"/>';
				$password = '<input id="dialog_user_edit_subaccount_list_grid_password_'.$id.'" value="" placeholder="Enter new password" type="text"/>';
				
				$ip = $row['ip'];
				
				// set modify buttons
				$modify = '<a href="#" onclick="userSubaccountEdit(\''.$id.'\');" title="'.$la['SAVE'].'"><img src="theme/images/save.svg" /></a>';
				$modify .= '<a href="#" onclick="userSubaccountDelete(\''.$id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				// set row
				$response->rows[$i]['id']=$id;
				$response->rows[$i]['cell']=array($username,$email,$password,$active,$ip,$modify);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
        
        if(@$_POST['cmd'] == 'edit_user_subaccount')
	{
		$id = $_POST['id'];
		$username = strtolower($_POST['username']);
		$email = strtolower($_POST['email']);
		$password = $_POST['password'];
		
		// check if same username and email is not used by another user
		$q = "SELECT * FROM `gs_users` WHERE `username`='".$username."' AND `id`<>'".$id."' LIMIT 1";
		$r = mysqli_query($ms, $q);
		$num = mysqli_num_rows($r);
		
		if ($num != 0)
		{
			echo 'ERROR_USERNAME_EXISTS';
			die;
		}
		
		$q = "SELECT * FROM `gs_users` WHERE `email`='".$email."' AND `id`<>'".$id."' LIMIT 1";
		$r = mysqli_query($ms, $q);
		$num = mysqli_num_rows($r);
		
		if ($num != 0)
		{
			echo 'ERROR_EMAIL_EXISTS';
			die;
		}
		
		$q = "UPDATE `gs_users` SET `username`='".$username."', `email`='".$email."' WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		// change password
		if ($password != '')
		{
			$q = "UPDATE `gs_users` SET `password`='".md5($password)."' WHERE `id`='".$id."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_user_subaccount')
	{
		$id = $_POST['id'];
		
		$q = "DELETE FROM `gs_users` WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'activate_user_subaccount')
	{
		$id = $_POST["id"];
				
		if ($_SESSION["user_id"] != $id)
		{			
			$q = "UPDATE `gs_users` SET `active`='true' WHERE `id`='".$id."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'deactivate_user_subaccount')
	{
		$id = $_POST["id"];
				
		if ($_SESSION["user_id"] != $id)
		{			
			$q = "UPDATE `gs_users` SET `active`='false' WHERE `id`='".$id."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'activate_selected_user_subaccounts')
	{
		$ids = $_POST["ids"];
				
		for ($i = 0; $i < count($ids); ++$i)
		{
			$id = $ids[$i];
			
			$q = "UPDATE `gs_users` SET `active`='true' WHERE `id`='".$id."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'deactivate_selected_user_subaccounts')
	{
		$ids = $_POST["ids"];
				
		for ($i = 0; $i < count($ids); ++$i)
		{
			$id = $ids[$i];
			
			$q = "UPDATE `gs_users` SET `active`='false' WHERE `id`='".$id."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_user_subaccounts')
	{
		$ids = $_POST["ids"];
				
		for ($i = 0; $i < count($ids); ++$i)
		{
			$id = $ids[$i];
			
			$q = "DELETE FROM `gs_users` WHERE `id`='".$id."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
        
        if(@$_GET['cmd'] == 'load_user_billing_plan_list')
	{
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		//$search = caseToUpper(@$_GET['s']); // get search
		$user_id = $_GET['id'];
		
		if(!$sidx) $sidx =1;
				
		// get records number
		$q = "SELECT * FROM `gs_user_billing_plans` WHERE `user_id`='".$user_id."'";
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
			while($row = mysqli_fetch_array($r)) {
				$plan_id = $row['plan_id'];
				$dt_purchase = $row['dt_purchase'];
				$name = $row['name'];
				$objects = $row['objects'];
				$period = $row['period'];
				$period_type = $row['period_type'];
				$price = $row['price'];
				
				$price .= ' '.$gsValues['BILLING_CURRENCY'];
				
				$dt_purchase = convUserTimezone($dt_purchase);
				
				if ($period == 1)
				{
					$period_type = $la[substr(strtoupper($period_type),0,-1)];	
				}
				else
				{
					$period_type = $la[strtoupper($period_type)];	
				}
				
				$period = $period.' '.strtolower($period_type);
				
				// set modify buttons
				$modify = '<a href="#" onclick="userBillingPlanEdit(\''.$plan_id.'\');" title="'.$la['SAVE'].'"><img src="theme/images/edit.svg" /></a>';
				$modify .= '<a href="#" onclick="userBillingPlanDelete(\''.$plan_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				
				// set row
				$response->rows[$i]['id']=$plan_id;
				$response->rows[$i]['cell']=array($dt_purchase,$name,$objects,$period,$price,$modify);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
	
	if(@$_POST['cmd'] == 'add_user_billing_plan')
	{
		$user_id = $_POST['user_id'];
		$dt_purchase = gmdate("Y-m-d H:i:s");
		$name = $_POST["name"];
		$objects = $_POST["objects"];
		$period = $_POST["period"];
		$period_type = $_POST["period_type"];
		$price = $_POST["price"];
		
		$q = "INSERT INTO `gs_user_billing_plans` 	(`user_id`,
								`dt_purchase`,
								`name`,
								`objects`,
								`period`,
								`period_type`,
								`price`
								) VALUES (
								'".$user_id."',
								'".$dt_purchase."',
								'".$name."',
								'".$objects."',
								'".$period."',
								'".$period_type."',
								'".$price."')";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'load_user_billing_plan')
	{
		$result = array();
		
		$plan_id = $_POST['plan_id'];
		
		$q = "SELECT * FROM `gs_user_billing_plans` WHERE `plan_id`='".$plan_id."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		$result = array('name' => $row['name'], 'objects' => $row['objects'], 'period' => $row['period'], 'period_type' => $row['period_type'], 'price' => $row['price']);
		
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'save_user_billing_plan')
	{
		$plan_id = $_POST["plan_id"];
		$name = $_POST["name"];
		$objects = $_POST["objects"];
		$period = $_POST["period"];
		$period_type = $_POST["period_type"];
		$price = $_POST["price"];
		
		$q = "UPDATE `gs_user_billing_plans` SET 	`name`='".$name."', 
								`objects`='".$objects."',
								`period`='".$period."',
								`period_type`='".$period_type."',
								`price`='".$price."'
								WHERE `plan_id`='".$plan_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_user_billing_plan')
	{
		$id = $_POST['id'];
		
		$q = "DELETE FROM `gs_user_billing_plans` WHERE `plan_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_user_billing_plans')
	{
		$ids = $_POST["ids"];
				
		for ($i = 0; $i < count($ids); ++$i)
		{
			$id = $ids[$i];
			
			$q = "DELETE FROM `gs_user_billing_plans` WHERE `plan_id`='".$id."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_GET['cmd'] == 'load_user_usage_list')
	{
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		//$search = caseToUpper(@$_GET['s']); // get search
		$user_id = $_GET['id'];
		
		if(!$sidx) $sidx =1;
				
		// get records number
		$q = "SELECT * FROM `gs_user_usage` WHERE `user_id`='".$user_id."'";
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
			while($row = mysqli_fetch_array($r)) {
				
				$usage_id = $row['usage_id'];
				$date = $row['dt_usage'];
				$login = $row['login'];
				$email = $row['email'];
				$sms = $row['sms'];
				$api = $row['api'];
								
				$date = convUserTimezone($date);
				$date = gmdate("Y-m-d", strtotime($date));
				
				// set modify buttons
				$modify = '<a href="#" onclick="userUsageDelete(\''.$usage_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				
				// set row
				$response->rows[$i]['id']=$usage_id;
				$response->rows[$i]['cell']=array($date,$login,$email,$sms,$api,$modify);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_user_usage')
	{
		$id = $_POST['id'];
		
		$q = "SELECT * FROM `gs_user_usage` WHERE `usage_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		if (strtotime($row['dt_usage']) == strtotime(gmdate("Y-m-d")))
		{
			$q = "UPDATE gs_users SET 	usage_email_daily_cnt=0,
							usage_sms_daily_cnt=0,
							usage_api_daily_cnt=0,
							`dt_usage_d`=''
							WHERE id='".$row['user_id']."'";	
			$r = mysqli_query($ms, $q);
		}
		
		$q = "DELETE FROM `gs_user_usage` WHERE `usage_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_user_usages')
	{
		$ids = $_POST["ids"];
				
		for ($i = 0; $i < count($ids); ++$i)
		{
			$id = $ids[$i];
			
			$q = "SELECT * FROM `gs_user_usage` WHERE `usage_id`='".$id."'";
			$r = mysqli_query($ms, $q);
			$row = mysqli_fetch_array($r);
			
			if (strtotime($row['dt_usage']) == strtotime(gmdate("Y-m-d")))
			{
				$q = "UPDATE gs_users SET 	usage_email_daily_cnt=0,
								usage_sms_daily_cnt=0,
								usage_api_daily_cnt=0,
								`dt_usage_d`=''
								WHERE id='".$row['user_id']."'";	
				$r = mysqli_query($ms, $q);
			}
			
			$q = "DELETE FROM `gs_user_usage` WHERE `usage_id`='".$id."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
?>