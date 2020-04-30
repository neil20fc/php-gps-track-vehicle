function billingPlanDeleteSelected()
{
        var billing_plans = $('#cpanel_billing_plan_list_grid').jqGrid ('getGridParam', 'selarrrow');
	
	if (billing_plans == '')
	{
		notifyDialog(la['NO_ITEMS_SELECTED']);
		return;
        }
	
	confirmDialog(la['ARE_YOU_SURE_YOU_WANT_TO_DELETE_SELECTED_ITEMS'], function(response){
		if (response)
		{
			var data = {
				cmd: 'delete_selected_billing_plans',
				ids: billing_plans
			};
			
			$.ajax({
				type: "POST",
				url: "func/fn_cpanel.billing.php",
				data: data,
				success: function(result)
				{
					if (result == 'OK')
					{
						initStats();
						
						$('#cpanel_billing_plan_list_grid').trigger("reloadGrid");	
					}
				}
			});
		}
	});
}