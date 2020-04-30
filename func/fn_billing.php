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
        
        if(@$_GET['cmd'] == 'load_billing_plan_list')
	{
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		//$search = caseToUpper(@$_GET['s']); // get search
		
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
			while($row = mysqli_fetch_array($r))
			{
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
				if ($objects == 0)
				{
					$modify = '<a href="#" onclick="billingPlanDelete(\''.$plan_id.'\');" title="'.$la['DELETE'].'"><img src="theme/images/remove3.svg" /></a>';
				}
				else
				{
					$modify = '<a href="#" onclick="billingPlanUse(\''.$plan_id.'\');" title="'.$la['USE_PLAN'].'"><img src="theme/images/use-plan.svg" /></a>';
				}
				
				// set row
				$response->rows[$i]['cell']=array($dt_purchase,$name,$objects,$period,$price,$modify);
				$i++;
			}
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		die;
	}
	
	if(@$_POST['cmd'] == 'load_billing_plan_purchase_list')
	{
		$result = '';
		
		// list plans
		$q = "SELECT * FROM `gs_billing_plans` ORDER BY `price` ASC";
		$r = mysqli_query($ms, $q);
		
		$nbr = 1;
		
		while($row=mysqli_fetch_array($r))
		{
			$plan_id = $row['plan_id'];
			$name = $row['name'];
			$active = $row['active'];
			$objects = $row['objects'];
			$period = $row['period'];
			$period_type = $row['period_type'];
			$price = $row['price'];
			
			if ($active == 'true')
			{
				if ($period == 1)
				{
					$period_type = $la[substr(strtoupper($period_type),0,-1)];	
				}
				else
				{
					$period_type = $la[strtoupper($period_type)];	
				}
				
				$period = $period.' '.strtolower($period_type);
				
				// generate url
				if ($gsValues['BILLING_GATEWAY'] == 'paypal')
				{
					$custom = $gsValues['BILLING_PAYPAL_CUSTOM'].','.$_SESSION["email"].','.$plan_id;
					
					$payment_url = 'https://www.paypal.com/cgi-bin/webscr?business='.$gsValues['BILLING_PAYPAL_ACCOUNT'].'&cmd=_xclick&currency_code='.$gsValues['BILLING_CURRENCY'];
					$payment_url .='&amount='.$price.'&item_name='.$name.' ('.$_SESSION["email"].')';
					$payment_url .='&custom='.$custom;
				}
				else if ($gsValues['BILLING_GATEWAY'] == 'custom')
				{
					$payment_url = $gsValues['BILLING_CUSTOM_URL'];
					
					$payment_url = str_replace("%USER_EMAIL%", $_SESSION["email"], $payment_url);
					$payment_url = str_replace("%PLAN_NAME%", $name, $payment_url);
					$payment_url = str_replace("%PLAN_ID%", $plan_id, $payment_url);
					$payment_url = str_replace("%PLAN_PRICE%", $price, $payment_url);
					$payment_url = str_replace("%CURRENCY%", $gsValues['BILLING_CURRENCY'], $payment_url);
				}
				
				$payment_button = '<a href="'.$payment_url.'" target="_blank" title="'.$la['PURCHASE'].'"><i class="purchase"></i></a>';
				
				$oddeven = ($nbr++%2 ? 'odd':'even');
				
				// generate item				
				$result .=  '<div class="row '.$oddeven.'"><div class="row2">';
				
				$result .= '<div class="width5"><i class="arrow"></i></div>';
				
				$result .= '<div class="width30 name">'.$name.'</div>';
				
				$result .= '<div class="width15">'.$objects.'</div>';
				
				$result .= '<div class="width15">'.$period.'</div>';
				
				$result .= '<div class="width20">'.$price.' '.$gsValues['BILLING_CURRENCY'].'</div>';
				
				$result .= '<div class="width15">'.$payment_button.'</div>';
				
				$result .= '</div></div>';	
			}
		}
		
		// generate header
		if ($result != '')
		{			
			$header = '<div class="row header"><div class="row2">';
			
			$header .= '<div class="width5"></div>';
			
			$header .= '<div class="width30 name">'.$la['NAME'].'</div>';
			
			$header .= '<div class="width15">'.$la['OBJECTS'].'</div>';
			
			$header .= '<div class="width15">'.$la['PERIOD'].'</div>';
			
			$header .= '<div class="width20">'.$la['PRICE'].'</div>';
			
			$header .= '<div class="width15"></div>';
			
			$header .= '</div></div>';
			
			$result = $header.$result;
		}
		
				
		echo $result;
		
		die;
	}
	
	if(@$_POST['cmd'] == 'use_billing_plan')
	{
		$plan = $_POST['plan'];
		$imeis = strtoupper($_POST['imeis']);
		$imeis_ = json_decode(stripslashes($imeis),true);
		
		// verify plan
		$q = "SELECT * FROM `gs_user_billing_plans` WHERE `plan_id`='".$plan['plan_id']."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		if (!$r)
		{
			echo 'ERROR_VERIFY';
			die;
		}
		
		$row = mysqli_fetch_array($r);
		
		if (($row['objects'] != $plan['objects']) || ($row['period'] != $plan['period']) || ($row['period_type'] != $plan['period_type']))
		{
			echo 'ERROR_VERIFY';
			die;
		}

		// activate objects
		if(count($imeis_) > $plan['objects'])
		{
			echo 'ERROR_ACTIVATE';
			die;
		}
		
		for($i=0; $i<count($imeis_); $i++)
		{		    
			$imei = $imeis_[$i];
			
			$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
			
			if (!$r)
			{
				echo 'ERROR_ACTIVATE';
				die;
			}
			
			$row = mysqli_fetch_array($r);
			
			$object_expire_dt = $row['object_expire_dt'];
			
			if (strtotime($object_expire_dt) < strtotime(gmdate("Y-m-d")))
			{
				$object_expire_dt = gmdate("Y-m-d");
			}
			
			$object_expire_dt = gmdate("Y-m-d", strtotime($object_expire_dt.' + '.$plan['period'].' '.$plan['period_type']));
			
			$q = "UPDATE `gs_objects` SET `active`='true', `object_expire`='true', `object_expire_dt`='".$object_expire_dt."' WHERE `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
			
			// reduce objects from plan
			if ($r)
			{
				$q = "UPDATE `gs_user_billing_plans` SET objects=objects-1 WHERE `plan_id`='".$plan['plan_id']."'";
				$r = mysqli_query($ms, $q);
			}
			else
			{
				echo 'ERROR_ACTIVATE';
				die;
			}
		}
		
		//write log
		writeLog('object_op', 'Activate object: successful. IMEI: '.implode(",", $imeis_));
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'load_billing_plan')
	{
		$result = array();
		
		$plan_id = $_POST['plan_id'];
		
		$q = "SELECT * FROM `gs_user_billing_plans` WHERE `plan_id`='".$plan_id."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		$result = array('plan_id' => $plan_id, 'name' => $row['name'], 'objects' => $row['objects'], 'period' => $row['period'], 'period_type' => $row['period_type'], 'price' => $row['price']);
		
		echo json_encode($result);
		die;
	}
	
	if(@$_POST['cmd'] == 'delete_billing_plan')
	{
		$plan_id = $_POST['plan_id'];
		
		$q = "DELETE FROM `gs_user_billing_plans` WHERE `plan_id`='".$plan_id."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		
		echo 'OK';
		die;
	}
	
	if(@$_POST['cmd'] == 'get_billing_plan_total_objects')
	{
		$result['objects'] = getUserBillingTotalObjects($user_id);
		
		echo json_encode($result);
		die;
	}
?>