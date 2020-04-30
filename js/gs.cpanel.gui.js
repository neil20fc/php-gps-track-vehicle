//#################################################
// DIALOGS, TABS, GROUPING
//#################################################

function initGui()
{
	// add callback to datepicker afterShow
	$.datepicker._updateDatepicker_original = $.datepicker._updateDatepicker;
	$.datepicker._updateDatepicker = function(inst) {
	$.datepicker._updateDatepicker_original(inst);
	var afterShow = this._get(inst, 'afterShow');
	if (afterShow)
	    afterShow.apply((inst.input ? inst.input[0] : null));  // trigger custom callback
	}
	
	// define calendar
	$('.inputbox-calendar').datepicker({
		afterShow: function() {
			$(".ui-datepicker select").multipleSelect({single: true});
		},
		changeMonth: true,
		changeYear: true,
		dateFormat: "yy-mm-dd",
		firstDay: 1,
		dayNamesMin: [la['DAY_SUNDAY_S'], la['DAY_MONDAY_S'], la['DAY_TUESDAY_S'], la['DAY_WEDNESDAY_S'], la['DAY_THURSDAY_S'], la['DAY_FRIDAY_S'], la['DAY_SATURDAY_S']],
		monthNames: [la['MONTH_JANUARY'], la['MONTH_FEBRUARY'], la['MONTH_MARCH'], la['MONTH_APRIL'], la['MONTH_MAY'], la['MONTH_JUNE'], la['MONTH_JULY'], la['MONTH_AUGUST'], la['MONTH_SEPTEMBER'], la['MONTH_OCTOBER'], la['MONTH_NOVEMBER'], la['MONTH_DECEMBER']]
	});
	
	$('.inputbox-calendar-mmdd').datepicker({
		afterShow: function() {
			$(".ui-datepicker select").multipleSelect({single: true});
		},
		changeMonth: true,
		changeYear: true,
		dateFormat: "mm-dd",
		firstDay: 1,
		dayNamesMin: [la['DAY_SUNDAY_S'], la['DAY_MONDAY_S'], la['DAY_TUESDAY_S'], la['DAY_WEDNESDAY_S'], la['DAY_THURSDAY_S'], la['DAY_FRIDAY_S'], la['DAY_SATURDAY_S']],
		monthNames: [la['MONTH_JANUARY'], la['MONTH_FEBRUARY'], la['MONTH_MARCH'], la['MONTH_APRIL'], la['MONTH_MAY'], la['MONTH_JUNE'], la['MONTH_JULY'], la['MONTH_AUGUST'], la['MONTH_SEPTEMBER'], la['MONTH_OCTOBER'], la['MONTH_NOVEMBER'], la['MONTH_DECEMBER']],
		monthNamesShort: [la['MONTH_JANUARY_S'], la['MONTH_FEBRUARY_S'], la['MONTH_MARCH_S'], la['MONTH_APRIL_S'], la['MONTH_MAY_S'], la['MONTH_JUNE_S'], la['MONTH_JULY_S'], la['MONTH_AUGUST_S'], la['MONTH_SEPTEMBER_S'], la['MONTH_OCTOBER_S'], la['MONTH_NOVEMBER_S'], la['MONTH_DECEMBER_S']]
	});
	
	// define tabs
	$("#manage_server_tabs, #dialog_user_edit_tabs").tabs({});
	
	// define tokenize
	$('#dialog_user_object_add_objects').tokenize({
		datas: "func/fn_cpanel.objects.php?cmd=load_object_search_list&manager_id=" + cpValues['manager_id'],
		placeholder: la['ENTER_OBJECT_NAME_OR_IMEI'],
		newElements: false
	});
	
	$('#dialog_object_add_users').tokenize({
		datas: "func/fn_cpanel.users.php?cmd=load_user_search_list&manager_id=" + cpValues['manager_id'],
		placeholder: la['ENTER_ACCOUNT_USERNAME_OR_EMAIL'],
		newElements: false
	});
	
	$('#dialog_object_edit_users').tokenize({
		datas: "func/fn_cpanel.users.php?cmd=load_user_search_list&manager_id=" + cpValues['manager_id'],
		placeholder: la['ENTER_ACCOUNT_USERNAME_OR_EMAIL'],
		newElements: false
	});
	
	$('#send_email_username').tokenize({
		datas: "func/fn_cpanel.users.php?cmd=load_user_search_list&manager_id=" + cpValues['manager_id'],
		placeholder: la['ENTER_ACCOUNT_USERNAME_OR_EMAIL'],
		newElements: false
	});
	
	// define dialogs
	$("#dialog_notify").dialog({
		autoOpen: false,
		width: "auto",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false,
		draggable: false,
		dialogClass: 'dialog-notify-titlebar'
	});
	
	$("#dialog_confirm").dialog({
		autoOpen: false,
		width: "auto",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false,
		draggable: false,
		dialogClass: 'dialog-notify-titlebar'
	});
		
	$("#dialog_set_expiration").dialog({
		autoOpen: false,
		width: "320px",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false
	});
	
	$("#dialog_send_email").dialog({
		autoOpen: false,
		width: "700px",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false
	});
	
	$("#dialog_user_add").dialog({
		autoOpen: false,
		width: "320px",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false
	});
	
	$("#dialog_user_edit").dialog({
		autoOpen: false,
		width: "850px",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false,
		close: function(event, ui) {
						$('#cpanel_object_list_grid').trigger("reloadGrid");
						$('#cpanel_user_list_grid').trigger("reloadGrid");
						$('#cpanel_billing_plan_list_grid').trigger("reloadGrid");
					}
	});
	
	
	$("#dialog_object_edit").dialog({
		autoOpen: false,
		width: "450px",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false,
		close: function(event, ui) {
						$('#cpanel_object_list_grid').trigger("reloadGrid");
						$('#cpanel_user_list_grid').trigger("reloadGrid");
						if ($('#dialog_user_edit').dialog('isOpen') == true)
						{
						       $('#dialog_user_edit_object_list_grid').trigger("reloadGrid"); 
						}
					}
	});
	
	$("#dialog_user_object_add").dialog({
		autoOpen: false,
		width: "320px",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false
	});
	
	$("#dialog_user_billing_plan_add").dialog({
		autoOpen: false,
		width: "400px",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false
	});
	
	$("#dialog_user_billing_plan_edit").dialog({
		autoOpen: false,
		width: "400px",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false,
		close: function(event, ui) {
						$('#cpanel_billing_plan_list_grid').trigger("reloadGrid");
						if ($('#dialog_user_edit').dialog('isOpen') == true)
						{
						       $('#dialog_user_edit_billing_plan_list_grid').trigger("reloadGrid"); 
						}
					}
	});
	
	$("#dialog_object_add").dialog({
		autoOpen: false,
		width: "450px",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false
	});
	
	$("#dialog_theme_properties").dialog({
		autoOpen: false,
		width: "800",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false
	});
	
	$("#dialog_custom_map_properties").dialog({
		autoOpen: false,
		width: "600",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false
	});
	
	$("#dialog_billing_properties").dialog({
		autoOpen: false,
		width: "400",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false
	});
	
	$("#dialog_language_properties").dialog({
		autoOpen: false,
		width: "1000",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false,
		close: function(event, ui) { 	cpValues['language_edit_items'] = new Array();
						document.getElementById('dialog_language_editor').innerHTML = '';
					}
	});
	
	$("#dialog_template_properties").dialog({
		autoOpen: false,
		width: "800",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false
	});
	
	$(".select").multipleSelect({single: true});
	$('.select-search').multipleSelect({width: "100%", single: true, filter: true});
	$(".select-multiple").multipleSelect({	width: "100%",
							selectAllText: la['SELECT_ALL'],
							allSelected: la['ALL_SELECTED'],
							countSelected: "# " + la['SELECTED'].toLowerCase(),
							noMatchesFound: la['NO_MATCHES_FOUND'],
							noItems: la['NO_ITEMS'],
							placeholder: la['NOTHING_SELECTED']
						});
	$(".select-multiple-search").multipleSelect({	width: "100%",
							filter: true,
							selectAllText: la['SELECT_ALL'],
							allSelected: la['ALL_SELECTED'],
							countSelected: "# " + la['SELECTED'].toLowerCase(),
							noMatchesFound: la['NO_MATCHES_FOUND'],
							noItems: la['NO_ITEMS'],
							placeholder: la['NOTHING_SELECTED']
						});
}

//#################################################
// END DIALOGS, TABS, GROUPING
//#################################################

//#################################################
// NOTIFY/CONFIRM DIALOGS/POPUPS
//#################################################

function loadingData(visible)
{
	if (visible == true)
	{
		document.getElementById("loading_data_panel").style.display = "";
	}
	else
	{
		document.getElementById("loading_data_panel").style.display = "none";
	}
}

function notifyDialog(text)
{
	document.getElementById('dialog_notify_text').innerHTML = text;
	
	$('#dialog_notify').dialog('open');
}

var confirmResponseValue = false;

function confirmDialog(text, response)
{
	confirmResponseValue = false;
	
	document.getElementById('dialog_confirm_text').innerHTML = text;
	
	$('#dialog_confirm').dialog('destroy');
	
	$("#dialog_confirm").dialog({
		autoOpen: false,
		width: "auto",
		height: "auto",
		minHeight: "auto",
		modal: true,
		resizable: false,
		draggable: false,
		dialogClass: 'dialog-notify-titlebar',
		close: function(event, ui) { response(confirmResponseValue); }
	});
	
	$('#dialog_confirm').dialog('open');
}

function confirmResponse(value)
{
        confirmResponseValue = value;
	$('#dialog_confirm').dialog('close');
}

//#################################################
// END NOTIFY/CONFIRM DIALOGS/POPUPS
//#################################################

function initGrids()
{
	// define user list grid
	$("#cpanel_user_list_grid").jqGrid({
		url:'func/fn_cpanel.users.php?cmd=load_user_list',
		datatype: "json",
		colNames:['ID',la['USERNAME'], la['EMAIL'],la['ACTIVE'],la['EXPIRES_ON'],la['PRIVILEGES'],la['API'], la['REG_TIME'],la['LOGIN_TIME'],la['IP'],la['SUB_ACC'],la['OBJECTS'],la['EMAIL'],la['SMS'],la['API'],'',''],
		colModel:[
			{name:'id',index:'id',width:50,align:"center"},
			{name:'username',index:'username',width:150},
			{name:'email',index:'email',width:150},
			{name:'active',index:'active',width:50,align:"center"},
			{name:'account_expire_dt',index:'account_expire_dt',width:60,align:"center"},
			{name:'privileges',index:'privileges',width:70,align:"center"},
			{name:'api',index:'api',width:50,align:"center"},
			{name:'dt_reg',index:'dt_reg',width:110,align:"center"},
			{name:'dt_login',index:'dt_login',width:110,align:"center"},		
			{name:'ip',index:'ip',width:110},
			{name:'subacc_cnt',index:'subacc_cnt',width:50,align:"center"},
			{name:'obj_cnt',index:'obj_cnt',width:50,align:"center"},
			{name:'usage_email_daily_cnt',index:'usage_email_daily_cnt',width:50,align:"center"},
			{name:'usage_sms_daily_cnt',index:'usage_sms_daily_cnt',width:50,align:"center"},
			{name:'usage_api_daily_cnt',index:'usage_api_daily_cnt',width:50,align:"center"},
			{name:'modify',index:'modify',width:75,align:"center",sortable: false, fixed: true},
			{name:'scroll_fix',index:'scroll_fix',width:13,sortable: false, fixed: true} // scroll fix
		],
		//altRows: true,
		//altclass: 'myAltRowClass',
		rowNum:50,
		rowList:[25,50,100,200,300,400,500],
		pager: '#cpanel_user_list_grid_pager',
		sortname: 'id',
		sortorder: "asc",
		viewrecords: true,
		rownumbers: true,
		height: '400px',
		shrinkToFit: true,
		multiselect: true,
		beforeSelectRow: function(id, e)
		{
			if (e.target.tagName.toLowerCase() === "input"){return true;}
			return false;
		}
	});
	$("#cpanel_user_list_grid").jqGrid('navGrid','#cpanel_user_list_grid_pager',{ 	add:true,
											edit:false,
											del:false,
											search:false,
											addfunc: function (e) {userAdd('open');}																		
											});
	
	$("#cpanel_user_list_grid").navButtonAdd('#cpanel_user_list_grid_pager',{	caption: "", 
											title: la['ACTION'],
											buttonicon: 'ui-icon-action',
											onClickButton: function(){}, 
											position:"last",
											id: "cpanel_user_list_grid_action_menu_button"
											});

	// action menu
	$("#cpanel_user_list_grid_action_menu").menu({
		role: 'listbox'
	});
	$("#cpanel_user_list_grid_action_menu").hide();
	
	$("#cpanel_user_list_grid_action_menu_button").click(function() {
			$("#cpanel_user_list_grid_action_menu").toggle().position({
			my: "left bottom",
			at: "right-5 top-5",
			of: this
		});
				
		$(document).one("click", function() {
			$("#cpanel_user_list_grid_action_menu").hide();
		});
		
		return false;
	});

	$("#cpanel_user_list_grid").setCaption(	'<div class="row4">\
							<div class="float-left">\
								<a href="#" onclick="sendEmail(\'open\');" title="'+la['SEND_EMAIL']+'">\
								<div class="panel-button">\
									<img src="theme/images/create.svg" width="16px" border="0"/>\
								</div>\
								</a>\
							</div>\
							<input id="cpanel_user_list_search" class="inputbox-search" type="text" value="" placeholder="'+la['SEARCH']+'" maxlength="25">\
						</div>');
	
	$("#cpanel_user_list_search").bind("keyup", function(e) {
		var manager_id = '&manager_id=' + cpValues['manager_id'];
		$('#cpanel_user_list_grid').setGridParam({url:'func/fn_cpanel.users.php?cmd=load_user_list&s=' + this.value + manager_id});
		$('#cpanel_user_list_grid').trigger("reloadGrid");	
	});
	
	$("#cpanel_user_list_grid").setGridWidth($(window).width() - 60 );
	$("#cpanel_user_list_grid").setGridHeight($(window).height() - 207);
	$(window).bind('resize', function() {$("#cpanel_user_list_grid").setGridWidth($(window).width() - 60);});
	$(window).bind('resize', function() {$("#cpanel_user_list_grid").setGridHeight($(window).height() - 207);});
	
	// define object list grid
	$("#cpanel_object_list_grid").jqGrid({
		url:'func/fn_cpanel.objects.php?cmd=load_object_list',
		datatype: "json",
		colNames:[la['NAME'],la['IMEI'],la['ACTIVE'],la['EXPIRES_ON'],la['SIM_CARD_NUMBER'],la['LAST_CONNECTION'],la['PROTOCOL'],la['NET_PROTOCOL'],la['PORT'],la['STATUS'],la['USER_ACCOUNT'],'',''],
		colModel:[
			{name:'name',index:'name',width:80},
			{name:'imei',index:'imei',width:80},
			{name:'active',index:'active',width:50,align:"center"},
			{name:'object_expire_dt',index:'object_expire_dt',width:60,align:"center"},
			{name:'sim_number',index:'sim_number',width:80},
			{name:'dt_server',index:'dt_server',width:80,align:"center"},
			{name:'protocol',index:'protocol',width:60,align:"center"},
			{name:'net_protocol',index:'net_protocol',width:40,align:"center"},
			{name:'port',index:'port',width:40,align:"center"},
			{name:'status',index:'status',width:80,sortable: false,align:"center"},
			{name:'used_in',index:'used_in',width:150,sortable: false},
			{name:'modify',index:'modify',width:75,align:"center",sortable: false, fixed: true},
			{name:'scroll_fix',index:'scroll_fix',width:13,sortable: false, fixed: true} // scroll fix
		],
		rowNum:50,
		rowList:[25,50,100,200,300,400,500],
		pager: '#cpanel_object_list_grid_pager',
		sortname: 'imei',
		sortorder: "asc",
		viewrecords: true,
		rownumbers: true,
		height: '400px',
		shrinkToFit: true,
		multiselect: true,
		beforeSelectRow: function(id, e)
		{
			if (e.target.tagName.toLowerCase() === "input"){return true;}
			return false;
		}
	});
	$("#cpanel_object_list_grid").jqGrid('navGrid','#cpanel_object_list_grid_pager',{	add:true,
												edit:false,																								
												del:false,
												search:false,
												addfunc: function (e) {objectAdd('open');}	
												});
	
	$("#cpanel_object_list_grid").navButtonAdd('#cpanel_object_list_grid_pager',{	caption: "", 
											title: la['ACTION'],
											buttonicon: 'ui-icon-action',
											onClickButton: function(){}, 
											position:"last",
											id: "cpanel_object_list_grid_action_menu_button"
											});

	// action menu
	$("#cpanel_object_list_grid_action_menu").menu({
		role: 'listbox'
	});
	$("#cpanel_object_list_grid_action_menu").hide();
	
	$("#cpanel_object_list_grid_action_menu_button").click(function() {
			$("#cpanel_object_list_grid_action_menu").toggle().position({
			my: "left bottom",
			at: "right-5 top-5",
			of: this
		});
				
		$(document).one("click", function() {
			$("#cpanel_object_list_grid_action_menu").hide();
		});
		
		return false;
	});
	
	$("#cpanel_object_list_grid").setCaption(	'<div class="row4">\
								<input id="cpanel_object_list_search" class="inputbox-search" type="text" value="" placeholder="'+la['SEARCH']+'" maxlength="25">\
							</div>');
	
	$("#cpanel_object_list_search").bind("keyup", function(e) {
		var manager_id = '&manager_id=' + cpValues['manager_id'];
		$('#cpanel_object_list_grid').setGridParam({url:'func/fn_cpanel.objects.php?cmd=load_object_list&s=' + this.value + manager_id});
		$('#cpanel_object_list_grid').trigger("reloadGrid");
	});
	
	$("#cpanel_object_list_grid").setGridWidth($(window).width() - 60 );
	$("#cpanel_object_list_grid").setGridHeight($(window).height() - 207);
	$(window).bind('resize', function() {$("#cpanel_object_list_grid").setGridWidth($(window).width() - 60 );});
	$(window).bind('resize', function() {$("#cpanel_object_list_grid").setGridHeight($(window).height() - 207);});
	
	// define unused object list grid
	if (document.getElementById('cpanel_unused_object_list_grid') != undefined)
	{
		$("#cpanel_unused_object_list_grid").jqGrid({
			url:'func/fn_cpanel.objects.php?cmd=load_unused_object_list',
			datatype: "json",
			colNames:[la['IMEI'],la['LAST_CONNECTION'],la['PROTOCOL'],la['NET_PROTOCOL'],la['PORT'],la['CONNECTION_ATTEMPTS'],'',''],
			colModel:[
				{name:'imei',index:'imei',width:160},
				{name:'dt_server',index:'dt_server',width:160,align:"center"},
				{name:'protocol',index:'protocol',width:100,align:"center"},
				{name:'net_protocol',index:'net_protocol',width:100,align:"center"},
				{name:'port',index:'port',width:100,align:"center"},
				{name:'count',index:'count',width:100,align:"center"},
				{name:'modify',index:'modify',width:75,align:"center",sortable: false, fixed: true},
				{name:'scroll_fix',index:'scroll_fix',width:13,sortable: false, fixed: true} // scroll fix
			],
			rowNum:50,
			rowList:[25,50,100,200,300,400,500],
			pager: '#cpanel_unused_object_list_grid_pager',
			sortname: 'imei',
			sortorder: "asc",
			viewrecords: true,
			rownumbers: true,
			height: '400px',
			shrinkToFit: true,
			multiselect: true,
			beforeSelectRow: function(id, e)
			{
				if (e.target.tagName.toLowerCase() === "input"){return true;}
				return false;
			}
		});
		$("#cpanel_unused_object_list_grid").jqGrid('navGrid','#cpanel_unused_object_list_grid_pager',{	add:false,
														edit:false,																								
														del:false,
														search:false
														});
		
		$("#cpanel_unused_object_list_grid").navButtonAdd('#cpanel_unused_object_list_grid_pager',{	caption: "", 
														title: la['ACTION'],
														buttonicon: 'ui-icon-action',
														onClickButton: function(){}, 
														position:"last",
														id: "cpanel_unused_object_list_grid_action_menu_button"
														});
	
		// action menu
		$("#cpanel_unused_object_list_grid_action_menu").menu({
			role: 'listbox'
		});
		$("#cpanel_unused_object_list_grid_action_menu").hide();
		
		$("#cpanel_unused_object_list_grid_action_menu_button").click(function() {
				$("#cpanel_unused_object_list_grid_action_menu").toggle().position({
				my: "left bottom",
				at: "right-5 top-5",
				of: this
			});
					
			$(document).one("click", function() {
				$("#cpanel_unused_object_list_grid_action_menu").hide();
			});
			
			return false;
		});
			
		$("#cpanel_unused_object_list_grid").setCaption('<div class="row4">\
									<input id="cpanel_unused_object_list_search" class="inputbox-search" type="text" value="" placeholder="'+la['SEARCH']+'" maxlength="25">\
								</div>');
		
		$("#cpanel_unused_object_list_search").bind("keyup", function(e) {
			$('#cpanel_unused_object_list_grid').setGridParam({url:'func/fn_cpanel.objects.php?cmd=load_unused_object_list&s=' + this.value});
			$('#cpanel_unused_object_list_grid').trigger("reloadGrid");
		});
		
		$("#cpanel_unused_object_list_grid").setGridWidth($(window).width() - 60 );
		$("#cpanel_unused_object_list_grid").setGridHeight($(window).height() - 207);
		$(window).bind('resize', function() {$("#cpanel_unused_object_list_grid").setGridWidth($(window).width() - 60 );});
		$(window).bind('resize', function() {$("#cpanel_unused_object_list_grid").setGridHeight($(window).height() - 207);});
	}
	
	// define billing plan list grid
	if (document.getElementById("cpanel_billing_plan_list_grid") != undefined)
	{
		$("#cpanel_billing_plan_list_grid").jqGrid({
			url:'func/fn_cpanel.billing.php?cmd=load_billing_plan_list',
			datatype: "json",
			colNames:[la['TIME'],la['NAME'],la['OBJECTS'],la['PERIOD'],la['PRICE'],la['USER_ACCOUNT'],'',''],
			colModel:[
				{name:'dt_purchase',index:'dt_purchase',width:50,align:"center"},
				{name:'name',index:'name',width:80},
				{name:'objects',index:'objects',width:50,align:"center"},
				{name:'period',index:'period',width:50,align:"center"},
				{name:'price',index:'price',width:50,align:"center"},
				{name:'used_in',index:'used_in',width:80,sortable: false},
				{name:'modify',index:'modify',width:75,align:"center",sortable: false, fixed: true},
				{name:'scroll_fix',index:'scroll_fix',width:13,sortable: false, fixed: true} // scroll fix
			],
			rowNum:50,
			rowList:[25,50,100,200,300,400,500],
			pager: '#cpanel_billing_plan_list_grid_pager',
			sortname: 'dt_purchase',
			sortorder: "desc",
			viewrecords: true,
			rownumbers: true,
			height: '400px',
			shrinkToFit: true,
			multiselect: true,
			beforeSelectRow: function(id, e)
			{
				if (e.target.tagName.toLowerCase() === "input"){return true;}
				return false;
			}
		});
		$("#cpanel_billing_plan_list_grid").jqGrid('navGrid','#cpanel_billing_plan_list_grid_pager',{	add:false,
														edit:false,																								
														del:false,
														search:false
														});
		
		$("#cpanel_billing_plan_list_grid").navButtonAdd('#cpanel_billing_plan_list_grid_pager',{	caption: "", 
														title: la['ACTION'],
														buttonicon: 'ui-icon-action',
														onClickButton: function(){}, 
														position:"last",
														id: "cpanel_billing_plan_list_grid_action_menu_button"
														});
	
		// action menu
		$("#cpanel_billing_plan_list_grid_action_menu").menu({
			role: 'listbox'
		});
		$("#cpanel_billing_plan_list_grid_action_menu").hide();
		
		$("#cpanel_billing_plan_list_grid_action_menu_button").click(function() {
				$("#cpanel_billing_plan_list_grid_action_menu").toggle().position({
				my: "left bottom",
				at: "right-5 top-5",
				of: this
			});
					
			$(document).one("click", function() {
				$("#cpanel_billing_plan_list_grid_action_menu").hide();
			});
			
			return false;
		});
				
		$("#cpanel_billing_plan_list_grid").setCaption(	'<div class="row4">\
									<input id="cpanel_billing_plan_list_search" class="inputbox-search" type="text" value="" placeholder="'+la['SEARCH']+'" maxlength="25">\
								</div>');
		
		$("#cpanel_billing_plan_list_search").bind("keyup", function(e) {
			var manager_id = '&manager_id=' + cpValues['manager_id'];
			$('#cpanel_billing_plan_list_grid').setGridParam({url:'func/fn_cpanel.billing.php?cmd=load_billing_plan_list&s=' + this.value + manager_id});
			$('#cpanel_billing_plan_list_grid').trigger("reloadGrid");
		});
		
		$("#cpanel_billing_plan_list_grid").setGridWidth($(window).width() - 60 );
		$("#cpanel_billing_plan_list_grid").setGridHeight($(window).height() - 207);
		$(window).bind('resize', function() {$("#cpanel_billing_plan_list_grid").setGridWidth($(window).width() - 60 );});
		$(window).bind('resize', function() {$("#cpanel_billing_plan_list_grid").setGridHeight($(window).height() - 207);});
	}
	
	// define user subaccount list grid
	$("#dialog_user_edit_subaccount_list_grid").jqGrid({
		url:'func/fn_cpanel.users.php',
		datatype: "json",
		colNames:[la['USERNAME'],la['EMAIL'],la['PASSWORD'],la['ACTIVE'],la['IP'],''],
		colModel:[
			{name:'username',index:'username',width:190},
			{name:'email',index:'email',width:160},
			{name:'password',index:'password',width:160,sortable: false},
			{name:'active',index:'active',width:40,align:"center"},
			{name:'ip',index:'ip',width:108},
			{name:'modify',index:'modify',width:60,align:"center",sortable: false},
		],
		rowNum:25,
		rowList:[25,50,100,200,300,400,500],
		pager: '#dialog_user_edit_subaccount_list_grid_pager',
		sortname: 'username',
		sortorder: "asc",
		viewrecords: true,
		rownumbers: true,
		height: '443px',
		width: '820',
		shrinkToFit: false,
		multiselect: true,
		beforeSelectRow: function(id, e)
		{
			if (e.target.tagName.toLowerCase() === "input"){return true;}
			return false;
		}
	});
	$("#dialog_user_edit_subaccount_list_grid").jqGrid('navGrid','#dialog_user_edit_subaccount_list_grid_pager',{ 	add:false,
															edit:false,
															del:false,
															search:false													
															});
	
	$("#dialog_user_edit_subaccount_list_grid").navButtonAdd('#dialog_user_edit_subaccount_list_grid_pager',{	caption: "", 
															title: la['ACTION'],
															buttonicon: 'ui-icon-action',
															onClickButton: function(){}, 
															position:"last",
															id: "dialog_user_edit_subaccount_list_grid_action_menu_button"
															});
	
	// action menu
	$("#dialog_user_edit_subaccount_list_grid_action_menu").menu({
		role: 'listbox'
	});
	$("#dialog_user_edit_subaccount_list_grid_action_menu").hide();
	
	$("#dialog_user_edit_subaccount_list_grid_action_menu_button").click(function() {
			$("#dialog_user_edit_subaccount_list_grid_action_menu").toggle().position({
			my: "left bottom",
			at: "right-5 top-5",
			of: this
		});
				
		$(document).one("click", function() {
			$("#dialog_user_edit_subaccount_list_grid_action_menu").hide();
		});
		
		return false;
	});
	
	// define user object list grid
	$("#dialog_user_edit_object_list_grid").jqGrid({
		url:'func/fn_cpanel.users.php',
		datatype: "json",
		colNames:[la['NAME'],la['IMEI'],la['ACTIVE'],la['EXPIRES_ON'],la['LAST_CONNECTION'],la['STATUS'],''],
		colModel:[
			{name:'name',index:'name',width:190},
			{name:'imei',index:'imei',width:160},
			{name:'active',index:'active',width:40,align:"center"},
			{name:'object_expire_dt',index:'object_expire_dt',width:80,align:"center"},
			{name:'dt_server',index:'dt_server',width:143,align:"center"},
			{name:'status',index:'dt_server',width:40,align:"center",sortable: false},
			{name:'modify',index:'modify',width:60,align:"center",sortable: false},
		],
		rowNum:25,
		rowList:[25,50,100,200,300,400,500],
		pager: '#dialog_user_edit_object_list_grid_pager',
		sortname: 'name',
		sortorder: "asc",
		viewrecords: true,
		rownumbers: true,
		height: '443px',
		width: '820',
		shrinkToFit: false,
		multiselect: true,
		beforeSelectRow: function(id, e)
		{
			if (e.target.tagName.toLowerCase() === "input"){return true;}
			return false;
		}
	});
	$("#dialog_user_edit_object_list_grid").jqGrid('navGrid','#dialog_user_edit_object_list_grid_pager',{ 	add:true,
														edit:false,
														del:false,
														search:false,
														addfunc: function (e) {userObjectAdd('open');}																		
														});
	
	$("#dialog_user_edit_object_list_grid").navButtonAdd('#dialog_user_edit_object_list_grid_pager',{	caption: "", 
														title: la['ACTION'],
														buttonicon: 'ui-icon-action',
														onClickButton: function(){}, 
														position:"last",
														id: "dialog_user_edit_object_list_grid_action_menu_button"
														});
	
	// action menu
	$("#dialog_user_edit_object_list_grid_action_menu").menu({
		role: 'listbox'
	});
	$("#dialog_user_edit_object_list_grid_action_menu").hide();
	
	$("#dialog_user_edit_object_list_grid_action_menu_button").click(function() {
			$("#dialog_user_edit_object_list_grid_action_menu").toggle().position({
			my: "left bottom",
			at: "right-5 top-5",
			of: this
		});
				
		$(document).one("click", function() {
			$("#dialog_user_edit_object_list_grid_action_menu").hide();
		});
		
		return false;
	});
	
	// define user billing plan list grid
	if (document.getElementById("dialog_user_edit_billing_plan_list_grid") != undefined)
	{
		$("#dialog_user_edit_billing_plan_list_grid").jqGrid({
			url:'func/fn_cpanel.users.php',
			datatype: "json",
			colNames:[la['TIME'],la['NAME'],la['OBJECTS'],la['PERIOD'],la['PRICE'],''],
			colModel:[
				{name:'dt_purchase',index:'dt_purchase',width:135,align:"center"},
				{name:'name',index:'name',width:228},
				{name:'objects',index:'objects',width:85,align:"center"},
				{name:'period',index:'period',width:105,align:"center"},
				{name:'price',index:'price',width:105,align:"center"},
				{name:'modify',index:'modify',width:60,align:"center",sortable: false},
			],
			rowNum:25,
			rowList:[25,50,100,200,300,400,500],
			pager: '#dialog_user_edit_billing_plan_list_grid_pager',
			sortname: 'dt_purchase',
			sortorder: "desc",
			viewrecords: true,
			rownumbers: true,
			height: '443px',
			width: '820',
			shrinkToFit: false,
			multiselect: true,
			beforeSelectRow: function(id, e)
			{
				if (e.target.tagName.toLowerCase() === "input"){return true;}
				return false;
			}
		});
		$("#dialog_user_edit_billing_plan_list_grid").jqGrid('navGrid','#dialog_user_edit_billing_plan_list_grid_pager',{ 	add:true,
																	edit:false,
																	del:false,
																	search:false,
																	addfunc: function (e) {userBillingPlanAdd('open');}	
																	});
		
		$("#dialog_user_edit_billing_plan_list_grid").navButtonAdd('#dialog_user_edit_billing_plan_list_grid_pager',{	caption: "", 
																title: la['ACTION'],
																buttonicon: 'ui-icon-action',
																onClickButton: function(){}, 
																position:"last",
																id: "dialog_user_edit_billing_plan_list_grid_action_menu_button"
																});
		
		// action menu
		$("#dialog_user_edit_billing_plan_list_grid_action_menu").menu({
			role: 'listbox'
		});
		$("#dialog_user_edit_billing_plan_list_grid_action_menu").hide();
		
		$("#dialog_user_edit_billing_plan_list_grid_action_menu_button").click(function() {
				$("#dialog_user_edit_billing_plan_list_grid_action_menu").toggle().position({
				my: "left bottom",
				at: "right-5 top-5",
				of: this
			});
					
			$(document).one("click", function() {
				$("#dialog_user_edit_billing_plan_list_grid_action_menu").hide();
			});
			
			return false;
		});
	}
	
	// define user usage list grid
	$("#dialog_user_edit_usage_list_grid").jqGrid({
		url:'func/fn_cpanel.users.php',
		datatype: "json",
		colNames:[la['DATE'],la['LOGIN'],la['EMAIL'],la['SMS'],la['API'],''],
		colModel:[
			{name:'dt_usage',index:'dt_usage',width:135,align:"center"},
			{name:'login',index:'login',width:131,align:"center"},
			{name:'email',index:'email',width:131,align:"center"},
			{name:'sms',index:'sms',width:131,align:"center"},
			{name:'api',index:'api',width:131,align:"center"},
			{name:'modify',index:'modify',width:60,align:"center",sortable: false},
		],
		rowNum:25,
		rowList:[25,50,100,200,300,400,500],
		pager: '#dialog_user_edit_usage_list_grid_pager',
		sortname: 'dt_usage',
		sortorder: "desc",
		viewrecords: true,
		rownumbers: true,
		height: '443px',
		width: '820',
		shrinkToFit: false,
		multiselect: true,
		beforeSelectRow: function(id, e)
		{
			if (e.target.tagName.toLowerCase() === "input"){return true;}
			return false;
		}
	});
	$("#dialog_user_edit_usage_list_grid").jqGrid('navGrid','#dialog_user_edit_usage_list_grid_pager',{ 	add:false,
														edit:false,
														del:false,
														search:false,
														});
	
	$("#dialog_user_edit_usage_list_grid").navButtonAdd('#dialog_user_edit_usage_list_grid_pager',{	caption: "", 
													title: la['ACTION'],
													buttonicon: 'ui-icon-action',
													onClickButton: function(){}, 
													position:"last",
													id: "dialog_user_edit_usage_list_grid_action_menu_button"
													});
	
	// action menu
	$("#dialog_user_edit_usage_list_grid_action_menu").menu({
		role: 'listbox'
	});
	$("#dialog_user_edit_usage_list_grid_action_menu").hide();
	
	$("#dialog_user_edit_usage_list_grid_action_menu_button").click(function() {
			$("#dialog_user_edit_usage_list_grid_action_menu").toggle().position({
			my: "left bottom",
			at: "right-5 top-5",
			of: this
		});
				
		$(document).one("click", function() {
			$("#dialog_user_edit_usage_list_grid_action_menu").hide();
		});
		
		return false;
	});
	
	// define theme list grid
	$("#cpanel_manage_server_theme_list_grid").jqGrid({
		datatype: "local",
		colNames:[la['NAME'],la['ACTIVE'],''],
		colModel:[
			{name:'name',index:'name',width:828,fixed:true,align:"left",sortable:true},
			{name:'active',index:'active',width:80,fixed:true,align:"center",sortable: false},
			{name:'modify',index:'modify',width:60,fixed:true,align:"center",sortable: false}
		],
		sortname: '',
		sortorder: '',
		rowNum: 100,
		width: '1000',
		height: '350',
		shrinkToFit: true
	});
	
	// define custom map list grid
	$("#cpanel_manage_server_custom_map_list_grid").jqGrid({
		datatype: "local",
		colNames:[la['NAME'],
			  la['ACTIVE'],
			  la['TYPE'],
			  la['URL'],
			  ''],
		colModel:[
			{name:'name',index:'name',width:230,fixed:true,align:"left",sortable:true},
			{name:'active',index:'active',width:80,fixed:true,align:"center",sortable:true},
			{name:'type',index:'type',width:80,fixed:true,align:"center",sortable:true},
			{name:'url',index:'url',width:513,fixed:true,align:"left",sortable:false},
			{name:'modify',index:'modify',width:60,fixed:true,align:"center",sortable: false}
		],
		sortname: '',
		sortorder: '',
		rowNum: 100,
		width: '1000',
		height: '350',
		shrinkToFit: true
	});
	
	// define billin plan list grid
	$("#cpanel_manage_server_billing_plan_list_grid").jqGrid({
		datatype: "local",
		colNames:[la['NAME'],
			  la['ACTIVE'],
			  la['OBJECTS'],
			  la['PERIOD'],
			  la['PRICE'],
			  ''],
		colModel:[
			{name:'name',index:'name',width:495,fixed:true,align:"left",sortable:true},
			{name:'active',index:'active',width:80,fixed:true,align:"center",sortable:true},
			{name:'objects',index:'objects',width:80,fixed:true,align:"center",sortable:true},
			{name:'period',index:'period',width:120,fixed:true,align:"center",sortable:true},
			{name:'price',index:'price',width:120,fixed:true,align:"center",sortable:true},
			{name:'modify',index:'modify',width:60,fixed:true,align:"center",sortable: false}
		],
		sortname: '',
		sortorder: '',
		rowNum: 100,
		width: '1000',
		height: '350',
		shrinkToFit: true
	});
	
	// define language list grid
	$("#cpanel_manage_server_language_list_grid").jqGrid({
		datatype: "local",
		colNames:[la['LANGUAGE'],la['ACTIVE'],''],
		colModel:[
			{name:'language',index:'language',width:828,fixed:true,align:"left",sortable:true},
			{name:'active',index:'active',width:80,fixed:true,align:"center",sortable: false},
			{name:'modify',index:'modify',width:60,fixed:true,align:"center",sortable: false}
		],
		sortname: '',
		sortorder: '',
		rowNum: 100,
		width: '1000',
		height: '350',
		shrinkToFit: true
	});
	
	// define template list grid
	$("#cpanel_manage_server_template_list_grid").jqGrid({
		datatype: "local",
		colNames:[la['NAME'],
			  ''],
		colModel:[
			{name:'name',index:'name',width:913,fixed:true,align:"left",sortable:true},
			{name:'modify',index:'modify',width:60,fixed:true,align:"center",sortable: false}
		],
		sortname: '',
		sortorder: '',
		rowNum: 100,
		width: '1000',
		height: '350',
		shrinkToFit: true
	});
	
	// define log list grid
	$("#cpanel_manage_server_log_list_grid").jqGrid({
		datatype: "local",
		colNames:[la['NAME'],
			  la['MODIFIED'],
			  la['SIZE_MB'],
			  ''],
		colModel:[
			{name:'name',index:'name',width:603,fixed:true,align:"left",sortable:true},
			{name:'modified',index:'modified',width:150,fixed:true,align:"center",sortable:true},
			{name:'size_mb',index:'size_mb',width:150,fixed:true,align:"center",sortable:true},
			{name:'modify',index:'modify',width:60,fixed:true,align:"center",sortable: false}
		],
		sortname: '',
		sortorder: '',
		rowNum: 100,
		width: '1000',
		height: '350',
		shrinkToFit: true
	});
	
	// hide jqgrid close button
	$(".ui-jqgrid-titlebar-close").hide();
	
	// page selects
	$(".ui-pg-selbox").multipleSelect({single: true, width: '50px'});
}

function loadGridList(list)
{
        switch (list)
	{
		case "themes":
			var data = {
				cmd: 'load_theme_list'
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.server.php",
				data: data,
				dataType: 'json',
				success: function(result)
				{
					var list_id = $("#cpanel_manage_server_theme_list_grid");
					var list_data = [];
					
					list_id.clearGridData(true);
					
					for (var i = 0; i < result.length; i++)
					{
						var theme_id = result[i].theme_id;
						var name = result[i].name;
						var active = result[i].active;
						
						if (active == 'true')
						{
							var active = '<a href="#" onclick="themeDeactivate(\''+theme_id+'\');" title="'+la['DEACTIVATE']+'"><img src="theme/images/tick-green.svg" /></a>';
                                                }
						else
						{
							var active = '<a href="#" onclick="themeActivate(\''+theme_id+'\');" title="'+la['ACTIVATE']+'"><img src="theme/images/remove-red.svg" style="width:12px;" />';
						}
						
						var modify = '<a href="#" onclick="themeProperties(\''+theme_id+'\');" title="'+la['EDIT']+'"><img src="theme/images/edit.svg" /></a>';
						modify += '<a href="#" onclick="themeDelete(\''+theme_id+'\');" title="'+la['DELETE']+'"><img src="theme/images/remove3.svg" /></a>';
						
						list_id.jqGrid('addRowData',i,{name: name, active: active, modify: modify});
					}
					
					list_id.setGridParam({sortname:'name', sortorder: 'asc'}).trigger('reloadGrid');
				}
			});
			break;
		
		case "custom_maps":
			var data = {
				cmd: 'load_custom_map_list'
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.server.php",
				data: data,
				dataType: 'json',
				success: function(result)
				{
					var list_id = $("#cpanel_manage_server_custom_map_list_grid");
					var list_data = [];
					
					list_id.clearGridData(true);
					
					for (var i = 0; i < result.length; i++)
					{
						var map_id = result[i].map_id;
						var name = result[i].name;
						var active = result[i].active;
						var type = result[i].type;
						var url = result[i].url;
						
						if (active == 'true')
						{
							var active = '<a href="#" onclick="customMapDeactivate(\''+map_id+'\');" title="'+la['DEACTIVATE']+'"><img src="theme/images/tick-green.svg" /></a>';
                                                }
						else
						{
							var active = '<a href="#" onclick="customMapActivate(\''+map_id+'\');" title="'+la['ACTIVATE']+'"><img src="theme/images/remove-red.svg" style="width:12px;" />';
						}
						
						var modify = '<a href="#" onclick="customMapProperties(\''+map_id+'\');" title="'+la['EDIT']+'"><img src="theme/images/edit.svg" /></a>';
						modify += '<a href="#" onclick="customMapDelete(\''+map_id+'\');" title="'+la['DELETE']+'"><img src="theme/images/remove3.svg" /></a>';
						
						list_id.jqGrid('addRowData',i,{name: name, active: active, type: type, url: url, modify: modify});
					}
					
					list_id.setGridParam({sortname:'name', sortorder: 'asc'}).trigger('reloadGrid');
				}
			});
			break;
		
		case "billing":
			var data = {
				cmd: 'load_billing_plan_list'
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.server.php",
				data: data,
				dataType: 'json',
				success: function(result)
				{
					var select = document.getElementById('dialog_user_billing_plan_add_plan');
					select.options.length = 0; // clear out existing items
					
					var list_id = $("#cpanel_manage_server_billing_plan_list_grid");
					var list_data = [];
					
					list_id.clearGridData(true);
					
					for (var i = 0; i < result.length; i++)
					{
						var plan_id = result[i].plan_id;
						var name = result[i].name;
						var active = result[i].active;
						var objects = result[i].objects;
						var period = result[i].period;
						var period_type = result[i].period_type;
						var price = result[i].price;
						
						if (period == 1)
						{
							var period_type = la[period_type.slice(0,-1).toUpperCase()];	
						}
						else
						{
							var period_type = la[period_type.toUpperCase()];	
						}
						
						period = period + ' ' + period_type.toLowerCase();
						
						if (active == 'true')
						{
							select.options.add(new Option(name, plan_id));
                                                }
						
						if (active == 'true')
						{
							var active = '<a href="#" onclick="billingPlanDeactivate(\''+plan_id+'\');" title="'+la['DEACTIVATE']+'"><img src="theme/images/tick-green.svg" /></a>';
                                                }
						else
						{
							var active = '<a href="#" onclick="billingPlanActivate(\''+plan_id+'\');" title="'+la['ACTIVATE']+'"><img src="theme/images/remove-red.svg" style="width:12px;" />';
						}
						
						var modify = '<a href="#" onclick="billingPlanProperties(\''+plan_id+'\');" title="'+la['EDIT']+'"><img src="theme/images/edit.svg" /></a>';
						modify += '<a href="#" onclick="billingPlanDelete(\''+plan_id+'\');" title="'+la['DELETE']+'"><img src="theme/images/remove3.svg" /></a>';
						
						list_id.jqGrid('addRowData',i,{name: name, active: active, objects: objects, period: period, price: price, modify: modify});
					}
					
					sortSelectList(select);
					
					select.options.add(new Option(la['CUSTOM'], ""), 0);
					
					list_id.setGridParam({sortname:'name', sortorder: 'asc'}).trigger('reloadGrid');
				}
			});
			break;
		
		case "languages":
			var data = {
				cmd: 'load_language_list'
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.server.php",
				data: data,
				dataType: 'json',
				success: function(result)
				{
					var list_id = $("#cpanel_manage_server_language_list_grid");
					var list_data = [];
					
					list_id.clearGridData(true);
					
					for (var i = 0; i < result.length; i++)
					{
						var language = strUcFirst(result[i].lng);
				
						if (result[i].active == true)
						{
							var active = '<a href="#" onclick="languageDeactivate(\''+result[i].lng+'\');" title="'+la['DEACTIVATE']+'"><img src="theme/images/tick-green.svg" /></a>';
                                                }
						else
						{
							var active = '<a href="#" onclick="languageActivate(\''+result[i].lng+'\');" title="'+la['ACTIVATE']+'"><img src="theme/images/remove-red.svg" style="width:12px;" />';
						}
						
						var modify = '<a href="#" onclick="languageProperties(\''+result[i].lng+'\');" title="'+la['EDIT']+'"><img src="theme/images/edit.svg" /></a>';
						
						list_id.jqGrid('addRowData',i,{language: language, active: active, modify: modify});
					}
					
					list_id.setGridParam({sortname:'language', sortorder: 'asc'}).trigger('reloadGrid');
				}
			});
			
			break;
		
		case "templates":
			var data = {
				cmd: 'load_template_list'
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.server.php",
				data: data,
				dataType: 'json',
				success: function(result)
				{
					var list_id = $("#cpanel_manage_server_template_list_grid");
					var list_data = [];
					
					list_id.clearGridData(true);
					
					for (var i = 0; i < result.length; i++)
					{
						var name = la['TEMPLATE_' + result[i].toUpperCase()];
						
						var modify = '<a href="#" onclick="templateProperties(\''+result[i]+'\');" title="'+la['EDIT']+'"><img src="theme/images/edit.svg" /></a>';
						
						list_id.jqGrid('addRowData',i,{name: name, modify: modify});
					}
					
					list_id.setGridParam({sortname:'name', sortorder: 'asc'}).trigger('reloadGrid');
				}
			});
			
			break;
		case "logs":
			var data = {
				cmd: 'load_log_list'
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.server.php",
				data: data,
				dataType: 'json',
				success: function(result)
				{
					var list_id = $("#cpanel_manage_server_log_list_grid");
					var list_data = [];
					
					list_id.clearGridData(true);
					
					for (var i = 0; i < result.length; i++)
					{
						var name = result[i].name;
						var modified = result[i].modified;
						var size = result[i].size;
						var modify = '<a href="#" onclick="logOpen(\''+name+'\');" title="'+la['OPEN']+'"><img src="theme/images/file.svg" /></a>';
						modify += '<a href="#" onclick="logDelete(\''+name+'\');" title="'+la['DELETE']+'"><img src="theme/images/remove3.svg" /></a>';
						
						list_id.jqGrid('addRowData',i,{name: name, modified: modified, size_mb: size, modify: modify});
					}
					
					list_id.setGridParam({sortname:'name', sortorder: 'asc'}).trigger('reloadGrid');
				}
			});
			
			break;
	}
}

function initSelectList(list)
{
	switch (list)
	{
		case "privileges_list_super_admin":
			var select = document.getElementById('dialog_user_edit_account_privileges');
			select.options.length = 0; // clear out existing items
			
			select.options.add(new Option(la['VIEWER'], 'viewer'));
			select.options.add(new Option(la['USER'], 'user'));
			select.options.add(new Option(la['MANAGER'], 'manager'));
			select.options.add(new Option(la['ADMINISTRATOR'], 'admin'));
			select.options.add(new Option(la['SUPER_ADMINISTRATOR'], 'super_admin'));
		break;
		case "privileges_list_admin":
			var select = document.getElementById('dialog_user_edit_account_privileges');
			select.options.length = 0; // clear out existing items
			
			select.options.add(new Option(la['VIEWER'], 'viewer'));
			select.options.add(new Option(la['USER'], 'user'));
			select.options.add(new Option(la['MANAGER'], 'manager'));
			select.options.add(new Option(la['ADMINISTRATOR'], 'admin'));
		break;
		case "privileges_list_manager":
			var select = document.getElementById('dialog_user_edit_account_privileges');
			select.options.length = 0; // clear out existing items
			
			select.options.add(new Option(la['VIEWER'], 'viewer'));
			select.options.add(new Option(la['USER'], 'user'));
			select.options.add(new Option(la['MANAGER'], 'manager'));
		break;
		case "privileges_list_user":
			var select = document.getElementById('dialog_user_edit_account_privileges');
			select.options.length = 0; // clear out existing items
			
			select.options.add(new Option(la['VIEWER'], 'viewer'));
			select.options.add(new Option(la['USER'], 'user'));
		break;
		case "manager_list":
			if ((cpValues['privileges'] == 'super_admin') || (cpValues['privileges'] == 'admin'))
			{		
				var data = {
					cmd: 'load_manager_list'
				};
				
				$.ajax({
					type: "POST",
					url: "func/fn_cpanel.php",
					data: data,
					dataType: 'json',
					cache: false,
					success: function(result)
					{
						var select = document.getElementById('cpanel_manager_list');
						if (select)
						{
							select.options.length = 0; // clear out existing items
							
							for (var key in result)
							{
								var obj = result[key];
								select.options.add(new Option(obj.username, key));
							}
							
							sortSelectList(select);
							
							select.options.add(new Option(la['ADMINISTRATOR'], 0), 0); 
							
						}
						document.getElementById('cpanel_manager_list').value = cpValues['manager_id'];
						
						var select_temp = document.getElementById('dialog_user_edit_account_manager_id').value;
						var select = document.getElementById('dialog_user_edit_account_manager_id');
						if (select)
						{
							select.options.length = 0; // clear out existing items	

							for (var key in result)
							{
								var obj = result[key];
								select.options.add(new Option(obj.username, key));
							}
							
							sortSelectList(select);
							
							select.options.add(new Option(la['NO_MANAGER'], 0), 0); 
						}
						document.getElementById('dialog_user_edit_account_manager_id').value = select_temp;
						
						var select = document.getElementById('dialog_object_add_manager_id');
						if (select)
						{
							select.options.length = 0; // clear out existing items	

							for (var key in result)
							{
								var obj = result[key];
								select.options.add(new Option(obj.username, key));
							}
							
							sortSelectList(select);
							
							select.options.add(new Option(la['NO_MANAGER'], 0), 0); 
						}
						
						var select = document.getElementById('dialog_object_edit_manager_id');
						if (select)
						{
							select.options.length = 0; // clear out existing items	

							for (var key in result)
							{
								var obj = result[key];
								select.options.add(new Option(obj.username, key));
							}
							
							sortSelectList(select);
							
							select.options.add(new Option(la['NO_MANAGER'], 0), 0); 
						}
					}
				});
			}
		break;
	}
}

function switchCPTab(name)
{
	document.getElementById("top_panel_button_user_list").className = "user-list-btn";
	document.getElementById("top_panel_button_object_list").className = "object-list-btn";
	
	if (document.getElementById("top_panel_button_unused_object_list") != undefined)
	{
		document.getElementById("top_panel_button_unused_object_list").className = "unused-object-list-btn";
	}
	
	if (document.getElementById("top_panel_button_manage_server") != undefined)
	{
		document.getElementById("top_panel_button_manage_server").className = "manage-server-btn";
	}
	
	if (document.getElementById("top_panel_button_billing_plan_list") != undefined)
	{
		document.getElementById("top_panel_button_billing_plan_list").className = "billing-plan-list-btn";
	}
	
	switch (name)
	{
		case "user_list":
			document.getElementById("top_panel_button_user_list").className = "user-list-btn active";

			document.getElementById('cpanel_user_list').style.display = '';
			document.getElementById('cpanel_object_list').style.display = 'none';
			
			if (document.getElementById("top_panel_button_unused_object_list") != undefined)
			{
				document.getElementById('cpanel_unused_object_list').style.display = 'none';
			}
			
			if (document.getElementById("top_panel_button_manage_server") != undefined)
			{
				document.getElementById('cpanel_manage_server').style.display = 'none';
			}
			
			if (document.getElementById("top_panel_button_billing_plan_list") != undefined)
			{
				document.getElementById('cpanel_billing_plan_list').style.display = 'none';
			}
			
			break;
		case "object_list":
			document.getElementById("top_panel_button_object_list").className = "object-list-btn active";
		
			document.getElementById('cpanel_user_list').style.display = 'none';
			document.getElementById('cpanel_object_list').style.display = '';

			if (document.getElementById("top_panel_button_unused_object_list") != undefined)
			{
				document.getElementById('cpanel_unused_object_list').style.display = 'none';
			}
			
			if (document.getElementById("top_panel_button_manage_server") != undefined)
			{
				document.getElementById('cpanel_manage_server').style.display = 'none';
			}
			
			if (document.getElementById("top_panel_button_billing_plan_list") != undefined)
			{
				document.getElementById('cpanel_billing_plan_list').style.display = 'none';
			}
			
			break;
		case "unused_object_list":
			document.getElementById("top_panel_button_unused_object_list").className = "unused-object-list-btn active";
	
			document.getElementById('cpanel_user_list').style.display = 'none';
			document.getElementById('cpanel_object_list').style.display = 'none';
			
			if (document.getElementById("top_panel_button_unused_object_list") != undefined)
			{
				document.getElementById('cpanel_unused_object_list').style.display = '';
			}
			
			if (document.getElementById("top_panel_button_manage_server") != undefined)
			{
				document.getElementById('cpanel_manage_server').style.display = 'none';
			}
			
			if (document.getElementById("top_panel_button_billing_plan_list") != undefined)
			{
				document.getElementById('cpanel_billing_plan_list').style.display = 'none';
			}
			
			break;
		case "billing_plan_list":
			document.getElementById("top_panel_button_billing_plan_list").className = "billing-plan-list-btn active";
	
			document.getElementById('cpanel_user_list').style.display = 'none';
			document.getElementById('cpanel_object_list').style.display = 'none';
			
			if (document.getElementById("top_panel_button_unused_object_list") != undefined)
			{
				document.getElementById('cpanel_unused_object_list').style.display = 'none';
			}
			
			if (document.getElementById("top_panel_button_manage_server") != undefined)
			{
				document.getElementById('cpanel_manage_server').style.display = 'none';
			}
			
			if (document.getElementById("top_panel_button_billing_plan_list") != undefined)
			{
				document.getElementById('cpanel_billing_plan_list').style.display = '';
			}
			
			break;
		case "manage_server":
			document.getElementById("top_panel_button_manage_server").className = "manage-server-btn active";
						
			document.getElementById('cpanel_user_list').style.display = 'none';
			document.getElementById('cpanel_object_list').style.display = 'none';
			
			if (document.getElementById("top_panel_button_unused_object_list") != undefined)
			{
				document.getElementById('cpanel_unused_object_list').style.display = 'none';
			}
			
			if (document.getElementById("top_panel_button_manage_server") != undefined)
			{
				document.getElementById('cpanel_manage_server').style.display = '';
			}
			
			if (document.getElementById("top_panel_button_billing_plan_list") != undefined)
			{
				document.getElementById('cpanel_billing_plan_list').style.display = 'none';
			}
			
			break;
	}
}