<?
	$gsValues = array();
	include ('../../config.custom.php');
	
	if(@$_GET['cmd'] == 'latlng')
	{
		$result = '';
		
		$search = $_GET["lat"].','.$_GET["lng"];
		$search = htmlentities(urlencode($search));
		
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$search.'&oe=utf-8&key='.$gsValues['GEOCODER_GOOGLE_KEY'];
		$data = @file_get_contents($url);
		$jsondata = json_decode($data,true);
		
		if(is_array($jsondata) && $jsondata['status']=="OK")
		{
			$result = $jsondata['results'][0]['formatted_address'];
		}
		
		echo json_encode($result);
		die;
	}
	
	if(@$_GET['cmd'] == 'address')
	{
		$result = array();
		
		$search = htmlentities(urlencode($_GET["search"]));
		
		$url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$search.'&oe=utf-8&key='.$gsValues['GEOCODER_GOOGLE_KEY'];
		$data = @file_get_contents($url);
		$jsondata = json_decode($data,true);
		
		if(is_array($jsondata) && $jsondata['status']=="OK")
		{
			for ($i=0; $i<count($jsondata['results']); $i++)
			{
				$address = $jsondata['results'][$i]['formatted_address'];
				$lat = $jsondata['results'][$i]['geometry']['location']['lat'];
				$lng = $jsondata['results'][$i]['geometry']['location']['lng'];
				
				$result[] = array('address' => $address, 'lat' => $lat, 'lng' => $lng);
			}
		}
		
		echo json_encode($result);
		die;
	}
?>