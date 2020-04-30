<?
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	checkUserSession();

	if (isset($_POST['path']))
	{
		$result = '';
		
		if ($_POST['path'] == 'img/markers/places')
		{
			$result = getFileList('img/markers/places');
		}
		else if ($_POST['path'] == 'data/user/places')
		{
			$result = getFileList('data/user/places');
		}
		else if ($_POST['path'] == 'img/markers/objects')
		{
			$result = getFileList('img/markers/objects');
		}
		else if ($_POST['path'] == 'data/user/objects')
		{
			$result = getFileList('data/user/objects');
		}
		else
		{
			die;
		}
		
		echo json_encode($result);
		die;
	}	
 ?>
 
 
