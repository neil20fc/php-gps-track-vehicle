<?
	set_time_limit(300);
	
	session_start();	
	include ('../init.php');
	include ('fn_common.php');
	include ('fn_route.php');
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
	
	if(@$_POST['cmd'] == 'load_route_data')
	{		
		$imei = $_POST['imei'];
		$dtf = $_POST['dtf'];
		$dtt = $_POST['dtt'];
		$min_stop_duration = $_POST['min_stop_duration'];
		
		if (!checkUserToObjectPrivileges($user_id, $imei))
		{
			die;
		}
		
		$result = getRoute($user_id, $imei, convUserUTCTimezone($dtf), convUserUTCTimezone($dtt), $min_stop_duration, true);		
		
		header('Content-type: application/json');
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_msgs')
	{
		if($_SESSION["obj_history_clear"] == 'true')
		{
			$imei = $_POST["imei"];
			$items = $_POST["items"];
					
			for ($i = 0; $i < count($items); ++$i)
			{
				$item = $items[$i];
				
				$q = "DELETE FROM `gs_object_data_".$imei."` WHERE `dt_tracker`='".$item."'";
				$r = mysqli_query($ms, $q);
			}
			
			echo 'OK';
		}
		
		die;
	}
	
	if(@$_GET['cmd'] == 'load_msg_list_empty')
	{
		$response = new stdClass();
		$response->page = 1;
		$response->total = 1;
		$response->records = 0;
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
	
	if(@$_GET['cmd'] == 'load_msg_list')
	{
		$imei = $_GET['imei'];
		$dtf = convUserUTCTimezone($_GET['dtf']);
		$dtt = convUserUTCTimezone($_GET['dtt']);
		
		if (!checkUserToObjectPrivileges($user_id, $imei))
		{
			die;
		}
		
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx =1;
		
		// get records number
		$q = "SELECT DISTINCT	dt_server,
					dt_tracker,
					lat,
					lng,
					altitude,
					angle,
					speed,
					params
					FROM `gs_object_data_".$imei."` WHERE dt_tracker BETWEEN '".$dtf."' AND '".$dtt."'";
					
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
				$dt_server = convUserTimezone($row['dt_server']);
				$dt_tracker = convUserTimezone($row['dt_tracker']);
				
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
				
				//$response->rows[$i]['id'] = $i;
				$response->rows[$i]['id']=$row['dt_tracker'];
				$response->rows[$i]['cell']=array($dt_tracker, $dt_server, $row['lat'], $row['lng'], $row['altitude'], $row['angle'], $row['speed'], $row['params']);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
?>