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
	
	if(@$_POST['cmd'] == 'load_task')
	{
		$task_id = $_POST['task_id'];
		
		$q = "SELECT * FROM `gs_object_tasks` WHERE `task_id`='".$task_id."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		$result = array('name' => $row['name'],
				'imei' => $row['imei'],
				'priority' => $row['priority'],
				'status' => $row['status'],
				'desc' => $row['desc'],
				'start_address' => $row['start_address'],
				'start_lat' => $row['start_lat'],
				'start_lng' => $row['start_lng'],
				'start_from_dt' => $row['start_from_dt'],
				'start_to_dt' => $row['start_to_dt'],
				'end_address' => $row['end_address'],
				'end_lat' => $row['end_lat'],
				'end_lng' => $row['end_lng'],
				'end_from_dt' => $row['end_from_dt'],
				'end_to_dt' => $row['end_to_dt']);
		
		echo json_encode($result);
		die;
	}
        
        if(@$_POST['cmd'] == 'save_task')
	{
		$task_id = $_POST["task_id"];
		$name = $_POST["name"];
		$imei = $_POST["imei"];
		$priority = $_POST["priority"];
		$status = $_POST["status"];
		$desc = $_POST["desc"];
		
		$start_address = $_POST["start_address"];
		$start_lat = $_POST["start_lat"];
		$start_lng = $_POST["start_lng"];
		$start_from_dt = $_POST["start_from_dt"];
		$start_to_dt = $_POST["start_to_dt"];
		$end_address = $_POST["end_address"];
		$end_lat = $_POST["end_lat"];
		$end_lng = $_POST["end_lng"];
		$end_from_dt = $_POST["end_from_dt"];
		$end_to_dt = $_POST["end_to_dt"];
		
		if ($task_id == 'false')
		{
			$q = "INSERT INTO `gs_object_tasks`(	`dt_task`,
                                                                `name`,
                                                                `imei`,
                                                                `priority`,
								`status`,
                                                                `desc`,
								`start_address`,
								`start_lat`,
								`start_lng`,
								`start_from_dt`,
								`start_to_dt`,
								`end_address`,
								`end_lat`,
								`end_lng`,
								`end_from_dt`,
								`end_to_dt`)
                                                                VALUES
                                                                ('".gmdate("Y-m-d H:i:s")."',
                                                                 '".$name."',
                                                                 '".$imei."',
                                                                 '".$priority."',
								 '".$status."',
                                                                 '".$desc."',
								 '".$start_address."',
								 '".$start_lat."',
								 '".$start_lng."',
								 '".$start_from_dt."',
								 '".$start_to_dt."',
								 '".$end_address."',
								 '".$end_lat."',
								 '".$end_lng."',
								 '".$end_from_dt."',
								 '".$end_to_dt."')";
		}
		else
		{
			$q = "UPDATE `gs_object_tasks` SET  	`name`='".$name."',
                                                                `imei`='".$imei."',
                                                                `priority`='".$priority."',
								`status`='".$status."',
                                                                `desc`='".$desc."',
								`start_address`='".$start_address."',
								`start_lat`='".$start_lat."',
								`start_lng`='".$start_lng."',
								`start_from_dt`='".$start_from_dt."',
								`start_to_dt`='".$start_to_dt."',
								`end_address`='".$end_address."',
								`end_lat`='".$end_lat."',
								`end_lng`='".$end_lng."',
								`end_from_dt`='".$end_from_dt."',
								`end_to_dt`='".$end_to_dt."'
                                                                WHERE `task_id`='".$task_id."'";
		}

		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
        
	if(@$_GET['cmd'] == 'load_task_list')
	{
		$imei = @$_GET['imei'];
		
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx =1;
		
		 // get records number		
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT * FROM `gs_object_tasks` WHERE `imei` IN (".$_SESSION["privileges_imei"].")";
		}
		else
		{
			$q = "SELECT * FROM `gs_object_tasks` WHERE `imei` IN (".getUserObjectIMEIs($user_id).")";
		}
		
		if (isset($imei))
		{
			$q .= ' AND `imei`="'.$imei.'"';
		}
		
		if (isset($_GET['dtf']) && isset($_GET['dtt']))
		{
			$q .= " AND dt_task BETWEEN '".convUserUTCTimezone($_GET['dtf'])."' AND '".convUserUTCTimezone($_GET['dtt'])."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		if (!$r){die;}
		
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
				
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT * FROM `gs_object_tasks` WHERE `imei` IN (".$_SESSION["privileges_imei"].")";
		}
		else
		{
			$q = "SELECT * FROM `gs_object_tasks` WHERE `imei` IN (".getUserObjectIMEIs($user_id).")";
		}
		
		if (isset($imei))
		{
			$q .= ' AND `imei`="'.$imei.'"';
		}
		
		if (isset($_GET['dtf']) && isset($_GET['dtt']))
		{
			$q .= " AND dt_task BETWEEN '".convUserUTCTimezone($_GET['dtf'])."' AND '".convUserUTCTimezone($_GET['dtt'])."'";
		}
		
		$q .=  " ORDER BY $sidx $sord LIMIT $start, $limit";
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
				$task_id = $row['task_id'];
				$dt_task = convUserTimezone($row['dt_task']);
                                $name = $row['name'];
				$imei = $row['imei'];
				$start_address = $row["start_address"];
				$end_address = $row["end_address"];
				$priority = $row["priority"];
				$status = $row["status"];
                                
                                $object_name = getObjectName($imei);
				
				if ($priority == 'low')
				{
					$priority = $la['LOW'];
				}
				else if ($priority == 'normal')
				{
					$priority = $la['NORMAL'];
				}
				else if ($priority == 'high')
				{
					$priority = $la['HIGH'];
				}
				
				if ($status == 0)
				{
					$status = $la['NEW'];
				}
				else if ($status == 1)
				{
					$status = $la['IN_PROGRESS'];
				}
				else if ($status == 2)
				{
					$status = $la['COMPLETED'];
				}
				else if ($status == 3)
				{
					$status = $la['FAILED'];
				}
				
				// set modify buttons
                                $modify = '<a href="#" onclick="taskProperties(\''.$task_id.'\');" title="'.$la['EDIT'].'"><img src="theme/images/edit.svg" />';
				$modify .= '</a><a href="#" onclick="tasksDelete(\''.$task_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				
				// set row
				$response->rows[$i]['id']=$task_id;
				$response->rows[$i]['cell']=array($dt_task,$name,$object_name,$start_address,$end_address,$priority,$status,$modify);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_task')
	{
		$task_id = $_POST["task_id"];
		
		$q = "DELETE FROM `gs_object_tasks` WHERE `task_id`='".$task_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_tasks')
	{
		$items = $_POST["items"];
		
		for ($i = 0; $i < count($items); ++$i)
		{
			$item = $items[$i];
			
			$q = "DELETE FROM `gs_object_tasks` WHERE `task_id`='".$item."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_all_tasks')
	{		
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "DELETE FROM `gs_object_tasks` WHERE `imei` IN (".$_SESSION["privileges_imei"].")";
		}
		else
		{
			$q = "DELETE FROM `gs_object_tasks` WHERE `imei` IN (".getUserObjectIMEIs($user_id).")";
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
?>