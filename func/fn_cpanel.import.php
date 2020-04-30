<?
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	checkUserSession();
	checkUserCPanelPrivileges();
	
	loadLanguage($_SESSION["language"], $_SESSION["units"]);
	
	if(@$_POST['format'] == 'user_csv')
	{
                if ($_SESSION["cpanel_privileges"] == 'manager')
                {
                        die;
                }
                
                $data = json_decode(stripslashes($_POST['data']),true);
                
                for ($i=0; $i<count($data); ++$i)
                {
                        $username = mysqli_real_escape_string($ms, $data[$i]['username']);
                        $email = mysqli_real_escape_string($ms, $data[$i]['email']);
                        $password = $data[$i]['password'];                        
                        
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
                        
                        addUser('false', 'true', 'false', '', $privileges, '', $username, $email, $password, $gsValues['OBJ_ADD'], $gsValues['OBJ_LIMIT'], $gsValues['OBJ_LIMIT_NUM'], $gsValues['OBJ_DAYS'], $gsValues['OBJ_DAYS_NUM'], $gsValues['OBJ_EDIT'], $gsValues['OBJ_DELETE'], $gsValues['OBJ_HISTORY_CLEAR']);  
                }
                
                echo 'OK';
                die;
        }
        
        if(@$_POST['format'] == 'object_csv')
	{
                if ($_SESSION["cpanel_privileges"] == 'manager')
                {
                        die;
                }
                
                $data = json_decode(stripslashes($_POST['data']),true);
                
                for ($i=0; $i<count($data); ++$i)
                {
                        $name = mysqli_real_escape_string($ms, $data[$i]['name']);
                        $imei = mysqli_real_escape_string($ms, $data[$i]['imei']);
                        
                        if ((!ctype_alnum($imei)) && (strlen($imei) > 15))
                        {
                                continue;
                        }
                        
                        if (isset($data[$i]['model']))
                        {
                                $model = mysqli_real_escape_string($ms, $data[$i]['model']);
                        }
                        else
                        {
                                $model = '';
                        }
                        
                        if (isset($data[$i]['vin']))
                        {
                                $vin = mysqli_real_escape_string($ms, $data[$i]['vin']);
                        }
                        else
                        {
                                $vin = '';
                        }
                        
                        if (isset($data[$i]['plate_number']))
                        {
                                $plate_number = mysqli_real_escape_string($ms, $data[$i]['plate_number']);
                        }
                        else
                        {
                                $plate_number = '';
                        }
                        
                        if (isset($data[$i]['device']))
                        {
                                $device = mysqli_real_escape_string($ms, $data[$i]['device']);
                        }
                        else
                        {
                                $device = '';
                        }
                        
                        if (isset($data[$i]['sim_number']))
                        {
                                $sim_number = mysqli_real_escape_string($ms, $data[$i]['sim_number']);
                        }
                        else
                        {
                                $sim_number = '';
                        }
                        
                        if (checkObjectLimitSystem())
			{
				echo 'ERROR_SYSTEM_OBJECT_LIMIT';
				die;
			}
                        
                        if (!checkObjectExistsSystem($imei))
			{
                                addObjectSystemExtended($name, $imei, $model, $vin, $plate_number, $device, $sim_number, 'true', 'false', '', 0);
                               
                                createObjectDataTable($imei);       
                        }
                }
                
                echo 'OK';
                die;
        }
?>