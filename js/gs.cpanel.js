//#################################################
// VARS
//#################################################

// language array/vars
var la = [];

var cpValues = new Array();
cpValues['set_expiration'] = false;
cpValues['edit_object_imei'] = '';
cpValues['edit_object_new_imei'] = '';
cpValues['edit_theme_id'] = false;
cpValues['edit_custom_map_id'] = false;
cpValues['edit_billing_plan_id'] = false;
cpValues['edit_user_billing_plan_id'] = false;

// timers
var timer_loadStats;
var timer_sessionCheck;

//#################################################
// END VARS
//#################################################

function load()
{
	loadLanguage(function(response){
	loadSettings('cpanel', function(response){
	loadSettings('server', function(response){
	
	load2();

	});});});
}

function load2()
{
	initGui();
	initGrids();
	initStats();
	
	loadGridList('themes');
	loadGridList('custom_maps');
	loadGridList('billing');
	loadGridList('languages');
	loadGridList('templates');
	loadGridList('logs');

	document.getElementById("loading_panel").style.display = "none";
	document.getElementById("content").style.visibility = "visible";
	
	notifyCheck('session_check');
}

function switchCPManager(manager_id)
{
	cpValues['manager_id'] = manager_id;
	
	$('#dialog_user_object_add_objects').tokenize().options.datas = "func/fn_cpanel.objects.php?cmd=load_object_search_list&manager_id=" + cpValues['manager_id'];
	$('#dialog_object_add_users').tokenize().options.datas = "func/fn_cpanel.users.php?cmd=load_user_search_list&manager_id=" + cpValues['manager_id'];
	$('#dialog_object_edit_users').tokenize().options.datas = "func/fn_cpanel.users.php?cmd=load_user_search_list&manager_id=" + cpValues['manager_id'];
	
	$('#cpanel_user_list_grid').setGridParam({url:'func/fn_cpanel.users.php?cmd=load_user_list&manager_id=' + cpValues['manager_id']});
	$('#cpanel_user_list_grid').trigger("reloadGrid");	
	
	$('#cpanel_object_list_grid').setGridParam({url:'func/fn_cpanel.objects.php?cmd=load_object_list&manager_id=' + cpValues['manager_id']});
	$('#cpanel_object_list_grid').trigger("reloadGrid");
	
	$('#cpanel_billing_plan_list_grid').setGridParam({url:'func/fn_cpanel.billing.php?cmd=load_billing_plan_list&manager_id=' + cpValues['manager_id']});
	$('#cpanel_billing_plan_list_grid').trigger("reloadGrid");
	
	initStats();
}

function initStats()
{
	clearTimeout(timer_loadStats);
	
	var data = {
		cmd: 'stats',
		manager_id: cpValues['manager_id']
	};
	
	$.ajax({
		type: "POST",
		url: "func/fn_cpanel.php",
		data: data,
		dataType: 'json',
		cache: false,
		error: function(statusCode, errorThrown) {
			// shedule next stats reload
			timer_loadStats = setTimeout("initStats();", 30000);
		},
		success: function(result)
		{
			document.getElementById('user_list_stats').innerHTML = '('+result['total_users']+')';
			document.getElementById('object_list_stats').innerHTML = '('+result['total_objects'] + '/' + result['total_objects_online']+')';
			
			if (document.getElementById('unused_object_list_stats') != undefined)
			{
				document.getElementById('unused_object_list_stats').innerHTML = '('+result['total_unused_objects']+')'; 
			}
			
			if (document.getElementById('billing_plan_list_stats') != undefined)
			{	
				document.getElementById('billing_plan_list_stats').innerHTML = '('+result['total_billing_plans']+')';
			}
			
			document.getElementById('cpanel_manage_server_sms_gateway_total_in_queue').innerHTML = result['sms_gateway_total_in_queue'];
			
			// shedule next stats reload
			timer_loadStats = setTimeout("initStats();", 30000);
		}
	});
}

function sendEmail(cmd)
{
	switch (cmd)
	{
		case "open":
			document.getElementById('send_email_send_to').value = 'all';
			$("#send_email_send_to").multipleSelect('refresh');			
			document.getElementById('send_email_subject').value = '';
			document.getElementById('send_email_message').value = '';
			document.getElementById('send_email_status').innerHTML = '';
			
			sendEmailSendToSwitch();
			
			$("#dialog_send_email").dialog("open");
			
			break;
		case "cancel":
			$("#dialog_send_email").dialog("close");
			break;
		case "send":
			var send_to = document.getElementById('send_email_send_to').value;
			var user_ids = $('#send_email_username').tokenize().toArray();
			var subject = document.getElementById('send_email_subject').value;
			var message = document.getElementById('send_email_message').value;
			
			if ((send_to == 'selected') && (user_ids.length == 0) || (subject == '') || (message == ''))
			{
				notifyDialog(la['ALL_AVAILABLE_FIELDS_SHOULD_BE_FILLED_OUT']);
				break;
                        }
			
			user_ids = JSON.stringify(user_ids);
			
			confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_SEND_THIS_MESSAGE'], function(response){
				if (response)
				{
					document.getElementById('send_email_status').innerHTML = la['SENDING_PLEASE_WAIT'];
				
					var data = {
						cmd: 'send_email',
						manager_id: cpValues['manager_id'],
						send_to: send_to,
						user_ids: user_ids,
						subject: subject,
						message: message
					};
					
					$.ajax({
						type: "POST",
						url: "func/fn_cpanel.php",
						data: data,
						success: function(result)
						{
							if (result == 'OK')
							{
								document.getElementById('send_email_status').innerHTML = la['SENDING_FINISHED'];
							}
							else
							{
								document.getElementById('send_email_status').innerHTML = la['CANT_SEND_EMAIL'] + ' ' + la['CONTACT_ADMINISTRATOR'];
							}
						}
					});
				}
			});
			
			break;
		case "test":
			var subject = document.getElementById('send_email_subject').value;
			var message = document.getElementById('send_email_message').value;
			
			if ((subject == '') || (message == ''))
			{
				notifyDialog(la['ALL_AVAILABLE_FIELDS_SHOULD_BE_FILLED_OUT']);
				break;
                        }
			
			confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_SEND_TEST_MESSAGE_TO_YOUR_EMAIL'], function(response){
				if (response)
				{
					document.getElementById('send_email_status').innerHTML = la['SENDING_PLEASE_WAIT'];
				
					var data = {
						cmd: 'send_email_test',
						subject: subject,
						message: message
					};
					
					$.ajax({
						type: "POST",
						url: "func/fn_cpanel.php",
						data: data,
						success: function(result)
						{
							if (result == 'OK')
							{
								document.getElementById('send_email_status').innerHTML = la['SENDING_FINISHED'];
							}
							else
							{
								document.getElementById('send_email_status').innerHTML = la['CANT_SEND_EMAIL'] + ' ' + la['CONTACT_ADMINISTRATOR'];
							}
						}
					});
				}
			});
			
			break;
	}
}

function sendEmailSendToSwitch()
{
	var send_to = document.getElementById('send_email_send_to').value;
	
	switch (send_to)
	{
		case "all":
			$('#send_email_username').tokenize().clear();
			document.getElementById('send_email_username_row').style.display = "none";
			break;
		
		case "selected":
			$('#send_email_username').tokenize().clear();
			document.getElementById('send_email_username_row').style.display = "";
			
			var users = $('#cpanel_user_list_grid').jqGrid ('getGridParam', 'selarrrow');
			
			$('#send_email_username').tokenize().options.newElements = true;
			for(var i=0;i<users.length;i++)
			{
				var value = users[i];
				var text = $('#cpanel_user_list_grid').jqGrid('getCell', value, 'username');
				$('#send_email_username').tokenize().tokenAdd(value, text);
			}
			$('#send_email_username').tokenize().options.newElements = false;
						
			break;
	}
}

function notifyCheck(what)
{
	switch (what)
	{
		case "session_check":
			
			if (gsValues['session_check'] == false)
			{
				break;
			}
			
			clearTimeout(timer_sessionCheck);
			
			var data = {
				cmd: 'session_check'
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_connect.php",
				data: data,
				cache: false,
				error: function(statusCode, errorThrown)
				{
					timer_sessionCheck = setTimeout("notifyCheck('session_check');", gsValues['session_check'] * 1000);
				},
				success: function(result)
				{
					if (result == 'false')
					{
						$("#blocking_panel").show();
					}
					else
					{
						timer_sessionCheck = setTimeout("notifyCheck('session_check');", gsValues['session_check'] * 1000);
					}
				}
			});
			break;
	}
}

function setExpirationSelected(cmd)
{
	switch (cmd)
	{
		case "open_users":
			var users = $('#cpanel_user_list_grid').jqGrid ('getGridParam', 'selarrrow');
			
			if (users == '')
			{
				notifyDialog(la['NO_ITEMS_SELECTED']);
				return;
			}
			
			var data = {
				cmd: 'get_user_expire_avg',
				ids: users
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.php",
				data: data,
				success: function(result)
				{
					cpValues['set_expiration'] = 'users';
					
					document.getElementById('dialog_set_expiration_expire').checked = true;
					document.getElementById('dialog_set_expiration_expire_dt').value = result;
					
					setExpirationCheck();
					
					$("#dialog_set_expiration").dialog("open");
				}
			});
			break;
		
		case "open_objects":
			var objects = $('#cpanel_object_list_grid').jqGrid ('getGridParam', 'selarrrow');
			
			if (objects == '')
			{
				notifyDialog(la['NO_ITEMS_SELECTED']);
				return;
			}
			
			var data = {
				cmd: 'get_object_expire_avg',
				imeis: objects
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.php",
				data: data,
				success: function(result)
				{
					cpValues['set_expiration'] = 'objects';
					
					document.getElementById('dialog_set_expiration_expire').checked = true;
					document.getElementById('dialog_set_expiration_expire_dt').value = result;
					
					setExpirationCheck();
					
					$("#dialog_set_expiration").dialog("open");
				}
			});
			break;
		
		case "open_user_objects":
			var objects = $('#dialog_user_edit_object_list_grid').jqGrid ('getGridParam', 'selarrrow');
			
			if (objects == '')
			{
				notifyDialog(la['NO_ITEMS_SELECTED']);
				return;
			}
			
			var data = {
				cmd: 'get_object_expire_avg',
				imeis: objects
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.php",
				data: data,
				success: function(result)
				{
					cpValues['set_expiration'] = 'user_objects';
					
					document.getElementById('dialog_set_expiration_expire').checked = true;
					document.getElementById('dialog_set_expiration_expire_dt').value = result;
					
					setExpirationCheck();
					
					$("#dialog_set_expiration").dialog("open");
				}
			});
			break;
		
		case "save":
			var expire = document.getElementById('dialog_set_expiration_expire').checked;
			var expire_dt = document.getElementById('dialog_set_expiration_expire_dt').value;
			
			// expire object
			if (expire == true)
			{
				if (expire_dt == '')
				{
					notifyDialog(la['DATE_CANT_BE_EMPTY']);
					break;
				}
			}
			else
			{
                                expire_dt = '';
                        }
			
			if (cpValues['set_expiration'] == 'users')
			{
				var users = $('#cpanel_user_list_grid').jqGrid ('getGridParam', 'selarrrow');
				
				confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_SET_EXPIRATION_FOR_SELECTED_ITEMS'], function(response){
					if (response)
					{
						var data = {
							cmd: 'set_user_expire_selected',
							ids: users,
							expire: expire,
							expire_dt: expire_dt
						};
						
						$.ajax({
							type: "POST",
							url: "func/fn_cpanel.php",
							data: data,
							success: function(result)
							{
								if (result == 'OK')
								{
									$('#cpanel_user_list_grid').trigger("reloadGrid");
									$("#dialog_set_expiration").dialog("close");
								}
							}
						});	
					}
				});	
                        }
			else if (cpValues['set_expiration'] == 'objects')
			{
				var objects = $('#cpanel_object_list_grid').jqGrid ('getGridParam', 'selarrrow');
				
				confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_SET_EXPIRATION_FOR_SELECTED_ITEMS'], function(response){
					if (response)
					{
						var data = {
							cmd: 'set_object_expire_selected',
							imeis: objects,
							expire: expire,
							expire_dt: expire_dt
						};
						
						$.ajax({
							type: "POST",
							url: "func/fn_cpanel.php",
							data: data,
							success: function(result)
							{
								if (result == 'OK')
								{
									$('#cpanel_object_list_grid').trigger("reloadGrid");
									
									$("#dialog_set_expiration").dialog("close");
								}
							}
						});
					}
				});	
			}
			else if (cpValues['set_expiration'] == 'user_objects')
			{
				var objects = $('#dialog_user_edit_object_list_grid').jqGrid ('getGridParam', 'selarrrow');
				
				confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_SET_EXPIRATION_FOR_SELECTED_ITEMS'], function(response){
					if (response)
					{
						var data = {
							cmd: 'set_object_expire_selected',
							imeis: objects,
							expire: expire,
							expire_dt: expire_dt
						};
						
						$.ajax({
							type: "POST",
							url: "func/fn_cpanel.php",
							data: data,
							success: function(result)
							{
								if (result == 'OK')
								{
									$('#dialog_user_edit_object_list_grid').trigger("reloadGrid");
									
									$("#dialog_set_expiration").dialog("close");
								}
							}
						});	
					}
				});	
			}
			else
			{
				$("#dialog_set_expiration").dialog("close");
			}
			
			break;
		
		case "cancel":			
			$("#dialog_set_expiration").dialog("close");
			break;
	}
}

function setExpirationCheck()
{
	var object_expire = document.getElementById('dialog_set_expiration_expire').checked;
	if (object_expire == true)
	{
                document.getElementById('dialog_set_expiration_expire_dt').disabled = false;
        }
	else
	{
		document.getElementById('dialog_set_expiration_expire_dt').disabled = true;
	}
}