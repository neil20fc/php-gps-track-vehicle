<div id="page_places" class="page">
	<div id="markers_panel" class="markers-panel">
		<form role="form">
			<div class="input-group form-group btn-group">
				<input id="page_markers_panel_search" class="form-control" type="search" placeholder="<? echo $la['SEARCH']; ?>..." onkeyup="placesMarkerLoadList();"/>
				<span id="page_markers_panel_search_clear" class="input-group-addon">
					<span class="glyphicon glyphicon-remove"></span>
				</span>
			</div>
			<div id="page_markers_panel_list" class="list-group"></div>
		</form>
	</div>
	
	<div id="routes_panel" class="routes-panel">
		<form role="form">
			<div class="input-group form-group btn-group">
				<input id="page_routes_panel_search" class="form-control" type="search" placeholder="<? echo $la['SEARCH']; ?>..." onkeyup="placesRouteLoadList();"/>
				<span id="page_routes_panel_search_clear" class="input-group-addon">
					<span class="glyphicon glyphicon-remove"></span>
				</span>
			</div>
			<div id="page_routes_panel_list" class="list-group"></div>
		</form>
	</div>
	
	<div id="zones_panel" class="zones-panel">
		<form role="form">
			<div class="input-group form-group btn-group">
				<input id="page_zones_panel_search" class="form-control" type="search" placeholder="<? echo $la['SEARCH']; ?>..." onkeyup="placesZoneLoadList();"/>
				<span id="page_zones_panel_search_clear" class="input-group-addon">
					<span class="glyphicon glyphicon-remove"></span>
				</span>
			</div>
			<div id="page_zones_panel_list" class="list-group"></div>
		</form>
	</div>
	
	<nav id="places_navbar" class="navbar navbar-default navbar-fixed-bottom">
		<div class="container-fluid">
			<div class="row vertical-align">
				<div class="width33">
					<a href="#" class="btn btn-default dropdown-toggle" onclick="showPlacesMarkersPanel();">
						<i class="glyphicon glyphicon-map-marker"></i>
						<? echo $la['MARKERS']; ?>
					</a>
				</div>
				<div class="width33">
					<a href="#" class="btn btn-default dropdown-toggle" onclick="showPlacesRoutesPanel();">
						<i class="glyphicon glyphicon-road"></i>
						<? echo $la['ROUTES']; ?>
					</a>
				</div>
				<div class="width33">
					<a href="#" class="btn btn-default dropdown-toggle" onclick="showPlacesZonesPanel();">
						<i class="glyphicon glyphicon-unchecked"></i>
						<? echo $la['ZONES']; ?>
					</a>
				</div>
			</div>
		</div>
	</nav>
</div>