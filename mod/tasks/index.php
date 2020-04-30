<?
	session_start();
	include ('../../init.php');
	include ('../../func/fn_common.php');
	
	if (isset($_GET['imei']))
	{
		$imei = $_GET['imei'];
		if (!checkObjectExistsSystem($imei))
		{
			die;
		}
	}
	else
	{
		die;
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Tasks</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scaleable=0">
		<meta name="HandheldFriendly" content="True" />
		
		<link type="text/css" href="style.css" rel="Stylesheet" />
		
		<script type="text/javascript" src="../../js/jquery-2.1.4.min.js"></script>
		<script type="text/javascript" src="../../js/moment.min.js"></script>
		<script type="text/javascript" src="tasks.js?v=<? echo gmdate("Y-m-d H:i:s"); ?>"></script>
                
                <script>tasksData['imei'] = <? echo $imei; ?>;</script>
	</head>
	
	<body onload="load()">
                <div id="task_list" class="task-list"></div>
		<div id="task_details" class="task-details" style="display: none;">			
			<div id="task_details_content">
			</div>
			
			<div id="task_details_controls_confirm" style="display: none;" class="task-details-controls">				
				<div class="button-block width50 block-a float-left">
					<a href="#" onclick="tasksCancel();">
						<span class="task-details-button cancel">Cancel</span>
					</a>
				</div>
				<div class="button-block width50 block-b float-left">
					<a href="#" onclick="tasksConfirm();">
						<span class="task-details-button confirm">Confirm</span>
					</a>
				</div>
			</div>
			
			<div id="task_details_controls_completed" style="display: none;" class="task-details-controls">				
				<div class="button-block width50 block-a float-left">
					<a href="#" onclick="tasksCancel();">
						<span class="task-details-button cancel">Cancel</span>
					</a>
				</div>
				<div class="button-block width50 block-a float-left">
					<a href="#" onclick="tasksComplete();">
						<span class="task-details-button complete">Complete</span>
					</a>
				</div>
			</div>
			
			<div class="task-details-close">
				<a href="#" onclick="tasksCloseItem();"><span class="icon icon-close"></span></a>
			</div>
		</div>
	</body>
</html>