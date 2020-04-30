<?
	$gsValues = array();
	include ('../../config.custom.php');
	
	if(@$_GET['cmd'] == 'latlng')
	{
		$result = '';
		
		$url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/'.$_GET["lng"].','.$_GET["lat"].'.json?access_token='.$gsValues['GEOCODER_MAPBOX_KEY'];
		
		$data = @file_get_contents($url);
		$jsondata = json_decode($data,true);
		
		if (isset($jsondata['features'][0]['place_name']))
		{
			$result = $jsondata['features'][0]['place_name'];
		}
		
		echo json_encode($result);
		die;
	}
	
	if(@$_GET['cmd'] == 'address')
	{
		$result = array();
		
		$search = htmlentities(urlencode($_GET["search"]));
		
		$url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/'.$search.'.json?access_token='.$gsValues['GEOCODER_MAPBOX_KEY'];
		
		$data = @file_get_contents($url);
		$jsondata = json_decode($data,true);
		
		for ($i=0; $i<count($jsondata['features']); $i++)
		{
			$address = $jsondata['features'][$i]['place_name'];
			$lat = $jsondata['features'][$i]['center'][1];
			$lng = $jsondata['features'][$i]['center'][0];
			
			$result[] = array('address' => $address, 'lat' => $lat, 'lng' => $lng);
		}
		
		echo json_encode($result);
		die;
	}
?>