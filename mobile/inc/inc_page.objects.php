<div id="page_objects" class="page">
	<form role="form">
		<div class="input-group form-group btn-group">
			<input id="page_object_search" class="form-control" type="search" placeholder="<? echo $la['SEARCH']; ?>..." onkeyup="objectLoadList();"/>
			<span id="page_object_search_clear" class="input-group-addon">
				<span class="glyphicon glyphicon-remove"></span>
			</span>
		</div>
		
		<div id="page_object_list_header">			
			<select id="page_object_list_group" class="form-control object-group" onChange="objectLoadList();"></select>
			
			<li class="list-group-item">
				<div class="row vertical-align">
					<div class="object-list-item">
						<div class="right">						
							<div class="visible checked" id="object_visible_all" onClick="objectVisibleAllToggle();">
								<span class="[ glyphicon glyphicon-ok ]"></span>
							</div>
							
							<div class="follow" id="object_follow_all" onClick="objectFollowAllToggle();">
								<span class="[ glyphicon glyphicon-search ]"></span>
							</div>
							
							<div class="details"></div>
						</div>
					</div>				
				</div>
			</li>	
		</div>			
		
		<div id="page_object_list" class="list-group"></div>
	</form>
	
	<a href="#" class="btn btn-default btn-blue show-menu pull-right back-btn" onclick="switchPage('menu');">
		<i class="glyphicon glyphicon-menu-left"></i>
		<? echo $la['BACK']; ?>
	</a>
</div>

<div id="page_object_details" class="page">
	<div id="page_object_detail_list" class="panel panel-default"></div>
	<a href="#" class="btn btn-default btn-blue show-menu pull-right back-btn" onclick="switchPage('objects');">
		<i class="glyphicon glyphicon-menu-left"></i>
		<? echo $la['BACK']; ?>
	</a>
</div>