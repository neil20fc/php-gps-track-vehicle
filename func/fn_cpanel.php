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
	
	if(@$_POST['cmd'] == 'load_cpanel_data')
	{	
		$result = array('user_id' => $_SESSION["cpanel_user_id"],
				'privileges' => $_SESSION["cpanel_privileges"],
				'manager_id' => $_SESSION["manager_id"],
				'obj_add' => $_SESSION["obj_add"],
				'obj_limit' => $_SESSION["obj_limit"],
				'obj_limit_num' => $_SESSION["obj_limit_num"],
				'obj_days' => $_SESSION["obj_days"],
				'obj_days_dt' => $_SESSION["obj_days_dt"],
				'language' => $_SESSION["language"]);
		
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'load_manager_list')
	{			
		$q = "SELECT * FROM `gs_users` WHERE privileges LIKE ('%manager%') ORDER BY `username` ASC";
		$r = mysqli_query($ms, $q);
		
		$result = array();
		
		while($row=mysqli_fetch_array($r))
		{
			$privileges = json_decode($row['privileges'],true);
			
			if ($privileges['type'] == 'manager')
			{
				$manager_id = $row['id'];
				
				// get user number
				$q2 = "SELECT * FROM `gs_users` WHERE `manager_id`='".$manager_id."'";
				$r2 = mysqli_query($ms, $q2);
				$row2 = mysqli_fetch_array($r2);
				
				$user_count = mysqli_num_rows($r2);
				
				// get obj number
				$q2 = "SELECT * FROM `gs_objects` WHERE `manager_id`='".$manager_id."'";
				$r2 = mysqli_query($ms, $q2);
				$obj_count = mysqli_num_rows($r2);
				
				// get obj num
				if ($row["obj_limit"] == 'true')
				{					
					$result[$manager_id] = array('username' => $row['username'].' ('.$user_count.' - '.$obj_count.'/'.$row['obj_limit_num'].')');
				}
				else
				{
					$result[$manager_id] = array('username' => $row['username'].' ('.$user_count.' - '.$obj_count.')');
				}	
			}
		}
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'stats')
	{
		// check if admin or manager
		if ($_SESSION["cpanel_privileges"] == 'super_admin')
		{
			$manager_id = @$_POST['manager_id'];
			
			// switch admin/manager
			if ($manager_id == 0)
			{
				$q_users = "SELECT * FROM `gs_users` WHERE `privileges` NOT LIKE ('%subuser%')";
				$q_objects = "SELECT * FROM `gs_objects`";
				$q_billing = "SELECT * FROM `gs_user_billing_plans`";
			}
			else
			{
				$q_users = "SELECT * FROM `gs_users` WHERE `privileges` NOT LIKE ('%subuser%') AND `manager_id`='".$manager_id."'";
				$q_objects = "SELECT * FROM `gs_objects` WHERE `manager_id`='".$manager_id."'";
				$q_billing = "SELECT gs_user_billing_plans.*, gs_users.manager_id FROM gs_user_billing_plans INNER JOIN gs_users ON gs_user_billing_plans.user_id = gs_users.id WHERE `manager_id`='".$manager_id."'";
			}
		}
		else if ($_SESSION["cpanel_privileges"] == 'admin')
		{
			$manager_id = @$_POST['manager_id'];
			
			// switch admin/manager
			if ($manager_id == 0)
			{
				$q_users = "SELECT * FROM `gs_users` WHERE `privileges` NOT LIKE ('%subuser%') AND `privileges` NOT LIKE ('%super_admin%') AND (`privileges` NOT LIKE ('%admin%') OR `id`='".$_SESSION["cpanel_user_id"]."')";
				$q_objects = "SELECT * FROM `gs_objects`";
				$q_billing = "SELECT * FROM `gs_user_billing_plans`";
			}
			else
			{
				$q_users = "SELECT * FROM `gs_users` WHERE `privileges` NOT LIKE ('%subuser%') AND `manager_id`='".$manager_id."'";
				$q_objects = "SELECT * FROM `gs_objects` WHERE `manager_id`='".$manager_id."'";
				$q_billing = "SELECT gs_user_billing_plans.*, gs_users.manager_id FROM gs_user_billing_plans INNER JOIN gs_users ON gs_user_billing_plans.user_id = gs_users.id WHERE `manager_id`='".$manager_id."'";
			}
		}
		else
		{
			$q_users = "SELECT * FROM `gs_users` WHERE `privileges` NOT LIKE ('%subuser%') AND `manager_id`='".$_SESSION["cpanel_manager_id"]."'";
			$q_objects = "SELECT * FROM `gs_objects` WHERE `manager_id`='".$_SESSION["cpanel_manager_id"]."'";
			$q_billing = "SELECT gs_user_billing_plans.*, gs_users.manager_id FROM gs_user_billing_plans INNER JOIN gs_users ON gs_user_billing_plans.user_id = gs_users.id WHERE `manager_id`='".$_SESSION["cpanel_manager_id"]."'";
		}
	    
		$r = mysqli_query($ms, $q_users);
		$total_users = mysqli_num_rows($r);
		
		$r = mysqli_query($ms, $q_objects);
		$total_objects = mysqli_num_rows($r);
		
		$total_objects_online = 0;
		
		while($row = mysqli_fetch_array($r))
		{            
			$last_connection = $row['dt_server'];
			$dt_now = gmdate("Y-m-d H:i:s");
			
			$dt_difference = strtotime($dt_now) - strtotime($last_connection);
			if($dt_difference < $gsValues['CONNECTION_TIMEOUT'] * 60)
			{
				$total_objects_online += 1;
			}
		}
		
		if ($_SESSION["cpanel_privileges"] == 'manager')
		{
			if ($_SESSION["obj_limit"] == 'true')
			{
				$total_objects .= '/'.$_SESSION["obj_limit_num"];	
			}		
		}
		
		// total unused objects
		$total_unused_objects = 0;
		
		if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin'))
		{
			$q_unused_objects = "SELECT * FROM `gs_objects_unused`";
			$r = mysqli_query($ms, $q_unused_objects);
			$total_unused_objects = mysqli_num_rows($r);
		}
		
		// total billing plans
		$r = mysqli_query($ms, $q_billing);
		$total_billing_plans = mysqli_num_rows($r);
		
		$sms_gateway_total_in_queue = getSMSAPPTotalInQueue($gsValues['SMS_GATEWAY_IDENTIFIER']);
		
		$result = array('total_users' => $total_users,
				'total_objects' => $total_objects,
				'total_objects_online' => $total_objects_online,
				'total_unused_objects' => $total_unused_objects,
				'total_billing_plans' => $total_billing_plans,
				'sms_gateway_total_in_queue' => $sms_gateway_total_in_queue);
		
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'send_email')
	{
		// close connection with web browser and start email sending loop on server side
		ob_start();
		echo 'OK';
		header("Connection: close");
		header("Content-length: " . (string)ob_get_length());
		ob_end_flush();
		
		$manager_id = $_POST['manager_id'];
		$send_to = $_POST['send_to'];
		$user_ids = $_POST['user_ids'];
		$subject = $_POST['subject'];
		$message = $_POST['message'];
		
		$count = 0;
		
		$email_arr = array();
		
		if ($send_to == 'all')
		{
			if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin'))
			{
				if ($manager_id == 0)
				{
					$q = "SELECT * FROM `gs_users` WHERE `privileges` NOT LIKE ('%subuser%')";
				}
				else
				{
					$q = "SELECT * FROM `gs_users` WHERE `manager_id`='".$manager_id."' AND `privileges` NOT LIKE ('%subuser%')";
				}
			}
			else
			{
				$q = "SELECT * FROM `gs_users` WHERE `manager_id`='".$_SESSION["cpanel_manager_id"]."' AND WHERE `privileges` NOT LIKE ('%subuser%')";
			}
			
			$r = mysqli_query($ms, $q);
			
			while($row = mysqli_fetch_array($r))
			{
				$email_arr[] = $row["email"];
			}
			
		}
		else if ($send_to == 'selected')
		{
			$user_ids_ = json_decode(stripslashes($user_ids),true);
			
			foreach ($user_ids_ as $user_id)
			{
				$q = "SELECT * FROM `gs_users` WHERE `id`='".$user_id."'";
				$r = mysqli_query($ms, $q);
				$row = mysqli_fetch_array($r);
				$email_arr[] = $row["email"];
			}
		}
		
		foreach ($email_arr as $email)
		{        
			sendEmail($email, $subject, $message);
			
			$count++;
			if ($count == 50);
			{
				sleep(1);
				$count = 0;
			}
		}
		
		die;
	}
	
	if(@$_POST['cmd'] == 'send_email_test')
	{
		// close connection with web browser and start email sending loop on server side
		ob_start();
		echo 'OK';		
		header("Connection: close");
		header("Content-length: " . (string)ob_get_length());
		ob_end_flush();
		
		$subject = $_POST['subject'];
		$message = $_POST['message'];
		$email = $_SESSION["email"];
		
		sendEmail($email, $subject, $message);
		die;
	}
		
	if(@$_POST['cmd'] == 'get_user_expire_avg')
	{
		$ids = $_POST["ids"];
		
		echo getUserExpireAvgDate($ids);
		die;
	}
	
	if(@$_POST['cmd'] == 'get_object_expire_avg')
	{
		$imeis = $_POST["imeis"];
		
		echo getObjectExpireAvgDate($imeis);
		die;
	}
	
	if(@$_POST['cmd'] == 'set_user_expire_selected')
	{
		$ids = $_POST["ids"];
		$expire = $_POST['expire'];
		$expire_dt = $_POST['expire_dt'];
		
		for ($i = 0; $i < count($ids); ++$i)
		{
			$id = $ids[$i];
			
			if ($_SESSION["user_id"] != $id)
			{
				checkCPanelToUserPrivileges($id);
				
				$q = "UPDATE `gs_users` SET `account_expire`='".$expire."',`account_expire_dt`='".$expire_dt."' WHERE `id`='".$id."'";
				$r = mysqli_query($ms, $q);	
			}
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'set_object_expire_selected')
	{
		$imeis = $_POST["imeis"];
		$expire = $_POST['expire'];
		$expire_dt = $_POST['expire_dt'];
		
		for ($i = 0; $i < count($imeis); ++$i)
		{
			$imei = $imeis[$i];
			
			checkCPanelToObjectPrivileges($imei);
			
			$q = "UPDATE `gs_objects` SET `object_expire`='".$expire."',`object_expire_dt`='".$expire_dt."' WHERE `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
?>