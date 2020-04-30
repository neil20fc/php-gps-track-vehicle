<?
	session_start();
	include ('../init.php');
	include ('fn_common.php');
	checkUserSession();
        
        if (isset($_POST['lat']) && isset($_POST['lng']) && isset($_POST['angle']))
        {
                $url = 'https://maps.googleapis.com/maps/api/streetview?size=316x177&location='.$_POST['lat'].','.$_POST['lng'].'&heading='.$_POST['angle'].'&key='.$gsValues['MAP_GOOGLE_KEY'];
                $image = @file_get_contents($url);
                
                if ($image != '')
                {
                        header('Content-Type: image/jpeg');
                        echo base64_encode($image);        
                }
        }
        
        die;
?>