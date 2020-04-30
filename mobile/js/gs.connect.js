//$( document ).ready(function() {
//	setTimeout(function(){ connectLoad(); }, 100);
//});

var la = [];

function getUrlVars()
{
	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
		vars[key] = value;
	});
	return vars;
}

function connectLoad()
{
	loadLanguage(function(response){});
	
	// set language selectbox
	var cookie = getCookie("gs_language");
	if (cookie!=null && cookie!="")
  	{
		document.getElementById("system_language").value = cookie;
	}
}

function connectServer()
{
	var server = document.getElementById("server").value;
	window.open (server+'/mobile','_self',false);
}

function connectLogin()
{	
	var username = document.getElementById("username").value;
	var password = document.getElementById("password").value;
	var remember_me = document.getElementById("remember_me").checked;
	
	if ((username == '') || (password == ''))
	{
		return;
	}
	
	var data = {
		cmd: 'login',
		username: username,
		password: password,
		remember_me: remember_me,
		mobile: 'true'
	};
	    
	jQuery.ajax({
		type: "POST",
		url: "../func/fn_connect.php",
		data: data,
		success: function(result)
		{
			if (result == 'LOGIN_TRACKING')
			{
				window.open ('tracking.php','_self',false);
			}
			else if (result == 'LOGIN_CPANEL')
			{
				window.open ('tracking.php','_self',false);
			}
			else if (result == 'ERROR_USERNAME_PASSWORD_INCORRECT')
			{
				bootbox.alert(la['USERNAME_OR_PASSWORD_INCORRECT']);
			}
			else if (result == 'ERROR_ACCOUNT_LOCKED')
			{
				bootbox.alert(la['THIS_ACCOUNT_IS_LOCKED']);
			}
		}
	});
}

function connectLogout(){
	var data = {
		cmd: 'logout'
	};
	    
	jQuery.ajax({
		type: "POST",
		url: "../func/fn_connect.php",
		data: data,
		success: function(result)
		{
			window.open (result+'/mobile','_self',false);
		}
	});
}