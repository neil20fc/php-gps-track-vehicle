<?
	include ('../init.php');
	include ('../func/fn_common.php');
	
	if(@$_POST['cmd'] == 'latlng')
	{
		$result = '';
		
		$lat = $_POST["lat"];
		$lng = $_POST["lng"];
		
		if ($gsValues['GEOCODER_CACHE'] == 'true')
		{
			$result = getGeocoderCache($lat, $lng);
		}
		
		if ($result == '')
		{
			usleep(50000);
			
			$url = $gsValues['URL_ROOT'].'/tools/gc/'.$gsValues['GEOCODER_SERVICE'].'.php';	
			$url .= '?cmd=latlng&lat='.$lat.'&lng='.$lng;
			$result = @file_get_contents($url);				
			$result = json_decode($result);
			
			if ($gsValues['GEOCODER_CACHE'] == 'true')
			{
				insertGeocoderCache($lat, $lng, $result);
			}
		}
		
		echo json_encode($result);
	}
	
	if(@$_POST['cmd'] == 'address')
	{
		$result = '';
		$search = htmlentities(urlencode($_POST["search"]));
		
		$url = $gsValues['URL_ROOT'].'/tools/gc/'.$gsValues['GEOCODER_SERVICE'].'.php';	
		$url .= '?cmd=address&search='.$search;
		$result = @file_get_contents($url);
		
		echo $result;
	}
?>