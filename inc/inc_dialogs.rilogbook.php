<div id="dialog_rilogbook" title="<? echo $la['RFID_AND_IBUTTON_LOGBOOK']; ?>">
	<div class="controls-block width100">
		<input style="width: 100px;" class="button panel float-right" type="button" value="<? echo $la['SHOW']; ?>" onclick="rilogbookShow();"/>
		<input style="width: 100px; margin-right: 3px;" class="button panel float-right" type="button" value="<? echo $la['EXPORT_CSV']; ?>" onclick="rilogbookExportCSV();"/>
		<input style="width: 100px; margin-right: 3px;" class="button panel float-right" type="button" value="<? echo $la['DELETE_ALL']; ?>" onclick="rilogbookDeleteAll();"/>
	</div>
	
	<div class="row">
		<div class="block width33">
			<div class="container">
				<div class="row2">
					<div class="width30"><? echo $la['OBJECT']; ?></div>
					<div class="width70"><select id="dialog_rilogbook_object_list" class="select-search width100"></select></div>
				</div>
				<div class="row2">
					<div class="width30"><? echo $la['FILTER'];?></div>
					<div class="width70">
						<select id="dialog_rilogbook_filter" class="select width100" onchange="switchDateFilter('rilogbook');">
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
						<input readonly class="inputbox-calendar inputbox width100" id="dialog_rilogbook_date_from" type="text" value=""/>
					</div>
					<div class="width2"></div>
					<div class="width15">
						<select id="dialog_rilogbook_hour_from" class="select width100">
						<? include ("inc/inc_dt.hours.php"); ?>
						</select>
					</div>
					<div class="width2"></div>
					<div class="width15">
						<select id="dialog_rilogbook_minute_from" class="select width100" >
						<? include ("inc/inc_dt.minutes.php"); ?>
						</select>
					</div>
				</div>
				<div class="row2">
					<div class="width35"><? echo $la['TIME_TO']; ?></div>
					<div class="width31">
						<input readonly class="inputbox-calendar inputbox width100" id="dialog_rilogbook_date_to" type="text" value=""/>
					</div>
					<div class="width2"></div>
					<div class="width15">
						<select id="dialog_rilogbook_hour_to" class="select width100">
						<? include ("inc/inc_dt.hours.php"); ?>
						</select>
					</div>
					<div class="width2"></div>
					<div class="width15">
						<select id="dialog_rilogbook_minute_to" class="select width100">
						<? include ("inc/inc_dt.minutes.php"); ?>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="block width33">
			<div class="container last">
				<div class="row2">
					<div class="width30">
						<? echo $la['DRIVERS']; ?>
					</div>
					<div class="width20">
						<input id="dialog_rilogbook_drivers" type="checkbox" class="checkbox" checked/>
					</div>
					<div class="width30">
						<? echo $la['PASSENGERS']; ?>
					</div>
					<div class="width20">
						<input id="dialog_rilogbook_passengers" type="checkbox" class="checkbox" checked/>
					</div>
				</div>
				<div class="row2">
					<div class="width30">
						<? echo $la['TRAILERS']; ?>
					</div>
					<div class="width20">
						<input id="dialog_rilogbook_trailers" type="checkbox" class="checkbox" checked/>
					</div>
				</div>
			</div>
		</div>
	</div>

	<table id="rilogbook_logbook_grid"></table>
	<div id="rilogbook_logbook_grid_pager"></div>
</div>
