<?
	session_start();

	include ('init.php');
	include ('func/fn_common.php');
	
	if (isset($_SESSION["user_id"]))
	{
		session_unset();
		session_destroy();
		session_start();
	}
		
	if (isset($_GET['au']))
	{
		$au = $_GET['au'];
		$mobile = @$_GET["m"];
		$user_id = getUserIdFromAU($au);
		
		if ($user_id == false)
		{
			if ($mobile == 'true')
			{
				header('Location: mobile/index.php');
				die;
			}
			else
			{
				header('Location: index.php');
				die;
			}
		}
		
		setUserSession($user_id);
		setUserSessionSettings($user_id);
		setUserSessionCPanel($user_id);
		
		//write log
		writeLog('user_access', 'User login via URL: successful');
		
		if ($mobile == 'true')
		{
			header('Location: mobile/tracking.php');
			die;
		}
		else
		{
			header('Location: tracking.php');
			die;
		}
		die;
	}
	
	if ((isset($_GET['username'])) && (isset($_GET['password'])))
	{
		$username = strtolower($_GET["username"]);
		$password = $_GET["password"];
		
		// check failed logins limit
		$q = "SELECT * FROM `gs_user_failed_logins` WHERE `ip`='".$_SERVER['REMOTE_ADDR']."' AND dt_login > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 10 MINUTE)";
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		if ($count >= 10)
		{
			echo 'ERROR_MANY_FAILED_LOGIN_ATTEMPTS';
			
			//write log
			writeLog('user_access', 'User login: too many failed login attempts. Username: "'.$username.'"');
		}
		else
		{
			$q = "SELECT * FROM `gs_users` WHERE `username`='".$username."' AND `password`='".md5($password)."' LIMIT 1";		
			$r = mysqli_query($ms, $q);
			
			if ($row = mysqli_fetch_array($r))
			{
				if ($row['active'] == 'true')
				{					
					// reset session array
					$_SESSION = array();
					
					setUserSession($row['id']);
					setUserSessionSettings($row['id']);
					setUserSessionCPanel($row['id']);
					
					//write log
					writeLog('user_access', 'User login: successful');
					
					//update user usage
					updateUserUsage($row['id'], 1, false, false, false);
					
					if (($gsValues['PAGE_AFTER_LOGIN'] == 'cpanel') && ($_SESSION["cpanel_privileges"] != false))
					{
						header('Location: cpanel.php');
						die;
					}
					else
					{
						header('Location: tracking.php');
						die;
					}
				}
				else
				{
					echo 'ERROR_ACCOUNT_LOCKED';
					
					//write log
					writeLog('user_access', 'User login: account locked. Username: "'.$username.'"');
				}
			}
			else
			{
				// insert failed login
				$q = "INSERT INTO `gs_user_failed_logins` (`ip`, `dt_login`) VALUES ('".$_SERVER['REMOTE_ADDR']."','".gmdate("Y-m-d H:i:s")."')";
				$r = mysqli_query($ms, $q);
								
				echo 'ERROR_USERNAME_PASSWORD_INCORRECT';
				
				//write log
				writeLog('user_access', 'User login: unsuccessful. Username: "'.$username.'"');
			}
		}	
		die;
	}
?>