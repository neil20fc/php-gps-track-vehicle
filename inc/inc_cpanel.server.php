<div id="dialog_theme_properties" title="<? echo $la['THEME_PROPERTIES'];?>">
	<div class="row">
		<div class="title-block"><? echo $la['THEME']; ?></div>
		<div class="row2">
			<div class="width245"><? echo $la['NAME']; ?></div>
			<div class="width755"><input id="dialog_theme_name" class="inputbox" type="text" value="" maxlength="50"></div>
		</div>
		<div class="row2">
			<div class="width245"><? echo $la['ACTIVE']; ?></div>
			<div class="width755"><input id="dialog_theme_active" type="checkbox"/></div>
		</div>
	</div>
	<div class="row">
		<div class="title-block"><? echo $la['LOGIN']; ?></div>
		<div class="block width50">
			<div class="container">
				<div class="row2">
					<div class="width50">
						<? echo $la['SHOW_LOGO']; ?>
					</div>
					<div class="width25">
						<select id="dialog_theme_login_dialog_logo" class="select width100" />
							<option value="yes"><? echo $la['YES']; ?></option>
							<option value="no"><? echo $la['NO']; ?></option>
						</select>
					</div>
				</div>
				<div class="row2">
					<div class="width50">
						<? echo $la['LOGO_POSITION']; ?>
					</div>
					<div class="width25">
						<select id="dialog_theme_login_dialog_logo_position" class="select width100" />
							<option value="left"><? echo $la['SIDE_LEFT']; ?></option>
							<option value="center"><? echo $la['CENTER']; ?></option>
							<option value="right"><? echo $la['SIDE_RIGHT']; ?></option>
						</select>
					</div>
				</div>					
				<div class="row2">
					<div class="width50">
						<? echo $la['BACKGROUND_COLOR']; ?>
					</div>
					<div class="width25">
						<input class="color inputbox width100" type='text' id='dialog_theme_login_bg_color'/>
					</div>
				</div>
				<div class="row2">
					<div class="width50">
						<? echo $la['DIALOG_BACKGROUND_COLOR']; ?>
					</div>
					<div class="width25">
						<input class="color inputbox width100" type='text' id='dialog_theme_login_dialog_bg_color'/>
					</div>
				</div>
			</div>
		</div>
		<div class="block width50">
			<div class="container last">
				<div class="row2">
					<div class="width50">
						<? echo $la['DIALOG_OPACITY']; ?>
					</div>
					<div class="width25">
						<select id="dialog_theme_login_dialog_opacity" class="select width100" />
							<option value="100">100%</option>
							<option value="90">90%</option>
							<option value="80">80%</option>
							<option value="70">70%</option>
							<option value="60">60%</option>
							<option value="50">50%</option>
						</select>
					</div>
				</div>
				<div class="row2">
					<div class="width50">
						<? echo $la['HORIZONTAL_DIALOG_POSITION']; ?>
					</div>
					<div class="width25">
						<select id="dialog_theme_login_dialog_h_position" class="select width100" />
							<option value="left"><? echo $la['SIDE_LEFT']; ?></option>
							<option value="center"><? echo $la['CENTER']; ?></option>
							<option value="right"><? echo $la['SIDE_RIGHT']; ?></option>
						</select>
					</div>
				</div>
				<div class="row2">
					<div class="width50">
						<? echo $la['VERTICAL_DIALOG_POSITION']; ?>
					</div>
					<div class="width25">
						<select id="dialog_theme_login_dialog_v_position" class="select width100" />
							<option value="top"><? echo $la['TOP']; ?></option>
							<option value="center"><? echo $la['CENTER']; ?></option>
							<option value="bottom"><? echo $la['BOTTOM']; ?></option>
						</select>
					</div>
				</div>				
			</div>
		</div>
		<div class="row2">
			<div class="width245">
				<? echo $la['BOTTOM_TEXT_HTML_COMPATIBLE']; ?>
			</div>
			<div class="width755">
				<textarea class="inputbox" id='dialog_theme_login_dialog_bottom_text' style="height: 60px;"></textarea>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="title-block"><? echo $la['USER_INTERFACE']; ?></div>
		<div class="block width50">
			<div class="container">
				<div class="row2">
					<div class="width50">
						<? echo $la['TOP_PANEL_COLOR']; ?>
					</div>
					<div class="width25">
						<input class="color inputbox width100" type='text' id='dialog_theme_ui_top_panel_color'/>
					</div>
				</div>
				<div class="row2">
					<div class="width50">
						<? echo $la['TOP_PANEL_BORDER_COLOR']; ?>
					</div>
					<div class="width25">
						<input class="color inputbox width100" type='text' id='dialog_theme_ui_top_panel_border_color'/>
					</div>
				</div>
				<div class="row2">
					<div class="width50">
						<? echo $la['TOP_PANEL_SELECTION_COLOR']; ?>
					</div>
					<div class="width25">
						<input class="color inputbox width100" type='text' id='dialog_theme_ui_top_panel_selection_color'/>
					</div>
				</div>
				<div class="row2">
					<div class="width50">
						<? echo $la['DIALOG_TITLEBAR_COLOR']; ?>
					</div>
					<div class="width25">
						<input class="color inputbox width100" type='text' id='dialog_theme_ui_dialog_titlebar_color'/>
					</div>
				</div>
			</div>
		</div>
		<div class="block width50">
			<div class="container last">
				<div class="row2">
					<div class="width50">
						<? echo $la['ACCENT_COLOR'].' 1'; ?>
					</div>
					<div class="width25">
						<input class="color inputbox width100" type='text' id='dialog_theme_ui_accent_color_1'/>
					</div>
				</div>
				<div class="row2">
					<div class="width50">
						<? echo $la['ACCENT_COLOR'].' 2'; ?>
					</div>
					<div class="width25">
						<input class="color inputbox width100" type='text' id='dialog_theme_ui_accent_color_2'/>
					</div>
				</div>
				<div class="row2">
					<div class="width50">
						<? echo $la['ACCENT_COLOR'].' 3'; ?>
					</div>
					<div class="width25">
						<input class="color inputbox width100" type='text' id='dialog_theme_ui_accent_color_3'/>
					</div>
				</div>
				<div class="row2">
					<div class="width50">
						<? echo $la['ACCENT_COLOR'].' 4'; ?>
					</div>
					<div class="width25">
						<input class="color inputbox width100" type='text' id='dialog_theme_ui_accent_color_4'/>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="title-block"><? echo $la['FONTS']; ?></div>
		<div class="block width50">
			<div class="container">
				<div class="row2">
					<div class="width50">
						<? echo $la['FONT_COLOR']; ?>
					</div>
					<div class="width25">
						<input class="color inputbox width100" type='text' id='dialog_theme_ui_font_color'/>
					</div>
				</div>
				<div class="row2">
					<div class="width50">
						<? echo $la['TOP_PANEL_FONT_COLOR']; ?>
					</div>
					<div class="width25">
						<input class="color inputbox width100" type='text' id='dialog_theme_ui_top_panel_font_color'/>
					</div>
				</div>
				<div class="row2">
					<div class="width50">
						<? echo $la['TOP_PANEL_COUNTERS_FONT_COLOR']; ?>
					</div>
					<div class="width25">
						<input class="color inputbox width100" type='text' id='dialog_theme_ui_top_panel_counters_font_color'/>
					</div>
				</div>
			</div>
		</div>
		<div class="block width50">
			<div class="container last">
				<div class="row2">
					<div class="width50">
						<? echo $la['HEADING_FONT_COLOR'].' 1'; ?>
					</div>
					<div class="width25">
						<input class="color inputbox width100" type='text' id='dialog_theme_ui_heading_font_color_1'/>
					</div>
				</div>
				<div class="row2">
					<div class="width50">
						<? echo $la['HEADING_FONT_COLOR'].' 2'; ?>
					</div>
					<div class="width25">
						<input class="color inputbox width100" type='text' id='dialog_theme_ui_heading_font_color_2'/>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<center>
		<input class="button icon-save icon" type="button" onclick="themeProperties('save');" value="<? echo $la['SAVE']; ?>" />&nbsp;
		<input class="button icon-close icon" type="button" onclick="themeProperties('cancel');" value="<? echo $la['CANCEL']; ?>" />
	</center>
</div>

<div id="dialog_custom_map_properties" title="<? echo $la['CUSTOM_MAP_PROPERTIES'];?>">
	<div class="row">
		<div class="title-block"><? echo $la['CUSTOM_MAP']; ?></div>
		<div class="row2">
			<div class="width30"><? echo $la['NAME']; ?></div>
			<div class="width70"><input id="dialog_custom_map_name" class="inputbox" type="text" value="" maxlength="50"></div>
		</div>
		<div class="row2">
			<div class="width30"><? echo $la['ACTIVE']; ?></div>
			<div class="width70"><input id="dialog_custom_map_active" type="checkbox" checked="checked"/></div>
		</div>
		<div class="row2">
			<div class="width30"><? echo $la['TYPE']; ?></div>
			<div class="width15">
				<select id="dialog_custom_map_type" class="select width100">
					<option value="tms">TMS</option>
					<option value="wms">WMS</option>
				</select>
			</div>
		</div>
		<div class="row2">
			<div class="width30"><? echo $la['URL']; ?></div>
			<div class="width70"><input id="dialog_custom_map_url" class="inputbox" type="text" value=""></div>
		</div>
		<div class="row2">
			<div class="width30"><? echo $la['LAYERS']; ?></div>
			<div class="width70"><input id="dialog_custom_map_layers" class="inputbox" type="text" value=""></div>
		</div>
	</div>
	
	<center>
		<input class="button icon-save icon" type="button" onclick="customMapProperties('save');" value="<? echo $la['SAVE']; ?>" />&nbsp;
		<input class="button icon-close icon" type="button" onclick="customMapProperties('cancel');" value="<? echo $la['CANCEL']; ?>" />
	</center>
</div>

<div id="dialog_language_properties" title="<? echo $la['LANGUAGE_PROPERTIES'];?>">
	<div class="row">
		<div class="title-block"><? echo $la['LANGUAGE']; ?></div>
		<div id="dialog_language_editor" style="height: 500px; overflow-y: scroll;"></div>
	</div>
	
	<center>
		<input class="button icon-save icon" type="button" onclick="languageProperties('save');" value="<? echo $la['SAVE']; ?>" />&nbsp;
		<input class="button icon-close icon" type="button" onclick="languageProperties('cancel');" value="<? echo $la['CANCEL']; ?>" />
	</center>
</div>

<div id="dialog_billing_properties" title="<? echo $la['BILLING_PROPERTIES'];?>">
	<div class="row">
		<div class="title-block"><? echo $la['BILLING_PLAN']; ?></div>
		<div class="row2">
			<div class="width35"><? echo $la['NAME']; ?></div>
			<div class="width65"><input id="dialog_billing_name" class="inputbox" type="text" value="" maxlength="50"></div>
		</div>
		<div class="row2">
			<div class="width35"><? echo $la['ACTIVE']; ?></div>
			<div class="width65"><input id="dialog_billing_active" type="checkbox" checked="checked"/></div>
		</div>
		<div class="row2">
			<div class="width35"><? echo $la['NUMBER_OF_OBJECTS']; ?></div>
			<div class="width30"><input id="dialog_billing_objects" onkeypress="return isNumberKey(event);" class="inputbox" type="text" value="" maxlength="10"></div>
		</div>
		<div class="row2">
			<div class="width35"><? echo $la['PERIOD']; ?></div>
			<div class="width30"><input id="dialog_billing_period" onkeypress="return isNumberKey(event);" class="inputbox" type="text" value="" maxlength="10"></div>
			<div class="width5"></div>
			<div class="width30">
				<select id="dialog_billing_period_type" class="select width100">
					<option value="days"><? echo $la['DAYS']; ?></option>
					<option value="months"><? echo $la['MONTHS']; ?></option>
					<option value="years"><? echo $la['YEARS']; ?></option>
				</select>
			</div>
		</div>
		<div class="row2">
			<div class="width35"><? echo $la['PRICE']; ?></div>
			<div class="width30"><input id="dialog_billing_price" onkeypress="return isNumberKey(event);" class="inputbox" type="text" value="" maxlength="10"></div>
		</div>
	</div>
	
	<center>
		<input class="button icon-save icon" type="button" onclick="billingPlanProperties('save');" value="<? echo $la['SAVE']; ?>" />&nbsp;
		<input class="button icon-close icon" type="button" onclick="billingPlanProperties('cancel');" value="<? echo $la['CANCEL']; ?>" />
	</center>
</div>

<div id="dialog_template_properties" title="<? echo $la['TEMPLATE_PROPERTIES'];?>">
	<div class="row">
		<div class="block width60">
			<div class="container">
				<div class="title-block"><? echo $la['TEMPLATE']; ?></div>
				<div class="row2">
					<div class="width30"><? echo $la['NAME']; ?></div>
					<div class="width70"><input id="dialog_template_name" class="inputbox" type="text" value="" maxlength="50" readonly></div>
				</div>
				<div class="row2">
					<div class="width30"><? echo $la['LANGUAGE']; ?>
					</div>
					<div class="width20">
						<select id="dialog_template_language" class="select width100" onChange="templateProperties('load');">
							<? echo getLanguageList(); ?>
						</select>
					</div>
				</div>
				<div class="row2">
					<div class="width30"><? echo $la['SUBJECT']; ?></div>
					<div class="width70"><input id="dialog_template_subject" class="inputbox" maxlength="100"></div>
				</div>
				<div class="row2">
					<div class="width30"><? echo $la['MESSAGE']; ?></div>
					<div class="width70"><textarea id="dialog_template_message" class="inputbox" style="height:255px;" maxlength="2000"></textarea></div>
				</div>
			</div>
		</div>
		<div class="block width40">
			<div class="container last">
				<div class="title-block"><? echo $la['VARIABLES']; ?></div>
				<div class="row2">
					<div id="dialog_template_variables" style="height: 334px; width: 100%; overflow-y: scroll;"></div>
				</div>
			</div>
		</div>
	</div>
	
	<center>
		<input class="button icon-save icon" type="button" onclick="templateProperties('save');" value="<? echo $la['SAVE']; ?>" />&nbsp;
		<input class="button icon-close icon" type="button" onclick="templateProperties('cancel');" value="<? echo $la['CANCEL']; ?>" />
	</center>
</div>

<div id="cpanel_manage_server" style="display:none;">
	<div class="float-left cpanel-title">
		<h1 class="title"><? echo $la['CONTROL_PANEL']; ?> <span> - <? echo $la['MANAGE_SERVER']; ?></span></h1>
	</div>
	<div id="manage_server_tabs" class="clearfix">
		<ul>
			<li class="cp-server"><a href="#manage_server_server"><? echo $la['SERVER']; ?></a></li>
			<li class="cp-branding_ui"><a href="#manage_server_branding_ui"><? echo $la['BRANDING_AND_UI']; ?></a></li>
			<li class="cp-languages"><a href="#manage_server_languages"><? echo $la['LANGUAGES']; ?></a></li>
			<li class="cp-maps"><a href="#manage_server_maps"><? echo $la['MAPS']; ?></a></li>
			<li class="cp-user"><a href="#manage_server_user"><? echo $la['USER']; ?></a></li>
			<li class="cp-templates"><a href="#manage_server_templates"><? echo $la['TEMPLATES']; ?></a></li>
			<li class="cp-email"><a href="#manage_server_email"><? echo $la['EMAIL']; ?></a></li>
			<li class="cp-logs"><a href="#manage_server_logs"><? echo $la['LOGS']; ?></a></li>
			<li class="save-btn"><input class="button panel ms-save icon-save icon" type="button" onclick="serverSave();" value="<? echo $la['SAVE']; ?>"></li>
		</ul>
		<div class="cpanel-tabs-content">
		<div id="manage_server_server">
			<div class="width-1000">
				<div class="row">
					<div class="title-block"><? echo $la['INFORMATION']; ?></div>
					<div class="row2">
						<div class="width50">
							<? echo $la['SERVER_IP']; ?>
						</div>
						<div class="width50">
							<input class="inputbox width100" readOnly="true" value="<? echo $gsValues['SERVER_IP']; ?>"/>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['SERVER_API_KEY']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_api_key" class="inputbox width100" readOnly="true"/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="title-block"><? echo $la['URL_ADDRESSES']; ?></div>
					<div class="row2">
						<div class="width50">
							<? echo $la['LOGIN_DIALOG_URL']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_url_login" class="inputbox width100" placeholder="<? echo $la['HTTP_FULL_ADDRESS_HERE']; ?>"/>
						</div>
					</div>
					<div class="row2" style="display: none;">
						<div class="width50">
							<? echo $la['HELP_PAGE_URL']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_url_help" class="inputbox width100" placeholder="<? echo $la['HTTP_FULL_ADDRESS_HERE']; ?>"/>
						</div>
					</div>
					<div class="row2" style="display: none;">
						<div class="width50">
							<? echo $la['CONTACT_PAGE_URL']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_url_contact" class="inputbox width100" placeholder="<? echo $la['HTTP_FULL_ADDRESS_HERE']; ?>"/>
						</div>
					</div>
					<div class="row2" style="display: none;">
						<div class="width50">
							<? echo $la['SHOP_PAGE_URL']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_url_shop" class="inputbox width100" placeholder="<? echo $la['HTTP_FULL_ADDRESS_HERE']; ?>"/>
						</div>
					</div>
					<div class="row2" style="display: none;">
						<div class="width50">
							<? echo $la['SMS_GATEWAY_APP_URL']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_url_sms_gateway_app" class="inputbox width100" placeholder="<? echo $la['HTTP_FULL_ADDRESS_HERE']; ?>"/>
						</div>
					</div>
				</div>
				<div class="row" style="display: none;">
					<div class="title-block"><? echo $la['OBJECTS']; ?></div>
					<div class="row2">
						<div class="width50">
							<? echo $la['OBJECT_CONNECTION_TIMEOUT_RESETS_CONNECTION_AND_GPS_STATUS']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_connection_timeout" class="select width100">
								<option value="1">1 <? echo $la['UNIT_MIN']; ?></option>
								<option value="2">2 <? echo $la['UNIT_MIN']; ?></option>
								<option value="3">3 <? echo $la['UNIT_MIN']; ?></option>
								<option value="4">4 <? echo $la['UNIT_MIN']; ?></option>
								<option value="5">5 <? echo $la['UNIT_MIN']; ?></option>
								<option value="10">10 <? echo $la['UNIT_MIN']; ?></option>
								<option value="20">20 <? echo $la['UNIT_MIN']; ?></option>
								<option value="30">30 <? echo $la['UNIT_MIN']; ?></option>
								<option value="40">40 <? echo $la['UNIT_MIN']; ?></option>
								<option value="50">50 <? echo $la['UNIT_MIN']; ?></option>
								<option value="60">1 <? echo $la['UNIT_H']; ?></option>
								<option value="120">2 <? echo $la['UNIT_H']; ?></option>
								<option value="180">3 <? echo $la['UNIT_H']; ?></option>
								<option value="240">4 <? echo $la['UNIT_H']; ?></option>
								<option value="300">5 <? echo $la['UNIT_H']; ?></option>
								<option value="1440">24 <? echo $la['UNIT_H']; ?></option>
								<option value="2880">48 <? echo $la['UNIT_H']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2" style="display: none;">
						<div class="width50">
							<? echo $la['KEEP_HISTORY_PERIOD']; ?><br/>
							<? echo $la['WARNING_CHANGING_THIS_VALUE_WILL_AFFECT_EXISTING_DATA']; ?>
						</div>
						<div class="width10">
							<input id="cpanel_manage_server_history_period" class="inputbox width100" onkeypress="return isNumberKey(event);" maxlength="4" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 30" <? if (file_exists('config.hosting.php')) { echo ' disabled'; } ?>/>
						</div>
					</div>
				</div>
				<div class="row" style="display: none;">
					<div class="title-block"><? echo $la['BACKUP']; ?></div>
					<div class="row2">
						<div class="width50"><? echo $la['SEND_DB_BACKUP_TO_EMAIL_AT_SET_UTC_TIME']; ?></div>
						<div class="width10">
							<select id="cpanel_manage_server_backup_time" class="select width100">		
								<? include ("inc/inc_dt.hours_minutes.php"); ?>
							</select>
						</div>
						<div class="width1"></div>
						<div class="width39"><input id="cpanel_manage_server_backup_email" class="inputbox width100" maxlength="50"/></div>
					</div>
				</div>
			</div>
		</div>	
		<div id="manage_server_branding_ui">
			<div class="width-1000">
				<div class="row">
					<div class="title-block"><? echo $la['BRANDING']; ?></div>
					<div class="row2">
						<div class="width50">
							<? echo $la['GPS_SERVER_NAME']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_name" class="inputbox width100" maxlength="50" placeholder="<? echo $la['EX_MY_GPS_SERVER']; ?>" />
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['PAGE_GENERATOR_TAG']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_generator" class="inputbox width100" maxlength="50" placeholder="<? echo $la['EX_MY_GPS_SERVER']; ?>" />
						</div>
					</div>
					<div class="row2" style="display: none;">
						<div class="width50">
							<? echo $la['SHOW_ABOUT_BUTTON']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_about" class="select width100" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="title-block"><? echo $la['IMAGES']; ?></div>
					<div class="row2">
						<div class="width235">
							<div class="ui-img-container">
								<img class="logo" id="cpanel_manage_server_logo" src="<? echo $gsValues['URL_ROOT'].'/img/'.$gsValues['LOGO']; ?>" />
							</div>
						</div>
						<div class="width2"></div>
						<div class="width235">
							<div class="ui-img-container">
								<img class="logo_small" id="cpanel_manage_server_logo_small" src="<? echo $gsValues['URL_ROOT'].'/img/'.$gsValues['LOGO_SMALL']; ?>" />
							</div>
						</div>
						<div class="width2"></div>
						<div class="width235">
							<div class="ui-img-container">
								<?
									$path_favicon = $gsValues['PATH_ROOT'].'favicon.ico';
									
									if (file_exists($path_favicon))
									{
										echo '<img class="favicon" id="cpanel_manage_server_favicon" src="favicon.ico" />';
									}
									else
									{
										echo '<img class="favicon" id="cpanel_manage_server_favicon" src="img/no-image.svg" />';
									}
								?>
							</div>
						</div>
						<div class="width2"></div>
						<div class="width235">
							<div class="ui-img-container">
								<?
									$path_login_background = $gsValues['PATH_ROOT'].'img/login-background.jpg';
									
									if (file_exists($path_login_background))
									{
										echo '<img class="login-background" id="cpanel_manage_server_login_background" src="img/login-background.jpg" />';
									}
									else
									{
										echo '<img class="login-background" id="cpanel_manage_server_login_background" src="img/no-image.svg" />';
									}
								?>
							</div>
						</div>
					</div>
					<div class="row2">
						<div class="width235 center-middle">
							<? echo $la['LOGO_SIZE_FORMAT']; ?>	
						</div>
						<div class="width2"></div>
						<div class="width235 center-middle">
							<? echo $la['LOGO_SMALL_SIZE_FORMAT']; ?>	
						</div>
						<div class="width2"></div>
						<div class="width235 center-middle">
							<? echo $la['FAVICON_SIZE_FORMAT']; ?>	
						</div>
						<div class="width2"></div>
						<div class="width235 center-middle">
							<? echo $la['LOGIN_BACKGROUND_SIZE_FORMAT']; ?>	
						</div>
					</div>
					<div class="row2">
						<div class="width235 center-middle">
							<input class="button" type="button" value="<? echo $la['UPLOAD']; ?>" onclick="uploadLogo();"/>
							<input id="cpanel_manage_server_logo_filename" class="inputbox" style="display: none;"/>
						</div>
						<div class="width2"></div>
						<div class="width235 center-middle">
							<input class="button" type="button" value="<? echo $la['UPLOAD']; ?>" onclick="uploadLogoSmall();"/>
							<input id="cpanel_manage_server_logo_small_filename" class="inputbox" style="display: none;"/>
						</div>
						<div class="width2"></div>
						<div class="width235 center-middle">
							<input class="button" type="button" value="<? echo $la['UPLOAD']; ?>" onclick="uploadFavicon();"/>&nbsp;
							<input class="button" type="button" value="<? echo $la['DELETE']; ?>" onclick="deleteFavicon();"/>
						</div>
						<div class="width2"></div>
						<div class="width235 center-middle">
							<input class="button" type="button" value="<? echo $la['UPLOAD']; ?>" onclick="uploadLoginBackground();"/>&nbsp;
							<input class="button" type="button" value="<? echo $la['DELETE']; ?>" onclick="deleteLoginBackground();"/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="title-block"><? echo $la['THEMES']; ?></div>
					<div class="row2">
						<div class="width100">
							<div class="float-right">
								<a href="#" onclick="loadGridList('themes');">
									<div class="panel-button"  title="<? echo $la['RELOAD']; ?>">
										<img src="theme/images/refresh-color.svg" width="16px" border="0"/>
									</div>
								</a>
								<a href="#" onclick="themeProperties('add');">
									<div class="panel-button"  title="<? echo $la['ADD']; ?>">
										<img src="theme/images/theme.svg" width="16px" border="0"/>
									</div>
								</a>
								<a href="#" onclick="themeDeleteAll();">
									<div class="panel-button"  title="<? echo $la['DELETE_ALL']; ?>">
										<img src="theme/images/remove2.svg" width="16px" border="0"/>
									</div>
								</a>
							</div>
						</div>
					</div>
					<div class="width100">
						<table id="cpanel_manage_server_theme_list_grid"></table>
					</div>
				</div>
			</div>
		</div>
		<div id="manage_server_languages">
			<div class="width-1000">
				<div class="row">
					<div class="title-block"><? echo $la['LANGUAGES']; ?></div>
					<div class="width100">
						<table id="cpanel_manage_server_language_list_grid"></table>
					</div>
				</div>
			</div>
		</div>
		<div id="manage_server_maps">		
			<div class="width-1000">
				<div class="row">
					<div class="title-block"><? echo $la['AVAILABLE_MAPS']; ?></div>
					<div class="row2">
						<div class="width50">
							OSM Map
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_map_osm" class="select width100" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							Bing Maps
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_map_bing" class="select width100" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							Google Maps
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_map_google" class="select width100" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							Google Maps Street View
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_map_google_street_view" class="select width100" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							Google Maps Traffic
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_map_google_traffic" class="select width100" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							Mapbox Maps
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_map_mapbox" class="select width100" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							Yandex Map
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_map_yandex" class="select width100" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="title-block"><? echo $la['GEOCODER']; ?></div>
					<div class="row2">
						<div class="width50">
							<? echo $la['GEOCODER_SERVICE']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_geocoder_service" class="select width100" />
								<option value="bing">Bing</option>
								<option value="google">Google</option>
								<option value="mapbox">Mapbox</option>
								<option value="nominatim">Nominatim</option>
								<option value="pickpoint">PickPoint</option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['USE_GEOCODER_CACHE']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_geocoder_cache" class="select width100" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['CLEAR_GEOCODER_CACHE']; ?>
						</div>
						<div class="width10">
							<input class="button width100" type="button" onclick="geocoderClearCache();" value="<? echo $la['CLEAR']; ?>" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="title-block"><? echo $la['MAP_LICENSE_KEYS']; ?></div>
					<div class="row2">
						<div class="width50">
							<? echo $la['BING_MAPS_KEY']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_map_bing_key" class="inputbox"/>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['GOOGLE_MAPS_KEY']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_map_google_key" class="inputbox"/>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['MAPBOX_MAPS_KEY']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_map_mapbox_key" class="inputbox"/>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['YANDEX_MAPS_KEY']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_map_yandex_key" class="inputbox"/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="title-block"><? echo $la['GEOCODER_LICENSE_KEYS']; ?></div>
					<div class="row2">
						<div class="width50">
							<? echo $la['BING_GEOCODER_KEY']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_geocoder_bing_key" class="inputbox"/>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['GOOGLE_GEOCODER_KEY']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_geocoder_google_key" class="inputbox"/>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['MAPBOX_GEOCODER_KEY']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_geocoder_mapbox_key" class="inputbox"/>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['PICKPOINT_GEOCODER_KEY']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_geocoder_pickpoint_key" class="inputbox"/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="title-block"><? echo $la['MAP_ROUTING']; ?></div>
					<div class="row2">
						<div class="width50">
							<? echo $la['OSMR_SERVICE_URL']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_routing_osmr_service_url" class="inputbox"/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="title-block"><? echo $la['MAP_LAYER_ZOOM_POSITION_AFTER_LOGIN']; ?></div>
					<div class="row2">
						<div class="width50">
							<? echo $la['LAYER']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_map_layer" class="select width100" />
								<option value="osm">OSM Map</option>
								<option value="broad">Bing Road</option>
								<option value="baer">Bing Aerial</option>
								<option value="bhyb">Bing Hybrid</option>
								<option value="gmap">Google Streets</option>
								<option value="gsat">Google Satellite</option>
								<option value="ghyb">Google Hybrid</option>
								<option value="gter">Google Terrain</option>
								<option value="mbmap">Mapbox Streets</option>
								<option value="mbsat">Mapbox Satellite</option>
								<option value="yandex">Yandex</option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['ZOOM']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_map_zoom" class="select width100" />
								<option value="3">3</option>
								<option value="4">4</option>
								<option value="5">5</option>
								<option value="6">6</option>
								<option value="7">7</option>
								<option value="8">8</option>
								<option value="9">9</option>
								<option value="10">10</option>
								<option value="11">11</option>
								<option value="12">12</option>
								<option value="13">13</option>
								<option value="14">14</option>
								<option value="15">15</option>
								<option value="16">16</option>
								<option value="17">17</option>
								<option value="18">18</option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['LATITUDE']; ?>
						</div>
						<div class="width10">
							<input id="cpanel_manage_server_map_lat" class="inputbox width100" onkeypress="return isNumberKey(event);" maxlength="10" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 25.000000"/>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['LONGITUDE']; ?>
						</div>
						<div class="width10">
							<input id="cpanel_manage_server_map_lng" class="inputbox width100" onkeypress="return isNumberKey(event);" maxlength="10" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 0.000000"/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="title-block"><? echo $la['ADDRESS_DISPLAY']; ?></div>
					<div class="row2 text">
						<div class="width100">
							<? echo $la['WARNING_DISPLAY_ADDRESS_ENABLE']; ?>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['OBJECT_DATA_LIST']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_address_display_object_data_list" class="select width100" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['EVENT_DATA_LIST']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_address_display_event_data_list" class="select width100" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['HISTORY_ROUTE_DATA_LIST']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_address_display_history_route_data_list" class="select width100" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="title-block"><? echo $la['CUSTOM_MAPS']; ?></div>
					<div class="row2">
						<div class="width100">
							<div class="float-right">
								<a href="#" onclick="loadGridList('custom_maps');">
									<div class="panel-button"  title="<? echo $la['RELOAD']; ?>">
										<img src="theme/images/refresh-color.svg" width="16px" border="0"/>
									</div>
								</a>
								<a href="#" onclick="customMapProperties('add');">
									<div class="panel-button"  title="<? echo $la['ADD']; ?>">
										<img src="theme/images/map.svg" width="16px" border="0"/>
									</div>
								</a>
								<a href="#" onclick="customMapDeleteAll();">
									<div class="panel-button"  title="<? echo $la['DELETE_ALL']; ?>">
										<img src="theme/images/remove2.svg" width="16px" border="0"/>
									</div>
								</a>
							</div>
						</div>
					</div>
					<div class="width100">
						<table id="cpanel_manage_server_custom_map_list_grid"></table>
					</div>
				</div>
			</div>
		</div>
		<div id="manage_server_user">				
			<div class="width-1000">
				<div class="row">
					<div class="title-block"><? echo $la['LOGIN']; ?></div>
					<div class="row2">
						<div class="width50">
							<? echo $la['PAGE_AFTER_ADMIN_OR_MANAGER_LOGIN']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_page_after_login" class="select width100" />
								<option value="account"><? echo $la['ACCOUNT']; ?></option>
								<option value="cpanel"><? echo $la['CONTROL_PANEL']; ?></option>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="title-block"><? echo $la['CREATE_ACCOUNT']; ?></div>
					<div class="row2">
						<div class="width50">
							<? echo $la['USER_REGISTRATION_VIA_LOGIN_DIALOG']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_allow_registration" class="select width100" onChange="serverCheck();" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2" style="display: none;">
						<div class="width50">
							<? echo $la['EXPIRE_ACCOUNT_DAYS_AFTER_REGISTRATION']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_account_expire" class="select width100" onChange="serverCheck();">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
						<div class="width1"></div>
						<div class="width8">
							<input id="cpanel_manage_server_account_expire_period" class="inputbox width100" onkeypress="return isNumberKey(event);" maxlength="4" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 7"/>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="title-block"><? echo $la['DEFAULTS']; ?></div>
					<div class="row2">
						<div class="width50">
							OSM Map
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_user_map_osm" class="select width100" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							Bing Maps
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_user_map_bing" class="select width100" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							Google Maps
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_user_map_google" class="select width100" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							Google Maps Street View
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_user_map_google_street_view" class="select width100" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							Google Maps Traffic
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_user_map_google_traffic" class="select width100" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							Mapbox Maps
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_user_map_mapbox" class="select width100" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							Yandex Map
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_user_map_yandex" class="select width100" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['LANGUAGE']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_language" class="select width100">
								<? echo getLanguageList(); ?>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50"><? echo $la['UNIT_OF_DISTANCE']; ?></div>
						<div class="width10">
							<select id="cpanel_manage_server_distance_unit" class="select width100">
								<option value="km"><? echo $la['KILOMETER'];?></option>
								<option value="mi"><? echo $la['MILE'];?></option>
								<option value="nm"><? echo $la['NAUTICAL_MILE'];?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50"><? echo $la['UNIT_OF_CAPACITY']; ?></div>
						<div class="width10">
							<select id="cpanel_manage_server_capacity_unit" class="select width100">
								<option value="l"><? echo $la['LITER'];?></option>
								<option value="g"><? echo $la['GALLON'];?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50"><? echo $la['UNIT_OF_TEMPERATURE']; ?></div>
						<div class="width10">
							<select id="cpanel_manage_server_temperature_unit" class="select width100">
								<option value="c"><? echo $la['CELSIUS'];?></option>
								<option value="f"><? echo $la['FAHRENHEIT'];?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50"><? echo $la['CURRENCY']; ?></div>
						<div class="width10">
							<input id="cpanel_manage_server_currency" class="inputbox width100" type="text" value="" maxlength="3">
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['TIMEZONE']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_timezone" class="select width100">
								<? include ("inc/inc_timezones.php"); ?>
							</select>
						</div>
					</div>
					<div class="row2" style="display: none;">
						<div class="width50"><? echo $la['DAYLIGHT_SAVING_TIME']; ?></div>
						<div class="width2">
							<input id="cpanel_manage_server_dst" type="checkbox" class="checkbox" onchange="serverCheck();"/>
						</div>
						<div class="width8">
							<input class="inputbox-calendar-mmdd inputbox width100" id="cpanel_manage_server_dst_start_mmdd" type="text" value=""/>
						</div>
						<div class="width1"></div>
						<div class="width8">
							<select id="cpanel_manage_server_dst_start_hhmm" class="select width100">
								<? include ("inc/inc_dt.hours_minutes.php"); ?>
							</select>
						</div>
						<div class="width2 center-middle">-</div>
						<div class="width8">
							<input class="inputbox-calendar-mmdd inputbox width100" id="cpanel_manage_server_dst_end_mmdd" type="text" value=""/>
						</div>
						<div class="width1"></div>
						<div class="width8">
							<select id="cpanel_manage_server_dst_end_hhmm" class="select width100">
								<? include ("inc/inc_dt.hours_minutes.php"); ?>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['ADD_OBJECTS']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_obj_add" class="select width100" onChange="serverCheck();">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
								<option value="trial"><? echo $la['TRIAL']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['OBJECT_LIMIT']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_obj_limit" class="select width100" onChange="serverCheck();">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
						<div class="width1"></div>
						<div class="width8">
							<input id="cpanel_manage_server_obj_limit_num" onkeypress="return isNumberKey(event);" class="inputbox width100" maxlength="4" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 10"/>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['OBJECT_DATE_LIMIT_DAYS_AFTER_REGISTRATION']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_obj_days" class="select width100" onChange="serverCheck();">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
						<div class="width1"></div>
						<div class="width8">
							<input id="cpanel_manage_server_obj_days_num" onkeypress="return isNumberKey(event);" class="inputbox width100" maxlength="4" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 30"/>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['OBJECT_TRIAL_LIMIT_DAYS']; ?>
						</div>
						<div class="width10">
							<input id="cpanel_manage_server_obj_days_trial" onkeypress="return isNumberKey(event);" class="inputbox width100" maxlength="4" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 7"/>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['EDIT_OBJECTS']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_obj_edit" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['DELETE_OBJECTS']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_obj_delete" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['CLEAR_OBJECTS_HISTORY']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_obj_history_clear" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['HISTORY']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_history" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['REPORTS']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_reports" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2" style="display: none;">
						<div class="width50">
							<? echo $la['TASKS']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_tasks" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2" style="display: none;">
						<div class="width50">
							<? echo $la['RFID_AND_IBUTTON_LOGBOOK']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_rilogbook" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2" style="display: none;">
						<div class="width50">
							<? echo $la['DIAGNOSTIC_TROUBLE_CODES']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_dtc" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['MAINTENANCE']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_maintenance" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['OBJECT_CONTROL']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_object_control" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2" style="display: none;">
						<div class="width50">
							<? echo $la['IMAGE_GALLERY']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_image_gallery" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2" style="display: none;">
						<div class="width50">
							<? echo $la['CHAT']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_chat" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['SUB_ACCOUNTS']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_subaccounts" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2" style="display: none;">
						<div class="width50">
							<? echo $la['SERVER_SMS_GATEWAY']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_sms_gateway_server" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2" style="display: none;">
						<div class="width50">
							<? echo $la['API']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_api" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
				</div>
				<div class="row" style="display: none;">
					<div class="title-block"><? echo $la['NOTIFICATIONS']; ?></div>
					<div class="row2">
						<div class="width50">
							<? echo $la['REMIND_USER_ABOUT_EXPIRING_OBJECTS']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_notify_obj_expire" class="select width100" onChange="serverCheck();">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
						<div class="width1"></div>
						<div class="width8">
							<input id="cpanel_manage_server_notify_obj_expire_period" onkeypress="return isNumberKey(event);" class="inputbox width100" maxlength="4" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 7"/>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['REMIND_USER_ABOUT_EXPIRING_ACCOUNT']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_notify_account_expire" class="select width100" onChange="serverCheck();">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
						<div class="width1"></div>
						<div class="width8">
							<input id="cpanel_manage_server_notify_account_expire_period" onkeypress="return isNumberKey(event);" class="inputbox width100" maxlength="4" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 7"/>
						</div>
					</div>
				</div>
				<div class="row" style="display: none;">
					<div class="title-block"><? echo $la['OTHER']; ?></div>
					<div class="row2">
						<div class="width50">
							<? echo $la['SCHEDULE_REPORTS']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_reports_schedule" class="select width100" />
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['MAX_MARKERS']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_places_markers" onkeypress="return isNumberKey(event);" class="inputbox" style="width: 100px;" maxlength="5" />
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['MAX_ROUTES']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_places_routes" onkeypress="return isNumberKey(event);" class="inputbox" style="width: 100px;" maxlength="5" />
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['MAX_ZONES']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_places_zones" onkeypress="return isNumberKey(event);" class="inputbox" style="width: 100px;" maxlength="5" />
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['MAX_EMAILS_DAILY']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_usage_email_daily" onkeypress="return isNumberKey(event);" class="inputbox" style="width: 100px;" maxlength="8" />
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['MAX_SMS_DAILY']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_usage_sms_daily" onkeypress="return isNumberKey(event);" class="inputbox" style="width: 100px;" maxlength="8" />
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['MAX_API_DAILY']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_usage_api_daily" onkeypress="return isNumberKey(event);" class="inputbox" style="width: 100px;" maxlength="8" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="manage_server_billing" style="display: none;">
			<div class="width-1000">
				<div class="row">
					<div class="title-block"><? echo $la['BILLING']; ?></div>
					<div class="row2">
						<div class="width50">
							<? echo $la['ENABLE_BILLING']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_billing" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['GATEWAY']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_billing_gateway" class="select width100" onChange="serverCheck();">
								<option value="paypal">PayPal</option>
								<option value="custom"><? echo $la['CUSTOM']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['CURRENCY']; ?>
						</div>
						<div class="width10">
							<input id="cpanel_manage_server_billing_currency" class="inputbox width100" maxlength="4" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> EUR"/>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['RECOVER_PLAN_FROM_OBJECT_BACK_TO_BILLING']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_billing_recover_plan" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
				</div>
				<div id="cpanel_manage_server_billing_paypal">
					<div class="row">
						<div class="title-block"><? echo $la['PAYPAL_GATEWAY']; ?></div>
						<div class="row2">
							<div class="width50">
								<? echo $la['PAYPAL_ACCOUNT']; ?>
							</div>
							<div class="width50">
								<input id="cpanel_manage_server_billing_paypal_account" class="inputbox width70" maxlength="50" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> my@email.com"/>
							</div>
						</div>
						<div class="row2">
							<div class="width50">
								<? echo $la['PAYPAL_CUSTOM']; ?>
							</div>
							<div class="width50">
								<input id="cpanel_manage_server_billing_paypal_custom" class="inputbox width70" />
							</div>
						</div>
						<div class="row2">
							<div class="width50">
								<? echo $la['PAYPAL_IPN_URL']; ?>
							</div>
							<div class="width50">
								<input id="cpanel_manage_server_billing_paypal_ipn_url" class="inputbox width70" readOnly="true"/>
							</div>
						</div>
					</div>
				</div>
				<div id="cpanel_manage_server_billing_custom" style="display: none;">
					<div class="row">
						<div class="title-block"><? echo $la['CUSTOM_GATEWAY']; ?></div>
						<div class="row2">
							<div class="width50">
								<? echo $la['CUSTOM_GATEWAY_URL']; ?>
							</div>
							<div class="width50">
								<textarea id="cpanel_manage_server_billing_custom_url" style="height: 75px;" class="inputbox width100" maxlength="2048" placeholder="<? echo $la['EXAMPLE_SHORT'].' '.$la['HTTP_FULL_ADDRESS_HERE']; ?>"/></textarea>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="title-block"><? echo $la['VARIABLES']; ?></div>
						<div class="row"><? echo $la['VAR_BILLING_USER_EMAIL']; ?></div>
						<div class="row"><? echo $la['VAR_BILLING_PLAN_ID']; ?></div>
						<div class="row"><? echo $la['VAR_BILLING_PLAN_NAME']; ?></div>
						<div class="row"><? echo $la['VAR_BILLING_PLAN_PRICE']; ?></div>
						<div class="row"><? echo $la['VAR_BILLING_CURRENCY']; ?></div>
					</div>
				</div>
				<div class="row">
					<div class="title-block"><? echo $la['BILLING_PLANS']; ?></div>
					<div class="row2">
						<div class="width100">
							<div class="float-right">
								<a href="#" onclick="loadGridList('billing');">
									<div class="panel-button"  title="<? echo $la['RELOAD']; ?>">
										<img src="theme/images/refresh-color.svg" width="16px" border="0"/>
									</div>
								</a>
								<a href="#" onclick="billingPlanProperties('add');">
									<div class="panel-button"  title="<? echo $la['ADD']; ?>">
										<img src="theme/images/billing-add.svg" width="16px" border="0"/>
									</div>
								</a>
								<a href="#" onclick="billingPlanDeleteAll();">
									<div class="panel-button"  title="<? echo $la['DELETE_ALL']; ?>">
										<img src="theme/images/remove2.svg" width="16px" border="0"/>
									</div>
								</a>
							</div>
						</div>
					</div>
					<div class="width100">
						<table id="cpanel_manage_server_billing_plan_list_grid"></table>
					</div>
				</div>
			</div>
		</div>
		<div id="manage_server_templates">
			<div class="width-1000">
				<div class="row">
					<div class="title-block"><? echo $la['TEMPLATES']; ?></div>
					<div class="width100">
						<table id="cpanel_manage_server_template_list_grid"></table>
					</div>
				</div>
			</div>
		</div>
		<div id="manage_server_email">				
			<div class="width-1000">
				<div class="row">
					<div class="title-block"><? echo $la['EMAIL_SETTINGS']; ?></div>
					<div class="row2">
						<div class="width50">
							<? echo $la['EMAIL_ADDRESS']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_email" class="inputbox width70" maxlength="50" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> server@email.com"/>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['NO_REPLY_EMAIL_ADDRESS']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_email_no_reply" class="inputbox width70" maxlength="50" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> no_reply@email.com"/>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['SIGNATURE']; ?>
						</div>
						<div class="width50">
							<textarea id="cpanel_manage_server_email_signature" class="inputbox width70" style="height: 50px;" type='text' maxlength="200"></textarea>
						</div>
					</div>	
					<div class="row2">
						<div class="width50">
							<? echo $la['USE_SMTP_SERVER']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_email_smtp" class="select width100" onChange="serverCheck();">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['SMTP_SERVER_HOST']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_email_smtp_host" class="inputbox width70" maxlength="50" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> smtp.gmail.com"/>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['SMTP_SERVER_PORT']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_email_smtp_port" class="inputbox width70" maxlength="4" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> 465"/>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['SMTP_AUTH']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_email_smtp_auth" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['SMTP_SECURITY']; ?>
						</div>
						<div class="width10">
							<select id="cpanel_manage_server_email_smtp_secure" class="select width100">
								<option value=""><? echo $la['NONE']; ?></option>
								<option value="ssl">SSL</option>
								<option value="tls">TLS</option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['SMTP_USERNAME']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_email_smtp_username" class="inputbox width70" maxlength="50" />
						</div>
					</div>
					<div class="row2">
						<div class="width50">
							<? echo $la['SMTP_PASSWORD']; ?>
						</div>
						<div class="width50">
							<input id="cpanel_manage_server_email_smtp_password" type="password" class="inputbox width70" maxlength="50" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="manage_server_sms" style="display: none;">
			<div class="width-1000">
				<div class="row">
					<div class="title-block"><? echo $la['SMS_GATEWAY']; ?></div>
					<div class="row2">
						<div class="width40">
							<? echo $la['ENABLE_SMS_GATEWAY']; ?>
						</div>
						<div class="width21">
							<select id="cpanel_manage_server_sms_gateway" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width40"><? echo $la['SMS_GATEWAY_TYPE']; ?></div>
						<div class="width21">
							<select id="cpanel_manage_server_sms_gateway_type" class="select width100" onchange="serverCheck()">
								<option value="app" selected><? echo $la['MOBILE_APPLICATION'];?></option>
								<option value="http">HTTP</option>
							</select>
						</div>
					</div>
					<div class="row2">
						<div class="width40"><? echo $la['SMS_GATEWAY_NUMBER_FILTER']; ?></div>
						<div class="width60">
							<input class="inputbox" id="cpanel_manage_server_sms_gateway_number_filter" placeholder="<? echo $la['EXAMPLE_SHORT']; ?> +370, +7, +44, +..."/>
						</div>
					</div>
				</div>
				
				<div id="cpanel_manage_server_sms_app">
					<div class="row">
						<div class="title-block"><? echo $la['MOBILE_APPLICATION'];?></div>
						<div class="row"><? echo $la['SMS_GATEWAY_MOBILE_APPLICATION_EXPLANATION']; ?></div>
						<div class="row2">
							<div class="width40"><? echo $la['SMS_GATEWAY_IDENTIFIER']; ?></div>
							<div class="width21">
								<input class="inputbox" id="cpanel_manage_server_sms_gateway_identifier" readonly />
							</div>
						</div>
						<div class="row2">
							<div class="width40"><? echo $la['TOTAL_SMS_IN_QUEUE_TO_SEND']; ?></div>
							<div class="width10" id="cpanel_manage_server_sms_gateway_total_in_queue">0</div>
							<div class="width1"></div>
							<div class="width10">
								<input class="button width100" type="button" onclick="SMSGatewayClearQueue();" value="<? echo $la['CLEAR']; ?>" />
							</div>
						</div>
					</div>
				</div>	
				<div id="cpanel_manage_server_sms_http" style="display: none;">
					<div class="row">
						<div class="title-block">HTTP</div>
						<div class="row"><? echo $la['SMS_GATEWAY_EXPLANATION']; ?></div>
						<div class="row"><? echo $la['SMS_GATEWAY_EXAMPLE']; ?></div>
						<div class="row2">
							<div class="width40"><? echo $la['SMS_GATEWAY_URL']; ?></div>
							<div class="width60">
								<textarea id="cpanel_manage_server_sms_gateway_url" style="height: 75px;" class="inputbox width100" maxlength="2048" placeholder="<? echo $la['EXAMPLE_SHORT'].' '.$la['HTTP_FULL_ADDRESS_HERE']; ?>"/></textarea>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="title-block"><? echo $la['VARIABLES']; ?></div>
						<div class="row"><? echo $la['VAR_SMS_GATEWAY_NUMBER']; ?></div>
						<div class="row"><? echo $la['VAR_SMS_GATEWAY_MESSAGE']; ?></div>
					</div>
				</div>
			</div>
		</div>
		    
		<div id="manage_server_tools" style="display: none;">
			<div class="width-1000">
				<div class="row">
					<div class="title-block"><? echo $la['SERVER_CLEANUP']; ?></div>
					<div class="row2">
						<div class="width40">
							<? echo $la['SERVER_CLEANUP_USERS']; ?>
						</div>
						<div class="width12">
							<? echo $la['LAST_LOGIN_DAYS_AGO']; ?>
						</div>
						<div class="width1"></div>
						<div class="width10">
							<input id="cpanel_manage_server_tools_server_cleanup_users_days" onkeypress="return isNumberKey(event);" class="inputbox width100" maxlength="5" />
						</div>
						<div class="width1"></div>
						<div class="width12">
							<? echo $la['AUTO_EXECUTE']; ?>
						</div>
						<div class="width1"></div>
						<div class="width10">
							<select id="cpanel_manage_server_tools_server_cleanup_users_ae" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
						<div class="width1"></div>
						<div class="width12">
							<input class="button icon-create icon width100" type="button" onclick="serverCleanup('users');" value="<? echo $la['EXECUTE_NOW']; ?>" />
						</div>
					</div>
					<div class="row2">
						<div class="width40">
							<? echo $la['SERVER_CLEANUP_OBJECTS_NOT_ACTIVATED']; ?>
						</div>
						<div class="width12">
							<? echo $la['MORE_THAN_DAYS']; ?>
						</div>
						<div class="width1"></div>
						<div class="width10">
							<input id="cpanel_manage_server_tools_server_cleanup_objects_not_activated_days" onkeypress="return isNumberKey(event);" class="inputbox width100" maxlength="5" />
						</div>
						<div class="width1"></div>
						<div class="width12">
							<? echo $la['AUTO_EXECUTE']; ?>
						</div>
						<div class="width1"></div>
						<div class="width10">
							<select id="cpanel_manage_server_tools_server_cleanup_objects_not_activated_ae" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
						<div class="width1"></div>
						<div class="width12">
							<input class="button icon-create icon width100" type="button" onclick="serverCleanup('objects_not_activated');" value="<? echo $la['EXECUTE_NOW']; ?>" />
						</div>
					</div>
					<div class="row2">
						<div class="width40">
							<? echo $la['SERVER_CLEANUP_OBJECTS_NOT_USED']; ?>
						</div>
						<div class="width12">
						</div>
						<div class="width1"></div>
						<div class="width10">
						</div>
						<div class="width1"></div>
						<div class="width12">
							<? echo $la['AUTO_EXECUTE']; ?>
						</div>
						<div class="width1"></div>
						<div class="width10">
							<select id="cpanel_manage_server_tools_server_cleanup_objects_not_used_ae" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
						<div class="width1"></div>
						<div class="width12">
							<input class="button icon-create icon width100" type="button" onclick="serverCleanup('objects_not_used');" value="<? echo $la['EXECUTE_NOW']; ?>" />
						</div>
					</div>
					<div class="row2">
						<div class="width40">
							<? echo $la['SERVER_CLEANUP_DB_JUNK']; ?>
						</div>
						<div class="width12">
						</div>
						<div class="width1"></div>
						<div class="width10">
						</div>
						<div class="width1"></div>
						<div class="width12">
							<? echo $la['AUTO_EXECUTE']; ?>
						</div>
						<div class="width1"></div>
						<div class="width10">
							<select id="cpanel_manage_server_tools_server_cleanup_db_junk_ae" class="select width100">
								<option value="true"><? echo $la['YES']; ?></option>
								<option value="false"><? echo $la['NO']; ?></option>
							</select>
						</div>
						<div class="width1"></div>
						<div class="width12">
							<input class="button icon-create icon width100" type="button" onclick="serverCleanup('db_junk');" value="<? echo $la['EXECUTE_NOW']; ?>" />
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div id="manage_server_logs">
			<div class="width-1000">
				<div class="row">
					<div class="title-block"><? echo $la['LOG_VIEWER']; ?></div>
					<div class="row2">
						<div class="width100">
							<div class="float-right">
								<a href="#" onclick="loadGridList('logs');">
									<div class="panel-button"  title="<? echo $la['RELOAD']; ?>">
										<img src="theme/images/refresh-color.svg" width="16px" border="0"/>
									</div>
								</a>
								<a href="#" onclick="logDeleteAll();">
									<div class="panel-button"  title="<? echo $la['DELETE_ALL']; ?>">
										<img src="theme/images/remove2.svg" width="16px" border="0"/>
									</div>
								</a>
							</div>
						</div>
					</div>
					<div class="width100">
						<table id="cpanel_manage_server_log_list_grid"></table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>