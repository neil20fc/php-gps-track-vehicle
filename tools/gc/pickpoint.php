<?
	$gsValues = array();
	include ('../../config.custom.php');
	
	if(@$_GET['cmd'] == 'latlng')
	{
		$result = '';
		
		$url = 'https://api.pickpoint.io/v1/reverse/?key='.$gsValues['GEOCODER_PICKPOINT_KEY'].'&lat='.$_GET["lat"].'&lon='.$_GET["lng"];
		$data = @file_get_contents($url);
		$jsondata = json_decode($data,true);
		
		if (isset($jsondata['display_name']))
		{
			$result = $jsondata['display_name'];
		}
		
		echo json_encode($result);
		die;
	}
	
	if(@$_GET['cmd'] == 'address')
	{
		$result = array();
		
		$search = htmlentities(urlencode($_GET["search"]));
		
		$url = 'https://api.pickpoint.io/v1/forward/?key='.$gsValues['GEOCODER_PICKPOINT_KEY'].'&q='.$search;
		
		$data = @file_get_contents($url);
		$jsondata = json_decode($data,true);
		
		for ($i=0; $i<count($jsondata); $i++)
		{
			$address = $jsondata[$i]['display_name'];
			$lat = $jsondata[$i]['lat'];
			$lng = $jsondata[$i]['lon'];
			
			$result[] = array('address' => $address, 'lat' => $lat, 'lng' => $lng);
		}
		
		echo json_encode($result);
		die;
	}
?>