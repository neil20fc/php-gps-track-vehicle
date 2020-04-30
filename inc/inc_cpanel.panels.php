	<div id="top_panel">
		<div class="tp-menu left-menu">
			<div class="map-btn">
				<a title="<? echo $la['MAP']; ?>" href="tracking.php">
					<img src="theme/images/globe.svg" />
				</a>
			</div>
			
			<? if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin')) { ?>
			<div class="select-view">
				<select id="cpanel_manager_list" class="select width100" onchange="switchCPManager(this.value);"/></select>
			</div>
			<? } ?>
			<div class="user-list-btn">
				<a title="<? echo $la['USER_LIST']; ?>" class="active" id="top_panel_button_user_list" href="#" onClick="switchCPTab('user_list');">
					<img src="theme/images/user.svg" />
					<span id="user_list_stats"></span>
				</a>
			</div>
			<div class="object-list-btn">
				<a title="<? echo $la['OBJECT_LIST']; ?>" id="top_panel_button_object_list" href="#" onClick="switchCPTab('object_list');">
					<img src="theme/images/marker.svg" />
					<span id="object_list_stats"></span>
				</a>
			</div>
			<? if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin')) { ?>
			<div class="unused-object-list-btn">
				<a title="<? echo $la['UNUSED_OBJECT_LIST']; ?>" id="top_panel_button_unused_object_list" href="#" onClick="switchCPTab('unused_object_list');">
				<img src="theme/images/marker-crossed.svg" />
				<span id="unused_object_list_stats"></span>
				</a>
			</div>
			<? } ?>
			<? if ($_SESSION["billing"] == true) { ?>
			<div class="billing-plan-list-btn" >
				<a title="<? echo $la['BILLING_PLAN_LIST']; ?>"id="top_panel_button_billing_plan_list" href="#" onClick="switchCPTab('billing_plan_list');">
				<img src="theme/images/billing.svg" />
				<span id="billing_plan_list_stats"></span>
				</a>
			</div>
			<? } ?>
			<? if ($_SESSION["cpanel_privileges"] == 'super_admin') { ?>
			<div class="manage-server-btn">
				<a title="<? echo $la['MANAGE_SERVER']; ?>" id="top_panel_button_manage_server" href="#" onClick="switchCPTab('manage_server');">
					<img src="theme/images/settings.svg" />
				</a>
			</div>
			<? } ?>
		</div>
		
		<div class="tp-menu right-menu">
			<div class="select-language"><select id="system_language" class="select" onChange="switchLanguageCPanel();"><? echo getLanguageList(); ?></select></div>
			<div class="user-btn">
				<a  href="#" onclick="userEdit('<? echo $_SESSION["user_id"]; ?>');" title="<? echo $la['MY_ACCOUNT']; ?>">
					<img src="theme/images/user.svg" border="0"/>
					<span><? echo $_SESSION["username"];?></span>
				</a>
			</div>
			<div class="logout-btn">
				<a title="<? echo $la['LOGOUT']; ?>" href="#" onclick="connectLogout();">
					<img src="theme/images/logout.svg" />
				</a>
			</div>
		</div>
	</div>
	
	<div id="cpanel_user_list">
		<div class="float-left cpanel-title">
			<div class="version">v<? echo $gsValues['VERSION']; ?></div>
			<h1 class="title"><? echo $la['CONTROL_PANEL']; ?> <span> - <? echo $la['USER_LIST']; ?></span></h1>
		</div>
		<table id="cpanel_user_list_grid"></table>
		<div id="cpanel_user_list_grid_pager"></div>
	</div>
	
	<div id="cpanel_object_list" style="display:none;">
		<div class="float-left cpanel-title">
			<div class="version">v<? echo $gsValues['VERSION']; ?></div>
			<h1 class="title"><? echo $la['CONTROL_PANEL']; ?> <span> - <? echo $la['OBJECT_LIST']; ?></span></h1>
		</div>	
		<table id="cpanel_object_list_grid"></table>
		<div id="cpanel_object_list_grid_pager"></div>
	</div>
	
	<? if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin')) { ?>
	<div id="cpanel_unused_object_list" style="display:none;">
		<div class="float-left cpanel-title">
			<div class="version">v<? echo $gsValues['VERSION']; ?></div>
			<h1 class="title"><? echo $la['CONTROL_PANEL']; ?> <span> - <? echo $la['UNUSED_OBJECT_LIST']; ?></span></h1>
		</div>	
		<table id="cpanel_unused_object_list_grid"></table>
		<div id="cpanel_unused_object_list_grid_pager"></div>
	</div>
	<? } ?>
	
	<? if ($_SESSION["billing"] == true) {?>
	<div id="cpanel_billing_plan_list" style="display:none;">
		<div class="float-left cpanel-title">
			<div class="version">v<? echo $gsValues['VERSION']; ?></div>
			<h1 class="title"><? echo $la['CONTROL_PANEL']; ?> <span> - <? echo $la['BILLING_PLAN_LIST']; ?></span></h1>
		</div>	
		<table id="cpanel_billing_plan_list_grid"></table>
		<div id="cpanel_billing_plan_list_grid_pager"></div>
	</div>
	<? } ?>