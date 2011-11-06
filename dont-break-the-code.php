<?php
/*
Plugin Name: Don't Break The Code
Plugin URI: http://www.strangerstudios.com/wp/dont-break-the-code/
Description: Disable the Visual editor on a per post basis.
Version: .1
Author: Stranger Studios (WordCamp Philly)
Author URI: http://www.strangerstudios.com
*/

/*
	Runs on the edit post page in wp-admin. Set the cookie for the HTML tab.
*/
function dbtc_load_post()
{
	//we're on the edit post page. let's rock
	require_once(dirname(__FILE__) . "/lib/screen-options.php");
	
	//save cookie
	$cookie_path = str_replace('/wp-admin/post.php','/',$_SERVER['PHP_SELF']);
	setcookie('wp-settings-1','m5=3&editor=html&m9=o&m1=o',0,$cookie_path);
		
	//filter/action for javascript and setting cookie when disabled already		
	add_filter("format_to_edit", "dbtc_format_to_edit");
	add_action("admin_footer", "dbtc_admin_footer", 10);
	
	//screen options
	add_screen_options_panel(
		'dbtc-screen-options',       	  //Panel ID
		"Don't Break the Code",           //Panel title. 
		'dbtc_screen_options', 			  //The function that generates panel contents.
		array('post', 'page'),            //Pages/screens where the panel is displayed. 
		'dbtc_save_screen_options',      //The function that gets triggered when settings are submitted/saved.
		true                              //Auto-submit settings (via AJAX) when they change. 
	);
}
add_action("load-post.php", "dbtc_load_post");

function dbtc_format_to_edit($content)
{
	if(isset($_GET['post']) && get_post_meta($_GET['post'], '_dbtc_disable_visual') != false){
		$cookie_path = str_replace('/wp-admin/post.php','/',$_SERVER['PHP_SELF']);
		setcookie('wp-settings-1','m5=3&editor=html&m9=o&m1=o',0,$cookie_path);
		
		add_filter('admin_footer', 'dbtc_admin_footer_disabled', 15);
	}
	
	return $content;
}

/*
	Javascript to select the HTML tab and disable the Visual tab.
*/
function dbtc_admin_footer()
{
?>
	<script type="text/javascript">
		function dbtc_disableVisualEditor()
		{
			switchEditors.go('content', 'html');
			jQuery('#edButtonPreview').attr('onclick', '');
			if(jQuery('#edButtonPreviewSpan').length)
			{				
				jQuery('#edButtonPreviewspan').attr('style', '');
			}
			else
			{
				var edButtonPreviewHTML = jQuery('#edButtonPreview').html();
				jQuery('#edButtonPreview').html('<span id="edButtonPreviewSpan" style="text-decoration:line-through">'+edButtonPreviewHTML+'</span>');
			}
		}
		
		function dbtc_enableVisualEditor()
		{
			switchEditors.go('content', 'tinymce');
			jQuery('#edButtonPreview').attr('onclick', "switchEditors.go('content', 'tinymce');");
			//var edButtonPreviewHTML = jQuery('#edButtonPreview').html();
			jQuery('#edButtonPreviewSpan').attr('style', '');
		}
		
		jQuery(document).ready(function(){
			jQuery('#dbtc_disable_visual').click(function(){
				//disable the visual editor
				if(jQuery('#dbtc_disable_visual').attr('checked'))
					dbtc_disableVisualEditor();								
				else
					dbtc_enableVisualEditor();
			});
		});
	</script>
<?php
}
function dbtc_admin_footer_disabled()
{
?>
	<script type="text/javascript">		
		jQuery(document).ready(function(){
			dbtc_disableVisualEditor();
		});
	</script>
<?php
}

/*
	Screen Option functions.
*/
function dbtc_screen_options()
{
	$checked = "";
	if(isset($_GET['post']) && get_post_meta($_GET['post'], 'dbtc_disable_visual') != false) $checked = ' checked="checked" ';
	
	//$output .= "<h5>Don't Break The Code</h5>";
	$output .= '<input type="hidden" name="post" value="' . $_GET['post'] . '" />';
	$output .= '<input type="checkbox" id="dbtc_disable_visual" name="dbtc_disable_visual" '.$checked.'/>';
    $output .= '<label for="dbtc_checkbox"> ';
	$output .=   __("Disable Visual Editor", 'dbtc_textbox' );
    $output .= '</label> ';
	
	return $output;
}

function dbtc_save_screen_options($params)
{		
	//get the post id
	$newparams = array();
	foreach($params as $param => $value)
	{		
		if($param == "post")
			$post_id = $value;
		else
			$newparams[$param] = $value;
	}
	
	//save the params
	foreach($newparams as $param => $value)
	{
		if($value == "on")
			update_post_meta($post_id, "_" . $param, $value);
		else
			update_post_meta($post_id, "_" . $param, $value);
	}
}
?>
