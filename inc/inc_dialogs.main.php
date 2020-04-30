<div id="dialog_notify" title="">
	<div class="row">
		<div class="row2">
			<div class="width100 center-middle">
				<span id="dialog_notify_text"></span>
			</div>
		</div>
	</div>
	<center>
		<input class="button" type="button" onclick="$('#dialog_notify').dialog('close');" value="<? echo $la['OK']; ?>" />
	</center>
</div>

<div id="dialog_confirm" title="">
	<div class="row">
		<div class="row2">
			<div class="width100 center-middle">
				<span id="dialog_confirm_text"></span>
			</div>
		</div>
	</div>
	<center>
		<input class="button" type="button" onclick="confirmResponse(true);" value="<? echo $la['YES']; ?>" />&nbsp;
		<input class="button" type="button" onclick="confirmResponse(false);" value="<? echo $la['NO']; ?>" />
	</center>
</div>

<div id="dialog_about" title="<? echo $la['ABOUT']; ?>">
	<div class="row">
		<center><img class="logo" src="<? echo $gsValues['URL_ROOT'].'/img/'.$gsValues['LOGO']; ?>" /></center>
	</div>
	<center><? echo '2010 - '.gmdate("Y").' © '.$gsValues['NAME'].'. '.$la['ALL_RIGHTS_RESERVED']; ?></center>
</div>

<div id="dialog_show_point" title="<? echo $la['SHOW_POINT'];?>">
	<div class="row">
		<div class="row2">
			<div class="width30"><? echo $la['LATITUDE']; ?></div>
			<div class="width70"><input id="dialog_show_point_lat" class="inputbox" type="text" value="" maxlength="15"></div>
		</div>
		<div class="row2">
			<div class="width30"><? echo $la['LONGITUDE']; ?></div>
			<div class="width70"><input id="dialog_show_point_lng" class="inputbox" type="text" maxlength="15"></div>
		</div>
	</div>
	
	<center>
	    <input class="button icon-show icon" type="button" onclick="utilsShowPoint();" value="<? echo $la['SHOW']; ?>" />
	    <input class="button icon-close icon" type="button" onclick="$('#dialog_show_point').dialog('close');" value="<? echo $la['CANCEL']; ?>" />
	</center>
</div>

<div id="dialog_address_search" title="<? echo $la['ADDRESS_SEARCH'];?>">
	<div class="row">
		<div class="row2">
			<div class="width100">
				<input class="inputbox" type='text' id='dialog_address_search_addr' onkeydown="if (event.keyCode == 13) utilsSearchAddress();" maxlength="100"/>
			</div>
		</div>
	</div>
		
	<center>
	    <input class="button icon-search icon" type="button" onclick="utilsSearchAddress();" value="<? echo $la['SEARCH']; ?>" />&nbsp;&nbsp;&nbsp;
	    <input class="button icon-close icon" type="button" onclick="$('#dialog_address_search').dialog('close');" value="<? echo $la['CANCEL']; ?>" />
	</center>
</div>

<div id="dialog_billing" title="<? echo $la['BILLING']; ?>">
	<div class="info">
		<? echo $la['BILLING_ALLOWS_TO_PURCHASE_ADDITIONAL_PLANS']; ?>
	</div>
	
	<table id="billing_plan_list_grid"></table>
	<div id="billing_plan_list_grid_pager"></div>
</div>

<div id="dialog_billing_plan_purchase" title="<? echo $la['PURCHASE_PLAN']; ?>">
	<div id="billing_plan_purchase_list"></div>
</div>

<div id="dialog_billing_plan_use" title="">
	<div class="controls-block width100">
		<div class="block width20">
			<div class="info2">
				<? echo $la['OBJECTS']; ?>:&nbsp;<span id="dialog_billing_plan_use_objects"></span>
			</div>
		</div>
		<div class="block width20">
			<div class="info2">
				<? echo $la['PERIOD']; ?>:&nbsp;<span id="dialog_billing_plan_use_period"></span>
			</div>
		</div>
		<div class="block width30">
			<div class="info2">
				<? echo $la['SELECTED']; ?>:&nbsp;<span id="dialog_billing_plan_use_selected"></span>
			</div>
		</div>
		<div class="block width30">
			<input class="button panel icon-check icon float-right" type="button" onclick="billingPlanUseActivate();" value="<? echo $la['ACTIVATE']; ?>" />
		</div>
	</div>
	
	<div class="info">
		<? echo $la['APPLY_CURRENT_PLAN_TO_BELOW_SELECTED_OBJECTS']; ?>
	</div>
	
	<table id="billing_plan_object_list_grid"></table>
	<div id="billing_plan_object_list_grid_pager"></div>
</div>