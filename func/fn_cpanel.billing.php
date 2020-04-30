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
	
	if(@$_GET['cmd'] == 'load_billing_plan_list')
	{
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		$search = caseToUpper(@$_GET['s']); // get search
		$manager_id = @$_GET['manager_id'];
		
		if(!$sidx) $sidx =1;
		
		$q = "SELECT gs_user_billing_plans.*, gs_users.privileges, gs_users.manager_id, gs_users.username, gs_users.email
					FROM gs_user_billing_plans
					INNER JOIN gs_users ON gs_user_billing_plans.user_id = gs_users.id";
		
		// check if admin or manager
		if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin'))
		{
			if ($manager_id == 0)
			{
				$q .= "	WHERE UPPER(`username`) LIKE '%$search%'
					OR UPPER(`email`) LIKE '%$search%'
					OR UPPER(`name`) LIKE '%$search%'";
			}
			else
			{
				$q .= "	WHERE `manager_id`='".$manager_id."' AND
					(UPPER(`username`) LIKE '%$search%'
					OR UPPER(`email`) LIKE '%$search%'
					OR UPPER(`name`) LIKE '%$search%')";
			}
		}
		else
		{
			$q .= " WHERE `manager_id`='".$_SESSION["cpanel_manager_id"]."' AND
					(UPPER(`username`) LIKE '%$search%'
					OR UPPER(`email`) LIKE '%$search%'
					OR UPPER(`name`) LIKE '%$search%')";
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
				
				$used_in = '';
				
				$user_id = $row['user_id'];
				$privileges = json_decode($row['privileges'],true);
				$manager_id = $row['manager_id'];
				$username = $row['username'];
				
				if ($_SESSION["cpanel_privileges"] == 'super_admin')
				{
					$used_in = '<a href="#" onclick="userEdit(\''.$user_id.'\');">'.$username.'</a>';
				}
				else if ($_SESSION["cpanel_privileges"] == 'admin')
				{
					if ($privileges["type"] == 'super_admin')
					{
						$used_in = $username;
					}
					else if (($privileges["type"] == 'admin') && ($user_id != $_SESSION["cpanel_user_id"]))
					{
						$used_in = $username;
					}
					else
					{
						$used_in = '<a href="#" onclick="userEdit(\''.$user_id.'\');">'.$username.'</a>';	
					}
				}
				else
				{
					if ($manager_id == $_SESSION["cpanel_manager_id"])
					{
						$used_in = '<a href="#" onclick="userEdit(\''.$user_id.'\');">'.$username.'</a>';
					}
				}
				
				// set modify buttons
				$modify = '<a href="#" onclick="userBillingPlanEdit(\''.$plan_id.'\');" title="'.$la['SAVE'].'"><img src="theme/images/edit.svg" /></a>';
				$modify .= '<a href="#" onclick="userBillingPlanDelete(\''.$plan_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				
				// set row
				$response->rows[$i]['id']=$plan_id;
				$response->rows[$i]['cell']=array($dt_purchase,$name,$objects,$period,$price,$used_in,$modify);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_selected_billing_plans')
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
?>