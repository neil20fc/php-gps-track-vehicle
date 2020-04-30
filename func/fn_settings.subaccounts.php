<?
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	checkUserSession();
	
	include ('../tools/email.php');
	
	loadLanguage($_SESSION["language"], $_SESSION["units"]);
	
	if(@$_POST['cmd'] == 'load_subaccount_data')
	{
		$manager_id = $_SESSION["user_id"];
		
		$q = "SELECT * FROM `gs_users` WHERE `privileges` LIKE '%subuser%' AND `manager_id`='".$manager_id."' ORDER BY `email` ASC";
		$r = mysqli_query($ms, $q);
		
		$result = array();
		
		while($row=mysqli_fetch_array($r))
		{
			$privileges = json_decode($row['privileges'],true);
			
			$privileges = checkUserPrivilegesArray($privileges);
			
			$imei = $privileges['imei'];
			$marker = $privileges['marker'];
			$route = $privileges['route'];
			$zone = $privileges['zone'];
			$history = $privileges['history'];
			$reports = $privileges['reports'];
			$tasks = $privileges['tasks'];
			$rilogbook = $privileges['rilogbook'];
			$dtc = $privileges['dtc'];
			$maintenance = $privileges['maintenance'];
			$object_control = $privileges['object_control'];
			$image_gallery = $privileges['image_gallery'];
			$chat = $privileges['chat'];
			
			if (!isset($privileges['au_active'])) { $privileges['au_active'] = false; }
			$au_active = $privileges['au_active'];
			
			if (!isset($privileges['au'])) { $privileges['au'] = ''; }
			$au = $privileges['au'];
			
			$subaccount_id = $row['id'];
			$result[$subaccount_id] = array('active' => $row['active'],
							'username' => $row['username'],
							'email' => $row['email'],							
							'account_expire' => $row['account_expire'],
							'account_expire_dt' => $row['account_expire_dt'],
							'history' => $history,
							'reports' => $reports,
							'tasks' => $tasks,
							'rilogbook' => $rilogbook,
							'dtc' => $dtc,
							'maintenance' => $maintenance,
							'object_control' => $object_control,
							'image_gallery' => $image_gallery,
							'chat' => $chat,
							'imei' => $imei,
							'marker' => $marker,
							'route' => $route,
							'zone' => $zone,							
							'au_active' => $au_active,
							'au' => $au
							);
		}
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_subaccount')
	{
		$subaccount_id= $_POST["subaccount_id"];
		$manager_id = $_SESSION["user_id"];
		
		$q = "DELETE FROM `gs_users` WHERE `id`='".$subaccount_id."' AND `manager_id`='".$manager_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_subaccounts')
	{
		$items = $_POST["items"];
		$manager_id = $_SESSION["user_id"];
				
		for ($i = 0; $i < count($items); ++$i)
		{
			$item = $items[$i];
			
			$q = "DELETE FROM `gs_users` WHERE `id`='".$item."' AND `manager_id`='".$manager_id."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'save_subaccount')
	{
		$result = '';
		
		$subaccount_id = $_POST["subaccount_id"];
		$active = $_POST["active"];
		$username = strtolower($_POST["username"]);
		$email = strtolower($_POST["email"]);
		$password = $_POST["password"];
		$send = $_POST["send"];
		$account_expire = $_POST["account_expire"];
		$account_expire_dt = $_POST["account_expire_dt"];
			
		$privileges = array();
                $privileges['type'] = 'subuser';
                $privileges['history'] = stringToBool($_POST["history"]);
                $privileges['reports'] = stringToBool($_POST["reports"]);
		$privileges['tasks'] = stringToBool($_POST["tasks"]);
                $privileges['rilogbook'] = stringToBool($_POST["rilogbook"]);
                $privileges['dtc'] = stringToBool($_POST["dtc"]);
		$privileges['maintenance'] = stringToBool($_POST['maintenance']);
                $privileges['object_control'] = stringToBool($_POST["object_control"]);
                $privileges['image_gallery'] = stringToBool($_POST["image_gallery"]);
                $privileges['chat'] = stringToBool($_POST["chat"]);
                $privileges['imei'] = $_POST["imei"];
		$privileges['marker'] = $_POST["marker"];
		$privileges['route'] = $_POST["route"];
		$privileges['zone'] = $_POST["zone"];
		$privileges['au_active'] = stringToBool($_POST["au_active"]);
		$privileges['au'] = $_POST["au"];
		
                $privileges = json_encode($privileges);
		
		$manager_id = $_SESSION["user_id"];
		
		if ($subaccount_id == 'false')
		{						
			$result = addUser($send, $active, $account_expire, $account_expire_dt, $privileges, $manager_id, $username, $email, $password, 'false', 'false', '', 'false', '', 'false', 'false', 'false');
		}
		else
		{
			// check if same username and email is not used by another user
			$q = "SELECT * FROM `gs_users` WHERE `username`='".$username."' AND `id`<>'".$subaccount_id."' LIMIT 1";
			$r = mysqli_query($ms, $q);
			$num = mysqli_num_rows($r);
			
			if ($num != 0)
			{
				echo 'ERROR_USERNAME_EXISTS';
				die;
			}
			
			$q = "SELECT * FROM `gs_users` WHERE `email`='".$email."' AND `id`<>'".$subaccount_id."' LIMIT 1";
			$r = mysqli_query($ms, $q);
			$num = mysqli_num_rows($r);
			
			if ($num != 0)
			{
				echo 'ERROR_EMAIL_EXISTS';
				die;
			}
			
			$q = "UPDATE `gs_users` SET 	`active`='".$active."',
							`account_expire`='".$account_expire."',
							`account_expire_dt`='".$account_expire_dt."',
							`username`='".$username."',
							`email`='".$email."',
							`privileges`='".$privileges."'
							WHERE `id`='".$subaccount_id."' AND `manager_id`='".$manager_id."'";
			$r = mysqli_query($ms, $q);
			
			if ($password != '')
			{
				$q = "UPDATE `gs_users` SET `password`='".md5($password)."' WHERE `id`='".$subaccount_id."' AND `manager_id`='".$manager_id."'";
				$r = mysqli_query($ms, $q);
			}
			
			$result = 'OK';
		}
		
		echo $result;
		die;
	}
	
	if(@$_GET['cmd'] == 'load_subaccount_list')
	{
		$manager_id = $_SESSION["user_id"];
		
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx = 1;
		
		// get records number
		$q = "SELECT * FROM `gs_users` WHERE `privileges` LIKE '%subuser%' AND `manager_id`='".$manager_id."'";
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
		
		$q = "SELECT * FROM `gs_users` WHERE `privileges` LIKE '%subuser%' AND `manager_id`='".$manager_id."' ORDER BY $sidx $sord LIMIT $start, $limit";
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
				$subaccount_id = $row["id"];
				$email = $row['email'];
				
				if ($row['active'] == 'true')
				{
					$active = '<img src="theme/images/tick-green.svg" />';
				}
				else
				{
					$active = '<img src="theme/images/remove-red.svg" style="width:12px;" />';
				}
				
				$privileges = json_decode($row['privileges'],true);
				
				$imeis = explode(",", $privileges['imei']);
				if ($imeis[0] != '')
				{
					$imeis = count($imeis);
				}
				else
				{
					$imeis = 0;
				}
				
				$markers = explode(",", $privileges['marker']);
				if ($markers[0] != '')
				{
					$markers = count($markers);
				}
				else
				{
					$markers = 0;
				}
				
				$routes = explode(",", $privileges['route']);
				if ($routes[0] != '')
				{
					$routes = count($routes);
				}
				else
				{
					$routes = 0;
				}
				
				$zones = explode(",", $privileges['zone']);
				if ($zones[0] != '')
				{
					$zones = count($zones);
				}
				else
				{
					$zones = 0;
				}
				
				$places = $markers.'/'.$routes.'/'.$zones;
				
				// set modify buttons
				$modify = '<a href="#" onclick="settingsSubaccountProperties(\''.$subaccount_id.'\');" title="'.$la['EDIT'].'"><img src="theme/images/edit.svg"/></a>';
				$modify .= '<a href="#" onclick="settingsSubaccountDelete(\''.$subaccount_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg"/></a>';
				// set row
				$response->rows[$i]['id']=$subaccount_id;
				$response->rows[$i]['cell']=array($email,$active,$imeis,$places,$modify);
				$i++;
			}
		}

		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
?>