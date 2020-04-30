<div id="dialog_object_add" title="<? echo $la['ADD_OBJECT'] ?>">		
        <div class="row">
                <div class="row2">
                        <div class="width40"><? echo $la['ACTIVE']; ?></div>
                        <div class="width60"><input id="dialog_object_add_active" class="checkbox" type="checkbox" /></div>
                </div>
                <div class="row2">
                        <div class="width40"><? echo $la['NAME']; ?></div>
                        <div class="width60"><input id="dialog_object_add_name" class="inputbox" type="text" value="" maxlength="25"></div>
                </div>
                <div class="row2">
                        <div class="width40"><? echo $la['IMEI']; ?></div>
                        <div class="width60"><input id="dialog_object_add_imei" class="inputbox" type="text" maxlength="15"></div>
                </div>
                <div class="row2">
                        <div class="width40"><? echo $la['TRANSPORT_MODEL']; ?></div>
                        <div class="width60"><input id="dialog_object_add_model" class="inputbox" type="text" value="" maxlength="30"></div>
                </div>
                <div class="row2">
                        <div class="width40"><? echo $la['VIN']; ?></div>
                        <div class="width60"><input id="dialog_object_add_vin" class="inputbox" type="text" maxlength="20"></div>
                </div>
                <div class="row2">
                        <div class="width40"><? echo $la['PLATE_NUMBER']; ?></div>
                        <div class="width60"><input id="dialog_object_add_plate_number" class="inputbox" type="text" maxlength="15"></div>
                </div>
                <div class="row2">
                        <div class="width40"><? echo $la['GPS_DEVICE']; ?></div>
                        <div class="width60"><input id="dialog_object_add_device" class="inputbox" type="text" maxlength="30"></div>
                </div>
                <div class="row2">
                        <div class="width40"><? echo $la['SIM_CARD_NUMBER']; ?></div>
                        <div class="width60"><input id="dialog_object_add_sim_number" class="inputbox" type="text" value="" maxlength="30"></div>
                </div>
                <div class="row2">
                        <div class="width40"><? echo $la['MANAGER']; ?></div>
                        <div class="width60"><select id="dialog_object_add_manager_id" class="select width100"></select></div>
                </div>
                <div class="row2">
                        <div class="width40"><? echo $la['EXPIRE_ON']; ?></div>
                        <div class="width10"><input id="dialog_object_add_object_expire" class="checkbox" type="checkbox" onChange="objectAddCheck();"/></div>
                        <div class="width50"><input class="inputbox-calendar inputbox width100" id="dialog_object_add_object_expire_dt"/></div>
                </div>
                <div class="row2">
                        <div class="width100">
                                <select id="dialog_object_add_users" multiple="multiple" class="width100"></select>
                        </div>
                </div>	
        </div>
        
        <center>
                <input class="button icon-new icon" type="button" onclick="objectAdd('add');" value="<? echo $la['ADD']; ?>" />&nbsp;
                <input class="button icon-close icon" type="button" onclick="objectAdd('cancel');" value="<? echo $la['CANCEL']; ?>" />
        </center>
</div>

<div id="dialog_object_edit" title="<? echo $la['EDIT_OBJECT']; ?>">
        <div class="row">
                <div class="row2">
                        <div class="width40"><? echo $la['ACTIVE']; ?></div>
                        <div class="width60"><input id="dialog_object_edit_active" class="checkbox" type="checkbox" /></div>
                </div>
                <div class="row2">
                        <div class="width40"><? echo $la['NAME']; ?></div>
                        <div class="width60"><input id="dialog_object_edit_name" class="inputbox" type="text" maxlength="25" /></div>
                </div>
                <div class="row2">
                        <div class="width40"><? echo $la['IMEI']; ?></div>
                        <div class="width60"><input id="dialog_object_edit_imei" class="inputbox" type="text" maxlength="15" /></div>
                </div>
                <div class="row2">
                        <div class="width40"><? echo $la['TRANSPORT_MODEL']; ?></div>
                        <div class="width60"><input id="dialog_object_edit_model" class="inputbox" type="text" value="" maxlength="30"></div>
                </div>
                <div class="row2">
                        <div class="width40"><? echo $la['VIN']; ?></div>
                        <div class="width60"><input id="dialog_object_edit_vin" class="inputbox" type="text" maxlength="20"></div>
                </div>
                <div class="row2">
                        <div class="width40"><? echo $la['PLATE_NUMBER']; ?></div>
                        <div class="width60"><input id="dialog_object_edit_plate_number" class="inputbox" type="text" maxlength="20"></div>
                </div>
                <div class="row2">
                        <div class="width40"><? echo $la['GPS_DEVICE']; ?></div>
                        <div class="width60"><input id="dialog_object_edit_device" class="inputbox" type="text" maxlength="30"></select></div>
                </div>
                <div class="row2">
                        <div class="width40"><? echo $la['SIM_CARD_NUMBER']; ?></div>
                        <div class="width60"><input id="dialog_object_edit_sim_number" class="inputbox" type="text" value="" maxlength="30"></div>
                </div>
                <div class="row2">
                        <div class="width40"><? echo $la['MANAGER']; ?></div>
                        <div class="width60"><select id="dialog_object_edit_manager_id" class="select width100"></select></div>
                </div>
                <div class="row2">
                        <div class="width40"><? echo $la['EXPIRE_ON']; ?></div>
                        <div class="width10"><input id="dialog_object_edit_object_expire" class="checkbox" type="checkbox" onChange="objectEditCheck();"/></div>
                        <div class="width50"><input class="inputbox-calendar inputbox width100" id="dialog_object_edit_object_expire_dt"/></div>
                </div>
                <div class="row2">
                        <div class="width100">
                                <select id="dialog_object_edit_users" multiple="multiple" class="width100"></select>
                        </div>
                </div>	
        </div>
        
        <center>
                <input class="button icon-save icon" type="button" onclick="objectEdit('save');" value="<? echo $la['SAVE']; ?>" />&nbsp;
                <input class="button icon-close icon" type="button" onclick="objectEdit('cancel');" value="<? echo $la['CANCEL']; ?>" />
        </center>
</div>