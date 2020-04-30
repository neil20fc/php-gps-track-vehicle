<div id="dialog_cmd" title="<? echo $la['OBJECT_CONTROL'];?>">
	<div id="cmd_tabs">
		<ul>
			<li><a href="#cmd_gprs_tab"><? echo $la['GPRS'];?></a></li>
			<li><a href="#cmd_templates_tab"><? echo $la['TEMPLATES'];?></a></li>
		</ul>
		
		<div id="cmd_gprs_tab">
			<div class="row">
				<div class="block width100">
					<div class="container last">
						<div class="row2">
							<div class="width20"><? echo $la['OBJECT'];?></div>
							<div class="width29"><select id="cmd_gprs_object_list" class="select-multiple-search width100" multiple="multiple" onchange="cmdGPRSTemplateList();"></select></div>
							<div class="width1"></div>
							<div class="width20"><? echo $la['TEMPLATE'];?></div>
							<div class="width30"><select id="cmd_gprs_template_list" class="select width100" onchange="cmdGPRSTemplateSwitch();"></select></div>
						</div>
					</div>
				</div>
				<div class="block width100">
					<div class="container last">
						<div class="row2">
							<div class="width20"><? echo $la['COMMAND'];?></div>
							<div class="width9">
								<select id="cmd_gprs_cmd_type" class="select width100"/>
									<option value="ascii">ASCII</option>
									<option value="hex">HEX</option>
								</select>
							</div>
							<div class="width1"></div>
							<div class="width55">
								<input id="cmd_gprs_cmd" class="inputbox" type="text" value="" maxlength="256">
							</div>
							<div class="width1"></div>
							<div class="width14">
								<input class="button width100" type="button" onclick="cmdGPRSSend();" value="<? echo $la['SEND']; ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
		
			<table id="cmd_gprs_status_list_grid"></table>
			<div id="cmd_gprs_status_list_grid_pager"></div>
		</div>
		
		<div id="cmd_sms_tab" style="display: none;">
			<div class="row">
				<div class="block width100">
					<div class="container last">
						<div class="row2">
							<div class="width20"><? echo $la['OBJECT'];?></div>
							<div class="width29"><select id="cmd_sms_object_list" class="select-multiple-search width100" multiple="multiple" onchange="cmdSMSTemplateList();""></select></div>
							<div class="width1"></div>
							<div class="width20"><? echo $la['TEMPLATE'];?></div>
							<div class="width30"><select id="cmd_sms_template_list" class="select width100" onchange="cmdSMSTemplateSwitch();""></select></div>
						</div>
					</div>
				</div>
				<div class="block width100">
					<div class="container last">
						<div class="row2">
							<div class="width20"><? echo $la['COMMAND'];?></div>						
							<div class="width65">
								<input id="cmd_sms_cmd" class="inputbox" type="text" value="" maxlength="256">
							</div>
							<div class="width1"></div>
							<div class="width14">
								<input class="button width100" type="button" onclick="cmdSMSSend();" value="<? echo $la['SEND']; ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
		
			<table id="cmd_sms_status_list_grid"></table>
			<div id="cmd_sms_status_list_grid_pager"></div>
		</div>
		
		<div id="cmd_schedule_tab" style="display: none;">
			<table id="cmd_schedule_list_grid"></table>
			<div id="cmd_schedule_list_grid_pager"></div>
		</div>
		
		<div id="cmd_templates_tab">
			<table id="cmd_template_list_grid"></table>
			<div id="cmd_template_list_grid_pager"></div>
		</div>
	</div>
</div>

<div id="dialog_cmd_schedule_properties" title="<? echo $la['SCHEDULE_PROPERTIES'];?>">
	<div class="row">
		<div class="block width60">
			<div class="container">
				<div class="title-block"><? echo $la['SCHEDULE']; ?></div>
				<div class="row2">
					<div class="width35"><? echo $la['ACTIVE']; ?></div>
					<div class="width65"><input id="dialog_cmd_schedule_active" type="checkbox" checked="checked"/></div>
				</div>
				<div class="row2">
					<div class="width35"><? echo $la['NAME']; ?></div>
					<div class="width65"><input id="dialog_cmd_schedule_name" class="inputbox" type="text" value="" maxlength="25"></div>
				</div>
				<div class="row2">
					<div class="width35"><? echo $la['PROTOCOL']; ?></div>
					<div class="width65">
						<select id="dialog_cmd_schedule_protocol" class="select-search width100" onchange="cmdScheduleProtocolSwitch();"></select>
					</div>
				</div>
				<div class="row2">
					<div class="width35"><? echo $la['OBJECTS']; ?></div>
					<div class="width65">
						<select id="dialog_cmd_schedule_object_list" class="select-multiple-search width100" multiple="multiple" onchange=""></select>
					</div>
				</div>				
				<div class="row2">
					<div class="width35"><? echo $la['TEMPLATE']; ?></div>
					<div class="width65">
						<select id="dialog_cmd_schedule_template_list" class="select width100" onchange="cmdScheduleTemplateSwitch();"></select>
					</div>
				</div>
				<div class="row2">
					<div class="width35"><? echo $la['GATEWAY']; ?></div>
					<div class="width65">
						<select id="dialog_cmd_schedule_cmd_gateway" class="select width100"/>
							<option value="gprs">GPRS</option>
							<option value="sms">SMS</option>
						</select>
					</div>
				</div>
				<div class="row2">
					<div class="width35"><? echo $la['TYPE']; ?></div>
					<div class="width65">
						<select id="dialog_cmd_schedule_cmd_type" class="select width100"/>
							<option value="ascii">ASCII</option>
							<option value="hex">HEX</option>
						</select>
					</div>
				</div>
				<div class="row2">
					<div class="width35"><? echo $la['COMMAND']; ?></div>
					<div class="width65">
						<input id="dialog_cmd_schedule_cmd_cmd" class="inputbox" type="text" value="" maxlength="256">
					</div>
				</div>
			</div>
		</div>
		<div class="block width40">
			<div class="container last">
				<div class="title-block"><? echo $la['TIME']; ?></div>
				<div class="row2">
					<div class="width35"><? echo $la['EXACT_TIME'];?></div>
					<div class="width10">
						<input id="dialog_cmd_schedule_exact_time" type="checkbox" class="checkbox" onchange="cmdScheduleExactTimeSwitch();"/>
					</div>
					<div class="width30">
						<input readonly class="inputbox-calendar inputbox width100" id="dialog_cmd_schedule_exact_time_date" type="text" value=""/>
					</div>
					<div class="width2"></div>
					<div class="width20">
						<select id="dialog_cmd_schedule_exact_time_time" class="select width100">
							<? include ("inc/inc_dt.hours_minutes.php"); ?>
						</select>
					</div>
				</div>
				<div class="row2">
					<div class="width35"><? echo $la['DAY_MONDAY']; ?></div>
					<div class="width10">
						<input id="dialog_cmd_schedule_daily_mon" type="checkbox" class="checkbox"/>
					</div>
					<div class="width20">
						<select id="dialog_cmd_schedule_daily_mon_time" class="select width100">		
							<? include ("inc/inc_dt.hours_minutes.php"); ?>
						</select>
					</div>
				</div>
				<div class="row2">
					<div class="width35"><? echo $la['DAY_TUESDAY']; ?></div>
					<div class="width10">
						<input id="dialog_cmd_schedule_daily_tue" type="checkbox" class="checkbox"/>
					</div>
					<div class="width20">
						<select id="dialog_cmd_schedule_daily_tue_time" class="select width100">		
							<? include ("inc/inc_dt.hours_minutes.php"); ?>
						</select>
					</div>
				</div>
				<div class="row2">
					<div class="width35"><? echo $la['DAY_WEDNESDAY']; ?></div>
					<div class="width10">
						<input id="dialog_cmd_schedule_daily_wed" type="checkbox" class="checkbox"/>
					</div>
					<div class="width20">
						<select id="dialog_cmd_schedule_daily_wed_time" class="select width100">		
							<? include ("inc/inc_dt.hours_minutes.php"); ?>
						</select>
					</div>
				</div>
				<div class="row2">
					<div class="width35"><? echo $la['DAY_THURSDAY']; ?></div>
					<div class="width10">
						<input id="dialog_cmd_schedule_daily_thu" type="checkbox" class="checkbox"/>
					</div>
					<div class="width20">
						<select id="dialog_cmd_schedule_daily_thu_time" class="select width100">		
							<? include ("inc/inc_dt.hours_minutes.php"); ?>
						</select>
					</div>
				</div>
				<div class="row2">
					<div class="width35"><? echo $la['DAY_FRIDAY']; ?></div>
					<div class="width10">
						<input id="dialog_cmd_schedule_daily_fri" type="checkbox" class="checkbox"/>
					</div>
					<div class="width20">
						<select id="dialog_cmd_schedule_daily_fri_time" class="select width100">		
							<? include ("inc/inc_dt.hours_minutes.php"); ?>
						</select>
					</div>
				</div>
				<div class="row2">
					<div class="width35"><? echo $la['DAY_SATURDAY']; ?></div>
					<div class="width10">
						<input id="dialog_cmd_schedule_daily_sat" type="checkbox" class="checkbox"/>
					</div>
					<div class="width20">
						<select id="dialog_cmd_schedule_daily_sat_time" class="select width100">		
							<? include ("inc/inc_dt.hours_minutes.php"); ?>
						</select>
					</div>
				</div>
				<div class="row2">
					<div class="width35"><? echo $la['DAY_SUNDAY']; ?></div>
					<div class="width10">
						<input id="dialog_cmd_schedule_daily_sun" type="checkbox" class="checkbox"/>
					</div>
					<div class="width20">
						<select id="dialog_cmd_schedule_daily_sun_time" class="select width100">		
							<? include ("inc/inc_dt.hours_minutes.php"); ?>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<center>
		<input class="button icon-save icon" type="button" onclick="cmdScheduleProperties('save');" value="<? echo $la['SAVE']; ?>" />&nbsp;
		<input class="button icon-close icon" type="button" onclick="cmdScheduleProperties('cancel');" value="<? echo $la['CANCEL']; ?>" />
	</center>
</div>

<div id="dialog_cmd_template_properties" title="<? echo $la['COMMAND_PROPERTIES'];?>">
	<div class="row">
		<div class="title-block"><? echo $la['TEMPLATE']; ?></div>
		<div class="row2">
			<div class="width35"><? echo $la['NAME']; ?></div>
			<div class="width65"><input id="dialog_cmd_template_name" class="inputbox" type="text" value="" maxlength="25"></div>
		</div>
		<div class="row2">
			<div class="width35"><? echo $la['HIDE_UNUSED_PROTOCOLS']; ?></div>
			<div class="width65">
				<input id="dialog_cmd_template_hide_unsed_protocols" type="checkbox" class="checkbox" onchange="cmdTemplateProtocolList();"/>
			</div>
		</div>
		<div class="row2">
			<div class="width35">
				<? echo $la['PROTOCOL']; ?>
			</div>
			<div class="width65">
				<select id="dialog_cmd_template_protocol" class="select-search width100"></select>
			</div>
		</div>
		<div class="row2" style="display: none;">
			<div class="width35"><? echo $la['GATEWAY'];?></div>
			<div class="width65">
				<select id="dialog_cmd_template_gateway" class="select width100"/>
					<option value="gprs">GPRS</option>
					<option value="sms">SMS</option>
				</select>
			</div>
		</div>
		<div class="row2">
			<div class="width35"><? echo $la['TYPE'];?></div>
			<div class="width65">
				<select id="dialog_cmd_template_type" class="select width100"/>
					<option value="ascii">ASCII</option>
					<option value="hex">HEX</option>
				</select>
			</div>
		</div>
		<div class="row2">
			<div class="width35"><? echo $la['COMMAND'];?></div>
			<div class="width65">
				<input id="dialog_cmd_template_cmd" class="inputbox" type="text" value="" maxlength="256">
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="block width100">
			<div class="container last">
				<div class="title-block"><? echo $la['VARIABLES']; ?></div>
				<div class="row2">
					<div class="row"><? echo $la['VAR_TEMPLATE_IMEI']; ?></div>
				</div>
			</div>
		</div>
	</div>
	
	<center>
		<input class="button icon-save icon" type="button" onclick="cmdTemplateProperties('save');" value="<? echo $la['SAVE']; ?>" />&nbsp;
		<input class="button icon-close icon" type="button" onclick="cmdTemplateProperties('cancel');" value="<? echo $la['CANCEL']; ?>" />
	</center>
</div>