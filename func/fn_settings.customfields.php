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

	if(@$_POST['cmd'] == 'delete_object_custom_field')
	{
		$field_id = $_POST["field_id"];
		$imei = $_POST["imei"];
		
		$q = "DELETE FROM `gs_object_custom_fields` WHERE `field_id`='".$field_id."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_object_custom_fields')
	{
		$items = $_POST["items"];
		$imei = $_POST["imei"];
		
		for ($i = 0; $i < count($items); ++$i)
		{
			$item = $items[$i];
			
			$q = "DELETE FROM `gs_object_custom_fields` WHERE `field_id`='".$item."' AND `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'save_object_custom_field')
	{
		$field_id = $_POST["field_id"];
		$imei = $_POST["imei"];
		$name = $_POST["name"];
		$value = $_POST["value"];
		$data_list = $_POST["data_list"];
		$popup = $_POST["popup"];
		
		if ($field_id == 'false')
		{
			$q = "INSERT INTO `gs_object_custom_fields` (`imei`, `name`, `value`, `data_list`, `popup`) VALUES ('".$imei."', '".$name."', '".$value."', '".$data_list."', '".$popup."')";
		}
		else
		{
			$q = "UPDATE `gs_object_custom_fields` SET `imei`='".$imei."', `name`='".$name."', `value`='".$value."', `data_list`='".$data_list."', `popup`='".$popup."' WHERE `field_id`='".$field_id."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
	}
	
	if(@$_GET['cmd'] == 'load_object_custom_field_list')
	{ 
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		$imei = $_GET['imei'];
		
		if(!$sidx) $sidx =1;
		
		// get records number
		$q = "SELECT * FROM `gs_object_custom_fields` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		$q = "SELECT * FROM `gs_object_custom_fields` WHERE `imei`='".$imei."' ORDER BY $sidx $sord";
		$r = mysqli_query($ms, $q);
		
		$response = new stdClass();
		$response->page = 1;
		//$response->total = $count;
		$response->records = $count;
		
		if ($r)
		{
			$i=0;
			while($row = mysqli_fetch_array($r)) {
				$field_id = $row["field_id"];
				$name = $row['name'];
				$value = $row['value'];
				$data_list = $row['data_list'];
				$popup = $row['popup'];
				
				if ($data_list == 'true')
				{
					$data_list = '<img src="theme/images/tick-green.svg" />';
				}
				else
				{
					$data_list = '<img src="theme/images/remove-red.svg" style="width:12px;" />';
				}
				
				if ($popup == 'true')
				{
					$popup = '<img src="theme/images/tick-green.svg" />';
				}
				else
				{
					$popup = '<img src="theme/images/remove-red.svg" style="width:12px;" />';
				}
				
				// set modify buttons
				$modify = '<a href="#" onclick="settingsObjectCustomFieldProperties(\''.$field_id.'\');" title="'.$la['EDIT'].'"><img src="theme/images/edit.svg" />';
				$modify .= '</a><a href="#" onclick="settingsObjectCustomFieldDelete(\''.$field_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				// set row
				$response->rows[$i]['id']=$field_id;
				$response->rows[$i]['cell']=array($name,$value,$data_list,$popup,$modify);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
?>