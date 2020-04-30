<?
	function sendPushQueue($identifier, $type, $message)
	{
		if ($identifier == '')
		{
			return false;
		}
		
		global $ms;
		
		$q = "INSERT INTO `gs_push_queue` 	(`dt_push`,
							`identifier`,
							`type`,
							`message`)
							VALUES
							('".gmdate("Y-m-d H:i:s")."',
							'".$identifier."',
							'".$type."',
							'".mysqli_real_escape_string($ms, $message)."')";
		$r = mysqli_query($ms, $q);
		
		if ($r)
                {
                        return true;
                }
                else
                {
                        return false;
                }
	}
?>