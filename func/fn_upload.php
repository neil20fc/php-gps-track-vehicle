<?
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	checkUserSession();

	if(@$_GET['file'] == 'logo_png')
	{
		if ($_SESSION["cpanel_privileges"] != 'super_admin')
		{
			die;
		}
		
		$postdata = file_get_contents("php://input");
		
		if (isset($postdata))
		{
			$imageData = $postdata;
			$filteredData = substr($imageData, strpos($imageData, ",")+1);
			
			$unencodedData=base64_decode($filteredData);
			
			$file_path = $gsValues['PATH_ROOT'].'img/logo.png';
			
			if (!isFilePathValid($file_path))
			{
				die;
			}
			
			$fp = fopen( $file_path, 'wb' );
			fwrite( $fp, $unencodedData);
			fclose( $fp );
			
			$file_url = $gsValues['URL_ROOT'].'/img/logo.png';
			echo $file_url;
		}
	}
	
	if(@$_GET['file'] == 'logo_svg')
	{
		if ($_SESSION["cpanel_privileges"] != 'super_admin')
		{
			die;
		}
		
		$postdata = file_get_contents("php://input");
		
		if (isset($postdata))
		{
			$imageData = $postdata;
			$filteredData = substr($imageData, strpos($imageData, ",")+1);
			
			$unencodedData=base64_decode($filteredData);
			
			$file_path = $gsValues['PATH_ROOT'].'img/logo.svg';
			
			if (!isFilePathValid($file_path))
			{
				die;
			}
			
			$fp = fopen( $file_path, 'wb' );
			fwrite( $fp, $unencodedData);
			fclose( $fp );
			
			$file_url = $gsValues['URL_ROOT'].'/img/logo.svg';
			echo $file_url;
		}   
	}
	
	if(@$_GET['file'] == 'logo_small_png')
	{
		if ($_SESSION["cpanel_privileges"] != 'super_admin')
		{
			die;
		}
		
		$postdata = file_get_contents("php://input");
		
		if (isset($postdata))
		{
			$imageData = $postdata;
			$filteredData = substr($imageData, strpos($imageData, ",")+1);
			
			$unencodedData=base64_decode($filteredData);
			
			$file_path = $gsValues['PATH_ROOT'].'img/logo_small.png';
			
			if (!isFilePathValid($file_path))
			{
				die;
			}
			
			$fp = fopen( $file_path, 'wb' );
			fwrite( $fp, $unencodedData);
			fclose( $fp );
			
			$file_url = $gsValues['URL_ROOT'].'/img/logo_small.png';
			echo $file_url;
		}
	}
	
	if(@$_GET['file'] == 'logo_small_svg')
	{
		if ($_SESSION["cpanel_privileges"] != 'super_admin')
		{
			die;
		}
		
		$postdata = file_get_contents("php://input");
		
		if (isset($postdata))
		{
			$imageData = $postdata;
			$filteredData = substr($imageData, strpos($imageData, ",")+1);
			
			$unencodedData=base64_decode($filteredData);
			
			$file_path = $gsValues['PATH_ROOT'].'img/logo_small.svg';
			
			if (!isFilePathValid($file_path))
			{
				die;
			}
			
			$fp = fopen( $file_path, 'wb' );
			fwrite( $fp, $unencodedData);
			fclose( $fp );
			
			$file_url = $gsValues['URL_ROOT'].'/img/logo_small.svg';
			echo $file_url;
		}   
	}
	
	if(@$_GET['file'] == 'favicon')
	{
		if ($_SESSION["cpanel_privileges"] != 'super_admin')
		{
			die;
		}
		
		$postdata = file_get_contents("php://input");
		
		if (isset($postdata))
		{
			$imageData = $postdata;
			$filteredData = substr($imageData, strpos($imageData, ",")+1);
			
			$unencodedData=base64_decode($filteredData);
			
			$file_path = $gsValues['PATH_ROOT'].'favicon.ico';
			
			if (!isFilePathValid($file_path))
			{
				die;
			}
			
			$fp = fopen( $file_path, 'wb' );
			fwrite( $fp, $unencodedData);
			fclose( $fp );
			
			$file_url = $gsValues['URL_ROOT'].'/favicon.ico';
			echo $file_url;
		}   
	}
	
	if(@$_GET['file'] == 'login_background')
	{
		if ($_SESSION["cpanel_privileges"] != 'super_admin')
		{
			die;
		}
		
		$postdata = file_get_contents("php://input");
		
		if (isset($postdata))
		{
			$imageData = $postdata;
			$filteredData = substr($imageData, strpos($imageData, ",")+1);
			
			$unencodedData=base64_decode($filteredData);
			
			$file_path = $gsValues['PATH_ROOT'].'img/login-background.jpg';
			
			if (!isFilePathValid($file_path))
			{
				die;
			}
			
			$fp = fopen( $file_path, 'wb' );
			fwrite( $fp, $unencodedData);
			fclose( $fp );
			
			$file_url = $gsValues['URL_ROOT'].'/img/login-background.jpg';
			echo $file_url;
		}   
	}
	
	if(@$_GET['file'] == 'driver_photo')
	{
		$postdata = file_get_contents("php://input");
		
		if (isset($postdata))
		{
			$imageData = $postdata;
			$filteredData = substr($imageData, strpos($imageData, ",")+1);
			
			$unencodedData=base64_decode($filteredData);
			
			$file_path = $gsValues['PATH_ROOT'].'data/user/drivers/'.$_SESSION["user_id"].'_temp.png';
			$file_url = $gsValues['URL_ROOT'].'/data/user/drivers/'.$_SESSION["user_id"].'_temp.png';
			
			if (!isFilePathValid($file_path))
			{
				die;
			}
			
			$fp = fopen( $file_path, 'wb' );
			fwrite( $fp, $unencodedData);
			fclose( $fp );
			
			echo $file_url;
		}
	}
	
	if(@$_GET['file'] == 'object_icon_png')
	{
		$postdata = file_get_contents("php://input");
		
		if (isset($postdata))
		{
			$imageData = $postdata;
			$filteredData = substr($imageData, strpos($imageData, ",")+1);
			
			$unencodedData=base64_decode($filteredData);
			
			$file_path = $gsValues['PATH_ROOT'].'data/user/objects/'.$_SESSION["user_id"].'_'.md5(gmdate("Y-m-d H:i:s")).'.png';
			
			if (!isFilePathValid($file_path))
			{
				die;
			}
			
			$fp = fopen( $file_path, 'wb' );
			fwrite( $fp, $unencodedData);
			fclose( $fp );
		}
	}
	
	if(@$_GET['file'] == 'object_icon_svg')
	{
		$postdata = file_get_contents("php://input");
		
		if (isset($postdata))
		{
			$imageData = $postdata;
			$filteredData = substr($imageData, strpos($imageData, ",")+1);
			
			$unencodedData=base64_decode($filteredData);
			
			$file_path = $gsValues['PATH_ROOT'].'data/user/objects/'.$_SESSION["user_id"].'_'.md5(gmdate("Y-m-d H:i:s")).'.svg';
			
			if (!isFilePathValid($file_path))
			{
				die;
			}
			
			$fp = fopen( $file_path, 'wb' );
			fwrite( $fp, $unencodedData);
			fclose( $fp );
		}
	}
	
	if(@$_GET['file'] == 'places_icon_png')
	{
		$postdata = file_get_contents("php://input");
		
		if (isset($postdata))
		{
			$imageData = $postdata;
			$filteredData = substr($imageData, strpos($imageData, ",")+1);
			
			$unencodedData=base64_decode($filteredData);
			
			$file_path = $gsValues['PATH_ROOT'].'data/user/places/'.$_SESSION["user_id"].'_'.md5(gmdate("Y-m-d H:i:s")).'.png';
			
			if (!isFilePathValid($file_path))
			{
				die;
			}
			
			$fp = fopen( $file_path, 'wb' );
			fwrite( $fp, $unencodedData);
			fclose( $fp );
		}
	}
	
	if(@$_GET['file'] == 'places_icon_svg')
	{
		$postdata = file_get_contents("php://input");
		
		if (isset($postdata))
		{
			$imageData = $postdata;
			$filteredData = substr($imageData, strpos($imageData, ",")+1);
			
			$unencodedData=base64_decode($filteredData);
			
			$file_path = $gsValues['PATH_ROOT'].'data/user/places/'.$_SESSION["user_id"].'_'.md5(gmdate("Y-m-d H:i:s")).'.svg';
			
			if (!isFilePathValid($file_path))
			{
				die;
			}
			
			$fp = fopen( $file_path, 'wb' );
			fwrite( $fp, $unencodedData);
			fclose( $fp );
		}
	}
 ?>