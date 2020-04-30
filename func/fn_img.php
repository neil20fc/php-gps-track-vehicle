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
	
	if(@$_POST['cmd'] == 'delete_img')
	{
		$img_id = $_POST["img_id"];
		
		$q = "SELECT * FROM `gs_object_img` WHERE `img_id`='".$img_id."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		$img_file = $gsValues['PATH_ROOT'].'data/img/'.$row['img_file'];
		if(is_file($img_file))
		{
			@unlink($img_file);
		}
		
		$q = "DELETE FROM `gs_object_img` WHERE `img_id`='".$img_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_imgs')
	{
		$items = $_POST["items"];
		
		for ($i = 0; $i < count($items); ++$i)
		{
			$item = $items[$i];
			
			$q = "SELECT * FROM `gs_object_img` WHERE `img_id`='".$item."'";
			$r = mysqli_query($ms, $q);
			$row = mysqli_fetch_array($r);
			
			$img_file = $gsValues['PATH_ROOT'].'data/img/'.$row['img_file'];
			if(is_file($img_file))
			{
				@unlink($img_file);
			}
			
			$q = "DELETE FROM `gs_object_img` WHERE `img_id`='".$item."'";
			$r = mysqli_query($ms, $q);
		}
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_all_imgs')
	{				
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT * FROM `gs_object_img` WHERE `imei` IN (".$_SESSION["privileges_imei"].")";
		}
		else
		{
			$q = "SELECT * FROM `gs_object_img` WHERE `imei` IN (".getUserObjectIMEIs($user_id).")";
		}
		
		$r = mysqli_query($ms, $q);
		
		if ($r)
		{
			while($row = mysqli_fetch_array($r))
			{
				$q2 = "DELETE FROM `gs_object_img` WHERE `img_id`='".$row['img_id']."'";
				$r2 = mysqli_query($ms, $q2);
				
				$img_file = $gsValues['PATH_ROOT'].'data/img/'.$row['img_file'];
				if(is_file($img_file))
				{
					@unlink($img_file);
				}
			}
		}
		
		echo 'OK';
		die;
	}
        
        if(@$_GET['cmd'] == 'load_img_list')
	{
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		
		if(!$sidx) $sidx =1;
                
                // get records number		
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT * FROM `gs_object_img` WHERE `imei` IN (".$_SESSION["privileges_imei"].")";
		}
		else
		{
			$q = "SELECT * FROM `gs_object_img` WHERE `imei` IN (".getUserObjectIMEIs($user_id).")";
		}
		
		if (isset($_GET['imei']))
		{
			$q .= ' AND `imei`="'.$_GET['imei'].'"';
		}
		
		if (isset($_GET['dtf']) && isset($_GET['dtt']))
		{
			$q .= " AND dt_server BETWEEN '".convUserUTCTimezone($_GET['dtf'])."' AND '".convUserUTCTimezone($_GET['dtt'])."'";
		}
		
		$r = mysqli_query($ms, $q);
		
		if (!$r){die;}
		
		$count = mysqli_num_rows($r);
		
                if ($count > 0) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 1;
		}
		
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
                
		if ($_SESSION["privileges"] == 'subuser')
		{
			$q = "SELECT * FROM `gs_object_img` WHERE `imei` IN (".$_SESSION["privileges_imei"].")";
		}
		else
		{
			$q = "SELECT * FROM `gs_object_img` WHERE `imei` IN (".getUserObjectIMEIs($user_id).")";
		}
		
		if (isset($_GET['imei']))
		{
			$q .= ' AND `imei`="'.$_GET['imei'].'"';
		}
		
		if (isset($_GET['dtf']) && isset($_GET['dtt']))
		{
			$q .= " AND dt_server BETWEEN '".convUserUTCTimezone($_GET['dtf'])."' AND '".convUserUTCTimezone($_GET['dtt'])."'";
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
				$img_id = $row['img_id'];
				$dt_tracker = convUserTimezone($row['dt_tracker']);
				$obj_name = getObjectName($row['imei']);
				
				$img_file = $row['img_file'];
				$lat =  $row['lat'];
				$lng =  $row['lng'];
				$speed =  $row['speed'];
				
				// set modify buttons
				$modify = '<a href="#" onclick="imgDelete(\''.$img_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				// set row
				$response->rows[$i]['id']=$img_id;
				$response->rows[$i]['cell']=array($dt_tracker,$obj_name,$modify,$img_file,$lat,$lng,$speed);
				$i++;
			}	
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
?>
