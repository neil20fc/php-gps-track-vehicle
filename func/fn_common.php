<?
	// #################################################
	//  CPANEL FUNCTIONS
	// #################################################
	
	function checkCPanelToUserPrivileges($id)
	{
		global $ms;
		
		if ($_SESSION["cpanel_privileges"] == 'manager')
		{
			$q = "SELECT * FROM `gs_users` WHERE `id`='".$id."'";
			$r = mysqli_query($ms, $q);
			$row = mysqli_fetch_array($r);
			
			if ($row["manager_id"] != $_SESSION["cpanel_manager_id"])
			{
				die;
			}
		}
	}
	
	function checkCPanelToObjectPrivileges($imei)
	{
		global $ms, $la;
		
		if ($_SESSION["cpanel_privileges"] == 'manager')
		{
			$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
			$row = mysqli_fetch_array($r);
			
			if ($row["manager_id"] != $_SESSION["cpanel_manager_id"])
			{
				die;
			}
		}
	}
	
	// #################################################
	//  END CPANEL FUNCTIONS
	// #################################################
	
	// #################################################
	//  PASSWORD, API, IDENTIFIER FUNCTIONS
	// #################################################
	
	function genLoginToken()
        {
		if (isset($_SESSION['token']))
		{
			return $_SESSION['token'];
		}
		else
		{
			$token = hash('sha1',rand().gmdate('Y-m-d H:i:s').rand());
			$_SESSION['token'] = $token;
			return $token;	
		}   
        }
	
	function genAccountPassword()
	{
		$pass = substr(hash('sha1',rand().gmdate('d F Y G i s u').rand()),0,6);
		return $pass;
	}
	
	function genAccountRecoverToken($email)
	{
		global $ms;
		
		while(true)
		{
			$token = strtoupper(md5(rand().$email.gmdate("Y-m-d H:i:s").rand()));
			
			$q = "SELECT * FROM `gs_user_account_recover` WHERE `token`='".$token."'";
			$r = mysqli_query($ms, $q);
			$num = mysqli_num_rows($r);
			
			if ($num == 0)
			{
				return $token;
			}	
		}
	}
	
	function genServerAPIKey()
	{
		global $ms, $gsValues;
		
		$api_key = '';
		
		if ($gsValues['HW_KEY'] != '')
		{
			$api_key = strtoupper(md5(rand().$gsValues['HW_KEY'].gmdate("Y-m-d H:i:s").rand()));	
		}
		
		return $api_key;
	}
	
	function genUserAPIKey($email)
	{
		global $ms;
		
		while(true)
		{
			$api_key = strtoupper(md5(rand().$email.gmdate("Y-m-d H:i:s").rand()));
			
			$q = "SELECT * FROM `gs_users` WHERE `api_key`='".$api_key."'";
			$r = mysqli_query($ms, $q);
			$num = mysqli_num_rows($r);
			
			if ($num == 0)
			{
				return $api_key;
			}	
		}
	}
	
	function genSMSGatewayIdn($email)
	{
		global $ms, $gsValues;
		
		while(true)
		{
			$sms_idn = strtoupper(md5(rand().$email.gmdate("Y-m-d H:i:s").rand()));
			
			$sms_idn = preg_replace("/[^0-9]/", "", $sms_idn);
			
			$sms_idn = substr($sms_idn.$sms_idn, 0, 20);
			
			$q = "SELECT * FROM `gs_users` WHERE `sms_gateway_identifier`='".$sms_idn."'";
			$r = mysqli_query($ms, $q);
			$num = mysqli_num_rows($r);
			
			if (($num == 0) && ($sms_idn != $gsValues['SMS_GATEWAY_IDENTIFIER']))
			{
				return $sms_idn;
			}	
		}
	}
	
	function genPushIdn($email)
	{
		global $ms, $gsValues;
		
		while(true)
		{
			$push_idn = strtoupper(md5(rand().$email.gmdate("Y-m-d H:i:s").rand()));
			
			$push_idn = preg_replace("/[^0-9]/", "", $push_idn);
			
			$push_idn = substr($push_idn.$push_idn, 0, 20);
			
			$q = "SELECT * FROM `gs_users` WHERE `push_notify_identifier`='".$push_idn."'";
			$r = mysqli_query($ms, $q);
			$num = mysqli_num_rows($r);
			
			if ($num == 0)
			{
				return $push_idn;
			}	
		}
	}
	
	// #################################################
	//  END PASSWORD, API, IDENTIFIER FUNCTIONS
	// #################################################
	
	// #################################################
	// USER FUNCTIONS
	// #################################################
	
	function getUserIdFromAU($au)
	{
		global $ms, $gsValues;
		
		$result = false;
		
		$q = "SELECT * FROM `gs_users` WHERE `privileges` LIKE '%subuser%' and `privileges` LIKE '%".$au."%'";
		$r = mysqli_query($ms, $q);
		
		if ($row = mysqli_fetch_array($r))
		{
			$privileges = json_decode($row['privileges'],true);
			
			if ($privileges['type'] == 'subuser')
			{
				if ($privileges['au_active'] == true)
				{
					if ($privileges['au'] == $au)
					{
						if ($row['active'] == "true")
						{
							$result = $row['id'];	
						}
					}
				}
			}
		}
		
		return $result;
	}
	
	function getUserIdFromSessionHash()
	{
		global $ms, $gsValues;
		
		$result = false;
		
		if (isset($_COOKIE['gs_sess_hash']))
		{
			$sess_hash = $_COOKIE['gs_sess_hash'];
			
			$q = "SELECT * FROM `gs_users` WHERE `sess_hash`='".$sess_hash."'";
			$r = mysqli_query($ms, $q);
			
			if ($row = mysqli_fetch_array($r))
			{
				$sess_hash_check = md5($gsValues['PATH_ROOT'].$row['id'].$row['username'].$row['password']);
				
				if ($sess_hash_check == $sess_hash)
				{
					$result = $row['id'];
				}
			}
		}
		
		return $result;
	}
	
	function setUserSessionHash($id)
	{
		global $ms, $gsValues;
		
		$q = "SELECT * FROM `gs_users` WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$row = mysqli_fetch_array($r);
		
		$sess_hash = md5($gsValues['PATH_ROOT'].$row['id'].$row['username'].$row['password']);
		
		$q = "UPDATE gs_users SET `sess_hash`='".$sess_hash."' WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$expire = time() + 2592000;
		setcookie("gs_sess_hash", $sess_hash, $expire, '/', null, null, true);
	}
	
	function deleteUserSessionHash($id)
	{
		global $ms;
		
		$q = "UPDATE gs_users SET `sess_hash`='' WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$expire = time() + 2592000;
		setcookie("gs_sess_hash","",time()-$expire, '/');
	}
	
	function setUserSession($id)
	{
		global $ms, $gsValues;
		
		if (!ctype_digit($id))
		{
			die;
		}
		
		$_SESSION["user_id"] = $id;
		$_SESSION["session"] = md5($gsValues['PATH_ROOT']);
		$_SESSION["remote_addr"] = md5($_SERVER['REMOTE_ADDR']);
		
		$q2 = "UPDATE gs_users SET `ip`='".$_SERVER['REMOTE_ADDR']."', `dt_login`='".gmdate("Y-m-d H:i:s")."' WHERE `id`='".$id."'";
		$r2 = mysqli_query($ms, $q2);
	}
	
	function setUserSessionSettings($id)
	{
		global $ms, $gsValues;
		
		// set user settings
		$_SESSION = array_merge($_SESSION, getUserData($id));
	}
	
	function setUserSessionCPanel($id)
	{
		global $ms, $gsValues;
		
		if (!isset($_SESSION["cpanel_privileges"]))
		{
			if ($_SESSION['privileges'] == 'super_admin')
			{
				$_SESSION["cpanel_user_id"] = $id;
				$_SESSION["cpanel_privileges"] = 'super_admin';
				$_SESSION["cpanel_manager_id"] = 0;
			}
			else if ($_SESSION['privileges'] == 'admin')
			{
				$_SESSION["cpanel_user_id"] = $id;
				$_SESSION["cpanel_privileges"] = 'admin';
				$_SESSION["cpanel_manager_id"] = 0;
			}
			else if ($_SESSION['privileges'] == 'manager')
			{
				$_SESSION["cpanel_user_id"] = $id;
				$_SESSION["cpanel_privileges"] = 'manager';
				$_SESSION["cpanel_manager_id"] = $id;
			}
			else
			{
				$_SESSION["cpanel_privileges"] = false;
			}
		}
	}
	
	function checkUserSession()
	{
		global $gsValues;
		
		$file = basename($_SERVER['SCRIPT_NAME']);
		
		if (($file == 'index.php') || (checkUserSession2() == false))
		{
			session_unset();
			session_destroy();
			session_start();
				
			$user_id = getUserIdFromSessionHash();
			
			if($user_id != false)
			{
				setUserSession($user_id);
				setUserSessionSettings($user_id);
				setUserSessionCPanel($user_id);
			}
		}
		
		if (checkUserSession2() == false)
		{
			if (($file == 'tracking.php') || ($file == 'cpanel.php'))
			{
				Header("Location: index.php");
			}
			
			if (($file != 'index.php') && ($file != 'fn_connect.php'))
			{
				die;
			}
		}
		else
		{
			if ($file == 'index.php')
			{
				if (($gsValues['PAGE_AFTER_LOGIN'] == 'cpanel') && ($_SESSION["cpanel_privileges"] != false))
				{
					if (file_exists('cpanel.php'))
					{
						Header("Location: cpanel.php");
					}
					else
					{
						Header("Location: tracking.php");
					}
				}
				else
				{
					Header("Location: tracking.php");
				}
			}
		}
	}
	
	function checkUserSession2()
	{
		global $ms, $gsValues;
		
		$result = false;
		
		if (isset($_SESSION["user_id"]) && isset($_SESSION["session"]) && isset($_SESSION["remote_addr"]) && isset($_SESSION["cpanel_privileges"]))
		{
			if (checkUserActive($_SESSION["user_id"]) == true)
			{
				if (($_SESSION["cpanel_privileges"] == false) || ($gsValues['ADMIN_IP_SESSION_CHECK'] == false))
				{
					if ($_SESSION["session"] == md5($gsValues['PATH_ROOT']))
					{
						$result = true;
					}	
				}
				else
				{
					if (($_SESSION["session"] == md5($gsValues['PATH_ROOT'])) && ($_SESSION["remote_addr"] == md5($_SERVER['REMOTE_ADDR'])))
					{
						$result = true;
					}	
				}
			}	
		}
		
		return $result;
	}
	
	function checkUserActive($id)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_users` WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		if ($row['active'] == 'true')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function checkUserCPanelPrivileges()
	{
		global $ms, $gsValues;
		
		if (!isset($_SESSION["cpanel_privileges"]))
		{
			die;
		}
		
		if ($_SESSION["cpanel_privileges"] == false)
		{
			die;
		}
		
		if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin'))
		{
			if ($gsValues['ADMIN_IP'] != '')
			{
				$admin_ips = explode(",", $gsValues['ADMIN_IP']);	
				if (!in_array($_SERVER['REMOTE_ADDR'], $admin_ips))
				{
					die;
				}
			}
		}
		
		if ($_SESSION["user_id"] != $_SESSION['cpanel_user_id'])
		{
			setUserSession($_SESSION['cpanel_user_id']);
		}
	}
	
	function getUserData($id)
	{
		global $gsValues, $ms, $la;
		
		$result = array();
		
		$q = "SELECT * FROM `gs_users` WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		$result["user_id"] = $id;
		$result["active"] = $row['active'];
		$result["manager_id"] = $row['manager_id'];
		$result["manager_billing"] = $row["manager_billing"];
		
		$privileges = json_decode($row['privileges'],true);
		$privileges = checkUserPrivilegesArray($privileges);
		
		if ($privileges["type"] == 'subuser')
		{
			$result["privileges"] = $privileges["type"];
			
			$privileges["imei"] = explode(",", $privileges["imei"]);
			$result["privileges_imei"] = '"'.implode('","', $privileges["imei"]).'"';
			
			$privileges["marker"] = explode(",", $privileges["marker"]);
			$result["privileges_marker"] = '"'.implode('","', $privileges["marker"]).'"';
			
			$privileges["route"] = explode(",", $privileges["route"]);
			$result["privileges_route"] = '"'.implode('","', $privileges["route"]).'"';
			
			$privileges["zone"] = explode(",", $privileges["zone"]);
			$result["privileges_zone"] = '"'.implode('","', $privileges["zone"]).'"';
			
			// check manager user privileges, in case some of them are not available, reset subuser privileges
			$q2 = "SELECT * FROM `gs_users` WHERE `id`='".$row['manager_id']."'";
			$r2 = mysqli_query($ms, $q2);
			$row2 = mysqli_fetch_array($r2);
			$manager_privileges = json_decode($row2['privileges'],true);
			$manager_privileges = checkUserPrivilegesArray($manager_privileges);
			
			if ($manager_privileges["history"] == false) { $privileges["history"] = false; }
			if ($manager_privileges["reports"] == false) { $privileges["reports"] = false; }
			if ($manager_privileges["tasks"] == false) { $privileges["tasks"] = false; }
			if ($manager_privileges["rilogbook"] == false) { $privileges["rilogbook"] = false; }
			if ($manager_privileges["dtc"] == false) { $privileges["dtc"] = false; }
			if ($manager_privileges["maintenance"] == false) { $privileges["maintenance"] = false; }
			if ($manager_privileges["object_control"] == false) { $privileges["object_control"] = false; }
			if ($manager_privileges["image_gallery"] == false) { $privileges["image_gallery"] = false; }
			if ($manager_privileges["chat"] == false) { $privileges["chat"] = false; }
			if ($manager_privileges["subaccounts"] == false) { $privileges["subaccounts"] = false; }
			
			$result["privileges_map_osm"] = $manager_privileges["map_osm"];
			$result["privileges_map_bing"] = $manager_privileges["map_bing"];
			$result["privileges_map_google"] = $manager_privileges["map_google"];
			$result["privileges_map_google_street_view"] = $manager_privileges["map_google_street_view"];
			$result["privileges_map_google_traffic"] = $manager_privileges["map_google_traffic"];
			$result["privileges_map_mapbox"] = $manager_privileges["map_mapbox"];
			$result["privileges_map_yandex"] = $manager_privileges["map_yandex"];
			
			$result["privileges_history"] = $privileges["history"];
			$result["privileges_reports"] = $privileges["reports"];
			$result["privileges_tasks"] = $privileges["tasks"];
			$result["privileges_rilogbook"] = $privileges["rilogbook"];
			$result["privileges_dtc"] = $privileges["dtc"];
			$result["privileges_maintenance"] = $privileges["maintenance"];
			$result["privileges_object_control"] = $privileges["object_control"];
			$result["privileges_image_gallery"] = $privileges["image_gallery"];
			$result["privileges_chat"] = $privileges["chat"];
			$result["privileges_subaccounts"] = $privileges["subaccounts"];
		}
		else
		{
			$result["privileges"] = $privileges["type"];
			$result["privileges_imei"] = '';
			$result["privileges_marker"] = '';
			$result["privileges_route"] = '';
			$result["privileges_zone"] = '';
			
			$result["privileges_map_osm"] = $privileges["map_osm"];
			$result["privileges_map_bing"] = $privileges["map_bing"];
			$result["privileges_map_google"] = $privileges["map_google"];
			$result["privileges_map_google_street_view"] = $privileges["map_google_street_view"];
			$result["privileges_map_google_traffic"] = $privileges["map_google_traffic"];
			$result["privileges_map_mapbox"] = $privileges["map_mapbox"];
			$result["privileges_map_yandex"] = $privileges["map_yandex"];
			
			$result["privileges_history"] = $privileges["history"];
			$result["privileges_reports"] = $privileges["reports"];
			$result["privileges_tasks"] = $privileges["tasks"];
			$result["privileges_rilogbook"] = $privileges["rilogbook"];
			$result["privileges_dtc"] = $privileges["dtc"];
			$result["privileges_maintenance"] = $privileges["maintenance"];
			$result["privileges_object_control"] = $privileges["object_control"];
			$result["privileges_image_gallery"] = $privileges["image_gallery"];
			$result["privileges_chat"] = $privileges["chat"];
			$result["privileges_subaccounts"] = $privileges["subaccounts"];
		}
		
		// billing
		if (($gsValues['BILLING'] == 'true') && ($privileges["type"] != 'subuser'))
		{
			$result["billing"] = true;
			
			if ($row["manager_id"] != 0)
			{
				$q2 = "SELECT * FROM `gs_users` WHERE `id`='".$row['manager_id']."'";
				$r2 = mysqli_query($ms, $q2);
				$row2 = mysqli_fetch_array($r2);
				
				if($row2['manager_billing'] == 'true')
				{
					$result["billing"] = true;
				}
				else
				{
					$result["billing"] = false;
				}
			}
		}
		else
		{
			$result["billing"] = false;
		}
		
		$result["username"] = $row['username'];
		$result["email"] = $row['email'];
		$result["api"] = stringToBool($row['api']);
		$result["api_key"] = $row['api_key'];
		$result["info"] = $row['info'];
		
		$result["obj_add"] = $row['obj_add'];
		$result["obj_limit"] = $row['obj_limit'];
		$result["obj_limit_num"] = $row['obj_limit_num'];
		$result["obj_days"] = $row['obj_days'];
		$result["obj_days_dt"] = $row['obj_days_dt'];
		$result["obj_edit"] = $row['obj_edit'];
		$result["obj_delete"] = $row['obj_delete'];
		$result["obj_history_clear"] = $row['obj_history_clear'];
		
		$result["currency"] = $row['currency'];
		$result["timezone"] = $row['timezone'];
		
		$result["dst"] = $row['dst'];
		$result["dst_start"] = $row['dst_start'];
		$result["dst_end"] = $row['dst_end'];
		
		if($row['startup_tab'] == '')
		{
			$result["startup_tab"] = 'map';
		}
		else
		{
			$result["startup_tab"] = $row['startup_tab'];
		}
		
		$result["language"] = $row['language'];
		
		$result["chat_notify"] = $row['chat_notify'];
		
		$result["map_sp"] = $row['map_sp'];
		$result["map_is"] = $row['map_is'];
		
		if($row['map_rc'] == '')
		{
			$result["map_rc"] = '#FF0000';
		}
		else
		{
			$result["map_rc"] = $row['map_rc'];
		}
		
		if($row['map_rhc'] == '')
		{
			$result["map_rhc"] = '#0800FF';
		}
		else
		{
			$result["map_rhc"] = $row['map_rhc'];
		}
		
		$result["groups_collapsed"] = $row['groups_collapsed'];
		$result["od"] = $row['od'];
		$result["ohc"] = $row['ohc'];
		
		if($row['datalist'] == '')
		{
			$result["datalist"] = 'bottom_panel';
		}
		else
		{
			$result["datalist"] = $row['datalist'];
		}
		
		if($row['datalist_items'] == '')
		{
			$result["datalist_items"] = 'odometer,engine_hours,status,model,vin,plate_number,sim_number,driver,trailer,time_position,time_server,address,position,speed,altitude,angle,nearest_zone,nearest_marker';
		}
		else
		{
			$result["datalist_items"] = $row['datalist_items'];
		}

		$result["push_notify_identifier"] = $row['push_notify_identifier'];
		
		if ($result["push_notify_identifier"] == '')
		{
			$result["push_notify_identifier"] = genPushIdn($result["email"]);
			
			$q2 = "UPDATE `gs_users` SET push_notify_identifier='".$result["push_notify_identifier"]."' WHERE `id`='".$result["user_id"]."'";
			$r2 = mysqli_query($ms, $q2);
		}
		
		if($row['push_notify_desktop'] == '')
		{
			$result["push_notify_desktop"] = 'false';
		}
		else
		{
			$result["push_notify_desktop"] = $row['push_notify_desktop'];
		}
		
		if($row['push_notify_mobile'] == '')
		{
			$result["push_notify_mobile"] = 'false';
		}
		else
		{
			$result["push_notify_mobile"] = $row['push_notify_mobile'];
		}
		
		if($row['push_notify_mobile_interval'] == 0)
		{
			$result["push_notify_mobile_interval"] = 10;
		}
		else
		{
			$result["push_notify_mobile_interval"] = $row['push_notify_mobile_interval'];
		}
		
		$result["sms_gateway_server"] = $row['sms_gateway_server'];
		$result["sms_gateway"] = $row['sms_gateway'];
		$result["sms_gateway_type"] = $row['sms_gateway_type'];
		$result["sms_gateway_url"] = $row['sms_gateway_url'];
		$result["sms_gateway_identifier"] = $row['sms_gateway_identifier'];
		
		if ($result['sms_gateway_identifier'] == '')
		{
			$result['sms_gateway_identifier'] = genSMSGatewayIdn($result["email"]);
			
			$q2 = "UPDATE `gs_users` SET sms_gateway_identifier='".$result["sms_gateway_identifier"]."' WHERE `id`='".$result["user_id"]."'";
			$r2 = mysqli_query($ms, $q2);
		}
		
		$result["places_markers"] = $row['places_markers'];
		$result["places_routes"] = $row['places_routes'];
		$result["places_zones"] = $row['places_zones'];
		
		if ($row['usage_email_daily'] == '')
		{
			$result["usage_email_daily"] = $gsValues['USAGE_EMAIL_DAILY'];
		}
		else
		{
			$result["usage_email_daily"] = $row['usage_email_daily'];	
		}
		
		if ($row['usage_sms_daily'] == '')
		{
			$result["usage_sms_daily"] = $gsValues['USAGE_SMS_DAILY'];
		}
		else
		{
			$result["usage_sms_daily"] = $row['usage_sms_daily'];	
		}
		
		if ($row['usage_api_daily'] == '')
		{
			$result["usage_api_daily"] = $gsValues['USAGE_API_DAILY'];
		}
		else
		{
			$result["usage_api_daily"] = $row['usage_api_daily'];	
		}
		
		// units
		$result["units"] = $row['units'];
		$result = array_merge($result, getUnits($row['units']));
		
		return $result;
	}
	
	function convUserTimezone($dt)
	{
		if (!isset($_SESSION["timezone"]))
		{
			$_SESSION["timezone"] = "+ 0 hour";
		}
		
		if (!isset($_SESSION["dst"]))
		{
			$_SESSION["dst"] = "false";
		}
		
		if (strtotime($dt) > 0)
		{
			$dt = gmdate("Y-m-d H:i:s", strtotime($dt.$_SESSION["timezone"]));
			
			// DST
			if ($_SESSION["dst"] == 'true')
			{
				$dt_ = gmdate('m-d H:i:s', strtotime($dt));
				$dst_start = $_SESSION["dst_start"].':00';
				$dst_end =  $_SESSION["dst_end"].':00';
				
				if (isDateInRange(convDateToNum($dt_), convDateToNum($dst_start), convDateToNum($dst_end)))
				{
					$dt = gmdate("Y-m-d H:i:s", strtotime($dt.'+ 1 hour'));
				}
			}
		}
		
		return $dt;
	}
	
	function convUserUTCTimezone($dt)
	{
		if (strtotime($dt) > 0)
		{
			if (substr($_SESSION["timezone"],0,1) == "+")
			{
				$timezone_diff = str_replace("+", "-", $_SESSION["timezone"]);
			}
			else
			{
				$timezone_diff = str_replace("-", "+", $_SESSION["timezone"]);
			}
			
			$dt = gmdate("Y-m-d H:i:s", strtotime($dt.$timezone_diff));
			
			// DST
			if ($_SESSION["dst"] == 'true')
			{
				$dt_ = gmdate('m-d H:i:s', strtotime($dt));
				$dst_start = $_SESSION["dst_start"].':00';
				$dst_end =  $_SESSION["dst_end"].':00';
				
				if (isDateInRange(convDateToNum($dt_), convDateToNum($dst_start), convDateToNum($dst_end)))
				{
					$dt = gmdate("Y-m-d H:i:s", strtotime($dt.'- 1 hour'));
				}
			}
		}
		
		return $dt;
	}
	
	function convUserIDTimezone($user_id, $dt)
	{
		global $ms;
		
		if (strtotime($dt) > 0)
		{
			$q = "SELECT * FROM `gs_users` WHERE `id`='".$user_id."'";
			$r = mysqli_query($ms, $q);
			
			if (!$r)
			{
				return false;
			}
			
			$row = mysqli_fetch_array($r);
			
			if ($row)
			{	
				$dt = gmdate("Y-m-d H:i:s", strtotime($dt.$row["timezone"]));
				
				// DST
				if ($row["dst"] == 'true')
				{
					$dt_ = gmdate('m-d H:i:s', strtotime($dt));
					$dst_start = $row["dst_start"].':00';
					$dst_end =  $row["dst_end"].':00';
					
					if (isDateInRange(convDateToNum($dt_), convDateToNum($dst_start), convDateToNum($dst_end)))
					{
						$dt = gmdate("Y-m-d H:i:s", strtotime($dt.'+ 1 hour'));
					}	
				}
			}
		}
		
		return $dt;
	}
	
	function convUserIDUTCTimezone($user_id, $dt)
	{
		global $ms;
		
		if (strtotime($dt) > 0)
		{
			$q = "SELECT * FROM `gs_users` WHERE `id`='".$user_id."'";
			$r = mysqli_query($ms, $q);
			
			if (!$r)
			{
				return false;
			}
			
			$row = mysqli_fetch_array($r);
			
			if ($row)
			{
				if (substr($row["timezone"],0,1) == "+")
				{
					$timezone_diff = str_replace("+", "-", $row["timezone"]);
				}
				else
				{
					$timezone_diff = str_replace("-", "+", $row["timezone"]);
				}
				
				$dt = gmdate("Y-m-d H:i:s", strtotime($dt.$timezone_diff));
				
				// DST
				if ($row["dst"] == 'true')
				{
					$dt_ = gmdate('m-d H:i:s', strtotime($dt));
					$dst_start = $row["dst_start"].':00';
					$dst_end =  $row["dst_end"].':00';
					
					if (isDateInRange(convDateToNum($dt_), convDateToNum($dst_start), convDateToNum($dst_end)))
					{
						$dt = gmdate("Y-m-d H:i:s", strtotime($dt.'- 1 hour'));
					}	
				}	
			}
		}
		
		return $dt;
	}
	
	function checkUserToObjectPrivileges($id, $imei)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$id."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		if ($row)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function checkSubuserToObjectPrivileges($imeis, $imei)
	{
		$imeis = str_replace('"', '', $imeis);
		
		$imeis = explode(',', $imeis);
		
		if (in_array($imei, $imeis))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function checkUsernameExists($username)
	{
		global $ms;
		
		$username = strtolower($username);
		
		$q = "SELECT * FROM `gs_users` WHERE `username`='".$username."' LIMIT 1";
		$r = mysqli_query($ms, $q);
		$num = mysqli_num_rows($r);
		
		if ($num == 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	function checkEmailExists($email)
	{
		global $ms;
		
		$email = strtolower($email);
		
		$q = "SELECT * FROM `gs_users` WHERE `email`='".$email."' LIMIT 1";
		$r = mysqli_query($ms, $q);
		$num = mysqli_num_rows($r);
		
		if ($num == 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	function addUser($send, $active, $account_expire, $account_expire_dt, $privileges, $manager_id, $username, $email, $password, $obj_add, $obj_limit, $obj_limit_num, $obj_days, $obj_days_num, $obj_edit, $obj_delete, $obj_history_clear)
	{
		global $ms, $gsValues, $la;
		
		$status = false;
		
		$result = '';
		
		$email = strtolower($email);
		$username = strtolower($username);
		
		if (!checkEmailExists($email))
		{
			if ($username == '')
			{
				$username = $email;
			}
			
			if (!checkUsernameExists($username))
			{
				if ($password == '')
				{
					$password = genAccountPassword();
				}
	
				$privileges_ = json_decode(stripslashes($privileges),true);
				
				if (isset($_SESSION['LANGUAGE']))
				{
					$language = $_SESSION['LANGUAGE'];
				}
				else
				{
					$language = $gsValues['LANGUAGE'];
				}
				
				if (($privileges_['type'] == 'subuser') && (@$privileges_['au_active'] == true))
				{
					$url_au = $gsValues['URL_ROOT']."/index.php?au=".$privileges_['au'];
					$url_au_mobile = $gsValues['URL_ROOT']."/index.php?au=".$privileges_['au'].'&m=true';
					
					$template = getDefaultTemplate('account_registration_au', $language);
					
					$subject = $template['subject'];
					$message = $template['message'];
					
					$subject = str_replace("%SERVER_NAME%", $gsValues['NAME'], $subject);
					$subject = str_replace("%URL_AU%", $url_au, $subject);
					$subject = str_replace("%URL_AU_MOBILE%", $url_au_mobile, $subject);
					
					$message = str_replace("%SERVER_NAME%", $gsValues['NAME'], $message);
					$message = str_replace("%URL_AU%", $url_au, $message);
					$message = str_replace("%URL_AU_MOBILE%", $url_au_mobile, $message);
				}
				else
				{
					$template = getDefaultTemplate('account_registration', $language);
					
					$subject = $template['subject'];
					$message = $template['message'];
					
					$subject = str_replace("%SERVER_NAME%", $gsValues['NAME'], $subject);
					$subject = str_replace("%URL_LOGIN%", $gsValues['URL_LOGIN'], $subject);
					$subject = str_replace("%EMAIL%", $email, $subject);
					$subject = str_replace("%USERNAME%", $username, $subject);
					$subject = str_replace("%PASSWORD%", $password, $subject);
					
					$message = str_replace("%SERVER_NAME%", $gsValues['NAME'], $message);
					$message = str_replace("%URL_LOGIN%", $gsValues['URL_LOGIN'], $message);
					$message = str_replace("%EMAIL%", $email, $message);
					$message = str_replace("%USERNAME%", $username, $message);
					$message = str_replace("%PASSWORD%", $password, $message);
				}
				
				if ($send == 'true')
				{
					if (sendEmail($email, $subject, $message))
					{
						$status = true;
					}
				}
				else
				{
					$status = true;
				}
				
				
				if ($status == true)
				{
					if ($privileges_['type'] == 'subuser')
					{
						$api = '';
						$api_key = '';	
					}
					else
					{
						$api = $gsValues['API'];
						$api_key = genUserAPIKey($email);
					}
					
					if ($obj_limit == 'false')
					{
						$obj_limit_num = 0;
					}
					
					if ($obj_days == 'true')
					{
						$obj_days_dt = gmdate("Y-m-d", strtotime(gmdate("Y-m-d").' + '.$obj_days_num.' days'));
					}
					else
					{
						$obj_days_dt = '';
					}
									
					$dst = $gsValues['DST'];
					
					if ($dst == 'true')
					{
						$dst_start = $gsValues['DST_START'];
						$dst_end = $gsValues['DST_END'];	
					}
					else
					{
						$dst_start = '';
						$dst_end = '';	
					}
					
					$units = $gsValues['UNIT_OF_DISTANCE'].','.$gsValues['UNIT_OF_CAPACITY'].','.$gsValues['UNIT_OF_TEMPERATURE'];
					
					$q = "INSERT INTO gs_users (	`active`,
									`account_expire`,
									`account_expire_dt`,
									`privileges`,
									`manager_id`,
									`username`, 
									`password`, 
									`email`,
									`api`,
									`api_key`,
									`dt_reg`,
									`obj_add`, 
									`obj_limit`,
									`obj_limit_num`,
									`obj_days`,
									`obj_days_dt`,
									`obj_edit`,
									`obj_delete`,
									`obj_history_clear`,
									`currency`,
									`timezone`,
									`dst`,
									`dst_start`,
									`dst_end`,
									`language`,
									`units`,
									`map_sp`,
									`map_is`,
									`sms_gateway_server`)
									VALUES
									('".$active."',
									'".$account_expire."',
									'".$account_expire_dt."',
									'".$privileges."',
									'".$manager_id."',
									'".$username."',
									'".md5($password)."',
									'".$email."',
									'".$api."',
									'".$api_key."',
									'".gmdate("Y-m-d H:i:s")."',
									'".$obj_add."',
									'".$obj_limit."',
									'".$obj_limit_num."',
									'".$obj_days."',
									'".$obj_days_dt."',
									'".$obj_edit."',
									'".$obj_delete."',
									'".$obj_history_clear."',
									'".$gsValues['CURRENCY']."',
									'".$gsValues['TIMEZONE']."',
									'".$dst."',
									'".$dst_start."',
									'".$dst_end."',
									'".$gsValues['LANGUAGE']."',
									'".$units."',
									'last',
									'1',
									'".$gsValues['SMS_GATEWAY_SERVER']."'
									)";
									
					$r = mysqli_query($ms, $q);
					
					//write log
					writeLog('user_access', 'User registration: successful. E-mail: '.$email);
					
					$result = 'OK';
				}
				else
				{
					$result = 'ERROR_NOT_SENT';
				}
			}
			else
			{
				$result = 'ERROR_USERNAME_EXISTS';
			}
		}
		else
		{
			$result = 'ERROR_EMAIL_EXISTS';
		}
		
		return $result;
	}
	
	function delUser($id)
	{
		global $ms, $gsValues;
		
		$q = "DELETE FROM `gs_users` WHERE `id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		// delete user sub users
		$q = "DELETE FROM `gs_users` WHERE `privileges` LIKE '%subuser%' AND `manager_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		//$q = "DELETE FROM `gs_user_usage` WHERE `user_id`='".$id."'";
		//$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_billing_plans` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_zones` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_markers` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_objects` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_object_groups` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);		
		
		$q = "SELECT * FROM `gs_user_object_drivers` WHERE `user_id`='".$id."'";
  		$r = mysqli_query($ms, $q);
		
		while ($row = mysqli_fetch_array($r))
		{
			$img_file = $gsValues['PATH_ROOT'].'data/user/drivers/'.$row['driver_img_file'];
			if(is_file($img_file))
			{
				@unlink($img_file);
			}			
		}
		
		$q = "DELETE FROM `gs_user_object_drivers` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_object_passengers` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_object_trailers` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_object_cmd_exec` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_cmd` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_cmd_schedule` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_reports` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		// delete user events
		$q = "SELECT * FROM `gs_user_events` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		while ($row = mysqli_fetch_array($r))
		{
			$event_id = $row['event_id'];
			
			$q2 = "DELETE FROM `gs_user_events_status` WHERE `event_id`='".$event_id."'";				
			$r2 = mysqli_query($ms, $q2);
		}
		
		$q = "DELETE FROM `gs_user_events` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_last_events_data` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_events_data` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
	}
	
	function getUserObjectIMEIs($id)
	{
		global $ms;
		
		$result = false;
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		while($row = mysqli_fetch_array($r))
		{
			$result .= '"'.$row['imei'].'",';
		}
		$result = rtrim($result, ',');
		
		return $result;
	}
	
	function getUserBillingTotalObjects($id)
	{
		global $ms;
		
		$objects = 0;
		
		$q = "SELECT * FROM `gs_user_billing_plans` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		
		while($row = mysqli_fetch_array($r))
		{
			$objects += $row['objects'];	
		}
		
		return $objects;
	}
	
	function getUserNumberOfMarkers($id)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_user_markers` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		return $count;
	}
	
	function getUserNumberOfZones($id)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_user_zones` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		return $count;
	}
	
	function getUserNumberOfRoutes($id)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_user_routes` WHERE `user_id`='".$id."'";
		$r = mysqli_query($ms, $q);
		$count = mysqli_num_rows($r);
		
		return $count;
	}
	
	function checkUserPrivilegesArray($privileges)
	{
		global $gsValues;
		
		if (!isset($privileges["map_osm"])) { $privileges["map_osm"] = stringToBool($gsValues['MAP_OSM']); }
		if (!isset($privileges["map_bing"])) { $privileges["map_bing"] = stringToBool($gsValues['MAP_BING']); }
		if (!isset($privileges["map_google"])) { $privileges["map_google"] = stringToBool($gsValues['MAP_GOOGLE']); }
		if (!isset($privileges["map_google_street_view"])) { $privileges["map_google_street_view"] = stringToBool($gsValues['MAP_GOOGLE_STREET_VIEW']); }
		if (!isset($privileges["map_google_traffic"])) { $privileges["map_google_traffic"] = stringToBool($gsValues['MAP_GOOGLE_TRAFFIC']); }
		if (!isset($privileges["map_mapbox"])) { $privileges["map_mapbox"] = stringToBool($gsValues['MAP_MAPBOX']); }
		if (!isset($privileges["map_yandex"])) { $privileges["map_yandex"] = stringToBool($gsValues['MAP_YANDEX']); }
		if (!isset($privileges["history"])) { $privileges["history"] = true; }
		if (!isset($privileges["reports"])) { $privileges["reports"] = true; }
		if (!isset($privileges["tasks"])) { $privileges["tasks"] = true; }
		if (!isset($privileges["rilogbook"])) { $privileges["rilogbook"] = true; }
		if (!isset($privileges["dtc"])) { $privileges["dtc"] = true; }
		if (!isset($privileges["maintenance"])) { $privileges["maintenance"] = true; }
		if (!isset($privileges["object_control"])) { $privileges["object_control"] = true; }
		if (!isset($privileges["image_gallery"])) { $privileges["image_gallery"] = true; }
		if (!isset($privileges["chat"])) { $privileges["chat"] = true; }
		if (!isset($privileges["subaccounts"])) { $privileges["subaccounts"] = true; }
		
		return $privileges;
	}
	
	// #################################################
	//  END USER FUNCTIONS
	// #################################################
	
	// #################################################
	// OBJECT FUNCTIONS
	// #################################################
	
	function checkObjectLimitSystem()
	{
		global $ms, $gsValues;
				
		if ($gsValues['OBJECT_LIMIT'] == 0)
		{
			return false;
		}
		
		$q = "SELECT * FROM `gs_objects`";
		$r = mysqli_query($ms, $q);
		$num = mysqli_num_rows($r);
		
		if ($num >= $gsValues['OBJECT_LIMIT'])
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function checkObjectLimitUser($id)
	{
		global $ms;
		
		if ($_SESSION["obj_limit"] == 'true')
		{
			$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$id."'";
			$r = mysqli_query($ms, $q);
			$num = mysqli_num_rows($r);
			
			if($num >= $_SESSION["obj_limit_num"])
			{
				return true;
			}
			
			return false;
		}
		else
		{
			return false;
		}
	}
	
	function checkObjectExistsSystem($imei)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		if (!$r)
		{
			return false;
		}
		
		$num = mysqli_num_rows($r);
		if ($num >= 1)
		{
			return true;
		}
		return false;	
	}
	
	function checkObjectExistsUser($imei)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		if (!$r)
		{
			return false;
		}
		
		$num = mysqli_num_rows($r);
		if ($num >= 1)
		{
			return true;
		}
		return false;
	}
	
	function adjustObjectTime($imei, $dt)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		if($row)
		{
			if (strtotime($dt) > 0)
			{
				$dt = gmdate("Y-m-d H:i:s", strtotime($dt.$row["time_adj"]));
			}
		}
		
		return $dt;
	}
	
	function createObjectDataTable($imei)
	{
		global $ms;
		
		if (!checkObjectExistsSystem($imei)) return false;
		
		$q = "CREATE TABLE IF NOT EXISTS gs_object_data_".$imei."(	dt_server datetime NOT NULL,
										dt_tracker datetime NOT NULL,
										lat double,
										lng double,
										altitude double,
										angle double,
										speed double,
										params varchar(2048) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
										KEY `dt_tracker` (`dt_tracker`)
										) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin";
		$r = mysqli_query($ms, $q);
		
		return true;
	}
	
	function addObjectSystem($name, $imei, $active, $object_expire, $object_expire_dt, $manager_id)
	{
		global $ms;
		
		if (checkObjectExistsSystem($imei)) return false;
		
		$q = "INSERT INTO `gs_objects` (`imei`,
						`active`,
						`object_expire`,
						`object_expire_dt`,
						`manager_id`,
						`name`,
						`map_icon`,
						`icon`,
						`tail_color`,
						`tail_points`,
						`odometer_type`,
						`engine_hours_type`)
						VALUES
						('".$imei."',
						'".$active."',
						'".$object_expire."',
						'".$object_expire_dt."',
						'".$manager_id."',
						'".$name."',
						'arrow',
						'img/markers/objects/land-truck.svg',
						'#00FF44',
						7,
						'gps',
						'off')";		
		$r = mysqli_query($ms, $q);
		
		// delete from unused objects
		$q = "DELETE FROM `gs_objects_unused` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		//write log
		writeLog('object_op', 'Add object: successful. IMEI: '.$imei);
		
		return true;
	}
	
	function addObjectSystemExtended($name, $imei, $model, $vin, $plate_number, $device, $sim_number, $active, $object_expire, $object_expire_dt, $manager_id)
	{
		global $ms;
		
		$q = "INSERT INTO `gs_objects` (`imei`,
						`active`,
						`object_expire`,
						`object_expire_dt`,
						`manager_id`,
						`name`,
						`map_icon`,
						`icon`,
						`tail_color`,
						`tail_points`,
						`device`,
						`sim_number`,
						`model`,
						`vin`,
						`plate_number`,
						`odometer_type`,
						`engine_hours_type`)
						VALUES
						('".$imei."',
						'".$active."',
						'".$object_expire."',
						'".$object_expire_dt."',
						'".$manager_id."',
						'".$name."',
						'arrow',
						'img/markers/objects/land-truck.svg',
						'#00FF44',
						7,
						'".$device."',
						'".$sim_number."',
						'".$model."',
						'".$vin."',
						'".$plate_number."',
						'gps',
						'off')";		
		$r = mysqli_query($ms, $q);
		
		// delete from unused objects
		$q = "DELETE FROM `gs_objects_unused` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		//write log
		writeLog('object_op', 'Add object: successful. IMEI: '.$imei);
	}
	
	function addObjectUser($user_id, $imei, $group_id, $driver_id, $trailer_id)
	{
		global $ms;
		
		if (!$user_id) return false;
		
		if (!checkObjectExistsSystem($imei)) return false;
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id."' AND `imei`='".$imei."'";
                $r = mysqli_query($ms, $q);
                $num = mysqli_num_rows($r);
                if ($num == 0)
                {
			$q = "INSERT INTO `gs_user_objects` 	(`user_id`,
								`imei`,
								`group_id`,
								`driver_id`,
								`trailer_id`)
								VALUES (
								'".$user_id."',
								'".$imei."',
								'".$group_id."',
								'".$driver_id."',
								'".$trailer_id."')";
			$r = mysqli_query($ms, $q);
                }
		
		return true;
	}
	
	function duplicateObjectSystem($duplicate_imei, $imei, $object_expire, $object_expire_dt, $manager_id, $name)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$duplicate_imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		$q = "INSERT INTO `gs_objects` (`imei`,
						`active`,
						`object_expire`,
						`object_expire_dt`,
						`manager_id`,
						`name`,
						`icon`,
						`map_arrows`,
						`map_icon`,
						`tail_color`,
						`tail_points`,
						`device`,
						`sim_number`,
						`model`,
						`vin`,
						`plate_number`,
						`odometer_type`,
						`engine_hours_type`,
						`odometer`,
						`engine_hours`,
						`fcr`,
						`time_adj`,
						`accuracy`)
						VALUES
						('".$imei."',
						'true',
						'".$object_expire."',
						'".$object_expire_dt."',
						'".$manager_id."',
						'".$name."',
						'".$row['icon']."',
						'".$row['map_arrows']."',
						'".$row['map_icon']."',
						'".$row['tail_color']."',
						'".$row['tail_points']."',
						'".$row['device']."',
						'".$row['sim_number']."',
						'".$row['model']."',
						'".$row['vin']."',
						'".$row['plate_number']."',
						'".$row['odometer_type']."',
						'".$row['engine_hours_type']."',
						'".$row['odometer']."',
						'".$row['engine_hours']."',
						'".$row['fcr']."',
						'".$row['time_adj']."',
						'".$row['accuracy']."')";		
		$r = mysqli_query($ms, $q);
		
		$q = "SELECT * FROM `gs_object_sensors` WHERE `imei`='".$duplicate_imei."'";
		$r = mysqli_query($ms, $q);
		while($row = mysqli_fetch_array($r))
		{
			$q2 = "INSERT INTO `gs_object_sensors` (`imei`,
								`name`,
								`type`,
								`param`,
								`data_list`, 
								`popup`, 
								`result_type`,
								`text_1`,
								`text_0`,
								`units`,
								`lv`,
								`hv`,
								`formula`,
								`calibration`,
								`dictionary`)
								VALUES
								('".$imei."',
								'".$row['name']."',
								'".$row['type']."',
								'".$row['param']."',
								'".$row['data_list']."',
								'".$row['popup']."',
								'".$row['result_type']."',
								'".$row['text_1']."',
								'".$row['text_0']."',
								'".$row['units']."',
								'".$row['lv']."',
								'".$row['hv']."',
								'".$row['formula']."',
								'".$row['calibration']."',
								'".$row['dictionary']."')";
			$r2 = mysqli_query($ms, $q2);
		}
		
		$q = "SELECT * FROM `gs_object_services` WHERE `imei`='".$duplicate_imei."'";
		$r = mysqli_query($ms, $q);
		while($row = mysqli_fetch_array($r))
		{
			$q2 = "INSERT INTO `gs_object_services` (`imei`,
								`name`,
								`data_list`,
								`popup`,
								`odo`,
								`odo_interval`,
								`odo_last`, 
								`engh`,
								`engh_interval`,
								`engh_last`,
								`days`,
								`days_interval`,
								`days_last`,
								`odo_left`,
								`odo_left_num`,
								`engh_left`,
								`engh_left_num`,
								`days_left`,
								`days_left_num`,
								`update_last`,
								`notify_service_expire`)
								VALUES
								('".$imei."',
								'".$row['name']."',
								'".$row['data_list']."',
								'".$row['popup']."',
								'".$row['odo']."',
								'".$row['odo_interval']."',
								'".$row['odo_last']."',
								'".$row['engh']."',
								'".$row['engh_interval']."',
								'".$row['engh_last']."',
								'".$row['days']."',
								'".$row['days_interval']."',
								'".$row['days_last']."',
								'".$row['odo_left']."',
								'".$row['odo_left_num']."',
								'".$row['engh_left']."',
								'".$row['engh_left_num']."',
								'".$row['days_left']."',
								'".$row['days_left_num']."',
								'".$row['update_last']."',
								'".$row['notify_service_expire']."')";
			$r2 = mysqli_query($ms, $q2);
		}
		
		$q = "SELECT * FROM `gs_object_custom_fields` WHERE `imei`='".$duplicate_imei."'";
		$r = mysqli_query($ms, $q);
		while($row = mysqli_fetch_array($r))
		{
			$q2 = "INSERT INTO `gs_object_custom_fields` (`imei`,
									`name`,
									`value`,
									`data_list`, 
									`popup`)
									VALUES
									('".$imei."',
									'".$row['name']."',
									'".$row['value']."',
									'".$row['data_list']."',
									'".$row['popup']."')";
			$r2 = mysqli_query($ms, $q2);
		}
	}
	
	function delObjectUser($user_id, $imei)
	{
		global $ms;
		
		$q = "DELETE FROM `gs_user_objects` WHERE `user_id`='".$user_id."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_last_events_data` WHERE `user_id`='".$user_id."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_events_data` WHERE `user_id`='".$user_id."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);	
		
		$q = "DELETE FROM `gs_user_events_status` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		//write log
		writeLog('object_op', 'Delete object: successful. IMEI: '.$imei);
	}

	function delObjectSystem($imei)
	{
		global $ms, $gsValues;
		
		$q = "DELETE FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_rilogbook_data` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_dtc_data` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_object_sensors` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_object_services` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_object_custom_fields` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_last_events_data` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_events_data` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_events_status` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "SELECT * FROM `gs_object_img` WHERE `imei`='".$imei."'";
  		$r = mysqli_query($ms, $q);
		
		while($row = mysqli_fetch_array($r))
		{
			$img_file = $gsValues['PATH_ROOT'].'data/img/'.$row['img_file'];
			if(is_file($img_file))
			{
				@unlink($img_file);
			}			
		}
		
		$q = "DELETE FROM `gs_object_img` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_object_chat` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DROP TABLE gs_object_data_".$imei;
		$r = mysqli_query($ms, $q);
		
		//write log
		writeLog('object_op', 'Delete object: successful. IMEI: '.$imei);
	}
	
	function changeObjectIMEI($old_imei, $new_imei)
	{
		global $ms;
		
		$old_imei = strtoupper($old_imei);
		$new_imei = strtoupper($new_imei);
		
		if (checkObjectExistsSystem($new_imei))
		{
			return false;
		}
		
		// data table
		$q = "alter table gs_object_data_".$old_imei." rename to gs_object_data_".$new_imei;
		$r = mysqli_query($ms, $q);
		
		// gs_user_reports
		$q = "SELECT * FROM `gs_user_reports` WHERE `imei` LIKE '%".$old_imei."%'";
		$r = mysqli_query($ms, $q);
		
		while($row = mysqli_fetch_array($r))
		{
			$imeis = explode(',', $row['imei']);
			
			for ($i = 0; $i < count($imeis); ++$i)
			{
				if ($imeis[$i] == $old_imei)
				{
					$imeis[$i] = $new_imei;
				}
			}
			
			$imeis_ = implode(",", $imeis);
			
			$q2 = "UPDATE `gs_user_reports` SET `imei`='".$imeis_."' WHERE `report_id`='".$row['report_id']."'";
			$r2 = mysqli_query($ms, $q2);
		}
		
		// gs_user_events
		$q = "SELECT * FROM `gs_user_events` WHERE `imei` LIKE '%".$old_imei."%'";
		$r = mysqli_query($ms, $q);
		
		while($row = mysqli_fetch_array($r))
		{
			$imeis = explode(',', $row['imei']);
			
			for ($i = 0; $i < count($imeis); ++$i)
			{
				if ($imeis[$i] == $old_imei)
				{
					$imeis[$i] = $new_imei;
				}
			}
			
			$imeis_ = implode(",", $imeis);
			
			$q2 = "UPDATE `gs_user_events` SET `imei`='".$imeis_."' WHERE `event_id`='".$row['event_id']."'";
			$r2 = mysqli_query($ms, $q2);
		}
		
		// gs_user_cmd_schedule
		$q = "SELECT * FROM `gs_user_cmd_schedule` WHERE `imei` LIKE '%".$old_imei."%'";
		$r = mysqli_query($ms, $q);
		
		while($row = mysqli_fetch_array($r))
		{
			$imeis = explode(',', $row['imei']);
			
			for ($i = 0; $i < count($imeis); ++$i)
			{
				if ($imeis[$i] == $old_imei)
				{
					$imeis[$i] = $new_imei;
				}
			}
			
			$imeis_ = implode(",", $imeis);
			
			$q2 = "UPDATE `gs_user_cmd_schedule` SET `imei`='".$imeis_."' WHERE `cmd_id`='".$row['cmd_id']."'";
			$r2 = mysqli_query($ms, $q2);
		}
		
		// gs_user_last_events_data
		$q = "UPDATE `gs_user_last_events_data` SET `imei`='".$new_imei."' WHERE `imei`='".$old_imei."'";
		$r = mysqli_query($ms, $q);
		
		// gs_user_events_data
		$q = "UPDATE `gs_user_events_data` SET `imei`='".$new_imei."' WHERE `imei`='".$old_imei."'";
		$r = mysqli_query($ms, $q);
		
		// gs_user_events_status
		$q = "UPDATE `gs_user_events_status` SET `imei`='".$new_imei."' WHERE `imei`='".$old_imei."'";
		$r = mysqli_query($ms, $q);
		
		// gs_user_objects
		$q = "UPDATE `gs_user_objects` SET `imei`='".$new_imei."' WHERE `imei`='".$old_imei."'";
		$r = mysqli_query($ms, $q);
		
		// gs_objects
		$q = "UPDATE `gs_objects` SET `imei`='".$new_imei."' WHERE `imei`='".$old_imei."'";
		$r = mysqli_query($ms, $q);
		
		// gs_object_cmd_exec
		$q = "UPDATE `gs_object_cmd_exec` SET `imei`='".$new_imei."' WHERE `imei`='".$old_imei."'";
		$r = mysqli_query($ms, $q);
		
		// gs_object_tasks
		$q = "UPDATE `gs_object_tasks` SET `imei`='".$new_imei."' WHERE `imei`='".$old_imei."'";
		$r = mysqli_query($ms, $q);
		
		// gs_object_img
		$q = "UPDATE `gs_object_img` SET `imei`='".$new_imei."' WHERE `imei`='".$old_imei."'";
		$r = mysqli_query($ms, $q);
		
		// gs_object_chat
		$q = "UPDATE `gs_object_chat` SET `imei`='".$new_imei."' WHERE `imei`='".$old_imei."'";
		$r = mysqli_query($ms, $q);
		
		// gs_object_sensors
		$q = "UPDATE `gs_object_sensors` SET `imei`='".$new_imei."' WHERE `imei`='".$old_imei."'";
		$r = mysqli_query($ms, $q);
		
		// gs_object_services
		$q = "UPDATE `gs_object_services` SET `imei`='".$new_imei."' WHERE `imei`='".$old_imei."'";
		$r = mysqli_query($ms, $q);
		
		// gs_object_custom_fields
		$q = "UPDATE `gs_object_custom_fields` SET `imei`='".$new_imei."' WHERE `imei`='".$old_imei."'";
		$r = mysqli_query($ms, $q);
		
		// gs_rilogbook_data
		$q = "UPDATE `gs_rilogbook_data` SET `imei`='".$new_imei."' WHERE `imei`='".$old_imei."'";
		$r = mysqli_query($ms, $q);
		
		// gs_dtc_data
		$q = "UPDATE `gs_dtc_data` SET `imei`='".$new_imei."' WHERE `imei`='".$old_imei."'";
		$r = mysqli_query($ms, $q);
		
		// delete from unused objects
		$q = "DELETE FROM `gs_objects_unused` WHERE `imei`='".$new_imei."'";
		$r = mysqli_query($ms, $q);
		
		return true;
	}
	
	function clearObjectHistory($imei)
	{
		global $ms;
		
		$q = "DELETE FROM gs_object_data_".$imei;
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_rilogbook_data` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_dtc_data` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_last_events_data` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_events_data` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "DELETE FROM `gs_user_events_status` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		$q = "UPDATE `gs_objects` SET  `dt_server`='0000-00-00 00:00:00',
						`dt_tracker`='0000-00-00 00:00:00',
						`lat`='0',
						`lng`='0',
						`altitude`='0',
						`angle`='0',
						`speed`='0',
						`loc_valid`='0',
						`params`='',
						`dt_last_stop`='0000-00-00 00:00:00',
						`dt_last_idle`='0000-00-00 00:00:00',
						`dt_last_move`='0000-00-00 00:00:00'
						WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		//write log
		writeLog('object_op', 'Clear object history: successful. IMEI: '.$imei);
	}
	
	function checkObjectActive($imei)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		if ($row['active'] == 'true')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function moveObjectPlanToBilling($user_id, $imei)
	{
		global $ms, $gsValues, $la;
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `imei`='".$imei."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		if($row)
		{
			$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
			$r = mysqli_query($ms, $q);
			$row = mysqli_fetch_array($r);
			
			if (($row['active'] == 'true') && ($row['object_expire'] == 'true'))
			{
				$days_diff = ceil((strtotime($row['object_expire_dt']) - strtotime(gmdate("Y-m-d"))) / 86400);
				$days_diff -= 1; // reduce one day to prevent cheating
				
				if (($days_diff > 0) && ($days_diff > $gsValues['OBJ_DAYS_TRIAL']))
				{
					// add billing plan
					$dt_purchase = gmdate("Y-m-d H:i:s");
					$name = $la['RECOVER_FROM_IMEI'].' '.$imei;
					$objects = 1;
					$period = $days_diff;
					$period_type = 'days';
					$price = 0;
					
					$q = "INSERT INTO `gs_user_billing_plans` 	(`user_id`,
											`dt_purchase`,
											`name`,
											`objects`,
											`period`,
											`period_type`,
											`price`
											) VALUES (
											'".$user_id."',
											'".$dt_purchase."',
											'".$name."',
											'".$objects."',
											'".$period."',
											'".$period_type."',
											'".$price."')";
					$r = mysqli_query($ms, $q);
					
					// reduce object expiration date
					$q = "UPDATE `gs_objects` SET `object_expire_dt`='".gmdate("Y-m-d")."' WHERE `imei`='".$imei."'";
					$r = mysqli_query($ms, $q);
				}
			}
		}
	}
	
	function getObjectName($imei)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		return $row['name'];
	}
	
	function getObjectDriverFromSensor($user_id, $imei, $params)
	{
		global $ms;
		
		$driver = false;
		
		$driver_assign_id = false;
		
		$sensor = getSensorFromType($imei, 'da');
		
		if ($sensor != false)
		{
			$sensor_ = $sensor[0];
		
			$sensor_data = getSensorValue($params, $sensor_);
			$driver_assign_id = $sensor_data['value'];
		}
		else
		{
			return $driver;
		}
		
		$q = "SELECT * FROM `gs_user_object_drivers` WHERE UPPER(`driver_assign_id`)='".strtoupper($driver_assign_id)."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		$driver = mysqli_fetch_array($r);
		
		return $driver;
	}
	
	function getObjectTrailerFromSensor($user_id, $imei, $params)
	{
		global $ms;
		
		$trailer = false;
		
		$trailer_assign_id = false;
		
		$sensor = getSensorFromType($imei, 'ta');
		
		if ($sensor != false)
		{
			$sensor_ = $sensor[0];
			
			$sensor_data = getSensorValue($params, $sensor_);
			$trailer_assign_id = $sensor_data['value'];
		}
		else
		{
			return $trailer;                                      
		
		}
		
		$q = "SELECT * FROM `gs_user_object_trailers` WHERE UPPER(`trailer_assign_id`)='".strtoupper($trailer_assign_id)."' AND `user_id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		$trailer = mysqli_fetch_array($r);
		
		return $trailer;
	}
	
	function getObjectDriver($user_id, $imei, $params)
	{
		global $ms;
		
		$driver = false;
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id ."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		$driver_id = $row['driver_id'];
		
		if ($driver_id == '-1')
		{
			return $driver;
		}
		
		if ($driver_id == '0')
		{
			return getObjectDriverFromSensor($user_id, $imei, $params);
		}
	       
		$q = "SELECT * FROM `gs_user_object_drivers` WHERE `user_id`='".$user_id ."' AND `driver_id`='".$driver_id."'";
		$r = mysqli_query($ms, $q);
		$driver = mysqli_fetch_array($r);
		
		return $driver;
	}
	
	function getObjectTrailer($user_id, $imei, $params)
	{
		global $ms;
		
		$trailer = false;
		
		$q = "SELECT * FROM `gs_user_objects` WHERE `user_id`='".$user_id ."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		$trailer_id = $row['trailer_id'];
		
		if ($trailer_id == '-1')
		{
			return $trailer;
		}
		
		if ($trailer_id == '0')
		{
			return getObjectTrailerFromSensor($user_id, $imei, $params);
		}
	       
		$q = "SELECT * FROM `gs_user_object_trailers` WHERE `user_id`='".$user_id ."' AND `trailer_id`='".$trailer_id."'";
		$r = mysqli_query($ms, $q);
		$trailer = mysqli_fetch_array($r);
		
		return $trailer;
	}
	
	function getObjectOdometer($imei)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		return floor($row['odometer']);
	}
	
	function getObjectEngineHours($imei, $details)
	{
		global $ms;
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		if ($details)
		{
			return getTimeDetails($row['engine_hours'], false);	
		}
		else
		{
			return floor($row['engine_hours'] / 60 / 60);	
		}
	}
	
	function getObjectFCR($imei)
	{
		global $ms, $gsValues;
		
		// default fcr
		$default = array(	'source' => 'rates',
					'measurement' => 'l100km',
					'cost' => 0,
					'summer' => 0,
					'winter' => 0,
					'winter_start' => '12-01',
					'winter_end' => '03-01');
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		// set default fcr if not set in DB
		if (($row['fcr'] == '') || (json_decode($row['fcr'],true) == null))
		{
			$fcr = $default;
		}
		else
		{
			$fcr = json_decode($row['fcr'],true);
			
			if (!isset($fcr["source"])) { $fcr["source"] = $default["source"]; }
			if (!isset($fcr["measurement"])) { $fcr["measurement"] = $default["measurement"]; }
			if (!isset($fcr["cost"])) { $fcr["cost"] = $default["cost"]; }
			if (!isset($fcr["summer"])) { $fcr["summer"] = $default["summer"]; }
			if (!isset($fcr["winter"])) { $fcr["winter"] = $default["winter"]; }
			if (!isset($fcr["winter_start"])) { $fcr["winter_start"] = $default["winter_start"]; }
			if (!isset($fcr["winter_end"])) { $fcr["winter_end"] = $default["winter_end"]; }
		}
		
		return $fcr;
	}
	
	function getObjectAccuracy($imei)
	{
		global $ms, $gsValues;
		
		// default accuracy
		$default = array(	'stops' => 'gps',
					'min_moving_speed' => 6,
					'min_idle_speed' => 3,
					'min_diff_points' => 0.0005,
					'use_gpslev' => false,
					'min_gpslev' => 5,
					'use_hdop' => false,
					'max_hdop' => 3,
					'min_fuel_speed' => 10,
					'min_ff' => 10,
					'min_ft' => 10);
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		// set default accuracy if not set in DB
		if (($row['accuracy'] == '') || (json_decode($row['accuracy'],true) == null))
		{
			$accuracy = $default;			
		}
		else
		{
			$accuracy = json_decode($row['accuracy'],true);
			
			if (!isset($accuracy["stops"])) { $accuracy["stops"] = $default["stops"]; }
			if (!isset($accuracy["min_moving_speed"])) { $accuracy["min_moving_speed"] = $default["min_moving_speed"]; }
			if (!isset($accuracy["min_idle_speed"])) { $accuracy["min_idle_speed"] = $default["min_idle_speed"]; }
			if (!isset($accuracy["min_diff_points"])) { $accuracy["min_diff_points"] = $default["min_diff_points"]; }
			if (!isset($accuracy["use_gpslev"])) { $accuracy["use_gpslev"] = $default["use_gpslev"]; }
			if (!isset($accuracy["min_gpslev"])) { $accuracy["min_gpslev"] = $default["min_gpslev"]; }
			if (!isset($accuracy["use_hdop"])) { $accuracy["use_hdop"] = $default["use_hdop"]; }
			if (!isset($accuracy["max_hdop"])) { $accuracy["max_hdop"] = $default["max_hdop"]; }
			if (!isset($accuracy["min_fuel_speed"])) { $accuracy["min_fuel_speed"] = $default["min_fuel_speed"]; }
			if (!isset($accuracy["min_ff"])) { $accuracy["min_ff"] = $default["min_ff"]; }
			if (!isset($accuracy["min_ft"])) { $accuracy["stops"] = $default["stops"]; }
		}
		
		return $accuracy;
	}
	
	function getObjectSensors($imei)
	{
		global $ms;
		
		// get object sensor list
		$q = "SELECT * FROM `gs_object_sensors` WHERE `imei`='".$imei."' ORDER BY `name` ASC";
		$r = mysqli_query($ms, $q);
		
		$sensors = array();
		
		while($row=mysqli_fetch_array($r))
		{
			$sensor_id = $row['sensor_id'];
			
			$calibration = json_decode($row['calibration'], true);
			if ($calibration == null)
			{
				$calibration = array();	
			}
			
			$dictionary = json_decode($row['dictionary'], true);
			if ($dictionary == null)
			{
				$dictionary = array();	
			}
			
			$sensors[$sensor_id] = array(	'name' => $row['name'],
							'type' => $row['type'],
							'param' => $row['param'],
							'data_list' => $row['data_list'],
							'popup' => $row['popup'],
							'result_type' => $row['result_type'],
							'text_1' => $row['text_1'],
							'text_0' => $row['text_0'],
							'units' => $row['units'],
							'lv' => $row['lv'],
							'hv' => $row['hv'],
							'acc_ignore' => $row['acc_ignore'],
							'formula' => $row['formula'],
							'calibration' => $calibration,
							'dictionary' => $dictionary
							);
		}
		
		return $sensors;
	}
	
	function getObjectService($imei)
	{
		global $ms;
		
		// get object service list
		$q = "SELECT * FROM `gs_object_services` WHERE `imei`='".$imei."' ORDER BY `name` ASC";
		$r = mysqli_query($ms, $q);
		
		$service = array();
		
		while($row=mysqli_fetch_array($r))
		{
			$row['odo_interval'] = floor(convDistanceUnits($row['odo_interval'], 'km', $_SESSION["unit_distance"]));
			$row['odo_last'] = floor(convDistanceUnits($row['odo_last'], 'km', $_SESSION["unit_distance"]));
			$row['odo_left_num'] = floor(convDistanceUnits($row['odo_left_num'], 'km', $_SESSION["unit_distance"]));
			
			$service_id = $row['service_id'];
			$service[$service_id] = array(	'name' => $row['name'],
							'data_list' => $row['data_list'],
							'popup' => $row['popup'],
							'odo' => $row['odo'],
							'odo_interval' => $row['odo_interval'],
							'odo_last' => $row['odo_last'],
							'engh' => $row['engh'],
							'engh_interval' => $row['engh_interval'],
							'engh_last' => $row['engh_last'],
							'days' => $row['days'],
							'days_interval' => $row['days_interval'],
							'days_last' => $row['days_last'],
							'odo_left' => $row['odo_left'],
							'odo_left_num' => $row['odo_left_num'],
							'engh_left' => $row['engh_left'],
							'engh_left_num' => $row['engh_left_num'],
							'days_left' => $row['days_left'],
							'days_left_num' => $row['days_left_num'],
							'update_last' => $row['update_last']
							);
		}
		
		return $service;
	}
	
	function getObjectCustomFields($imei)
	{
		global $ms;
		
		// get object service list
		$q = "SELECT * FROM `gs_object_custom_fields` WHERE `imei`='".$imei."' ORDER BY `name` ASC";
		$r = mysqli_query($ms, $q);
		
		$custom_fields = array();
		
		while($row=mysqli_fetch_array($r))
		{			
			$field_id = $row['field_id'];
			$custom_fields[$field_id] = array(	'name' => $row['name'],
								'value' => $row['value'],
								'data_list' => $row['data_list'],
								'popup' => $row['popup']
								);
		}
		
		return $custom_fields;
	}
	
	function getUserExpireAvgDate($ids)
        {
		global $ms;
		
		$date_from_today = '';
                $total_days = 0;
                $count = 0;
		
		$ids_ = '';
		for ($i = 0; $i < count($ids); ++$i)
		{
			if ($_SESSION["user_id"] != $ids[$i])
			{
				$ids_ .= '"'.$ids[$i].'",';	
			}
		}
		$ids_ = rtrim($ids_, ',');
                
                $q = "SELECT * FROM `gs_users` WHERE `id` IN (".$ids_.")";
		$r = mysqli_query($ms, $q);
                
		if (!$r)
		{
			return $date_from_today;
		}
		
		while($row = mysqli_fetch_array($r))
		{			
			if ($row['account_expire'] == 'true')
			{
				$object_expire_dt = strtotime($row['account_expire_dt']);
				$today = strtotime(gmdate('Y-m-d'));
				
				$diff_days = round(($object_expire_dt - $today) / 86400);
				
				if ($diff_days > 0)
				{
					$total_days += $diff_days;
				}	
			}
			
			$count++;
		}	
                
		if ($count == 0)
		{
			return $date_from_today;
		}
		
		$total_days = round($total_days/$count);
		      
		$date_from_today = gmdate('Y-m-d', strtotime(gmdate('Y-m-d'). ' + '.$total_days.' days'));
                
		return $date_from_today;
        }
	
	function getObjectExpireAvgDate($imeis)
        {
		global $ms;
		
		$date_from_today = '';
                $total_days = 0;
                $count = 0;
		
		$imeis_ = '';
		for ($i = 0; $i < count($imeis); ++$i)
		{
			$imeis_ .= '"'.$imeis[$i].'",';
		}
		$imeis_ = rtrim($imeis_, ',');
                
                $q = "SELECT * FROM `gs_objects` WHERE `imei` IN (".$imeis_.")";
		$r = mysqli_query($ms, $q);
                
		if (!$r)
		{
			return $date_from_today;
		}
		
		while($row = mysqli_fetch_array($r))
		{			
			if ($row['object_expire'] == 'true')
			{
				$object_expire_dt = strtotime($row['object_expire_dt']);
				$today = strtotime(gmdate('Y-m-d'));
				
				$diff_days = round(($object_expire_dt - $today) / 86400);
				
				if ($diff_days > 0)
				{
					$total_days += $diff_days;
				}	
			}
			
			$count++;
		}	
                
		if ($count == 0)
		{
			return $date_from_today;
		}
		
		$total_days = round($total_days/$count);
		      
		$date_from_today = gmdate('Y-m-d', strtotime(gmdate('Y-m-d'). ' + '.$total_days.' days'));
                
		return $date_from_today;
        }
	
	function sendObjectSMSCommand($user_id, $imei, $name, $cmd)
	{
		global $ms, $gsValues;
		
		$result = false;
		
		// validate
		if (($imei == '') || ($cmd == '')) return $result;
		
		$imei = strtoupper($imei);
		
		//check user usage
		if (!checkUserUsage($user_id, 'sms')) return $result;
		
		// variables
		$cmd = str_replace("%IMEI%", $imei, $cmd);
		$cmd = str_replace("%imei%", $imei, $cmd);
		
		$q = "SELECT * FROM `gs_users` WHERE `id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		$ud = mysqli_fetch_array($r);
		
		$q = "SELECT * FROM `gs_objects` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		$od = mysqli_fetch_array($r);
		
		$number = $od['sim_number'];
		
		if ($ud['sms_gateway'] == 'true')
		{
			if ($ud['sms_gateway_type'] == 'http')
			{
				$result = sendSMSHTTP($ud['sms_gateway_url'], '', $number, $cmd);
			}
			else if ($ud['sms_gateway_type'] == 'app')
			{
				$result = sendSMSAPP($ud['sms_gateway_identifier'], '', $number, $cmd);
			}
		}
		else
		{
			if (($ud['sms_gateway_server'] == 'true') && ($gsValues['SMS_GATEWAY'] == 'true'))
			{
				if ($gsValues['SMS_GATEWAY_TYPE'] == 'http')
				{
					$result = sendSMSHTTP($gsValues['SMS_GATEWAY_URL'], $gsValues['SMS_GATEWAY_NUMBER_FILTER'], $number, $cmd);
				}
				else if ($gsValues['SMS_GATEWAY_TYPE'] == 'app')
				{
					$result = sendSMSAPP($gsValues['SMS_GATEWAY_IDENTIFIER'], $gsValues['SMS_GATEWAY_NUMBER_FILTER'], $number, $cmd);
				}
			}
		}
		
		if ($result == true)
		{
			$q = "INSERT INTO `gs_object_cmd_exec`(`user_id`,
								`dt_cmd`,
								`imei`,
								`name`,
								`gateway`,
								`type`,
								`cmd`,
								`status`)
								VALUES
								('".$user_id."',
								'".gmdate("Y-m-d H:i:s")."',
								'".$imei."',
								'".$name."',
								'sms',
								'ascii',
								'".$cmd."',							 
								'1')";
			$r = mysqli_query($ms, $q);
			
			//update user usage
			updateUserUsage($user_id, false, false, 1, false);
		}
		
		return $result;
	}
	
	function sendObjectGPRSCommand($user_id, $imei, $name, $type, $cmd)
	{
		global $ms;
		
		$result = false;
		
		// validate
		if (($imei == '') || ($cmd == '')) return $result;
		
		$imei = strtoupper($imei);
		$type = strtolower($type);
				
		if ($type == 'ascii')
		{
			// variables
			$cmd = str_replace("%IMEI%", $imei, $cmd);
			$cmd = str_replace("%imei%", $imei, $cmd);
		}
		else if ($type == 'hex')
		{
			$hex_imei = $imei;
			
			if (strlen($hex_imei) & 1)
			{
				$hex_imei = '0'.$hex_imei;
			}
			
			$cmd = strtoupper($cmd);
			
			// variables
			$cmd = str_replace("%IMEI%", $hex_imei, $cmd);
			
			if (!ctype_xdigit($cmd)) return $result;
		}
		else
		{
			return $result;
		}
		
		$q = "SELECT * FROM `gs_object_cmd_exec` WHERE `imei`='".$imei."' AND `type`='".$type."' AND `cmd`='".$cmd."' AND `status`='0'";
		$r = mysqli_query($ms, $q);
		$num = mysqli_num_rows($r);
		if ($num == 0)
		{
			$q = "INSERT INTO `gs_object_cmd_exec`(`user_id`,
								`dt_cmd`,
								`imei`,
								`name`,
								`gateway`,
								`type`,
								`cmd`,
								`status`)
								VALUES
								('".$user_id."',
								'".gmdate("Y-m-d H:i:s")."',
								'".$imei."',
								'".$name."',
								'gprs',
								'".$type."',
								'".$cmd."',							 
								'0')";
			$r = mysqli_query($ms, $q);
			
			$result = true;
		}
		
		return $result;
	}
	
	// #################################################
	// END OBJECT FUNCTIONS
	// #################################################
	
	// #################################################
	// SENSOR FUNCTIONS
	// #################################################
	
	function mergeParams($old, $new)
	{
		if (is_array($old) && is_array($new))
		{
			$new = array_merge($old, $new);	
		}
		
		return $new;
	}
	
	function getParamsArray($params)
	{
		$arr_params = array();
		
		if ($params != '')
		{
			$params = json_decode($params,true);
			
			if (is_array($params))
			{
				foreach ($params as $key => $value)
				{
					array_push($arr_params, $key);
				}
			}
		}
		
		return $arr_params;
	}
	
	function getParamValue($params, $param)
	{
		$result = 0;
		
		if (isset($params[$param]))
		{
			$result = $params[$param];
		}
		
		return $result;
	}
	
	function paramsToArray($params)
	{
		// keep compatibility with old software versions which used '|' and with software versions using JSON
		
		$arr_params = array();
		if (substr($params, -1) == '|')
		{
			$params = explode("|", $params);
			
			for ($i = 0; $i < count($params)-1; ++$i)
			{
				$param = explode("=", $params[$i]);
				$arr_params[$param[0]] = $param[1];
			}
		}
		else
		{
			$arr_params = json_decode($params,true);
		}
		
		if (!is_array($arr_params))
		{
			$arr_params = array();
		}
		
		return $arr_params;
	}
	
	function getSensorValue($params, $sensor)
	{
		$result = array();
		$result['value'] = 0;
		$result['value_full'] = '';
		
		$param_value = getParamValue($params, $sensor['param']);
		
		// formula
		if (($sensor['result_type'] == 'abs') || ($sensor['result_type'] == 'rel') || ($sensor['result_type'] == 'value'))
		{
			if ($sensor['formula'] != '')
			{
				$formula = strtolower($sensor['formula']);
				if (!is_numeric($param_value))
				{
					$param_value = 0;
				}
				$formula = str_replace('x',$param_value,$formula);
				$param_value = calcString($formula);
			}
		}
		
		if (($sensor['result_type'] == 'abs') || ($sensor['result_type'] == 'rel'))
		{
			$param_value = sprintf("%01.3f", $param_value);
			
			$result['value'] = $param_value;
			$result['value_full'] = $param_value;
		}
		else if ($sensor['result_type'] == 'logic')
		{
			if($param_value == 1)
			{
				$result['value'] = $param_value;
				$result['value_full'] = $sensor['text_1'];
			}
			else
			{
				$result['value'] = $param_value;
				$result['value_full'] = $sensor['text_0'];
			}
		}
		else if ($sensor['result_type'] == 'value')
		{
			// calibration
			$out_of_cal = true;
			
			$calibration = json_decode($sensor['calibration'], true);
			if ($calibration == null)
			{
				$calibration = array();	
			}
			
			if (count($calibration) >= 2)
			{
				// put all X values to separate array
				$x_arr = array();
				
				for ($i=0; $i<count($calibration); $i++)
				{
					$x_arr[] = $calibration[$i]['x'];
				}
			    
				sort($x_arr);
				
				for ($i=0; $i<count($x_arr)-1; $i++)
				{
					$x_low = $x_arr[$i];
					$x_high = $x_arr[$i+1];
					
					if (($param_value >= $x_low) && ($param_value <= $x_high))
					{
						// get Y low and high
						$y_low = 0;
						$y_high = 0;
						
						for($j=0; $j<count($calibration); $j++)
						{
							if ($calibration[$j]['x'] == $x_low)
							{
								$y_low = $calibration[$j]['y'];
							}
							
							if ($calibration[$j]['x'] == $x_high)
							{
								$y_high = $calibration[$j]['y'];
							}
						}
						
						// get coeficient
						$a = $param_value - $x_low;
						$b = $x_high - $x_low;
						
						$coef = ($a/$b);
						
						$c = $y_high - $y_low;
						$coef = $c * $coef;
						
						$param_value = $y_low + $coef;
						
						$out_of_cal = false;
						
						break;
					}
				}
			    
				if ($out_of_cal)
				{
					// check if lower than cal
					$x_low = $x_arr[0];
					
					if ($param_value < $x_low)
					{
						for($j=0; $j<count($calibration); $j++)
						{		    
							if ($calibration[$j]['x'] == $x_low)
							{
							    $param_value = $calibration[$j]['y'];
							}
						}
					}
					
					// check if higher than cal
					$x_high = end($x_arr);
					
					if ($param_value > $x_high)
					{		    
						for($j=0; $j<count($calibration); $j++)
						{		    
							if ($calibration[$j]['x'] == $x_high)
							{
							    $param_value = $calibration[$j]['y'];
							}
						}
					}
				}
			}
			
			$param_value = sprintf("%01.2f", $param_value);
			
			// dictionary
			// not needed for PHP version, only in JS
			
			$result['value'] = $param_value;
			$result['value_full'] = $param_value.' '.$sensor['units'];
		}
		else if ($sensor['result_type'] == 'string')
		{
			$result['value'] = $param_value;
			$result['value_full'] = $param_value;
		}
		else if ($sensor['result_type'] == 'percentage')
		{
			if (($param_value > $sensor['lv']) && ($param_value < $sensor['hv']))
			{
				$a = $param_value - $sensor['lv'];
				$b = $sensor['hv'] - $sensor['lv'];
				
				$result['value'] = floor(($a/$b) * 100);
			}
			else if ($param_value <= $sensor['lv'])
			{
				$result['value'] = 0;
			}
			else if ($param_value >= $sensor['hv'])
			{
				$result['value'] = 100;
			}
			
			$result['value_full'] = $result['value'].' %';
		}
		
		return $result;
	}
	
	function getSensors($imei)
	{
		global $ms;
		
		$result = array();
		
		$q = "SELECT * FROM `gs_object_sensors` WHERE `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
		
		while($sensor=mysqli_fetch_array($r))
		{
			$result[] = $sensor;
		}
		
		if (count($result) > 0)
		{
			return $result;
		}
		else
		{
			return false;
		}
	}
	
	function getSensorFromType($imei, $type)
	{
		global $ms;
		
		$result = array();
		
		$q = "SELECT * FROM `gs_object_sensors` WHERE `imei`='".$imei."' AND `type`='".$type."'";
		$r = mysqli_query($ms, $q);
		
		while($sensor=mysqli_fetch_array($r))
		{
			$result[] = $sensor;
		}
		
		if (count($result) > 0)
		{
			return $result;
		}
		else
		{
			return false;
		}
	}
	
	// #################################################
	// END SENSOR FUNCTIONS
	// #################################################
	
	// #################################################
	// MATH FUNCTIONS
	// #################################################
	
	// needed for older than PHP 5.4 version
	if (!function_exists('hex2bin'))
	{
		function hex2bin( $str )
		{
			$sbin = "";
			$len = strlen($str);
			for ($i = 0; $i < $len; $i += 2)
			{
				$sbin .= pack("H*", substr($str, $i, 2));
			}
			return $sbin;
		}
	}
	
	function calcString($str)
	{
		$result = 0;
		try
		{
			$str = trim($str);
			$str = preg_replace('/[^0-9\(\)+-\/\*.]/', '', $str);
			$str = $str.';';
			
			return $result + eval('return '.$str);	
		}
		catch (Exception $e)
		{
			return $result;
		}
	}
	
	function getUnits($units)
	{
		$result = array();
		
		$units = explode(",", $units);
		
		$result["unit_distance"] = @$units[0];
		if ($result["unit_distance"] == '')
		{
			$result["unit_distance"] = 'km';
		}
		
		$result["unit_capacity"] = @$units[1];
		if ($result["unit_capacity"] == '')
		{
			$result["unit_capacity"] = 'l';
		}
		
		$result["unit_temperature"] = @$units[2];
		if ($result["unit_temperature"] == '')
		{
			$result["unit_temperature"] = 'c';
		}
		
		return $result;
	}
	
	function convSpeedUnits($val, $from, $to)
	{
		return floor(convDistanceUnits($val, $from, $to));
	}
	
	function convDistanceUnits($val, $from, $to)
	{
		if ($from == 'km')
		{
			if ($to == 'mi')
			{
				$val = $val * 0.621371;
			}
			else if ($to == 'nm')
			{
				$val = $val * 0.539957;
			}
		}
		else if ($from == 'mi')
		{
			if ($to == 'km')
			{
				$val = $val * 1.60934;
			}
			else if ($to == 'nm')
			{
				$val = $val * 0.868976;
			}
		}
		else if ($from == 'nm')
		{
			if ($to == 'km')
			{
				$val = $val * 1.852;
			}
			else if ($to == 'nm')
			{
				$val = $val * 1.15078;
			}
		}
		
		return $val;	
	}
	
	function convAltitudeUnits($val, $from, $to)
	{
		if ($from == 'km')
		{
			if (($to == 'mi') || ($to == 'nm')) // to feet
			{
				$val = floor($val * 3.28084);
			}
		}
		
		return $val;
	}
	
	//function convTempUnits($val, $from, $to)
	//{
	//	
	//}
	
	function convDateToNum($dt)
        {
                $dt = str_replace('-', '', $dt);
                $dt = str_replace(':', '', $dt);
                $dt = str_replace(' ', '', $dt);
                
                return $dt;
        }
        
        function isDateInRange($dt, $start, $end)
        {
                 if ($start > $end)
                {
                        return ($dt > $start) || ($dt < $end);
                }
                else
                {
                        return ($dt > $start) && ($dt < $end);
                }
        }
	
	function getTimeDetails($sec, $show_days)
	{
		global $la;
		
		$seconds = 0;
 		$hours   = 0;
 		$minutes = 0;
		
		if($sec % 86400 <= 0){$days = $sec / 86400;}
		if($sec % 86400 > 0)
		{
			$rest = ($sec % 86400);
			$days = ($sec - $rest) / 86400;
			
			if($rest % 3600 > 0)
			{
				$rest1 = ($rest % 3600);
				$hours = ($rest - $rest1) / 3600;
				
				if($rest1 % 60 > 0)
				{
					$rest2 = ($rest1 % 60);
					$minutes = ($rest1 - $rest2) / 60;
					$seconds = $rest2;
				}
				else
				{
					$minutes = $rest1 / 60;
				}
			}
			else
			{
				$hours = $rest / 3600;
			}
		}
		
		if ($show_days == false)
		{
			$hours += $days * 24;
			$days = 0;
		}
		
		if($days > 0){$days = $days.' '.$la['UNIT_D'].' ';}
		else{$days = false;}
		if($hours > 0){$hours = $hours.' '.$la['UNIT_H'].' ';}
		else{$hours = false;}
		if($minutes > 0){$minutes = $minutes.' '.$la['UNIT_MIN'].' ';}
		else{$minutes = false;}
		$seconds = $seconds.' '.$la['UNIT_S'];
		
		return $days.$hours.$minutes.$seconds;
	}
	
	function getTimeDifferenceDetails($start_date, $end_date)
	{
		$diff = strtotime($end_date)-strtotime($start_date);
		return getTimeDetails($diff, true);
	}

	function getLengthBetweenCoordinates($lat1, $lon1, $lat2, $lon2)
	{
		if (($lat1 == $lat2) && ($lon1 == $lon2))
		{
			return 0;
		}
		
		$theta = $lon1 - $lon2; 
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
		$dist = acos($dist); 
		$dist = rad2deg($dist); 
		$km = $dist * 60 * 1.1515 * 1.609344;
		
		return sprintf("%01.6f", $km);
	}
	
	function getAngle($lat1, $lng1, $lat2, $lng2)
	{
		$angle = (rad2deg(atan2(sin(deg2rad($lng2) - deg2rad($lng1)) * cos(deg2rad($lat2)), cos(deg2rad($lat1)) * sin(deg2rad($lat2)) - sin(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($lng2) - deg2rad($lng1)))) + 360) % 360;
		
		return floor($angle);
	}
	
	function isPointInPolygon($vertices, $lat, $lng)
	{
		$polyX = array();
		$polyY = array();
		
		$ver_arr = explode(',', $vertices);
		
		// check for all X and Y
                if(!is_int(count($ver_arr)/2))
                {
                        array_pop($ver_arr);
                }
		
		$polySides = 0;
		$i = 0;
		
		while ($i < count($ver_arr))
		{
			$polyX[] = $ver_arr[$i+1];
			$polyY[] = $ver_arr[$i];
			
			$i+=2;
			$polySides++;
		}
		
		$j = $polySides-1 ;
		$oddNodes = 0;
		
		for ($i=0; $i<$polySides; $i++)
		{
			if ($polyY[$i]<$lat && $polyY[$j]>=$lat || $polyY[$j]<$lat && $polyY[$i]>=$lat)
			{
				if ($polyX[$i]+($lat-$polyY[$i])/($polyY[$j]-$polyY[$i])*($polyX[$j]-$polyX[$i])<$lng)
				{
					$oddNodes=!$oddNodes;
				}
			}
			$j=$i;
		}
		
		return $oddNodes;
	}
	       
        function isPointOnLine($points, $lat, $lng)
        {
		$new_points = array();
                
		$points = explode(',', $points);                
		
		// check for all X and Y
                if(!is_int(count($points)/2))
                {
                        array_pop($points);
                }
		
		$i = 0;		
		while ($i < count($points))
		{
                        $new_points[] = array($points[$i], $points[$i+1]);			
			$i+=2;
		}
                
                // add mid points
                $new_points = isPointOnLineAddMidPoints($new_points);
                $new_points = isPointOnLineAddMidPoints($new_points);
                 
                // find closes point
                for ($i=0; $i<count($new_points); $i++)
		{
                        $dist = getLengthBetweenCoordinates($new_points[$i][0], $new_points[$i][1], $lat, $lng);                        
			$dist = sprintf('%0.6f', $dist);
                        			                     
			if (!isset($distance))
			{
				$distance = $dist;
			}
			else
			{
				if ($distance > $dist)
				{
					$distance = $dist;                                        
				}	
			}
		}
		
                return $distance;                
        }
	
	function isPointOnLineAddMidPoints($points)
        {
                $new_points = array();
                
                for ($i=0; $i<count($points)-1; $i++)
		{
                        $new_points[] = array($points[$i][0], $points[$i][1]);
                        $new_points[] = array(($points[$i][0]+$points[$i+1][0])/2, ($points[$i][1]+$points[$i+1][1])/2);
                }
                
                // add last point
                $new_points[] = array($points[count($points)-1][0], $points[count($points)-1][1]);
                
                return $new_points;
        }
	
	// #################################################
	// END MATH FUNCTIONS
	// #################################################
	
	// #################################################
	// STRING/ARRAY/VALIDATION FUNCTIONS
	// #################################################
	
	function isFilePathValid($path)
	{
		$path = caseToLower($path);
		
		if ((strpos($path, '..') !== false) || (strpos($path, 'php') !== false))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	function isDateValid($date)
	{
		if (empty($date) or $date === '0000-00-00' or $date === '0000-00-00 00:00:00')
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	function stringToBool($str)
	{
		return filter_var($str, FILTER_VALIDATE_BOOLEAN);
	}

	function searchString($str, $findme)
	{
		return preg_match('/'.$findme.'/',$str);
	}
	
	function truncateString($text, $chars)
	{
		if (strlen($text) > $chars)
		{
			$text = substr($text, 0, $chars).'...';
		}
		return $text;
	}
	
	function caseToLower($str)
	{
		return mb_strtolower($str, 'UTF-8');
	}
	
	function caseToUpper($str)
	{
		return mb_strtoupper($str, 'UTF-8');
	}
	
	function caseFirstToUpper($str)
	{
		$fc = mb_strtoupper(mb_substr($str, 0, 1), 'UTF-8');
		return $fc.mb_substr($str, 1);
	}
	
	// #################################################
	// END STRING/ARRAY/VALIDATION FUNCTIONS
	// #################################################
	
	// #################################################
	// TEMPLATE FUNCTIONS
	// #################################################
	
	function getDefaultTemplate($name, $language)
	{
		global $ms;
		
		$result = false;
		
		$q = "SELECT * FROM `gs_templates` WHERE `name`='".$name."' AND `language`='".$language."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		if (!$row)
		{
			$q = "SELECT * FROM `gs_templates` WHERE `name`='".$name."' AND `language`='english'";
			$r = mysqli_query($ms, $q);
			$row = mysqli_fetch_array($r);
		}
		
		if ($row)
		{
			$result = array('subject' => $row['subject'], 'message' => $row['message']);	
		}
		
		return $result;
	}
	
	// #################################################
	// END TEMPLATE FUNCTIONS
	// #################################################
	
	// #################################################
	// GEOCODER FUNCTIONS
	// #################################################
	
	function getGeocoderCache($lat, $lng)
	{
		global $ms;
		
		$result = '';
		
		// set lat and lng search ranges
		$lat_a = $lat - 0.000050;
		$lat_b = $lat + 0.000050;
		
		$lng_a = $lng - 0.000050;
		$lng_b = $lng + 0.000050;
		
		$q = "SELECT * FROM gs_geocoder_cache WHERE (lat BETWEEN ".$lat_a." AND ".$lat_b.") AND (lng BETWEEN ".$lng_a." AND ".$lng_b.")";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		if ($row)
		{
			return $row['address'];
		}
		
		return $result;
	}
	
	function insertGeocoderCache($lat, $lng, $address)
	{
		global $ms;
		
		if (($lat == '') || ($lng == '') || ($address == ''))
		{
			return;
		}
		
		$q = "INSERT INTO `gs_geocoder_cache`(	`lat`,
							`lng`,
							`address`)
							VALUES
							('".$lat."',
							'".$lng."',
							'".mysqli_real_escape_string($ms, $address)."')";
		$r = mysqli_query($ms, $q);
	}
	
	// #################################################
	// END GEOCODER FUNCTIONS
	// #################################################
	
	// #################################################
	// THEME FUNCTIONS
	// #################################################
	
	function getThemeDefault()
	{
		$theme = array(	'login_dialog_logo' => 'yes',
				'login_dialog_logo_position' => 'left',
				'login_bg_color' => '#FFFFFF',
				'login_dialog_bg_color' => '#FFFFFF',
				'login_dialog_opacity' => 90,
				'login_dialog_h_position' => 'center',
				'login_dialog_v_position' => 'center',				
				'login_dialog_bottom_text' => '',
				'ui_top_panel_color' => '#FFFFFF',
				'ui_top_panel_border_color' => '#F5F5F5',
				'ui_top_panel_selection_color' => '#F5F5F5',
				'ui_dialog_titlebar_color' => '#2B82D4',
				'ui_accent_color_1' => '#2B82D4',
				'ui_accent_color_2' => '#FAB444',
				'ui_accent_color_3' => '#9CC602',
				'ui_accent_color_4' => '#808080',
				'ui_font_color' => '#444444',
				'ui_top_panel_font_color' => '#808080',
				'ui_top_panel_counters_font_color' => '#808080',
				'ui_heading_font_color_1' => '#2B82D4',
				'ui_heading_font_color_2' => '#808080');
		
		return $theme;
	}
	
	function getTheme()
	{
		global $ms;
		
		$theme = false;
		
		$q = "SELECT * FROM `gs_themes` WHERE `active`='true'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		if ($row)
		{
			if (($row['theme'] != '') && (json_decode($row['theme'],true) != null))
			{
				$theme = json_decode($row['theme'],true);		
			}	
		}
		
		return $theme;
	}
	
	// #################################################
	// END THEME FUNCTIONS
	// #################################################
	
	// #################################################
	// LANGUAGE FUNCTIONS
	// #################################################
	
	function loadLanguage($lng, $units = '')
	{
		global $ms, $la, $gsValues;
		
		if (!isFilePathValid($lng))
		{
			die;
		}
		
		// always load main english language to prevet error if something is not translated in another language
		include ($gsValues['PATH_ROOT'].'lng/english/lng_main.php');
		
		// load another language
		if ($lng != 'english')
		{
			$lng = $gsValues['PATH_ROOT'].'lng/'.$lng.'/lng_main.php';
												
			if (file_exists($lng))
			{
				include($lng);
			}
		}
		
		// set unit strings
		$units = getUnits($units);
		
		if ($units["unit_distance"] == 'km')
		{
			$la["UNIT_SPEED"] = $la['UNIT_KPH'];
			$la["UNIT_DISTANCE"] = $la['UNIT_KM'];
			$la["UNIT_HEIGHT"] = $la['UNIT_M'];
		}
		else if ($units["unit_distance"] == 'mi')
		{
			$la["UNIT_SPEED"] = $la['UNIT_MPH'];
			$la["UNIT_DISTANCE"] = $la['UNIT_MI'];
			$la["UNIT_HEIGHT"] = $la['UNIT_FT'];
		}
		else if ($units["unit_distance"] == 'nm')
		{
			$la["UNIT_SPEED"] = $la['UNIT_KN'];
			$la["UNIT_DISTANCE"] = $la['UNIT_NM'];
			$la["UNIT_HEIGHT"] = $la['UNIT_FT'];
		}
		
		if ($units["unit_capacity"] == 'l')
		{
			$la["UNIT_CAPACITY"] = $la['UNIT_LITERS'];
		}
		else
		{
			$la["UNIT_CAPACITY"] = $la['UNIT_GALLONS'];
		}
		
		if ($units["unit_temperature"] == 'c')
		{
			$la["UNIT_TEMPERATURE"] = 'C';
		}
		else
		{
			$la["UNIT_TEMPERATURE"] = 'F';
		}
	}
	
	function getLanguageList()
	{
		global $ms, $gsValues;
		
		$result = '';
		$languages = array();
		
		$q = "SELECT * FROM `gs_system` WHERE `key`='LANGUAGES'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		$languages = explode(",", $row['value']);
		
		array_unshift($languages , 'english');
			
		foreach ($languages as $value)
		{
			if ($value != '')
			{
				$result .= '<option value="'.$value.'">'.ucfirst($value).'</option>';	
			}
		}

		return $result;
	}
	
	// #################################################
	// END LANGUAGE FUNCTIONS
	// #################################################
	
	// #################################################
	// FILE FUNCTIONS
	// #################################################
	
	function getFileList($path)
	{
		global $gsValues;
		
		if (!isFilePathValid($path))
		{
			die;
		}
		
		$filter = false;
		
		if ($path == 'data/user/places')
		{
			$filter = $_SESSION['user_id'].'_';
		}
		
		if ($path == 'data/user/objects')
		{
			$filter = $_SESSION['user_id'].'_';
		}
		
		$dh = opendir($gsValues['PATH_ROOT'].$path);
	    
		$result = array();
		    
		while (($file = readdir($dh)) !== false)
		{
			if ($file != '.' && $file != '..' && $file != 'Thumbs.db')
			{
				if ($filter != false)
				{
					if (0 === strpos($file, $filter))
					{
						$result[] = $file;
					}
				}
				else
				{
					$result[] = $file;
				}
			}
		}
		
		closedir($dh);
		
		sort($result);
		
		return $result;
	}
	
	// #################################################
	// END FILE FUNCTIONS
	// #################################################
	
	// #################################################
	// USAGE FUNCTIONS
	// #################################################
	
	function checkUserUsage($user_id, $service)
	{
		global $gsValues, $ms;
		
		$result = false;
		
		if ($user_id == false)
		{
			die;
		}
		
		// get gs_users counters
		$q = "SELECT * FROM `gs_users` WHERE `id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		$email = $row['usage_email_daily'];
		$sms = $row['usage_sms_daily'];
		$api = $row['usage_api_daily'];
		
		$email_cnt = $row['usage_email_daily_cnt'];
		$sms_cnt = $row['usage_sms_daily_cnt'];
		$api_cnt = $row['usage_api_daily_cnt'];
		
		if ($service == 'email')
		{
			if ($email != '')
                        {
                                if ($email_cnt < $email)
                                {
                                        $result = true;
                                }
                        }
                        else
                        {
                                if ($email_cnt < $gsValues['USAGE_EMAIL_DAILY'])
                                {
                                        $result = true;
                                }
                        }  
		}
		
		if ($service == 'sms')
		{
			if ($sms != '')
                        {
                                if ($sms_cnt < $sms)
                                {
                                        $result = true;
                                }
                        }
                        else
                        {
                                if ($sms_cnt < $gsValues['USAGE_SMS_DAILY'])
                                {
                                        $result = true;
                                }
                        }  
		}
		
		if ($service == 'api')
		{
			if ($api != '')
                        {
                                if ($api_cnt < $api)
                                {
                                        $result = true;
                                }
                        }
                        else
                        {
                                if ($api_cnt < $gsValues['USAGE_API_DAILY'])
                                {
                                        $result = true;
                                }
                        }  
		}
		
		return $result;
	}
	
	function updateUserUsage($user_id, $login, $email, $sms, $api)
	{
		global $ms;
		
		if ($user_id == false)
		{
			die;
		}
		
		$date = gmdate("Y-m-d");
		
		if ($login == false){$login = 0;}
		if ($email == false){$email = 0;}
		if ($sms == false){$sms = 0;}
		if ($api == false){$api = 0;}
		
		// update gs_users counters
		$q = "UPDATE gs_users SET 	usage_email_daily_cnt=usage_email_daily_cnt+".$email.",
						usage_sms_daily_cnt=usage_sms_daily_cnt+".$sms.",
						usage_api_daily_cnt=usage_api_daily_cnt+".$api."
						WHERE id='".$user_id."'";	
		$r = mysqli_query($ms, $q);
		
		// get gs_users counters
		$q = "SELECT * FROM `gs_users` WHERE `id`='".$user_id."'";
		$r = mysqli_query($ms, $q);
		$row = mysqli_fetch_array($r);
		
		$email = $row['usage_email_daily_cnt'];
		$sms = $row['usage_sms_daily_cnt'];
		$api = $row['usage_api_daily_cnt'];
		
		// add/update user usage table
		$q = "SELECT * FROM `gs_user_usage` WHERE `user_id`='".$user_id."' AND `dt_usage`='".$date."'";
		$r = mysqli_query($ms, $q);
		
		$row = mysqli_fetch_array($r);
		
		if ($row)
		{
			$q = "UPDATE gs_user_usage SET 	login=login+".$login.",
								email=".$email.",
								sms=".$sms.",
								api=".$api."
								WHERE usage_id='".$row['usage_id']."'";	
			$r = mysqli_query($ms, $q);	
		}
	}
	
	// #################################################
	// END USAGE FUNCTIONS
	// #################################################
	
	// #################################################
	// LOG FUNCTIONS
	// #################################################
	
	function writeLog($log, $log_data)
	{
		global $ms, $gsValues;
		
		$file = gmdate("Y_m").'_'.$log.'.log';
		$path = $gsValues['PATH_ROOT'].'logs/'.$file;
		
		$str = '['.gmdate("Y-m-d H:i:s").'] '.$_SERVER['REMOTE_ADDR'].' ';
		
		if (isset($_SESSION["user_id"]) && isset($_SESSION["username"]))
		{
			$str .= '['.$_SESSION["user_id"].']'.$_SESSION["username"].' ';	
		}
		
		$str .= '- '.$log_data."\r\n";
		
		file_put_contents($path, $str, FILE_APPEND);
	}
	
	// #################################################
	// END LOG FUNCTIONS
	// #################################################
?>