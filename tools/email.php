<?
	function sendEmailQueue($email, $subject, $message, $no_reply = false)
	{
		global $ms;
		
		$q = "INSERT INTO `gs_email_queue` 	(`dt_email`,
							`email`,
							`subject`,
							`message`, 
							`no_reply`)
							VALUES
							('".gmdate("Y-m-d H:i:s")."',
							'".$email."',
							'".mysqli_real_escape_string($ms, $subject)."',
							'".mysqli_real_escape_string($ms, $message)."',
							'".$no_reply."')";
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
	
	function sendEmail($email, $subject, $message, $no_reply = false, $att_name = false, $att_str = false)
	{
		require_once 'PHPMailerAutoload.php';
		
		global $ms, $gsValues;
		
		$signature = "\r\n\r\n".$gsValues['EMAIL_SIGNATURE'];
		$message .= $signature;
		$message = str_replace("\\n", "\n", $message); 
		
		$mail = new PHPMailer();
		
		if ($gsValues['EMAIL_SMTP'] == 'true')
		{
			$mail->IsSMTP(); // telling to use SMTP
			$mail->Host       = $gsValues['EMAIL_SMTP_HOST'];
			$mail->Port       = $gsValues['EMAIL_SMTP_PORT'];
			$mail->SMTPDebug  = 0;
			$mail->SMTPAuth   = $gsValues['EMAIL_SMTP_AUTH'];
			$mail->SMTPSecure = $gsValues['EMAIL_SMTP_SECURE'];
			$mail->Username   = $gsValues['EMAIL_SMTP_USERNAME'];
			$mail->Password   = $gsValues['EMAIL_SMTP_PASSWORD'];
		}
		else
		{
			return false;
		}
		
		$email_from = $gsValues['EMAIL'];
		
		if ($no_reply != false)
		{
			if ($gsValues['EMAIL_NO_REPLY'] != '')
			{
				$email_from = $gsValues['EMAIL_NO_REPLY'];
			}	
		}
		
		$mail->From = $email_from;
		$mail->FromName = $gsValues['NAME'];		
		$mail->SetFrom($email_from, $gsValues['NAME']);
		$mail->AddReplyTo($email_from, $gsValues['NAME']);
		
		// multiple emails
		$emails = explode(",", $email);
		
		for ($i = 0; $i < count($emails); ++$i)
		{
			if ($i > 4)
			{
				break;
			}
			
			$email = trim($emails[$i]);
			
			$mail->AddAddress($email, '');
		}
		
		if ($att_name != false)
		{
			$mail->AddStringAttachment($att_str,$att_name);
		}
		
		$mail->Subject = $subject;
		$mail->Body = $message;
		
		if(!$mail->Send())
		{
			return false;
		}
		else
		{
			return count($emails);
		}
	}
?>