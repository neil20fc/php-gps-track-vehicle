<? 
	session_start();
	include ('../../init.php');
        
        if(@$_POST['cmd'] == 'load_tasks_data')
	{
                $result = array();
                
                $imei = $_POST['imei'];
                
                $q = "SELECT * FROM `gs_object_tasks` WHERE `imei`='".$imei."' AND dt_task > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 24 HOUR) ORDER BY `task_id` ASC";
                $r = mysqli_query($ms, $q);
                
                while ($row = mysqli_fetch_assoc($r))
                {
                         $result[$row['task_id']] = $row;
                }
                
                header('Content-type: application/json');
		echo json_encode($result);
		die;
        }
        
        if(@$_POST['cmd'] == 'confirm_task')
        {
                $imei = $_POST['imei'];
                $id = $_POST['id'];
                
                $q = "UPDATE `gs_object_tasks` SET `status`='1' WHERE `task_id`='".$id."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
                
                echo 'OK';
                die;
        }
        
        if(@$_POST['cmd'] == 'complete_task')
        {
                $imei = $_POST['imei'];
                $id = $_POST['id'];
                
                $q = "UPDATE `gs_object_tasks` SET `status`='2' WHERE `task_id`='".$id."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
                
                echo 'OK';
                die;
        }
        
        if(@$_POST['cmd'] == 'cancel_task')
        {
                $imei = $_POST['imei'];
                $id = $_POST['id'];
                
                $q = "UPDATE `gs_object_tasks` SET `status`='3' WHERE `task_id`='".$id."' AND `imei`='".$imei."'";
		$r = mysqli_query($ms, $q);
                
                echo 'OK';
                die;
        }
?>