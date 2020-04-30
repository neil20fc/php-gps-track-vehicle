<div id="dialog_send_email" title="<? echo $la['SEND_EMAIL']; ?>">
        <div class="row">
                <div class="row2">
                        <div class="width20"><? echo $la['SEND_TO']; ?></div>
                        <div class="width80">
                                <select id="send_email_send_to" class="select width100" onchange="sendEmailSendToSwitch('test');">
                                        <option value="all"><? echo $la['ALL_USER_ACCOUNTS']; ?></option>
                                        <option value="selected"><? echo $la['SELECTED_USER_ACCOUNTS']; ?></option>
                                </select>
                        </div>
                </div>
                <div class="row2" id="send_email_username_row">
                        <div class="width20"><? echo $la['USERNAME']; ?></div>
                        <div class="width80"><select id="send_email_username" multiple="multiple" class="width100"></select></div>
                </div>
                <div class="row2">
                        <div class="width20"><? echo $la['SUBJECT']; ?></div>
                        <div class="width80"><input id="send_email_subject" class="inputbox" type="text" value="" maxlength="50"></div>
                </div>
                <div class="row3">
                        <div class="width20"><? echo $la['MESSAGE']; ?></div>
                        <div class="width80"><textarea id="send_email_message" class="inputbox" style="height: 250px;" type='text'></textarea></div>
                </div>
                <div class="row3">
                        <div class="width20"><? echo $la['STATUS']; ?></div>
                        <div class="width80"><div id="send_email_status" style="text-align:center;"></div></div>
                </div>
        </div>
        
        <center>
                <input class="button icon-time icon" type="button" onclick="sendEmail('test');" value="<? echo $la['TEST']; ?>" />&nbsp;
                <input class="button icon-create icon" type="button" onclick="sendEmail('send');" value="<? echo $la['SEND']; ?>" />&nbsp;
                <input class="button icon-close icon" type="button" onclick="sendEmail('cancel');" value="<? echo $la['CANCEL']; ?>" />
        </center>
</div>

<div id="dialog_user_add" title="<? echo $la['ADD_USER']; ?>">
        <div class="row">
                <div class="row2">
                        <div class="width40"><? echo $la['EMAIL']; ?></div>
                        <div class="width60"><input id="dialog_user_add_email" class="inputbox" type="text" maxlength="50"></div>
                </div>
                <div class="row2">
                        <div class="width40"><? echo $la['SEND_CREDENTIALS']; ?></div>
                        <div class="width60"><input id="dialog_user_add_send" type="checkbox" class="checkbox" checked/></div>
                </div>
        </div>
        
        <center>
                <input class="button icon-new icon" type="button" onclick="userAdd('register');" value="<? echo $la['REGISTER']; ?>" />&nbsp;
                <input class="button icon-close icon" type="button" onclick="userAdd('cancel');" value="<? echo $la['CANCEL']; ?>" />
        </center>
</div>

<div id="dialog_user_edit" title="<? echo $la['EDIT_USER']; ?>">
        <div id="dialog_user_edit_tabs">
                <ul>           
                        <li><a href="#dialog_user_edit_account"><? echo $la['ACCOUNT']; ?></a></li>
                        <li><a href="#dialog_user_edit_contact_info"><? echo $la['CONTACT_INFO']; ?></a></li>
                        <li><a href="#dialog_user_edit_subaccounts"><? echo $la['SUB_ACCOUNTS']; ?></a></li>
                        <li><a href="#dialog_user_edit_objects"><? echo $la['OBJECTS']; ?></a></li>
                        <? if ($_SESSION["billing"] == true) { ?>
                        <li><a href="#dialog_user_edit_billing_plans"><? echo $la['BILLING_PLANS']; ?></a></li>
                        <? } ?>
                        <li><a href="#dialog_user_edit_usage"><? echo $la['USAGE']; ?></a></li>
                </ul>
                
                <div id="dialog_user_edit_account">
                        <div class="controls">
                                <input class="button panel icon-save icon" type="button" onclick="userEdit('save');" value="<? echo $la['SAVE']; ?>">
                                <input class="button panel icon-key icon" type="button" onclick="userEditLogin();" value="<? echo $la['LOGIN_AS_USER']; ?>">
                        </div>					
                        <div class="block width40">						
                                <div class="container">
                                        <div class="row">
                                                <div class="title-block"><? echo $la['USER']; ?></div>
                                                <div class="row2">
                                                        <div class="width40"><? echo $la['ACTIVE']; ?></div>
                                                        <div class="width60"><input id="dialog_user_edit_account_active" class="checkbox" type="checkbox" /></div>
                                                </div>
                                                <div class="row2">
                                                        <div class="width40"><? echo $la['USERNAME']; ?></div>
                                                        <div class="width60"><input id="dialog_user_edit_account_username" class="inputbox" maxlength="50" /></div>
                                                </div>
                                                <div class="row2">
                                                        <div class="width40"><? echo $la['EMAIL']; ?></div>
                                                        <div class="width60"><input id="dialog_user_edit_account_email" class="inputbox" maxlength="50" /></div>
                                                </div>
                                                <div class="row2">
                                                        <div class="width40"><? echo $la['PASSWORD']; ?></div>
                                                        <div class="width60"><input id="dialog_user_edit_account_password" class="inputbox" maxlength="20" placeholder="<? echo $la['ENTER_NEW_PASSWORD']; ?>"/></div>
                                                </div>
                                                <div class="row2">
                                                        <div class="width40"><? echo $la['PRIVILEGES']; ?></div>
                                                        <div class="width60"><select id="dialog_user_edit_account_privileges" class="select width100" onChange="userEditCheck();"></select></div>
                                                </div>
                                                <? if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin')) { ?>
                                                <div class="row2">
                                                        <div class="width40"><? echo $la['MANAGER']; ?></div>
                                                        <div class="width60"><select id="dialog_user_edit_account_manager_id" class="select width100" onChange="userEditCheck();"></select></div>
                                                </div>
                                                <? } ?>
                                                <div class="row2">
                                                        <div class="width40">
                                                                <? echo $la['EXPIRE_ON']; ?>
                                                        </div>
                                                        <div class="width10">
                                                                <input id="dialog_user_edit_account_expire" type="checkbox" class="checkbox" onChange="userEditCheck();"/>
                                                        </div>
                                                        <div class="width50">
                                                                <input class="inputbox-calendar inputbox width100" id="dialog_user_edit_account_expire_dt"/>
                                                        </div>
                                                </div>
                                        </div>
                                        <? if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin')) { ?>
                                        <div class="row">
                                                <div class="title-block"><? echo $la['MANAGER_PRIVILEGES']; ?></div>
                                                <div class="row2">
                                                        <div class="width40"><? echo $la['BILLING']; ?></div>
                                                        <div class="width30">
                                                                <select id="dialog_user_edit_account_manager_billing" class="select width100">
                                                                        <option value="true"><? echo $la['YES']; ?></option>
                                                                        <option value="false"><? echo $la['NO']; ?></option>
                                                                </select>
                                                        </div>
                                                </div>
                                        </div>
                                        <? } ?>
                                </div>
                        </div>
                        
                        <div class="block width60">
                                <div class="container last">
                                        <div class="row">
                                                <div class="title-block"><? echo $la['ACCOUNT_PRIVILEGES']; ?></div>
                                                <div style="height: 460px; overflow-y: scroll;">
                                                        <div class="row2">
                                                                <div class="width50">
                                                                        OSM Map
                                                                </div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_map_osm" class="select width100"/>
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50">
                                                                        Bing Maps
                                                                </div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_map_bing" class="select width100"/>
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50">
                                                                        Google Maps
                                                                </div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_map_google" class="select width100"/>
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50">
                                                                        Google Maps Street View
                                                                </div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_map_google_street_view" class="select width100"/>
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50">
                                                                        Google Maps Traffic
                                                                </div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_map_google_traffic" class="select width100"/>
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50">
                                                                        Mapbox Maps
                                                                </div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_map_mapbox" class="select width100"/>
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50">
                                                                        Yandex Map
                                                                </div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_map_yandex" class="select width100"/>
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <? if (($_SESSION["cpanel_privileges"] == 'super_admin') || ($_SESSION["cpanel_privileges"] == 'admin')) { ?>
                                                        <div class="row2">
                                                                <div class="width50"><? echo $la['ADD_OBJECTS']; ?></div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_obj_add" class="select width100" onChange="userEditCheck();">
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                                <option value="trial"><? echo $la['TRIAL']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50"><? echo $la['OBJECT_LIMIT']; ?></div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_obj_limit" class="select width100" onChange="userEditCheck();">
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                                <div class="width2"></div>
                                                                <div class="width20">   
                                                                        <input id="dialog_user_edit_account_obj_limit_num" class="inputbox width100" onkeypress="return isNumberKey(event);" maxlength="4"/>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50"><? echo $la['OBJECT_DATE_LIMIT']; ?></div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_obj_days" class="select width100" onChange="userEditCheck();">
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                                <div class="width2"></div>
                                                                <div class="width20">   
                                                                        <input class="inputbox-calendar inputbox width100" id="dialog_user_edit_account_obj_days_dt"/>
                                                                </div>
                                                        </div>
                                                        <? } ?>
                                                        <div class="row2">
                                                                <div class="width50"><? echo $la['EDIT_OBJECTS']; ?></div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_obj_edit" class="select width100">
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                         <div class="row2">
                                                                <div class="width50"><? echo $la['DELETE_OBJECTS']; ?></div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_obj_delete" class="select width100">
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50"><? echo $la['CLEAR_OBJECTS_HISTORY']; ?></div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_obj_history_clear" class="select width100">
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>																		
                                                        <div class="row2">
                                                                <div class="width50"><? echo $la['HISTORY']; ?></div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_history" class="select width100">
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50"><? echo $la['REPORTS']; ?></div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_reports" class="select width100">
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50"><? echo $la['TASKS']; ?></div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_tasks" class="select width100">
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50"><? echo $la['RFID_AND_IBUTTON_LOGBOOK']; ?></div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_rilogbook" class="select width100">
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50"><? echo $la['DIAGNOSTIC_TROUBLE_CODES']; ?></div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_dtc" class="select width100">
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50"><? echo $la['MAINTENANCE']; ?></div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_maintenance" class="select width100">
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50"><? echo $la['OBJECT_CONTROL']; ?></div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_object_control" class="select width100">
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50"><? echo $la['IMAGE_GALLERY']; ?></div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_image_gallery" class="select width100">
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50"><? echo $la['CHAT']; ?></div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_chat" class="select width100">
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50"><? echo $la['SUB_ACCOUNTS']; ?></div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_subaccounts" class="select width100">
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50"><? echo $la['SERVER_SMS_GATEWAY']; ?></div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_account_sms_gateway_server" class="select width100">
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50"><? echo $la['API']; ?></div>
                                                                <div class="width20">
                                                                        <select id="dialog_user_edit_api_active" class="select width100">
                                                                                <option value="true"><? echo $la['YES']; ?></option>
                                                                                <option value="false"><? echo $la['NO']; ?></option>
                                                                        </select>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50"><? echo $la['API_KEY']; ?></div>
                                                                <div class="width50">
                                                                        <input id="dialog_user_edit_api_key" class="inputbox width100" readOnly="true"/>
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50">
                                                                        <? echo $la['MAX_MARKERS']; ?>
                                                                </div>
                                                                <div class="width20">
                                                                        <input id="dialog_user_edit_places_markers" class="inputbox width100" onkeypress="return isNumberKey(event);" maxlength="5" />
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50">
                                                                        <? echo $la['MAX_ROUTES']; ?>
                                                                </div>
                                                                <div class="width20">
                                                                        <input id="dialog_user_edit_places_routes" class="inputbox width100" onkeypress="return isNumberKey(event);" maxlength="5" />
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50">
                                                                        <? echo $la['MAX_ZONES']; ?>
                                                                </div>
                                                                <div class="width20">
                                                                        <input id="dialog_user_edit_places_zones" class="inputbox width100" onkeypress="return isNumberKey(event);" maxlength="5" />
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50">
                                                                        <? echo $la['MAX_EMAILS_DAILY']; ?>
                                                                </div>
                                                                <div class="width20">
                                                                        <input id="dialog_user_edit_usage_email_daily" class="inputbox width100" onkeypress="return isNumberKey(event);" maxlength="8" />
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50">
                                                                        <? echo $la['MAX_SMS_DAILY']; ?>
                                                                </div>
                                                                <div class="width20">
                                                                        <input id="dialog_user_edit_usage_sms_daily" class="inputbox width100" onkeypress="return isNumberKey(event);" maxlength="8" />
                                                                </div>
                                                        </div>
                                                        <div class="row2">
                                                                <div class="width50">
                                                                        <? echo $la['MAX_API_DAILY']; ?>
                                                                </div>
                                                                <div class="width20">
                                                                        <input id="dialog_user_edit_usage_api_daily" class="inputbox width100" onkeypress="return isNumberKey(event);" maxlength="8" />
                                                                </div>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>
                
                <div id="dialog_user_edit_contact_info">
                        <div class="block width100">	
                                <div class="container last">
                                        <div class="title-block"><? echo $la['CONTACT_INFO']; ?></div>
                                        <div class="row2">
                                                <div class="width40"><? echo $la['NAME_SURNAME']; ?></div>
                                                <div class="width60"><input class="inputbox" id="dialog_user_edit_account_contact_surname"></div>
                                        </div>
                                        <div class="row2">
                                                <div class="width40"><? echo $la['COMPANY']; ?></div>
                                                <div class="width60"><input class="inputbox" id="dialog_user_edit_account_contact_company"></div>
                                        </div>
                                        <div class="row2">
                                                <div class="width40"><? echo $la['ADDRESS']; ?></div>
                                                <div class="width60"><input class="inputbox" id="dialog_user_edit_account_contact_address"></div>
                                        </div>
                                        <div class="row2">
                                                <div class="width40"><? echo $la['POST_CODE']; ?></div>
                                                <div class="width60"><input class="inputbox" id="dialog_user_edit_account_contact_post_code"></div>
                                        </div>
                                        <div class="row2">
                                                <div class="width40"><? echo $la['CITY']; ?></div>
                                                <div class="width60"><input class="inputbox" id="dialog_user_edit_account_contact_city"></div>
                                        </div>
                                        <div class="row2">
                                                <div class="width40"><? echo $la['COUNTRY_STATE']; ?></div>
                                                <div class="width60"><input class="inputbox" id="dialog_user_edit_account_contact_country"></div>
                                        </div>
                                        <div class="row2">
                                                <div class="width40"><? echo $la['PHONE_NUMBER_1']; ?></div>
                                                <div class="width60"><input class="inputbox" id="dialog_user_edit_account_contact_phone1"></div>
                                        </div>
                                        <div class="row2">
                                                <div class="width40"><? echo $la['PHONE_NUMBER_2']; ?></div>
                                                <div class="width60"><input class="inputbox" id="dialog_user_edit_account_contact_phone2"></div>
                                        </div>
                                        <div class="row2">
                                                <div class="width40"><? echo $la['EMAIL']; ?></div>
                                                <div class="width60"><input class="inputbox" id="dialog_user_edit_account_contact_email"></div>
                                        </div>
                                        <div class="row2">
                                                <div class="width40"><? echo $la['COMMENT']; ?></div>
                                                <div class="width60">
                                                        <textarea id="dialog_user_edit_account_comment" class="inputbox" style="height:109px;" maxlength="500" placeholder="<? echo $la['COMMENT_ABOUT_USER']; ?>"></textarea>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>
                
                <div id="dialog_user_edit_subaccounts">
                        <div id="dialog_user_edit_subaccount_list">
                                <table id="dialog_user_edit_subaccount_list_grid"></table>
                                <div id="dialog_user_edit_subaccount_list_grid_pager"></div>
                        </div>
                </div>
                
                <div id="dialog_user_edit_objects">
                        <div id="dialog_user_edit_object_list">
                                <table id="dialog_user_edit_object_list_grid"></table>
                                <div id="dialog_user_edit_object_list_grid_pager"></div>
                        </div>
                </div>
                <? if ($_SESSION["billing"] == true) { ?>
                <div id="dialog_user_edit_billing_plans">
                        <div id="dialog_user_edit_billing_plan_list">
                                <table id="dialog_user_edit_billing_plan_list_grid"></table>
                                <div id="dialog_user_edit_billing_plan_list_grid_pager"></div>
                        </div>
                </div>
                <? } ?>
                <div id="dialog_user_edit_usage">
                        <div id="dialog_user_edit_usage_list">
                                <table id="dialog_user_edit_usage_list_grid"></table>
                                <div id="dialog_user_edit_usage_list_grid_pager"></div>
                        </div>
                </div>
        </div>
</div>

<div id="dialog_user_object_add" title="<? echo $la['ADD_OBJECT'] ?>">
        <div class="row">
                <div class="row2">
                        <div class="width100">
                                <select id="dialog_user_object_add_objects" multiple="multiple" class="width100"></select>
                        </div>
                </div>
        </div>
        <center>
                <input class="button icon-new icon" type="button" onclick="userObjectAdd('add');" value="<? echo $la['ADD']; ?>" />&nbsp;
                <input class="button icon-close icon" type="button" onclick="userObjectAdd('cancel');" value="<? echo $la['CANCEL']; ?>" />
        </center>
</div>

<div id="dialog_user_billing_plan_add" title="<? echo $la['ADD_PLAN'] ?>">
        <div class="row">
                <div class="row2">
                        <div class="width35"><? echo $la['PLAN']; ?></div>
                        <div class="width65">
                                <select id="dialog_user_billing_plan_add_plan" class="select width100" onchange="userBillingPlanAdd('load');"></select>
                        </div>
                </div>
                <div class="row2">
                        <div class="width35"><? echo $la['NAME']; ?></div>
                        <div class="width65"><input id="dialog_user_billing_plan_add_name" class="inputbox" type="text" value="" maxlength="50"></div>
                </div>
                <div class="row2">
                        <div class="width35"><? echo $la['NUMBER_OF_OBJECTS']; ?></div>
                        <div class="width30"><input id="dialog_user_billing_plan_add_objects" onkeypress="return isNumberKey(event);" class="inputbox" type="text" value="" maxlength="10"></div>
                </div>
                <div class="row2">
                        <div class="width35"><? echo $la['PERIOD']; ?></div>
                        <div class="width30"><input id="dialog_user_billing_plan_add_period" onkeypress="return isNumberKey(event);" class="inputbox" type="text" value="" maxlength="10"></div>
                        <div class="width5"></div>
                        <div class="width30">
                                <select id="dialog_user_billing_plan_add_period_type" class="select width100">
                                        <option value="days"><? echo $la['DAYS']; ?></option>
                                        <option value="months"><? echo $la['MONTHS']; ?></option>
                                        <option value="years"><? echo $la['YEARS']; ?></option>
                                </select>
                        </div>
                </div>
                <div class="row2">
                        <div class="width35"><? echo $la['PRICE']; ?></div>
                        <div class="width30"><input id="dialog_user_billing_plan_add_price" onkeypress="return isNumberKey(event);" class="inputbox" type="text" value="" maxlength="10"></div>
                </div>
        </div>
        <center>
                <input class="button icon-new icon" type="button" onclick="userBillingPlanAdd('add');" value="<? echo $la['ADD']; ?>" />&nbsp;
                <input class="button icon-close icon" type="button" onclick="userBillingPlanAdd('cancel');" value="<? echo $la['CANCEL']; ?>" />
        </center>
</div>

<div id="dialog_user_billing_plan_edit" title="<? echo $la['EDIT_PLAN'] ?>">
        <div class="row">
                <div class="row2">
                        <div class="width35"><? echo $la['NAME']; ?></div>
                        <div class="width65"><input id="dialog_user_billing_plan_edit_name" class="inputbox" type="text" value="" maxlength="50"></div>
                </div>
                <div class="row2">
                        <div class="width35"><? echo $la['NUMBER_OF_OBJECTS']; ?></div>
                        <div class="width30"><input id="dialog_user_billing_plan_edit_objects" onkeypress="return isNumberKey(event);" class="inputbox" type="text" value="" maxlength="10"></div>
                </div>
                <div class="row2">
                        <div class="width35"><? echo $la['PERIOD']; ?></div>
                        <div class="width30"><input id="dialog_user_billing_plan_edit_period" onkeypress="return isNumberKey(event);" class="inputbox" type="text" value="" maxlength="10"></div>
                        <div class="width5"></div>
                        <div class="width30">
                                <select id="dialog_user_billing_plan_edit_period_type" class="select width100">
                                        <option value="days"><? echo $la['DAYS']; ?></option>
                                        <option value="months"><? echo $la['MONTHS']; ?></option>
                                        <option value="years"><? echo $la['YEARS']; ?></option>
                                </select>
                        </div>
                </div>
                <div class="row2">
                        <div class="width35"><? echo $la['PRICE']; ?></div>
                        <div class="width30"><input id="dialog_user_billing_plan_edit_price" onkeypress="return isNumberKey(event);" class="inputbox" type="text" value="" maxlength="10"></div>
                </div>
        </div>
        <center>
                <input class="button icon-save icon" type="button" onclick="userBillingPlanEdit('save');" value="<? echo $la['SAVE']; ?>" />&nbsp;
                <input class="button icon-close icon" type="button" onclick="userBillingPlanEdit('cancel');" value="<? echo $la['CANCEL']; ?>" />
        </center>
</div>