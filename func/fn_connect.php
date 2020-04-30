<?
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	include ('../tools/email.php');
	loadLanguage($gsValues['LANGUAGE']);
	
	if(@$_POST['cmd'] == 'session_check')
	{
		checkUserSession();
	
		if (checkUserSession2() == true)
		{
			echo 'true';
		}
		else
		{
			echo 'false';
		}
		die;
	}

	if(@$_POST['cmd'] == 'login')
	{
		$username = strtolower($_POST["username"]);
		$password = $_POST["password"];
		$remember_me = $_POST["remember_me"];
		$mobile = $_POST["mobile"];
		
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
					if ($remember_me == 'true')
					{
						setUserSessionHash($row['id']);
					}
					
					// reset session array
					$_SESSION = array();
					
					setUserSession($row['id']);
					setUserSessionSettings($row['id']);
					setUserSessionCPanel($row['id']);
					
					if (($gsValues['PAGE_AFTER_LOGIN'] == 'cpanel') && ($_SESSION["cpanel_privileges"] != false))
					{
						echo 'LOGIN_CPANEL';	
					}
					else
					{
						echo 'LOGIN_TRACKING';	
					}
					
					//write log
					writeLog('user_access', 'User login: successful');
					
					//update user usage
					updateUserUsage($row['id'], 1, false, false, false);
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
	
	if (@$_POST['cmd'] == 'logout')
	{
		//write log
		writeLog('user_access', 'User logout');
		
		if (isset($_SESSION["user_id"]))
		{
			deleteUserSessionHash($_SESSION["user_id"]);	
		}
		
		session_unset();
		session_destroy();
		
		echo $gsValues['URL_LOGIN'];
		
		die;
	}
	
	if (@$_POST['cmd'] == 'recover_url')
	{
		$email = $_POST['email'];
		$token = $_POST['token'];
		
		if ($email != "")
		{
			if ($token == $_SESSION["token"])
			{
				$email = strtolower($email);
				
				$q = "SELECT * FROM `gs_users` WHERE `email`='".$email."' AND `privileges` NOT LIKE ('%subuser%') LIMIT 1";
				$r = mysqli_query($ms, $q);
				$num = mysqli_num_rows($r);
				
				if ($num > 0)
				{
					$row = mysqli_fetch_array($r);
					
					$token = genAccountRecoverToken($email);
					
					$url_recover = $gsValues['URL_ROOT'].'/index.php?cmd=recover&token='.$token;
					
					$template = getDefaultTemplate('account_recover_url', $gsValues['LANGUAGE']);
					
					$subject = $template['subject'];
					$message = $template['message'];
					
					$subject = str_replace("%SERVER_NAME%", $gsValues['NAME'], $subject);
					$subject = str_replace("%URL_RECOVER%", $url_recover, $subject);
					
					$message = str_replace("%SERVER_NAME%", $gsValues['NAME'], $message);
					$message = str_replace("%URL_RECOVER%", $url_recover, $message);
					
					if (sendEmail($email, $subject, $message))
					{
						// inset token
						$q = "INSERT INTO `gs_user_account_recover` (`token`, `email`, `dt_recover`) VALUES ('".$token."','".$email."','".gmdate("Y-m-d H:i:s")."')";
						$r = mysqli_query($ms, $q);
				
						echo 'OK';
						
						//write log
						writeLog('user_access', 'User recover: URL sent. E-mail: '.$email);
					}
					else
					{
						echo 'ERROR_NOT_SENT';
					}
				}
				else
				{
					echo 'ERROR_EMAIL_NOT_FOUND';
					
					//write log
					writeLog('user_access', 'User recover: no such e-mail. E-mail: '.$email);
				}
			}
		}
		
		die;
	}
	
	if (@$_POST['cmd'] == 'recover')
	{
		$token = $_POST['token'];
		
		$q = "SELECT * FROM `gs_user_account_recover` WHERE `token`='".$token."' LIMIT 1";
		$r = mysqli_query($ms, $q);
		$num = mysqli_num_rows($r);
		
		if ($num > 0)
		{
			$row = mysqli_fetch_array($r);
			
			$email = $row['email'];
			
			$q = "SELECT * FROM `gs_users` WHERE `email`='".$email."' AND `privileges` NOT LIKE ('%subuser%') LIMIT 1";
			$r = mysqli_query($ms, $q);
			$num = mysqli_num_rows($r);
			
			if ($num > 0)
			{
				$row = mysqli_fetch_array($r);
				
				$new_password = genAccountPassword();
				
				$template = getDefaultTemplate('account_recover', $gsValues['LANGUAGE']);
				
				$subject = $template['subject'];
				$message = $template['message'];
				
				$subject = str_replace("%SERVER_NAME%", $gsValues['NAME'], $subject);
				$subject = str_replace("%URL_LOGIN%", $gsValues['URL_LOGIN'], $subject);
				$subject = str_replace("%EMAIL%", $email, $subject);
				$subject = str_replace("%USERNAME%", $row['username'], $subject);
				$subject = str_replace("%PASSWORD%", $new_password, $subject);
				
				$message = str_replace("%SERVER_NAME%", $gsValues['NAME'], $message);
				$message = str_replace("%URL_LOGIN%", $gsValues['URL_LOGIN'], $message);
				$message = str_replace("%EMAIL%", $email, $message);
				$message = str_replace("%USERNAME%", $row['username'], $message);
				$message = str_replace("%PASSWORD%", $new_password, $message);
				
				if (sendEmail($email, $subject, $message))
				{
					$q = "UPDATE gs_users SET password='".md5($new_password)."' WHERE email='".$email."'";
					$r = mysqli_query($ms, $q);
					
					$q = "DELETE FROM `gs_user_account_recover` WHERE `token`='".$token."'";
					$r = mysqli_query($ms, $q);
					
					echo 'OK';
					
					//write log
					writeLog('user_access', 'User recover: successful. E-mail: '.$email);
				}
				else
				{
					echo 'ERROR_NOT_SENT';
				}
			}
			else
			{
				echo 'ERROR_EMAIL_NOT_FOUND';
					
				//write log
				writeLog('user_access', 'User recover: no such e-mail. E-mail: '.$email);
			}
		}
		else
		{
			echo 'ERROR_RECOVER_EXPIRED';
		}
		
		die;
	}
	
	if ((@$_POST['cmd'] == 'register') && ($gsValues['ALLOW_REGISTRATION'] == "true"))
	{
		$email = $_POST['email'];
		$token = $_POST['token'];
		
		if ($email != '')
		{
			if ($token == @$_SESSION["token"])
			{
				$account_expire = $gsValues['ACCOUNT_EXPIRE'];
				$account_expire_dt = '';
				
				if ($account_expire == 'true')
				{
					$account_expire_dt = gmdate("Y-m-d", strtotime(gmdate("Y-m-d").' + '.$gsValues['ACCOUNT_EXPIRE_PERIOD'].' days'));
				}
				
				$privileges = array();
				$privileges['type'] = 'user';
				$privileges['map_osm'] = stringToBool($gsValues['USER_MAP_OSM']);
				$privileges['map_bing'] = stringToBool($gsValues['USER_MAP_BING']);
				$privileges['map_google'] = stringToBool($gsValues['USER_MAP_GOOGLE']);
				$privileges['map_google_street_view'] = stringToBool($gsValues['USER_MAP_GOOGLE_STREET_VIEW']);
				$privileges['map_google_traffic'] = stringToBool($gsValues['USER_MAP_GOOGLE_TRAFFIC']);
				$privileges['map_mapbox'] = stringToBool($gsValues['USER_MAP_MAPBOX']);
				$privileges['map_yandex'] = stringToBool($gsValues['USER_MAP_YANDEX']);  
				$privileges['history'] = stringToBool($gsValues['HISTORY']);
				$privileges['reports'] = stringToBool($gsValues['REPORTS']);
				$privileges['tasks'] = stringToBool($gsValues['TASKS']);
				$privileges['rilogbook'] = stringToBool($gsValues['RILOGBOOK']);
				$privileges['dtc'] = stringToBool($gsValues['DTC']);
				$privileges['maintenance'] = stringToBool($gsValues['MAINTENANCE']);
				$privileges['object_control'] = stringToBool($gsValues['OBJECT_CONTROL']);
				$privileges['image_gallery'] = stringToBool($gsValues['IMAGE_GALLERY']);
				$privileges['chat'] = stringToBool($gsValues['CHAT']);
				$privileges['subaccounts'] = stringToBool($gsValues['SUBACCOUNTS']);
				$privileges = json_encode($privileges);
				
				$result = addUser('true', 'true', $account_expire, $account_expire_dt, $privileges, '', $email, $email, '', $gsValues['OBJ_ADD'], $gsValues['OBJ_LIMIT'], $gsValues['OBJ_LIMIT_NUM'], $gsValues['OBJ_DAYS'], $gsValues['OBJ_DAYS_NUM'], $gsValues['OBJ_EDIT'], $gsValues['OBJ_DELETE'], $gsValues['OBJ_HISTORY_CLEAR']);
				
				echo $result;
			}
		}
		
		die;
	}
?>