<?
        header("Content-type: text/css; charset=utf-8");

        include ('../init.php');
	include ('../func/fn_common.php');
        
        $theme = getTheme();
        
        if (!$theme)
        {
                die;
        }
        
        $style = '';
        
        // #################################################
	// LOGIN DIALOG
	// #################################################
        
        // logo
        if (isset($theme['login_dialog_logo']))
        {
                if ($theme['login_dialog_logo'] == 'no')
                {
                        $style .= '#login .logo-block { display: none; }';
                }
        }
        
        if (isset($theme['login_dialog_logo_position']))
        {
                if ($theme['login_dialog_logo_position'] == 'center')
                {
                        $style .= '#login .logo-block { float: none; display: flex; justify-content: center; align-items: center; }';
                }
                else if ($theme['login_dialog_logo_position'] == 'right')
                {
                        $style .= '#login .logo-block { float: right; }';
                }    
        }

        // opacity
        if (isset($theme['login_dialog_opacity']))
        {
                $opacity = $theme['login_dialog_opacity'] / 100;
                $style .= '#login .wrapper .inner-wrapper { opacity: '.$opacity.'; }';
        }
       
        
        // h dialog pos
        if (isset($theme['login_dialog_h_position']))
        {
                if ($theme['login_dialog_h_position'] == 'left')
                {
                        $style .= '#login .wrapper .inner-wrapper { float: left; }';
                }
                else if ($theme['login_dialog_h_position'] == 'right')
                {
                        $style .= '#login .wrapper .inner-wrapper { float: right; }';
                }    
        }
        
        // v dialog pos
        if (isset($theme['login_dialog_v_position']))
        {
                if ($theme['login_dialog_v_position'] == 'top')
                {
                        $style .= '#login .wrapper { vertical-align: top }';
                        $style .= '#login .wrapper .inner-wrapper { margin: auto auto; }';
                }
                else if ($theme['login_dialog_v_position'] == 'bottom')
                {
                        $style .= '#login .wrapper { vertical-align: bottom }';
                        $style .= '#login .wrapper .inner-wrapper { margin: auto auto; }';
                }
        }
        
        // bg color
        if (isset($theme['login_bg_color']))
        {
                $style .= 'body#login { background-color: '.$theme["login_bg_color"].'; }';
        }
        
        // login bg color
        if (isset($theme['login_dialog_bg_color']))
        {
                $style .= '#login .wrapper .inner-wrapper { background-color: '.$theme["login_dialog_bg_color"].'; }';
        }
        
        // #################################################
	// END LOGIN DIALOG
	// #################################################
        
        // #################################################
	// UI
	// #################################################
        
        // top panel color
        if (isset($theme['ui_top_panel_color']))
        {
                $style .= '#top_panel { background-color: '.$theme["ui_top_panel_color"].'; }';
                $style .= '#top_panel .chat-btn span { color: '.$theme["ui_top_panel_color"].'; }';
                $style .= '#top_panel .billing-btn span { color: '.$theme["ui_top_panel_color"].'; }';
        }
        
        // top panel border color
        if (isset($theme['ui_top_panel_border_color']))
        {
                $style .= '#top_panel { border-bottom-color: '.$theme["ui_top_panel_border_color"].'; }';
                $style .= '#top_panel .chat-btn span { background-color: '.$theme["ui_top_panel_border_color"].'; }';
                $style .= '#top_panel .billing-btn span { background-color: '.$theme["ui_top_panel_border_color"].'; }';
        }
        
        // top panel selection color
        if (isset($theme['ui_top_panel_selection_color']))
        {
                $style .= '#top_panel a:hover { background-color: '.$theme["ui_top_panel_selection_color"].'; }';
                $style .= '#top_panel a.active { background-color: '.$theme["ui_top_panel_selection_color"].'; }';        
        }
          
        // dialog titlebar color
        if (isset($theme['ui_dialog_titlebar_color']))
        {
                $style .= '.ui-dialog .ui-dialog-titlebar { background-color: '.$theme["ui_dialog_titlebar_color"].'; }';
        }
        
        // accent color 1
        if (isset($theme['ui_accent_color_1']))
        {
                $style .= '#login .wrapper .content-block input.button { background-color: '.$theme["ui_accent_color_1"].'; }';
                $style .= '#top_panel .map-btn a { background-color: '.$theme["ui_accent_color_1"].'; }';
                $style .= '#top_panel .map-btn a:hover { background-color: '.$theme["ui_accent_color_1"].'; opacity: 0.9; }';
                $style .= '.ui-pg-div .ui-icon-plus { background-color: '.$theme["ui_accent_color_1"].'; }';
                $style .= '.menu.ui-menu .first-item { border-top: 3px solid '.$theme["ui_accent_color_1"].'; }';
                $style .= '#cpanel_user_list, #cpanel_object_list, #cpanel_unused_object_list, #cpanel_billing_plan_list, #cpanel_manage_server { border-top-color: '.$theme["ui_accent_color_1"].'; }';
        }
        
        // accent color 2
        if (isset($theme['ui_accent_color_2']))
        {
                $style .= '#login .registration-closed{ background-color: '.$theme["ui_accent_color_2"].'; }';
                $style .= '#top_panel .cpanel-btn a{ background-color: '.$theme["ui_accent_color_2"].'; }';
                $style .= '#top_panel .cpanel-btn a:hover { background-color: '.$theme["ui_accent_color_2"].'; opacity: 0.9; }';      
        }
        
        // accent color 3
        if (isset($theme['ui_accent_color_3']))
        {
                $style .= '#top_panel .billing-btn a { background-color: '.$theme["ui_accent_color_3"].'; }';
                $style .= '#top_panel .billing-btn a:hover { background-color: '.$theme["ui_accent_color_3"].'; opacity: 0.9; }';
                $style .= '.dialog-billing-titlebar .ui-dialog-titlebar { background-color: '.$theme["ui_accent_color_3"].'; }';
                $style .= '#dialog_billing .ui-pg-div .ui-icon-plus { background-color: '.$theme["ui_accent_color_3"].'; }';
        }
        
        // accent color 4
        if (isset($theme['ui_accent_color_4']))
        {
                $style .= '#top_panel .logout-btn a { background-color: '.$theme["ui_accent_color_4"].'; }';
                $style .= '#top_panel .logout-btn a:hover { background-color: '.$theme["ui_accent_color_4"].'; opacity: 0.9; }';
                $style .= '#cpanel #cpanel_manage_server .ui-tabs .ui-tabs-nav li.ui-tabs-active a { background-color: '.$theme["ui_accent_color_4"].'; }';   
        }
        
        // font color
        if (isset($theme['ui_font_color']))
        {
                $style .= 'body { color: '.$theme["ui_font_color"].'; }';
                $style .= 'input, select, .inputbox, .textarea { color: '.$theme["ui_font_color"].'; }';
                $style .= 'input:focus, textarea:focus { color: '.$theme["ui_font_color"].'; }';
                $style .= 'optgroup, option { color: '.$theme["ui_font_color"].'; }';
                $style .= 'input.button { color: '.$theme["ui_font_color"].'; }';
                $style .= '.ms-choice span, .ms-drop span { color: '.$theme["ui_font_color"].'; }';
                $style .= '.ui-state-default a, .ui-state-default a:link, .ui-state-default a:visited { color: '.$theme["ui_font_color"].'; }';
                $style .= '.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default { color: '.$theme["ui_font_color"].'; }';
                $style .= '.ui-state-highlight, .ui-widget-content .ui-state-highlight, .ui-widget-header .ui-state-highlight { color: '.$theme["ui_font_color"].'; }';
                $style .= '.ui-state-highlight a, .ui-widget-content .ui-state-highlight a, .ui-widget-header .ui-state-highlight a { color: '.$theme["ui_font_color"].'; }';
                $style .= '.ui-state-hover, .ui-widget-content .ui-state-hover, .ui-widget-header .ui-state-hover, .ui-state-focus, .ui-widget-content .ui-state-focus, .ui-widget-header .ui-state-focus { color: '.$theme["ui_font_color"].'; }';
                $style .= '.menu.ui-menu .ui-menu-item a { color: '.$theme["ui_font_color"].'; }';
        }
        
        // top panel font color
        if (isset($theme['ui_top_panel_font_color']))
        {
                $style .= '#top_panel .user-btn span, #top_panel .user-list-btn span, #top_panel .object-list-btn span, #top_panel .billing-plan-list-btn span, #top_panel .unused-object-list-btn span { color: '.$theme["ui_top_panel_font_color"].'; }';     
        }
        
        // top panel counters font color
        if (isset($theme['ui_top_panel_counters_font_color']))
        {
                $style .= '#top_panel .chat-btn span { color: '.$theme["ui_top_panel_counters_font_color"].'; }';
                $style .= '#top_panel .billing-btn span { color: '.$theme["ui_top_panel_counters_font_color"].'; }';   
        }
        
        // heading color 1
        if (isset($theme['ui_heading_font_color_1']))
        {
                $style .= '.title-block { color: '.$theme["ui_heading_font_color_1"].'; }';
                $style .= '#cpanel h1.title { color: '.$theme["ui_heading_font_color_1"].'; }';   
        }
        
        // heading color 2
        if (isset($theme['ui_heading_font_color_2']))
        {
                $style .= '#cpanel h1.title span { color: '.$theme["ui_heading_font_color_2"].'; }';
        }
        
        // #################################################
	// END UI
	// #################################################
        
        // echo style
        echo $style;
?>