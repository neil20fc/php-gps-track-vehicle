function userAdd(cmd)
{
	switch (cmd)
	{
		case "open":
			document.getElementById('dialog_user_add_email').value = '';
			$("#dialog_user_add").dialog("open");
			break;
		case "register":
			var email = document.getElementById('dialog_user_add_email').value;
			var send = document.getElementById('dialog_user_add_send').checked;
			
			if(!isEmailValid(email))
			{
				notifyDialog(la['THIS_EMAIL_IS_NOT_VALID']);
				return;
			}
			
			var data = {
				cmd: 'register_user',
				email: email,
				send: send,
				manager_id: cpValues['manager_id']
			};
			
		   $.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						initStats();
						initSelectList('manager_list');
						
						$('#cpanel_user_list_grid').trigger("reloadGrid");
						$("#dialog_user_add").dialog("close");
					}
					else if (result == 'ERROR_EMAIL_EXISTS')
					{
						notifyDialog(la['THIS_EMAIL_ALREADY_EXISTS']);
					}
					else if (result == 'ERROR_NOT_SENT')
					{
						notifyDialog(la['CANT_SEND_EMAIL'] + ' ' + la['CONTACT_ADMINISTRATOR']);
					}
				}
			});
			break;
		case "cancel":
			$("#dialog_user_add").dialog("close");
			break;
	}
}

function userLogin(id)
{
	var data = {
		cmd: 'login_user',
		id: id
	};
	
	$.ajax({
		type: "POST",
		url: "func/fn_cpanel.users.php",
		data: data,
		success: function(result)
		{
			if (result == 'OK')
			{
				location.href = 'tracking.php';
			}
		}
	});
}

function userEditLogin()
{
	userLogin(cpValues['user_edit_id']);
}

function userEdit(cmd)
{
	switch (cmd)
	{
		default:
			cpValues['user_edit_id'] = cmd;
			
			var data = {
				cmd: 'load_user_data',
				user_id: cpValues['user_edit_id']
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				dataType: 'json',
				cache: false,
				success: function(result)
				{					
					// set values
					document.getElementById('dialog_user_edit_account_active').checked = strToBoolean(result['active']);
					
					document.getElementById('dialog_user_edit_account_expire').checked = strToBoolean(result['account_expire']);
					
					if (document.getElementById('dialog_user_edit_account_expire').checked == true)
					{
                                                document.getElementById('dialog_user_edit_account_expire_dt').value = result['account_expire_dt'];
                                        }
					else
					{
						document.getElementById('dialog_user_edit_account_expire_dt').value = '';
					}
					
					document.getElementById('dialog_user_edit_account_username').value = result['username'];
					document.getElementById('dialog_user_edit_account_email').value = result['email'];
					document.getElementById('dialog_user_edit_account_password').value = '';					

					var privileges = result['privileges'];
					
					if (cpValues['privileges'] == 'super_admin')
					{
						initSelectList("privileges_list_super_admin");
					}
					else if (cpValues['privileges'] == 'admin')
					{
						if (privileges['type'] == 'admin')
						{
							initSelectList("privileges_list_admin");
						}
						else
						{
							initSelectList("privileges_list_manager");
						}
					}
					else
					{
						if (privileges['type'] == 'manager')
						{
							initSelectList("privileges_list_manager");
						}
						else
						{
							initSelectList("privileges_list_user");
						}
					}
					
					document.getElementById('dialog_user_edit_account_privileges').value = privileges['type'];
					$("#dialog_user_edit_account_privileges").multipleSelect('refresh');
					
					if (cpValues['privileges'] != 'manager')
					{
						document.getElementById('dialog_user_edit_account_manager_id').value = result['manager_id'];
						$("#dialog_user_edit_account_manager_id").multipleSelect('refresh');
						document.getElementById('dialog_user_edit_account_manager_billing').value = result['manager_billing'];
						$("#dialog_user_edit_account_manager_billing").multipleSelect('refresh');
						
						document.getElementById('dialog_user_edit_account_obj_add').value = result['obj_add'];
						$("#dialog_user_edit_account_obj_add").multipleSelect('refresh');
						document.getElementById('dialog_user_edit_account_obj_limit').value = result['obj_limit'];
						$("#dialog_user_edit_account_obj_limit").multipleSelect('refresh');
						
						if (result['obj_limit'] == 'true')
						{
							document.getElementById('dialog_user_edit_account_obj_limit_num').value = result['obj_limit_num'];
						}
						else
						{
							document.getElementById('dialog_user_edit_account_obj_limit_num').value = '';
						}
						
						document.getElementById('dialog_user_edit_account_obj_days').value = result['obj_days'];
						$("#dialog_user_edit_account_obj_days").multipleSelect('refresh');
						
						if (result['obj_days'] == 'true')
						{
							document.getElementById('dialog_user_edit_account_obj_days_dt').value = result['obj_days_dt'];
						}
						else
						{
							document.getElementById('dialog_user_edit_account_obj_days_dt').value = '';
						}
					}
					
					document.getElementById('dialog_user_edit_account_map_osm').value = privileges['map_osm'];
					$("#dialog_user_edit_account_map_osm").multipleSelect('refresh');
					document.getElementById('dialog_user_edit_account_map_bing').value = privileges['map_bing'];
					$("#dialog_user_edit_account_map_bing").multipleSelect('refresh');
					document.getElementById('dialog_user_edit_account_map_google').value = privileges['map_google'];
					$("#dialog_user_edit_account_map_google").multipleSelect('refresh');
					document.getElementById('dialog_user_edit_account_map_google_street_view').value = privileges['map_google_street_view'];
					$("#dialog_user_edit_account_map_google_street_view").multipleSelect('refresh');
					document.getElementById('dialog_user_edit_account_map_google_traffic').value = privileges['map_google_traffic'];
					$("#dialog_user_edit_account_map_google_traffic").multipleSelect('refresh');
					document.getElementById('dialog_user_edit_account_map_mapbox').value = privileges['map_mapbox'];
					$("#dialog_user_edit_account_map_mapbox").multipleSelect('refresh');
					document.getElementById('dialog_user_edit_account_map_yandex').value = privileges['map_yandex'];
					$("#dialog_user_edit_account_map_yandex").multipleSelect('refresh');
					
					document.getElementById('dialog_user_edit_account_obj_edit').value = result['obj_edit'];
					$("#dialog_user_edit_account_obj_edit").multipleSelect('refresh');
					document.getElementById('dialog_user_edit_account_obj_delete').value = result['obj_delete'];
					$("#dialog_user_edit_account_obj_delete").multipleSelect('refresh');
					document.getElementById('dialog_user_edit_account_obj_history_clear').value = result['obj_history_clear'];
					$("#dialog_user_edit_account_obj_history_clear").multipleSelect('refresh');
					
					document.getElementById('dialog_user_edit_account_history').value = privileges['history'];
					$("#dialog_user_edit_account_history").multipleSelect('refresh');
					document.getElementById('dialog_user_edit_account_reports').value = privileges['reports'];
					$("#dialog_user_edit_account_reports").multipleSelect('refresh');
					document.getElementById('dialog_user_edit_account_tasks').value = privileges['tasks'];
					$("#dialog_user_edit_account_tasks").multipleSelect('refresh');
					document.getElementById('dialog_user_edit_account_rilogbook').value = privileges['rilogbook'];
					$("#dialog_user_edit_account_rilogbook").multipleSelect('refresh');
					document.getElementById('dialog_user_edit_account_dtc').value = privileges['dtc'];
					$("#dialog_user_edit_account_dtc").multipleSelect('refresh');
					document.getElementById('dialog_user_edit_account_maintenance').value = privileges['maintenance'];
					$("#dialog_user_edit_account_maintenance").multipleSelect('refresh');
					document.getElementById('dialog_user_edit_account_object_control').value = privileges['object_control'];
					$("#dialog_user_edit_account_object_control").multipleSelect('refresh');
					document.getElementById('dialog_user_edit_account_image_gallery').value = privileges['image_gallery'];
					$("#dialog_user_edit_account_image_gallery").multipleSelect('refresh');
					document.getElementById('dialog_user_edit_account_chat').value = privileges['chat'];
					$("#dialog_user_edit_account_chat").multipleSelect('refresh');
					document.getElementById('dialog_user_edit_account_subaccounts').value = privileges['subaccounts'];
					$("#dialog_user_edit_account_subaccounts").multipleSelect('refresh');
					
					document.getElementById('dialog_user_edit_account_sms_gateway_server').value = result['sms_gateway_server'];
					$("#dialog_user_edit_account_sms_gateway_server").multipleSelect('refresh');

					document.getElementById('dialog_user_edit_api_active').value = result['api'];
					$("#dialog_user_edit_api_active").multipleSelect('refresh');
					document.getElementById('dialog_user_edit_api_key').value = result['api_key'];
					
					document.getElementById('dialog_user_edit_places_markers').value = result['places_markers'];
					document.getElementById('dialog_user_edit_places_routes').value = result['places_routes'];
					document.getElementById('dialog_user_edit_places_zones').value = result['places_zones'];
					
					document.getElementById('dialog_user_edit_usage_email_daily').value = result['usage_email_daily'];
					document.getElementById('dialog_user_edit_usage_sms_daily').value = result['usage_sms_daily'];
					document.getElementById('dialog_user_edit_usage_api_daily').value = result['usage_api_daily'];
					
					var info = result['info'];
					
					document.getElementById('dialog_user_edit_account_contact_surname').value = info['name'];
					document.getElementById('dialog_user_edit_account_contact_company').value = info['company'];
					document.getElementById('dialog_user_edit_account_contact_address').value = info['address'];
					document.getElementById('dialog_user_edit_account_contact_post_code').value = info['post_code'];
					document.getElementById('dialog_user_edit_account_contact_city').value = info['city'];
					document.getElementById('dialog_user_edit_account_contact_country').value = info['country'];
					document.getElementById('dialog_user_edit_account_contact_phone1').value = info['phone1'];
					document.getElementById('dialog_user_edit_account_contact_phone2').value = info['phone2'];
					document.getElementById('dialog_user_edit_account_contact_email').value = info['email'];
					
					document.getElementById('dialog_user_edit_account_comment').value = result['comment'];
					
					// set values for later check while saving
					cpValues['user_edit_privileges'] = privileges['type'];
					
					// set object edit properties availability
					userEditCheck();
				}
			});
			
			$('#dialog_user_edit_object_list_grid').setGridParam({url:'func/fn_cpanel.users.php?cmd=load_user_object_list&id=' + cpValues['user_edit_id']});
			$('#dialog_user_edit_object_list_grid').trigger("reloadGrid");
			
			$('#dialog_user_edit_subaccount_list_grid').setGridParam({url:'func/fn_cpanel.users.php?cmd=load_user_subaccount_list&id=' + cpValues['user_edit_id']});
			$('#dialog_user_edit_subaccount_list_grid').trigger("reloadGrid");
			
			$('#dialog_user_edit_billing_plan_list_grid').setGridParam({url:'func/fn_cpanel.users.php?cmd=load_user_billing_plan_list&id=' + cpValues['user_edit_id']});
			$('#dialog_user_edit_billing_plan_list_grid').trigger("reloadGrid");
			
			$('#dialog_user_edit_usage_list_grid').setGridParam({url:'func/fn_cpanel.users.php?cmd=load_user_usage_list&id=' + cpValues['user_edit_id']});
			$('#dialog_user_edit_usage_list_grid').trigger("reloadGrid");
			
			$("#dialog_user_edit").dialog("open");
			break;
		case "save":
			var active = document.getElementById('dialog_user_edit_account_active').checked;
			
			var account_expire = document.getElementById('dialog_user_edit_account_expire').checked;
			var account_expire_dt = document.getElementById('dialog_user_edit_account_expire_dt').value;
			// expire account
			if (account_expire == true)
			{
				if (account_expire_dt == '')
				{
					notifyDialog(la['DATE_CANT_BE_EMPTY']);
					break;
				}
			}
			else
			{
                                account_expire_dt = '';
                        }
			
			var username = document.getElementById('dialog_user_edit_account_username').value;
			// username check
			if (username == '')
			{
				notifyDialog(la['USERNAME_CANT_BE_EMPTY']);
				break;
			}
			
			if (username.indexOf(" ") != -1)
			{
				notifyDialog(la['USERNAME_SPACE_CHARACTERS']);
				break;
			}
			
			var password = document.getElementById('dialog_user_edit_account_password').value;
			
			var email = document.getElementById('dialog_user_edit_account_email').value;
			// email check
			if(!isEmailValid(email))
			{
				notifyDialog(la['THIS_EMAIL_IS_NOT_VALID']);
				break;
			}
			
			var privileges_ = document.getElementById('dialog_user_edit_account_privileges').value;
			
			var map_osm = document.getElementById('dialog_user_edit_account_map_osm').value;
			var map_bing = document.getElementById('dialog_user_edit_account_map_bing').value;
			var map_google = document.getElementById('dialog_user_edit_account_map_google').value;
			var map_mapbox = document.getElementById('dialog_user_edit_account_map_mapbox').value;
			var map_yandex = document.getElementById('dialog_user_edit_account_map_yandex').value;
			
			if ((map_osm == 'false') && (map_bing == 'false') && (map_google == 'false') && (map_mapbox== 'false') && (map_yandex == 'false'))
			{
				notifyDialog(la['AT_LEAST_ONE_MAP_SHOULD_BE_ENABLED']);
				return;
			}
			
			var map_osm = strToBoolean(map_osm);
			var map_bing = strToBoolean(map_bing);
			var map_google = strToBoolean(map_google);
			var map_google_street_view = strToBoolean(document.getElementById('dialog_user_edit_account_map_google_street_view').value);
			var map_google_traffic = strToBoolean(document.getElementById('dialog_user_edit_account_map_google_traffic').value);
			var map_mapbox = strToBoolean(map_mapbox);
			var map_yandex = strToBoolean(map_yandex);	
			var history = strToBoolean(document.getElementById('dialog_user_edit_account_history').value);
			var reports = strToBoolean(document.getElementById('dialog_user_edit_account_reports').value);
			var tasks = strToBoolean(document.getElementById('dialog_user_edit_account_tasks').value);
			var rilogbook = strToBoolean(document.getElementById('dialog_user_edit_account_rilogbook').value);
			var dtc = strToBoolean(document.getElementById('dialog_user_edit_account_dtc').value);
			var maintenance = strToBoolean(document.getElementById('dialog_user_edit_account_maintenance').value);
			var object_control = strToBoolean(document.getElementById('dialog_user_edit_account_object_control').value);
			var image_gallery = strToBoolean(document.getElementById('dialog_user_edit_account_image_gallery').value);
			var chat = strToBoolean(document.getElementById('dialog_user_edit_account_chat').value);
			var subaccounts = strToBoolean(document.getElementById('dialog_user_edit_account_subaccounts').value);
			var sms_gateway_server = document.getElementById('dialog_user_edit_account_sms_gateway_server').value;
			
			var privileges = {
				type: privileges_,
				map_osm: map_osm,
				map_bing: map_bing,
				map_google: map_google,
				map_google_street_view: map_google_street_view,
				map_google_traffic: map_google_traffic,
				map_mapbox: map_mapbox,
				map_yandex: map_yandex,
				history: history,
				reports: reports,
				tasks: tasks,
				rilogbook: rilogbook,
				dtc: dtc,
				maintenance: maintenance,
				object_control: object_control,
				image_gallery: image_gallery,
				chat: chat,
				subaccounts: subaccounts
			};
			
			privileges = JSON.stringify(privileges);
			
			if (cpValues['privileges'] != 'manager')
			{
				var manager_id = document.getElementById('dialog_user_edit_account_manager_id').value;
				var manager_billing = document.getElementById('dialog_user_edit_account_manager_billing').value;
				
				var obj_add = document.getElementById('dialog_user_edit_account_obj_add').value;
				var obj_limit = document.getElementById('dialog_user_edit_account_obj_limit').value;
				var obj_limit_num = document.getElementById('dialog_user_edit_account_obj_limit_num').value;
				var obj_days = document.getElementById('dialog_user_edit_account_obj_days').value;
				var obj_days_dt = document.getElementById('dialog_user_edit_account_obj_days_dt').value;
				
				// account obj num check
				if (obj_limit == 'true')
				{
					if ((obj_limit_num < 1) || !isIntValid(obj_limit_num))
					{
						obj_limit_num = 0;
					}
                                }
				else
				{
					obj_limit_num = 0;
				}
				
				// account obj dt check
				if (obj_days == 'true')
				{
					if (obj_days_dt == '')
					{
						notifyDialog(la['DATE_CANT_BE_EMPTY']);
						break;
					}
				}
				else
				{
					obj_days_dt = '';
				}
			}
			else
			{
				var manager_id = '';
				var manager_billing = '';
				
				var obj_add = '';
				var obj_limit = '';
				var obj_limit_num = '';
				var obj_days = '';
				var obj_days_dt = '';
			}
			
			var obj_edit = document.getElementById('dialog_user_edit_account_obj_edit').value;
			var obj_delete = document.getElementById('dialog_user_edit_account_obj_delete').value;
			var obj_history_clear = document.getElementById('dialog_user_edit_account_obj_history_clear').value;
			
			var api = document.getElementById('dialog_user_edit_api_active').value;
			var api_key = document.getElementById('dialog_user_edit_api_key').value;
			
			var places_markers = document.getElementById('dialog_user_edit_places_markers').value;
			var places_routes = document.getElementById('dialog_user_edit_places_routes').value;
			var places_zones = document.getElementById('dialog_user_edit_places_zones').value;
			
			var usage_email_daily = document.getElementById('dialog_user_edit_usage_email_daily').value;
			var usage_sms_daily = document.getElementById('dialog_user_edit_usage_sms_daily').value;
			var usage_api_daily = document.getElementById('dialog_user_edit_usage_api_daily').value;
			
			var contact_name = document.getElementById('dialog_user_edit_account_contact_surname').value;
			var contact_company = document.getElementById('dialog_user_edit_account_contact_company').value;
			var contact_address = document.getElementById('dialog_user_edit_account_contact_address').value;
			var contact_post_code = document.getElementById('dialog_user_edit_account_contact_post_code').value;
			var contact_city = document.getElementById('dialog_user_edit_account_contact_city').value;
			var contact_country = document.getElementById('dialog_user_edit_account_contact_country').value;
			var contact_phone1 = document.getElementById('dialog_user_edit_account_contact_phone1').value;
			var contact_phone2 = document.getElementById('dialog_user_edit_account_contact_phone2').value;
			var contact_email = document.getElementById('dialog_user_edit_account_contact_email').value;
			
			var info = {
				name: contact_name,
				company: contact_company,
				address: contact_address,
				post_code: contact_post_code,
				city: contact_city,
				country: contact_country,
				phone1: contact_phone1,
				phone2: contact_phone2,
				email: contact_email
			};
			
			info = JSON.stringify(info);
			
			var comment = document.getElementById('dialog_user_edit_account_comment').value;
			
			// password change
			if (password.length > 0)
			{
				if (password.length >= 6)
				{
					if (password.indexOf(" ") != -1)
					{
						notifyDialog(la['PASSWORD_SPACE_CHARACTERS']);
						break;
					}
					
					confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_CHANGE_USER_PASSWORD'], function(response){
						if (response)
						{
							responseSave();
						}
					});
				}
				else
				{
					notifyDialog(la['PASSWORD_LENGHT_AT_LEAST']);
					break;
				}
			}
			else
			{
				responseSave();
			}
		break;
	}
	
	function responseSave()
	{
                var data = {
			cmd: 'edit_user',
			id: cpValues['user_edit_id'],
			active: active,
			account_expire: account_expire,
			account_expire_dt: account_expire_dt,
			privileges: privileges,
			manager_id: manager_id,
			manager_billing: manager_billing,
			username: username,
			password: password,
			email: email,
			api: api,
			api_key: api_key,
			info: info,
			comment: comment,
			obj_add: obj_add,
			obj_limit: obj_limit,
			obj_limit_num: obj_limit_num,
			obj_days: obj_days,
			obj_days_dt: obj_days_dt,
			obj_edit: obj_edit,
			obj_delete: obj_delete,
			obj_history_clear: obj_history_clear,				
			sms_gateway_server: sms_gateway_server,
			places_markers: places_markers,
			places_routes: places_routes,
			places_zones: places_zones,
			usage_email_daily: usage_email_daily,
			usage_sms_daily: usage_sms_daily,
			usage_api_daily: usage_api_daily
		};
		
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.users.php",
			data: data,
			success: function(result)
			{
				if (result == 'OK')
				{
					initSelectList('manager_list');
					
					if ((cpValues['user_edit_privileges'] == 'manager') && (privileges_ != 'manager') && (cpValues['manager_id'] != 0))
					{
						switchCPManager(0);
					}
					
					$("#dialog_user_edit").dialog("close");
				}
				else if (result == 'ERROR_USERNAME_EXISTS')
				{
					notifyDialog(la['THIS_USERNAME_ALREADY_EXISTS']);
				}
				else if (result == 'ERROR_EMAIL_EXISTS')
				{
					notifyDialog(la['THIS_EMAIL_ALREADY_EXISTS']);
				}
			}
		});
        }
}

function userEditCheck()
{	
	var selected_privileges = document.getElementById('dialog_user_edit_account_privileges').value;
			
	// prevent self user deactivation, expire account, level change
	if ((cpValues['user_id'] == cpValues['user_edit_id']))
	{
		document.getElementById('dialog_user_edit_account_active').disabled = true;
		document.getElementById('dialog_user_edit_account_expire').disabled = true;
		document.getElementById('dialog_user_edit_account_expire_dt').disabled = true;
		document.getElementById('dialog_user_edit_account_expire').checked = false;
		document.getElementById('dialog_user_edit_account_expire_dt').value = '';
		document.getElementById('dialog_user_edit_account_privileges').disabled = true;
	}
	else
	{
		document.getElementById('dialog_user_edit_account_active').disabled = false;
		document.getElementById('dialog_user_edit_account_expire').disabled = false;
		document.getElementById('dialog_user_edit_account_expire_dt').disabled = false;
		document.getElementById('dialog_user_edit_account_privileges').disabled = false;			
	}

	// expire account
	if (document.getElementById('dialog_user_edit_account_expire').checked == true)
	{
                document.getElementById('dialog_user_edit_account_expire_dt').disabled = false;
        }
	else
	{
		document.getElementById('dialog_user_edit_account_expire_dt').disabled = true;
	}
	
	// if not manager
	if (cpValues['privileges'] != 'manager')
	{
		switch (selected_privileges)
		{
			case "viewer":			
				document.getElementById('dialog_user_edit_account_manager_id').disabled = false;
				
				document.getElementById('dialog_user_edit_account_manager_billing').disabled = true;
				document.getElementById('dialog_user_edit_account_manager_billing').value = 'false';
				break;
			case "user":				
				document.getElementById('dialog_user_edit_account_manager_id').disabled = false;
				
				document.getElementById('dialog_user_edit_account_manager_billing').disabled = true;
				document.getElementById('dialog_user_edit_account_manager_billing').value = 'false';
				break;
			case "manager":
				document.getElementById('dialog_user_edit_account_manager_id').disabled = true;
				document.getElementById('dialog_user_edit_account_manager_id').value = 0;
				
				document.getElementById('dialog_user_edit_account_manager_billing').disabled = false;
				break;
			case "admin":			
				document.getElementById('dialog_user_edit_account_manager_id').disabled = true;
				document.getElementById('dialog_user_edit_account_manager_id').value = 0;
				
				document.getElementById('dialog_user_edit_account_manager_billing').disabled = true;
				document.getElementById('dialog_user_edit_account_manager_billing').value = 'false';
				break;
			case "super_admin":			
				document.getElementById('dialog_user_edit_account_manager_id').disabled = true;
				document.getElementById('dialog_user_edit_account_manager_id').value = 0;
				
				document.getElementById('dialog_user_edit_account_manager_billing').disabled = true;
				document.getElementById('dialog_user_edit_account_manager_billing').value = 'false';
				break;
		}
		
		// if user has manager
		if (document.getElementById('dialog_user_edit_account_manager_id').value != 0)
		{
			document.getElementById('dialog_user_edit_account_obj_add').disabled = true;
			document.getElementById('dialog_user_edit_account_obj_add').value = 'false';
		}
		else
		{
			document.getElementById('dialog_user_edit_account_obj_add').disabled = false;
		}
		
		switch (document.getElementById('dialog_user_edit_account_obj_add').value)
		{
			case "true":
				document.getElementById('dialog_user_edit_account_obj_limit').disabled = false;
				
				if (document.getElementById('dialog_user_edit_account_obj_limit').value == 'true')
				{
					document.getElementById('dialog_user_edit_account_obj_limit_num').disabled = false;
				}
				else
				{
					document.getElementById('dialog_user_edit_account_obj_limit_num').disabled = true;
				}
				
				document.getElementById('dialog_user_edit_account_obj_days').disabled = false;
				
				if (document.getElementById('dialog_user_edit_account_obj_days').value == 'true')
				{
					document.getElementById('dialog_user_edit_account_obj_days_dt').disabled = false;
				}
				else
				{
					document.getElementById('dialog_user_edit_account_obj_days_dt').disabled = true;
				}
				
				break;
			case "false":
				document.getElementById('dialog_user_edit_account_obj_limit').disabled = true;
				document.getElementById('dialog_user_edit_account_obj_limit').value = 'false';
				$("#dialog_user_edit_account_obj_limit").multipleSelect('refresh');
				
				document.getElementById('dialog_user_edit_account_obj_limit_num').disabled = true;
				document.getElementById('dialog_user_edit_account_obj_limit_num').value = '';
				
				document.getElementById('dialog_user_edit_account_obj_days').disabled = true;
				document.getElementById('dialog_user_edit_account_obj_days').value = 'false';
				$("#dialog_user_edit_account_obj_days").multipleSelect('refresh');
				
				document.getElementById('dialog_user_edit_account_obj_days_dt').disabled = true;
				document.getElementById('dialog_user_edit_account_obj_days_dt').value = '';
				break;
			case "trial":
				document.getElementById('dialog_user_edit_account_obj_limit').disabled = true;
				document.getElementById('dialog_user_edit_account_obj_limit').value = 'false';
				$("#dialog_user_edit_account_obj_limit").multipleSelect('refresh');
				
				document.getElementById('dialog_user_edit_account_obj_limit_num').disabled = true;
				document.getElementById('dialog_user_edit_account_obj_limit_num').value = '';
				
				document.getElementById('dialog_user_edit_account_obj_days').disabled = true;
				document.getElementById('dialog_user_edit_account_obj_days').value = 'false';
				$("#dialog_user_edit_account_obj_days").multipleSelect('refresh');
				
				document.getElementById('dialog_user_edit_account_obj_days_dt').disabled = true;
				document.getElementById('dialog_user_edit_account_obj_days_dt').value = '';
				break;
		}
	}
}

function userDelete(id)
{
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_DELETE'], function(response){
		if (response)
		{
			var data = {
				cmd: 'delete_user',
				id: id
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						initStats();
						initSelectList('manager_list');
							
						$('#cpanel_user_list_grid').trigger("reloadGrid");	
					}
				}
			});
		}
	});
}

function userActivate(id)
{
	var data = {
		cmd: 'activate_user',
		id: id
	};
	
	$.ajax({
		type: "POST",
		url: "func/fn_cpanel.users.php",
		data: data,
		success: function(result)
		{
			if (result == 'OK')
			{
				$('#cpanel_user_list_grid').trigger("reloadGrid");	
			}
		}
	});
}

function userDeactivate(id)
{
	var data = {
		cmd: 'deactivate_user',
		id: id
	};
	
	$.ajax({
		type: "POST",
		url: "func/fn_cpanel.users.php",
		data: data,
		success: function(result)
		{
			if (result == 'OK')
			{
				$('#cpanel_user_list_grid').trigger("reloadGrid");	
			}
		}
	});
}

function userActivateSelected()
{
	var users = $('#cpanel_user_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (users == '')
	{
		notifyDialog(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_ACTIVATE_SELECTED_ITEMS'], function(response){
		if (response)
		{
			var data = {
				cmd: 'activate_selected_users',
				ids: users
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{	
						$('#cpanel_user_list_grid').trigger("reloadGrid");	
					}
				}
			});
		}
	});
}

function userDeactivateSelected()
{
	var users = $('#cpanel_user_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (users == '')
	{
		notifyDialog(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_DEACTIVATE_SELECTED_ITEMS'], function(response){
		if (response)
		{
			var data = {
				cmd: 'deactivate_selected_users',
				ids: users
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						$('#cpanel_user_list_grid').trigger("reloadGrid");	
					}
				}
			});
                }
	});
}

function userAPIActivate(id)
{
	var data = {
		cmd: 'activate_user_api',
		id: id
	};
	
	$.ajax({
		type: "POST",
		url: "func/fn_cpanel.users.php",
		data: data,
		success: function(result)
		{
			if (result == 'OK')
			{
				$('#cpanel_user_list_grid').trigger("reloadGrid");	
			}
		}
	});
}

function userAPIDeactivate(id)
{
	var data = {
		cmd: 'deactivate_user_api',
		id: id
	};
	
	$.ajax({
		type: "POST",
		url: "func/fn_cpanel.users.php",
		data: data,
		success: function(result)
		{
			if (result == 'OK')
			{	
				$('#cpanel_user_list_grid').trigger("reloadGrid");	
			}
		}
	});
}

function userDeleteSelected()
{
	var users = $('#cpanel_user_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (users == '')
	{
		notifyDialog(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_SELECTED_ITEMS'], function(response){
		if (response)
		{
			var data = {
				cmd: 'delete_selected_users',
				ids: users
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						initStats();
						initSelectList('manager_list');
							
						$('#cpanel_user_list_grid').trigger("reloadGrid");	
					}
				}
			});
		}
	});
}

function userObjectAdd(cmd){
	switch (cmd)
	{
		case "open":
			$('#dialog_user_object_add_objects').tokenize().clear();
			$("#dialog_user_object_add").dialog("open");
			break;
		case "add":
			var imeis = $('#dialog_user_object_add_objects').tokenize().toArray();
			
			imeis = JSON.stringify(imeis);
			
			var data = {
				cmd: 'add_user_objects',
				user_id: cpValues['user_edit_id'],
				imeis: imeis
			};
			
		   $.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						$('#dialog_user_edit_object_list_grid').trigger("reloadGrid");
						$("#dialog_user_object_add").dialog("close");
					}
				}
			});
			break;
		case "cancel":
			$("#dialog_user_object_add").dialog("close");
			break;
	}
}

function userObjectDelete(imei){
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_OBJECT_FROM_USER_ACCOUNT'], function(response){
		if (response)
		{
			var data = {
				cmd: 'delete_user_object',
				user_id: cpValues['user_edit_id'],
				imei: imei
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						$('#dialog_user_edit_object_list_grid').trigger("reloadGrid");
					}
				}
			});
		}
	});
}

function userObjectActivateSelected()
{
        var objects = $('#dialog_user_edit_object_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (objects == '')
	{
		notifyDialog(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_ACTIVATE_SELECTED_ITEMS'], function(response){
		if (response)
		{
			var data = {
				cmd: 'activate_selected_user_objects',
				user_id: cpValues['user_edit_id'],
				imeis: objects
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						$('#dialog_user_edit_object_list_grid').trigger("reloadGrid");	
					}
				}
			});
		}
	});
}

function userObjectDeactivateSelected()
{
        var objects = $('#dialog_user_edit_object_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (objects == '')
	{
		notifyDialog(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_DEACTIVATE_SELECTED_ITEMS'], function(response){
		if (response)
		{
			var data = {
				cmd: 'deactivate_selected_user_objects',
				user_id: cpValues['user_edit_id'],
				imeis: objects
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						$('#dialog_user_edit_object_list_grid').trigger("reloadGrid");	
					}
				}
			});
		}
	});
}

function userObjectClearHistorySelected()
{
        var objects = $('#dialog_user_edit_object_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (objects == '')
	{
		notifyDialog(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_CLEAR_SELECTED_ITEMS_HISTORY_EVENTS'], function(response){
		if (response)
		{
			var data = {
				cmd: 'clear_history_selected_objects',
				user_id: cpValues['user_edit_id'],
				imeis: objects
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.objects.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						initStats();
						initSelectList('manager_list');
							
						$('#dialog_user_edit_object_list_grid').trigger("reloadGrid");	
					}
				}
			});
		}
	});
}

function userObjectDeleteSelected()
{
        var objects = $('#dialog_user_edit_object_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (objects == '')
	{
		notifyDialog(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_SELECTED_ITEMS'], function(response){
		if (response)
		{
			var data = {
				cmd: 'delete_selected_user_objects',
				user_id: cpValues['user_edit_id'],
				imeis: objects
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						initStats();
						initSelectList('manager_list');
							
						$('#dialog_user_edit_object_list_grid').trigger("reloadGrid");	
					}
				}
			});
		}
	});
}

function userSubaccountEdit(id)
{
	var username = document.getElementById('dialog_user_edit_subaccount_list_grid_username_' + id).value;
	var email = document.getElementById('dialog_user_edit_subaccount_list_grid_email_'+ id ).value;
	var password = document.getElementById('dialog_user_edit_subaccount_list_grid_password_'+ id ).value;
	
	// username check
	if (username == '')
	{
		notifyDialog(la['USERNAME_CANT_BE_EMPTY']);
		return;
	}
	
	if (username.indexOf(" ") != -1)
	{
		notifyDialog(la['USERNAME_SPACE_CHARACTERS']);
		return;
	}
	
	// email check
	if(!isEmailValid(email))
	{
		notifyDialog(la['THIS_EMAIL_IS_NOT_VALID']);
		return;
	}
	
	// password change
	if (password.length > 0)
	{
		if (password.length >= 6)
		{
			confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_CHANGE_USER_PASSWORD'], function(response){
				if (response)
				{
					responseSave();
				}
			});
		}
		else
		{
			notifyDialog(la['PASSWORD_LENGHT_AT_LEAST']);
			return;
		}
	}
	else
	{
		responseSave();
	}
	
	function responseSave() {
		var data = {
			cmd: 'edit_user_subaccount',
			id: id,
			username: username,
			email: email,
			password: password
		};
			
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.users.php",
			data: data,
			success: function(result)
			{
				if (result == 'OK')
				{
					$('#dialog_user_edit_subaccount_list_grid').trigger("reloadGrid");
				}
				else if (result == 'ERROR_USERNAME_EXISTS')
				{
					notifyDialog(la['THIS_USERNAME_ALREADY_EXISTS']);
				}
				else if (result == 'ERROR_EMAIL_EXISTS')
				{
					notifyDialog(la['THIS_EMAIL_ALREADY_EXISTS']);
				}
			}
		});	
        }
}

function userSubaccountDelete(id){
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_DELETE'], function(response){
		if (response)
		{
			var data = {
				cmd: 'delete_user_subaccount',
				id: id
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						$('#dialog_user_edit_subaccount_list_grid').trigger("reloadGrid");
					}
				}
			});	
		}
	});
}

function userSubaccountActivate(id)
{
	var data = {
		cmd: 'activate_user_subaccount',
		id: id
	};
	
	$.ajax({
		type: "POST",
		url: "func/fn_cpanel.users.php",
		data: data,
		success: function(result)
		{
			if (result == 'OK')
			{
				$('#dialog_user_edit_subaccount_list_grid').trigger("reloadGrid");	
			}
		}
	});
}

function userSubaccountDeactivate(id)
{
	var data = {
		cmd: 'deactivate_user_subaccount',
		id: id
	};
	
	$.ajax({
		type: "POST",
		url: "func/fn_cpanel.users.php",
		data: data,
		success: function(result)
		{
			if (result == 'OK')
			{
				$('#dialog_user_edit_subaccount_list_grid').trigger("reloadGrid");	
			}
		}
	});
}

function userSubaccountActivateSelected()
{
        var subaccounts = $('#dialog_user_edit_subaccount_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (subaccounts == '')
	{
		notifyDialog(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_ACTIVATE_SELECTED_ITEMS'], function(response){
		if (response)
		{
			var data = {
				cmd: 'activate_selected_user_subaccounts',
				ids: subaccounts
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{						
						$('#dialog_user_edit_subaccount_list_grid').trigger("reloadGrid");	
					}
				}
			});
		}
	});
}

function userSubaccountDeactivateSelected()
{
        var subaccounts = $('#dialog_user_edit_subaccount_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (subaccounts == '')
	{
		notifyDialog(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_DEACTIVATE_SELECTED_ITEMS'], function(response){
		if (response)
		{
			var data = {
				cmd: 'deactivate_selected_user_subaccounts',
				ids: subaccounts
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{						
						$('#dialog_user_edit_subaccount_list_grid').trigger("reloadGrid");	
					}
				}
			});
		}
	});
}

function userSubaccountDeleteSelected()
{
        var subaccounts = $('#dialog_user_edit_subaccount_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (subaccounts == '')
	{
		notifyDialog(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_SELECTED_ITEMS'], function(response){
		if (response)
		{
			var data = {
				cmd: 'delete_selected_user_subaccounts',
				ids: subaccounts
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{						
						$('#dialog_user_edit_subaccount_list_grid').trigger("reloadGrid");	
					}
				}
			});
		}
	});
}

function userBillingPlanAdd(cmd){
	switch (cmd)
	{
		case "open":
			document.getElementById('dialog_user_billing_plan_add_plan').value = '';
			$("#dialog_user_billing_plan_add_plan").multipleSelect('refresh');
			document.getElementById('dialog_user_billing_plan_add_name').value = '';
			document.getElementById('dialog_user_billing_plan_add_objects').value = '';
			document.getElementById('dialog_user_billing_plan_add_period').value = '';
			document.getElementById('dialog_user_billing_plan_add_period_type').value = 'years';
			$("#dialog_user_billing_plan_add_period_type").multipleSelect('refresh');
			document.getElementById('dialog_user_billing_plan_add_price').value = '';
			$("#dialog_user_billing_plan_add").dialog("open");
			break;
		case "load":
			
			var plan_id = document.getElementById('dialog_user_billing_plan_add_plan').value;
			
			if (plan_id == '')
			{
					document.getElementById('dialog_user_billing_plan_add_name').value = '';
					document.getElementById('dialog_user_billing_plan_add_objects').value = '';
					document.getElementById('dialog_user_billing_plan_add_period').value = '';
					document.getElementById('dialog_user_billing_plan_add_period_type').value = 'years';
					$("#dialog_user_billing_plan_add_period_type").multipleSelect('refresh');
					document.getElementById('dialog_user_billing_plan_add_price').value = '';
					
					break;
                        }
			
			var data = {
				cmd: 'load_billing_plan',
				plan_id: plan_id
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.server.php",
				data: data,
				dataType: 'json',
				cache: false,
				success: function(result)
				{
					document.getElementById('dialog_user_billing_plan_add_name').value = result['name'];
					document.getElementById('dialog_user_billing_plan_add_objects').value = result['objects'];
					document.getElementById('dialog_user_billing_plan_add_period').value = result['period'];
					document.getElementById('dialog_user_billing_plan_add_period_type').value = result['period_type'];
					$("#dialog_user_billing_plan_add_period_type").multipleSelect('refresh');
					document.getElementById('dialog_user_billing_plan_add_price').value = result['price'];
				}
			});
			
			break;
		case "add":
			var name = document.getElementById('dialog_user_billing_plan_add_name').value;
			var objects = document.getElementById('dialog_user_billing_plan_add_objects').value;
			var period = document.getElementById('dialog_user_billing_plan_add_period').value;
			var period_type = document.getElementById('dialog_user_billing_plan_add_period_type').value;
			var price = document.getElementById('dialog_user_billing_plan_add_price').value;
			
			var data = {
				cmd: 'add_user_billing_plan',
				user_id: cpValues['user_edit_id'],
				name: name,
				objects: objects,
				period: period,
				period_type: period_type,
				price: price
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						initStats();
						
						$('#dialog_user_edit_billing_plan_list_grid').trigger("reloadGrid");
						$("#dialog_user_billing_plan_add").dialog("close");
					}
				}
			});
			break;
		case "cancel":
			$("#dialog_user_billing_plan_add").dialog("close");
			break;
	}
}

function userBillingPlanEdit(cmd)
{
	switch (cmd)
	{
		default:
			var id = cmd;
			
			cpValues['edit_user_billing_plan_id'] = id;
			
			var data = {
				cmd: 'load_user_billing_plan',
				plan_id: cpValues['edit_user_billing_plan_id']
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				dataType: 'json',
				cache: false,
				success: function(result)
				{
					document.getElementById('dialog_user_billing_plan_edit_name').value = result['name'];
					document.getElementById('dialog_user_billing_plan_edit_objects').value = result['objects'];
					document.getElementById('dialog_user_billing_plan_edit_period').value = result['period'];
					document.getElementById('dialog_user_billing_plan_edit_period_type').value = result['period_type'];
					$("#dialog_user_billing_plan_edit_period_type").multipleSelect('refresh');
					document.getElementById('dialog_user_billing_plan_edit_price').value = result['price'];
				}
			});
			
			$("#dialog_user_billing_plan_edit").dialog("open");
			break;
			
		case "cancel":
			$("#dialog_user_billing_plan_edit").dialog("close");	
			break;
			
		case "save":
			var name = document.getElementById('dialog_user_billing_plan_edit_name').value;
			var objects = document.getElementById('dialog_user_billing_plan_edit_objects').value;
			var period = document.getElementById('dialog_user_billing_plan_edit_period').value;
			var period_type = document.getElementById('dialog_user_billing_plan_edit_period_type').value;
			var price = document.getElementById('dialog_user_billing_plan_edit_price').value;
			
			if ((name == "") || (objects == "") || (period == "") || (price == ""))
			{
				notifyDialog(la['ALL_AVAILABLE_FIELDS_SHOULD_BE_FILLED_OUT']);
				break;
			}
			
			var data = {
				cmd: 'save_user_billing_plan',
				plan_id: cpValues['edit_user_billing_plan_id'],
				name: name,
				objects: objects,
				period: period,
				period_type: period_type,
				price: price
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				cache: false,
				success: function(result)
				{
					if (result == 'OK')
					{
						$('#dialog_user_edit_billing_plan_list_grid').trigger("reloadGrid");
						$("#dialog_user_billing_plan_edit").dialog("close");
					}
				}
			});
			break;
	}
}

function userBillingPlanDelete(id)
{
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_DELETE'], function(response){
		if (response)
		{
			var data = {
				cmd: 'delete_user_billing_plan',
				id: id
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						initStats();
						
						if ($('#dialog_user_edit').dialog('isOpen') == true)
						{
						       $('#dialog_user_edit_billing_plan_list_grid').trigger("reloadGrid");
						}
						else
						{
							$('#cpanel_billing_plan_list_grid').trigger("reloadGrid"); 
						}
					}
				}
			});
		}
	});
}

function userBillingPlanDeleteSelected()
{
        var billing_plans = $('#dialog_user_edit_billing_plan_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (billing_plans == '')
	{
		notifyDialog(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_SELECTED_ITEMS'], function(response){
		if (response)
		{
			var data = {
				cmd: 'delete_selected_user_billing_plans',
				ids: billing_plans
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						initStats();
						
						$('#dialog_user_edit_billing_plan_list_grid').trigger("reloadGrid");	
					}
				}
			});
		}
	});
}

function userUsageDelete(id)
{
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_DELETE'], function(response){
		if (response)
		{
			var data = {
				cmd: 'delete_user_usage',
				id: id
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						$('#dialog_user_edit_usage_list_grid').trigger("reloadGrid"); 
					}
				}
			});
		}
	});
}

function userUsageDeleteSelected()
{
        var usages = $('#dialog_user_edit_usage_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (usages == '')
	{
		notifyDialog(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_SELECTED_ITEMS'], function(response){
		if (response)
		{
			var data = {
				cmd: 'delete_selected_user_usages',
				ids: usages
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.users.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						$('#dialog_user_edit_usage_list_grid').trigger("reloadGrid");	
					}
				}
			});
		}
	});
}

function userImport()
{
        // a bit dirty sollution, maybe will make better in the feature :)
        document.getElementById('load_file').addEventListener('change', userImportCSVFile, false);
        document.getElementById('load_file').click();
}

function userImportCSVFile(evt)
{         
        var files = evt.target.files;
        var reader = new FileReader();
        reader.onload = function(event)
        {
                try
                {
                        if (files[0].name.split('.').pop().toLowerCase() == 'csv')
                        {
                                var data_json = csv2json(event.target.result);
				
				for (i=0; i<data_json.length; i+=1)
				{
					if ((data_json[i].username != undefined) && (data_json[i].email != undefined) && (data_json[i].password != undefined))
					{
						if ((data_json[i].username == '') || (!isEmailValid(data_json[i].email)) || (data_json[i].password.length < 6))
						{
							notifyDialog(la['INVALID_FILE_FORMAT']);
							return;
                                                }
                                        }
					else
					{
						notifyDialog(la['INVALID_FILE_FORMAT']);
						return;
					}
				}						
				
                                var users = JSON.stringify(data_json);				
				var users_count = data_json.length;
				
				if (users_count == 0)
                                {
					notifyDialog(la['NOTHING_HAS_BEEN_FOUND_TO_IMPORT']);
                                        return;
                                }
				
				var text = sprintf(la['USERS_FOUND'], users_count) + ' ' + la['ARE_YOU_SURE_YOU_WANT_TO_IMPORT'];
				
                                confirmDialog(text, function(response){
                                        if (response)
                                        {
                                                loadingData(true);
                                                
                                                var data = {
                                                        format: 'user_csv',
                                                        data: users
                                                };
                                                
                                                $.ajax({
                                                        type: "POST",
                                                        url: "func/fn_cpanel.import.php",
                                                        data: data,
                                                        cache: false,
                                                        success: function(result)
                                                        {
                                                                loadingData(false);
                                                                
								if (result == 'OK')
								{
									initStats();
									$('#cpanel_user_list_grid').trigger("reloadGrid");
								}
                                                        },
                                                        error: function(statusCode, errorThrown)
							{
								loadingData(false);
							}
                                                });
                                        }
                                });
                        }
                        else
                        {
                                notifyDialog(la['INVALID_FILE_FORMAT']);
                        }
                } 
                catch (ex)
                {
			notifyDialog(la['INVALID_FILE_FORMAT']);
                }
                
                document.getElementById('load_file').value = '';
        }        	
        reader.readAsText(files[0], "UTF-8");
        
        this.removeEventListener('change', userImportCSVFile, false);
}