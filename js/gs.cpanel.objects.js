function objectAdd(cmd)
{
	switch (cmd)
	{
		case "open":
			// set object add properties availability
			if (cpValues['privileges'] == 'manager')
			{
				document.getElementById('dialog_object_add_manager_id').disabled = true;
			}
			
			document.getElementById('dialog_object_add_active').checked = true;
			document.getElementById('dialog_object_add_object_expire').checked = true;
			
			if (cpValues['privileges'] == 'manager')
			{
				if (cpValues['obj_days'] == 'true')
				{
					document.getElementById('dialog_object_add_object_expire_dt').value = cpValues['obj_days_dt'];
				}
				else
				{
					document.getElementById('dialog_object_add_object_expire_dt').value = moment().add('years', 1).format("YYYY-MM-DD");	
				}	
			}
			else
			{
				document.getElementById('dialog_object_add_object_expire_dt').value = moment().add('years', 1).format("YYYY-MM-DD");	
			}
			
			document.getElementById('dialog_object_add_name').value = '';
			document.getElementById('dialog_object_add_imei').value = '';
			document.getElementById('dialog_object_add_model').value = '';
			document.getElementById('dialog_object_add_vin').value = '';
			document.getElementById('dialog_object_add_plate_number').value = '';
			document.getElementById('dialog_object_add_device').value = '';
			document.getElementById('dialog_object_add_sim_number').value = '';
			document.getElementById('dialog_object_add_manager_id').value = 0;
			$("#dialog_object_add_manager_id").multipleSelect('refresh');
			
			objectAddCheck();
			
			$('#dialog_object_add_users').tokenize().clear();
			$("#dialog_object_add").dialog("open");
			break;
		case "add":
			var name = document.getElementById('dialog_object_add_name').value;
			var imei = document.getElementById('dialog_object_add_imei').value;
			var model = document.getElementById('dialog_object_add_model').value;
			var vin = document.getElementById('dialog_object_add_vin').value;
			var plate_number = document.getElementById('dialog_object_add_plate_number').value;
			var device = document.getElementById('dialog_object_add_device').value;
			var sim_number = document.getElementById('dialog_object_add_sim_number').value;
			var manager_id = document.getElementById('dialog_object_add_manager_id').value;
			var active = document.getElementById('dialog_object_add_active').checked;
			var object_expire = document.getElementById('dialog_object_add_object_expire').checked;
			var object_expire_dt = document.getElementById('dialog_object_add_object_expire_dt').value;
			
			var user_ids = $('#dialog_object_add_users').tokenize().toArray();
			
			user_ids = JSON.stringify(user_ids);
			
			if (name == "")
			{
				notifyDialog(la['NAME_CANT_BE_EMPTY']);
				return;
			}
			
			if(!isIMEIValid(imei))
			{
				notifyDialog(la['IMEI_IS_NOT_VALID']);
				return;
			}
			
			// expire object
			if (object_expire == true)
			{
				if (object_expire_dt == '')
				{
					notifyDialog(la['DATE_CANT_BE_EMPTY']);
					break;
				}
			}
			else
			{
                                object_expire_dt = '';
                        }
			
			var data = {
				cmd: 'add_object',
				name: name,
				imei: imei,
				model: model,
				vin: vin,
				plate_number: plate_number,
				device: device,
				sim_number: sim_number,
				manager_id: manager_id,
				active: active,
				object_expire: object_expire,
				object_expire_dt: object_expire_dt,
				user_ids: user_ids
			};
			
		   $.ajax({
				type: "POST",
				url: "func/fn_cpanel.objects.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						initSelectList('manager_list');
						initStats();
						
						$('#cpanel_user_list_grid').trigger("reloadGrid");
						$('#cpanel_object_list_grid').trigger("reloadGrid");
						$('#cpanel_unused_object_list_grid').trigger("reloadGrid");
						$("#dialog_object_add").dialog("close");
					}
					else if (result == 'ERROR_SYSTEM_OBJECT_LIMIT')
					{
						notifyDialog(la['SYSTEM_OBJECT_LIMIT_IS_REACHED']);
					}
					else if (result == 'ERROR_OBJECT_LIMIT')
					{
						notifyDialog(la['OBJECT_LIMIT_IS_REACHED']);
					}
					else if (result == 'ERROR_EXPIRATION_DATE_NOT_SET')
					{
						notifyDialog(la['OBJECT_EXPIRATION_DATE_MUST_BE_SET']);
					}
					else if (result == 'ERROR_EXPIRATION_DATE_TOO_LATE')
					{
						notifyDialog(la['OBJECT_EXPIRATION_DATE_IS_TOO_LATE']);
					}
					else if (result == 'ERROR_NO_PRIVILEGES')
					{
						notifyDialog(la['THIS_ACCOUNT_HAS_NO_PRIVILEGES_TO_DO_THAT']);
					}
					else if (result == 'ERROR_IMEI_EXISTS')
					{
						notifyDialog(la['THIS_IMEI_ALREADY_EXISTS']);
					}
				}
			});
			break;
		case "cancel":
			$("#dialog_object_add").dialog("close");
			break;
	}
}

function objectAddCheck()
{
	var object_expire = document.getElementById('dialog_object_add_object_expire').checked;
	if (object_expire == true)
	{
                document.getElementById('dialog_object_add_object_expire_dt').disabled = false;
        }
	else
	{
		document.getElementById('dialog_object_add_object_expire_dt').disabled = true;
	}
}

function objectEdit(cmd)
{
	switch (cmd)
	{
		default:			
			var data = {
				cmd: 'load_object_data',
				imei: cmd
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.objects.php",
				data: data,
				dataType: 'json',
				cache: false,
				success: function(result)
				{
					// set object edit properties availability
					if (cpValues['privileges'] == 'manager')
					{
						document.getElementById('dialog_object_edit_manager_id').disabled = true;
					}
					
					// set loaded properties
					cpValues['edit_object_imei'] = result['imei'];
					cpValues['edit_object_new_imei'] = '';
					
					document.getElementById('dialog_object_edit_active').checked = strToBoolean(result['active']);
					
					var object_expire = strToBoolean(result['object_expire']);
					document.getElementById('dialog_object_edit_object_expire').checked = object_expire;
					if (object_expire == true)
					{
                                                document.getElementById('dialog_object_edit_object_expire_dt').value = result['object_expire_dt'];
                                        }
					else
					{
						document.getElementById('dialog_object_edit_object_expire_dt').value = '';
					}
					
					document.getElementById('dialog_object_edit_name').value = result['name'];
					document.getElementById('dialog_object_edit_imei').value = result['imei'];
					document.getElementById('dialog_object_edit_model').value = result['model'];
					document.getElementById('dialog_object_edit_vin').value = result['vin'];
					document.getElementById('dialog_object_edit_plate_number').value = result['plate_number'];
					document.getElementById('dialog_object_edit_device').value = result['device'];
					document.getElementById('dialog_object_edit_sim_number').value = result['sim_number'];
					document.getElementById('dialog_object_edit_manager_id').value = result['manager_id'];
					$("#dialog_object_edit_manager_id").multipleSelect('refresh');
					
					objectEditCheck();
					
					$('#dialog_object_edit_users').tokenize().clear();
					
					$('#dialog_object_edit_users').tokenize().options.newElements = true;
					var users = result['users'];
					for(var i=0;i<users.length;i++)
					{
						var value = users[i].value;
						var text = users[i].text;
						$('#dialog_object_edit_users').tokenize().tokenAdd(value, text);
					}
					$('#dialog_object_edit_users').tokenize().options.newElements = false;
				}
			});
			
			$("#dialog_object_edit").dialog("open");
			break;
		case "save":
			var active = document.getElementById('dialog_object_edit_active').checked;
			var object_expire = document.getElementById('dialog_object_edit_object_expire').checked;
			var object_expire_dt = document.getElementById('dialog_object_edit_object_expire_dt').value;
			var name = document.getElementById('dialog_object_edit_name').value;
			var imei = document.getElementById('dialog_object_edit_imei').value;
			var model = document.getElementById('dialog_object_edit_model').value;
			var vin = document.getElementById('dialog_object_edit_vin').value;
			var plate_number = document.getElementById('dialog_object_edit_plate_number').value;
			var device = document.getElementById('dialog_object_edit_device').value;
			var sim_number = document.getElementById('dialog_object_edit_sim_number').value;
			var manager_id = document.getElementById('dialog_object_edit_manager_id').value;
			
			var user_ids = $('#dialog_object_edit_users').tokenize().toArray();
			
			user_ids = JSON.stringify(user_ids);
			
			if (name == "")
			{
				notifyDialog(la['NAME_CANT_BE_EMPTY']);
				return;
			}
			
			if(!isIMEIValid(imei))
			{
				notifyDialog(la['IMEI_IS_NOT_VALID']);
				return;
			}
			
			// expire object
			if (object_expire == true)
			{
				if (object_expire_dt == '')
				{
					notifyDialog(la['DATE_CANT_BE_EMPTY']);
					break;
				}
			}
			else
			{
                                object_expire_dt = '';
                        }
			
			if (imei != cpValues['edit_object_imei'])
			{
				cpValues['edit_object_new_imei'] = imei;
				  
				confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_CHANGE_OBJECT_IMEI'], function(response){
					if (response)
					{
						responseSave();
					}
				});
                        }
			else
			{
				responseSave();
			}
			
			break;
		case "cancel":
			$("#dialog_object_edit").dialog("close");
			break;
	}
	
	function responseSave()
	{
                var data = {
			cmd: 'edit_object',
			active: active,
			object_expire: object_expire,
			object_expire_dt: object_expire_dt,
			name: name,
			imei: cpValues['edit_object_imei'],
			new_imei: cpValues['edit_object_new_imei'],
			model: model,
			vin: vin,
			plate_number: plate_number,
			device: device,
			sim_number: sim_number,
			manager_id: manager_id,
			user_ids: user_ids
		};
		
		$.ajax({
			type: "POST",
			url: "func/fn_cpanel.objects.php",
			data: data,
			success: function(result)
			{
				if (result == 'OK')
				{
					initSelectList('manager_list');
					
					$("#dialog_object_edit").dialog("close");
				}
				else if (result == 'ERROR_EXPIRATION_DATE_NOT_SET')
				{
					notifyDialog(la['OBJECT_EXPIRATION_DATE_MUST_BE_SET']);
				}
				else if (result == 'ERROR_EXPIRATION_DATE_TOO_LATE')
				{
					notifyDialog(la['OBJECT_EXPIRATION_DATE_IS_TOO_LATE']);
				}
				else if (result == 'ERROR_IMEI_EXISTS')
				{
					notifyDialog(la['THIS_IMEI_ALREADY_EXISTS']);
				}
			}
		});
        }
}

function objectEditCheck()
{
	var object_expire = document.getElementById('dialog_object_edit_object_expire').checked;
	if (object_expire == true)
	{
                document.getElementById('dialog_object_edit_object_expire_dt').disabled = false;
        }
	else
	{
		document.getElementById('dialog_object_edit_object_expire_dt').disabled = true;
	}
}

function objectClearHistory(imei)
{
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_CLEAR_HISTORY_EVENTS'], function(response){
		if (response)
		{
			var data = {
				cmd: 'clear_history_object',
				imei: imei
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.objects.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						$('#cpanel_object_list_grid').trigger("reloadGrid");	
					}
				}
			});
		}
	});
}

function objectDelete(imei){
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_OBJECT_FROM_SYSTEM'], function(response){
		if (response)
		{
			var data = {
				cmd: 'delete_object',
				imei: imei
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
						
						$('#cpanel_user_list_grid').trigger("reloadGrid");
						$('#cpanel_object_list_grid').trigger("reloadGrid");	
					}
				}
			});
		}
	});
}

function objectActivate(imei)
{
	var data = {
		cmd: 'activate_object',
		imei: imei
	};
	
	$.ajax({
		type: "POST",
		url: "func/fn_cpanel.objects.php",
		data: data,
		success: function(result)
		{
			if (result == 'OK')
			{				
				if ($('#dialog_user_edit').dialog('isOpen') == true)
				{
				       $('#dialog_user_edit_object_list_grid').trigger("reloadGrid"); 
				}
				else
				{
					$('#cpanel_user_list_grid').trigger("reloadGrid");
					$('#cpanel_object_list_grid').trigger("reloadGrid");
				}
			}
		}
	});
}

function objectDeactivate(imei)
{
	var data = {
		cmd: 'deactivate_object',
		imei: imei
	};
	
	$.ajax({
		type: "POST",
		url: "func/fn_cpanel.objects.php",
		data: data,
		success: function(result)
		{
			if (result == 'OK')
			{					
				if ($('#dialog_user_edit').dialog('isOpen') == true)
				{
				       $('#dialog_user_edit_object_list_grid').trigger("reloadGrid"); 
				}
				else
				{
					$('#cpanel_user_list_grid').trigger("reloadGrid");
					$('#cpanel_object_list_grid').trigger("reloadGrid");
				}
			}
		}
	});
}

function objectActivateSelected()
{
	var objects = $('#cpanel_object_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (objects == '')
	{
		notifyDialog(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_ACTIVATE_SELECTED_ITEMS'], function(response){
		if (response)
		{
			var data = {
				cmd: 'activate_selected_objects',
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
						$('#cpanel_user_list_grid').trigger("reloadGrid");
						$('#cpanel_object_list_grid').trigger("reloadGrid");		
					}
				}
			});
		}
	});
}

function objectDeactivateSelected()
{
	var objects = $('#cpanel_object_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (objects == '')
	{
		notifyDialog(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_DEACTIVATE_SELECTED_ITEMS'], function(response){
		if (response)
		{
			var data = {
				cmd: 'deactivate_selected_objects',
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
						$('#cpanel_user_list_grid').trigger("reloadGrid");
						$('#cpanel_object_list_grid').trigger("reloadGrid");		
					}
				}
			});
		}
	});
}

function objectClearHistorySelected()
{
        var objects = $('#cpanel_object_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
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
							
						$('#cpanel_object_list_grid').trigger("reloadGrid");	
					}
				}
			});
		}
	});
}

function objectDeleteSelected()
{
	var objects = $('#cpanel_object_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (objects == '')
	{
		notifyDialog(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_SELECTED_ITEMS'], function(response){
		if (response)
		{
			var data = {
				cmd: 'delete_selected_objects',
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
							
						$('#cpanel_user_list_grid').trigger("reloadGrid");
						$('#cpanel_object_list_grid').trigger("reloadGrid");		
					}
				}
			});
		}
	});
}

function unusedObjectDelete(imei){
	
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_UNUSED_OBJECT'], function(response){
		if (response)
		{
			var data = {
				cmd: 'delete_unused_object',
				imei: imei
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
						$('#cpanel_unused_object_list_grid').trigger("reloadGrid");	
					}
				}
			});	
		}
	});
}

function unusedObjectDeleteSelected()
{	
	var objects = $('#cpanel_unused_object_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (objects == '')
	{
		notifyDialog(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_SELECTED_ITEMS'], function(response){
		if (response)
		{
			var data = {
				cmd: 'delete_selected_unused_objects',
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
						$('#cpanel_unused_object_list_grid').trigger("reloadGrid");		
					}
				}
			});
		}
	});
}
function objectImport()
{
        // a bit dirty sollution, maybe will make better in the feature :)
        document.getElementById('load_file').addEventListener('change', objectImportCSVFile, false);
        document.getElementById('load_file').click();
}

function objectImportCSVFile(evt)
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
				
				console.log(data_json);
				
				for (i=0; i<data_json.length; i+=1)
				{
					if ((data_json[i].name != undefined) && (data_json[i].imei != undefined))
					{
						if ((data_json[i].name == '') || (data_json[i].imei == ''))
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
								
                                var objects = JSON.stringify(data_json);				
				var objects_count = data_json.length;
				
				if (objects_count == 0)
                                {
					notifyDialog(la['NOTHING_HAS_BEEN_FOUND_TO_IMPORT']);
                                        return;
                                }
				
				var text = sprintf(la['OBJECTS_FOUND'], objects_count) + ' ' + la['ARE_YOU_SURE_YOU_WANT_TO_IMPORT'];
				
                                confirmDialog(text, function(response){
                                        if (response)
                                        {
                                                loadingData(true);
                                                
                                                var data = {
                                                        format: 'object_csv',
                                                        data: objects
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
									$('#cpanel_object_list_grid').trigger("reloadGrid");
								}
								else if (result == 'ERROR_SYSTEM_OBJECT_LIMIT')
								{
									notifyDialog(la['SYSTEM_OBJECT_LIMIT_IS_REACHED']);
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
        
        this.removeEventListener('change', objectImportCSVFile, false);
}