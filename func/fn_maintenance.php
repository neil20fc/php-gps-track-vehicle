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
        
	if(@$_GET['cmd'] == 'load_maintenance_list')
	{
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx =1;
		
		 // get records number
	
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT gs_objects.*, gs_object_services.*
				FROM gs_objects
				INNER JOIN gs_object_services ON gs_objects.imei = gs_object_services.imei
				WHERE gs_object_services.imei IN (".$_SESSION["privileges_imei"].")";
		}
		else
		{
			$q = "SELECT gs_objects.*, gs_object_services.*
				FROM gs_objects
				INNER JOIN gs_object_services ON gs_objects.imei = gs_object_services.imei
				WHERE gs_object_services.imei IN (".getUserObjectIMEIs($user_id).")";
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
			$q = "SELECT gs_objects.*, gs_object_services.*
				FROM gs_objects
				INNER JOIN gs_object_services ON gs_objects.imei = gs_object_services.imei
				WHERE gs_object_services.imei IN (".$_SESSION["privileges_imei"].")";
		}
		else
		{
			$q = "SELECT gs_objects.*, gs_object_services.*
				FROM gs_objects
				INNER JOIN gs_object_services ON gs_objects.imei = gs_object_services.imei
				WHERE gs_object_services.imei IN (".getUserObjectIMEIs($user_id).")";
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
				$service_id = $row['service_id'];
				$imei = $row['imei'];
				$object_name = getObjectName($imei);
				$name = $row['name'];
				
				$odometer = getObjectOdometer($imei);
				$odometer = floor(convDistanceUnits($odometer, 'km', $_SESSION["unit_distance"]));
				
				$odometer_left = '-';
				
				if ($row['odo'] == 'true')
				{			    
					$row['odo_interval'] = floor(convDistanceUnits($row['odo_interval'], 'km', $_SESSION["unit_distance"]));
					$row['odo_last'] = floor(convDistanceUnits($row['odo_last'], 'km', $_SESSION["unit_distance"]));
					
					$odo_diff = $odometer - $row['odo_last'];
					$odo_diff = $row['odo_interval'] - $odo_diff;
					
					if ($odo_diff <= 0)
					{
						$odo_diff = abs($odo_diff);
						$odometer_left = '<font color="red">'.$la["EXPIRED"].' ('.$odo_diff.' '.$la["UNIT_DISTANCE"].')</font>';
					}
					else
					{
						$odometer_left = $odo_diff.' '.$la["UNIT_DISTANCE"];
					}
				}
				
				$odometer = $odometer.' '.$la["UNIT_DISTANCE"];
				
				$engine_hours = getObjectEngineHours($imei, false);
				
				$engine_hours_left = '-';
				
				if ($row['engh'] == 'true')
				{
					$engh_diff = $engine_hours - $row['engh_last'];
					$engh_diff = $row['engh_interval'] - $engh_diff;
					
					if ($engh_diff <= 0)
					{
						$engh_diff = abs($engh_diff);
						$engine_hours_left = '<font color="red">'.$la["EXPIRED"].' ('.$engh_diff.' '.$la["UNIT_H"].')</font>';
					}
					else
					{
						$engine_hours_left = $engh_diff.' '.$la["UNIT_H"];
					}
				}
				
				$engine_hours = $engine_hours.' '.$la["UNIT_H"];
				
				$days = '-';
				$days_left = '-';
				
				if ($row['days'] == 'true')
				{
					$days_diff = strtotime(gmdate("Y-m-d")) - (strtotime($row['days_last']));					
					$days_diff = floor($days_diff/3600/24);
					$days = $days_diff;
					$days_diff = $row['days_interval'] - $days_diff;
					
					if ($days_diff <= 0)
					{
						$days_left = abs($days_diff);
						$days_left = '<font color="red">'.$la["EXPIRED"].' ('.$days_left.' '.$la["UNIT_D"].')</font>';
					}
					else
					{
						$days_left = $days_diff;
					}
				}				
				
				if (($row['odo_left'] == 'true') || ($row['engh_left'] == 'true') || ($row['days_left'] == 'true'))
				{
					$event = '<img src="theme/images/tick-green.svg" />';
				}
				else
				{
					$event = '<img src="theme/images/remove-red.svg" style="width:12px;" />';
				}
				
				// set modify buttons
				$modify = '<a href="#" onclick="maintenanceServiceProperties(\''.$imei.'\',\''.$service_id.'\');" title="'.$la['EDIT'].'"><img src="theme/images/edit.svg" />';
				$modify .= '</a><a href="#" onclick="maintenanceServiceDelete(\''.$service_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				// set row
				
				// set row
				$response->rows[$i]['id']=$service_id;
				$response->rows[$i]['cell']=array($object_name,$name,$odometer,$odometer_left,$engine_hours,$engine_hours_left,$days,$days_left,$event,$modify);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
	
        if(@$_POST['cmd'] == 'delete_service')
	{
		$service_id = $_POST["service_id"];
		
		$q = "DELETE FROM `gs_object_services` WHERE `service_id`='".$service_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_services')
	{
		$items = $_POST["items"];
		
		for ($i = 0; $i < count($items); ++$i)
		{
			$item = $items[$i];
			
			$q = "DELETE FROM `gs_object_services` WHERE `service_id`='".$item."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
?>