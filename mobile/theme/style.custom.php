<?
        header("Content-type: text/css; charset=utf-8");

        include ('../../init.php');
	include ('../../func/fn_common.php');
        
        $theme = getTheme();
        
        if (!$theme)
        {
                die;
        }
        
        $style = '';
        
        // #################################################
	// LOGIN DIALOG
	// #################################################
        
        // #################################################
	// END LOGIN DIALOG
	// #################################################
        
        // #################################################
	// UI
	// #################################################
        
         // accent color 1
        if (isset($theme['ui_accent_color_1']))
        {
                $style .= '.page-menu .page-title { background-color: '.$theme["ui_accent_color_1"].'; }';
                $style .= '.navbar-default { background-color: '.$theme["ui_accent_color_1"].'; }';
                $style .= '.btn.btn-blue { background-color: '.$theme["ui_accent_color_1"].'; border-color: '.$theme["ui_accent_color_1"].'; }';
                $style .= '.list-group-item .checked  { color: '.$theme["ui_accent_color_1"].'; }';
                $style .= '.list-group-item .details  { color: '.$theme["ui_accent_color_1"].'; }';    
        }
        
        // #################################################
	// END UI
	// #################################################
        
        // echo style
        echo $style;
?>