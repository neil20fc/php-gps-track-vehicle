<div id="dialog_task_properties" title="<? echo $la['TASK_PROPERTIES']; ?>">
        <div class="row">	
		<div class="title-block"><? echo $la['TASK'];?></div>
                <div class="report-block block width50">
                        <div class="container">
                                <div class="row2">
					<div class="width30"><? echo $la['NAME'];?></div>
					<div class="width70"><input id="dialog_task_name" class="inputbox" type="text" value="" maxlength="30"></div>
				</div>
                                <div class="row2">
					<div class="width30"><? echo $la['OBJECT']; ?></div>
					<div class="width70"><select id="dialog_task_object_list" class="select-search width100"></select></div>
				</div>
                                <div class="row2">
                                        <div class="width30"><? echo $la['PRIORITY']; ?></div>
					<div class="width70">
						<select id="dialog_task_priority" class="select width100"/>
							<option value="low"><? echo $la['LOW'];?></option>
							<option value="normal"><? echo $la['NORMAL'];?></option>
							<option value="high"><? echo $la['HIGH'];?></option>
						</select>
					</div>
                                </div>
				<div class="row2">
                                        <div class="width30"><? echo $la['STATUS']; ?></div>
					<div class="width70">
						<select id="dialog_task_status" class="select width100"/>
							<option value="0"><? echo $la['NEW'];?></option>
							<option value="1"><? echo $la['IN_PROGRESS'];?></option>
							<option value="2"><? echo $la['COMPLETED'];?></option>
							<option value="3"><? echo $la['FAILED'];?></option>
						</select>
					</div>
                                </div>
                        </div>
                </div>
                <div class="report-block block width50">
			<div class="container last">
				<div class="row2">
                                        <div class="width30"><? echo $la['DESCRIPTION']; ?></div>
                                        <div class="width70"><textarea id="dialog_task_desc" class="inputbox" style="height:105px;" maxlength="500"></textarea></div>
                                </div>
                        </div>
                </div>
        </div>        
        <div class="row">	
                <div class="report-block block width50">
			<div class="container">
                                <div class="title-block"><? echo $la['START'];?></div>
				<div class="row2">
					<div class="width30"><? echo $la['ADDRESS'];?></div>
                                        <div class="width60"><input id="dialog_task_start_address" class="inputbox width100" type="text"/></div>
					<div class="width1"></div>
					<div class="width9">
						<input style="min-width: 0;" class="width100 button icon-marker no-text icon" type="button" onclick="tasksPickAddress('start');" />
					</div>
                                </div>
				<div class="row2">
					<input id="dialog_task_start_lat" class="inputbox" style="display: none;"/>
					<input id="dialog_task_start_lng" class="inputbox" style="display: none;"/>
				</div>
                                <div class="row2">
					<div class="width30"><? echo $la['FROM'];?></div>
                                        <div class="width21">
                                                <input readonly class="inputbox-calendar inputbox width100" id="dialog_task_start_from_date" type="text" value=""/>
                                        </div>
                                        <div class="width1"></div>
                                        <div class="width15">
                                                <select id="dialog_task_start_from_time" class="select width100">
                                                        <? include ("inc/inc_dt.hours_minutes.php"); ?>
                                                </select>
                                        </div>
                                </div>
                                <div class="row2">
					<div class="width30"><? echo $la['TO'];?></div>
                                        <div class="width21">
                                                <input readonly class="inputbox-calendar inputbox width100" id="dialog_task_start_to_date" type="text" value=""/>
                                        </div>
                                        <div class="width1"></div>
                                        <div class="width15">
                                                <select id="dialog_task_start_to_time" class="select width100">
                                                        <? include ("inc/inc_dt.hours_minutes.php"); ?>
                                                </select>
                                        </div>
                                </div>
                        </div>
                </div>
                
                <div class="report-block block width50">
			<div class="container last">
                                <div class="title-block"><? echo $la['DESTINATION'];?></div>
				<div class="row2">
					<div class="width30"><? echo $la['ADDRESS']; ?></div>
                                        <div class="width60"><input id="dialog_task_end_address" class="inputbox width100" type="text"/></div>
					<div class="width1"></div>
					<div class="width9">
						<input style="min-width: 0;" class="width100 button icon-marker no-text icon" type="button" onclick="tasksPickAddress('end');" />
					</div>
                                </div>
				<div class="row2">
					<input id="dialog_task_end_lat" class="inputbox" style="display: none;"/>
					<input id="dialog_task_end_lng" class="inputbox" style="display: none;"/>
				</div>
                                <div class="row2">
					<div class="width30"><? echo $la['FROM'];?></div>
                                        <div class="width21">
                                                <input readonly class="inputbox-calendar inputbox width100" id="dialog_task_end_from_date" type="text" value=""/>
                                        </div>
                                        <div class="width1"></div>
                                        <div class="width15">
                                                <select id="dialog_task_end_from_time" class="select width100">
                                                        <? include ("inc/inc_dt.hours_minutes.php"); ?>
                                                </select>
                                        </div>
                                </div>
                                <div class="row2">
					<div class="width30"><? echo $la['TO'];?></div>
                                        <div class="width21">
                                                <input readonly class="inputbox-calendar inputbox width100" id="dialog_task_end_to_date" type="text" value=""/>
                                        </div>
                                        <div class="width1"></div>
                                        <div class="width15">
                                                <select id="dialog_task_end_to_time" class="select width100">
                                                        <? include ("inc/inc_dt.hours_minutes.php"); ?>
                                                </select>
                                        </div>
                                </div>
                        </div>
                </div>
        </div>
        
        <center>
		<input class="button icon-save icon" type="button" onclick="taskProperties('save');" value="<? echo $la['SAVE']; ?>" />&nbsp;
		<input class="button icon-close icon" type="button" onclick="taskProperties('cancel');" value="<? echo $la['CANCEL']; ?>" />
	</center>
</div>

<div id="dialog_tasks" title="<? echo $la['TASKS']; ?>">
	<div class="controls-block width100">
		<input style="width: 100px;" class="button panel float-right" type="button" value="<? echo $la['SHOW']; ?>" onclick="tasksShow();"/>
		<input style="width: 100px; margin-right: 3px;" class="button panel float-right" type="button" value="<? echo $la['EXPORT_CSV']; ?>" onclick="tasksExportCSV();"/>
		<input style="width: 100px; margin-right: 3px;" class="button panel float-right" type="button" value="<? echo $la['DELETE_ALL']; ?>" onclick="tasksDeleteAll();"/>
	</div>
        
        <div class="row">
		<div class="block width33">
			<div class="container">
				<div class="row2">
					<div class="width30"><? echo $la['OBJECT']; ?></div>
					<div class="width70"><select id="dialog_tasks_object_list" class="select-search width100"></select></div>
				</div>
				<div class="row2">
					<div class="width30"><? echo $la['FILTER'];?></div>
					<div class="width70">
						<select id="dialog_tasks_filter" class="select width100" onchange="switchDateFilter('tasks');">
							<option value="0" selected><? echo $la['WHOLE_PERIOD'];?></option>
							<option value="1"><? echo $la['LAST_HOUR'];?></option>
							<option value="2"><? echo $la['TODAY'];?></option>
							<option value="3"><? echo $la['YESTERDAY'];?></option>
							<option value="4"><? echo $la['BEFORE_2_DAYS'];?></option>
							<option value="5"><? echo $la['BEFORE_3_DAYS'];?></option>
							<option value="6"><? echo $la['THIS_WEEK'];?></option>
							<option value="7"><? echo $la['LAST_WEEK'];?></option>
							<option value="8"><? echo $la['THIS_MONTH'];?></option>
							<option value="9"><? echo $la['LAST_MONTH'];?></option>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="block width33">
			<div class="container">
				<div class="row2">
					<div class="width35"><? echo $la['TIME_FROM']; ?></div>
					<div class="width31">
						<input readonly class="inputbox-calendar inputbox width100" id="dialog_tasks_date_from" type="text" value=""/>
					</div>
					<div class="width2"></div>
					<div class="width15">
						<select id="dialog_tasks_hour_from" class="select width100">
						<? include ("inc/inc_dt.hours.php"); ?>
						</select>
					</div>
					<div class="width2"></div>
					<div class="width15">
						<select id="dialog_tasks_minute_from" class="select width100">
						<? include ("inc/inc_dt.minutes.php"); ?>
						</select>
					</div>
				</div>
				<div class="row2">
					<div class="width35"><? echo $la['TIME_TO']; ?></div>
					<div class="width31">
						<input readonly class="inputbox-calendar inputbox width100" id="dialog_tasks_date_to" type="text" value=""/>
					</div>
					<div class="width2"></div>
					<div class="width15">
						<select id="dialog_tasks_hour_to" class="select width100">
						<? include ("inc/inc_dt.hours.php"); ?>
						</select>
					</div>
					<div class="width2"></div>
					<div class="width15">
						<select id="dialog_tasks_minute_to" class="select width100">
						<? include ("inc/inc_dt.minutes.php"); ?>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
	
        <table id="tasks_task_grid"></table>
	<div id="tasks_task_grid_pager"></div>
</div>