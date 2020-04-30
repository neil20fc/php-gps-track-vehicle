<?
	session_start();
	include ('init.php');
	include ('func/fn_common.php');
	checkUserSession();
	checkUserCPanelPrivileges();
	
	setUserSessionSettings($_SESSION["user_id"]);
	loadLanguage($_SESSION['language'], $_SESSION["units"]);
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><? echo $gsValues['NAME'].' '.$gsValues['VERSION']; ?></title>
		
		<?
			if (file_exists('favicon.png'))
			{
				echo '<link rel="shortcut icon" href="'.$gsValues['URL_ROOT'].'/favicon.png" type="image/x-icon">';
			}
			else
			{
				echo '<link rel="shortcut icon" href="'.$gsValues['URL_ROOT'].'/favicon.ico" type="image/x-icon">';
			}	
		?>
		
		<link type="text/css" href="theme/jquery-ui.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
		<link type="text/css" href="theme/ui.jqgrid.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
		<link type="text/css" href="theme/jquery.multiple.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
		<link type="text/css" href="theme/jquery.tokenize.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
		
		<link type="text/css" href="theme/style.css?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
		<link type="text/css" href="theme/style.custom.php?v=<? echo $gsValues['VERSION_ID']; ?>" rel="Stylesheet" />
	
		<script type="text/javascript" src="js/md5.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/csv2json.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/sprintf.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		
		<script type="text/javascript" src="js/jscolor.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/jquery-2.1.4.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/jquery-migrate-1.2.1.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/jquery-ui.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/jquery.jqGrid.locale.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/jquery.jqGrid.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/jquery.multiple.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/jquery.tokenize.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		
		<script type="text/javascript" src="js/moment.min.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		
		<script type="text/javascript" src="js/gs.config.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/gs.common.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/gs.connect.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/gs.cpanel.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/gs.cpanel.gui.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/gs.cpanel.users.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/gs.cpanel.objects.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/gs.cpanel.billing.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
		<script type="text/javascript" src="js/gs.cpanel.server.js?v=<? echo $gsValues['VERSION_ID']; ?>"></script>
	</head>
    
	<body id="cpanel" onload="load()" >
		<input id="load_file" type="file" style="display: none;" onchange=""/>
		
		<div id="loading_panel">
			<div class="table">
				<div class="table-cell center-middle">
					<div id="loading_panel_text">
						<div class="row">
							<img class="logo" src="<? echo $gsValues['URL_ROOT'].'/img/'.$gsValues['LOGO']; ?>" />
						</div>
						<div class="row">
							<div class="loader">
								<span></span><span></span><span></span><span></span><span></span><span></span><span></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div id="loading_data_panel" style="display: none;">
			<div class="table">
				<div class="table-cell center-middle">
					<div class="loader">
						<span></span><span></span><span></span><span></span><span></span><span></span><span></span>
					</div>
				</div>
			</div>
		</div>
			
		<div id="blocking_panel">
			<div class="table">
				<div class="table-cell center-middle">
					<div id="blocking_panel_text">
						<div class="row">
							<img class="logo" src="<? echo $gsValues['URL_ROOT'].'/img/'.$gsValues['LOGO']; ?>" />
						</div>
						<? echo '<a href="'.$gsValues['URL_LOGIN'].'">'.$la['SESSION_HAS_EXPIRED'].'</a>'; ?>
					</div>
				</div>
			</div>
		</div>
	
		<div id="content" style="visibility: hidden;">
			<div id="dialog_notify" title="">
				<div class="row">
					<div class="row2">
						<div class="width100 center-middle">
							<span id="dialog_notify_text"></span>
						</div>
					</div>
				</div>
				<center>
					<input class="button" type="button" onclick="$('#dialog_notify').dialog('close');" value="<? echo $la['OK']; ?>" />
				</center>
			</div>
			
			<div id="dialog_confirm" title="">
				<div class="row">
					<div class="row2">
						<div class="width100 center-middle">
							<span id="dialog_confirm_text"></span>
						</div>
					</div>
				</div>
				<center>
					<input class="button" type="button" onclick="confirmResponse(true);" value="<? echo $la['YES']; ?>" />&nbsp;
					<input class="button" type="button" onclick="confirmResponse(false);" value="<? echo $la['NO']; ?>" />
				</center>
			</div>
			
			<div id="dialog_set_expiration" title="<? echo $la['SET_EXPIRATION'] ?>">
				<div class="row">
					<div class="row2">
						<div class="width40">
							<? echo $la['EXPIRE_ON']; ?>
						</div>
						<div class="width10"><input id="dialog_set_expiration_expire" class="checkbox" type="checkbox" onChange="setExpirationCheck();"/></div>
						<div class="width50">
							<input class="inputbox-calendar inputbox width100" id="dialog_set_expiration_expire_dt"/>
						</div>
					</div>
				</div>
				<center>
					<input class="button icon-save icon" type="button" onclick="setExpirationSelected('save');" value="<? echo $la['SAVE']; ?>" />&nbsp;
					<input class="button icon-close icon" type="button" onclick="setExpirationSelected('cancel');" value="<? echo $la['CANCEL']; ?>" />
				</center>
			</div>
			
			<? include ("inc/inc_cpanel.panels.php"); ?>
			<? include ("inc/inc_cpanel.menus.php"); ?>
			<? include ("inc/inc_cpanel.billing.php"); ?>
			<? include ("inc/inc_cpanel.objects.php"); ?>
			<? include ("inc/inc_cpanel.server.php"); ?>
			<? include ("inc/inc_cpanel.users.php"); ?>
		</div>
	</body>
</html>