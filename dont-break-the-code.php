<?php
/*
Plugin Name: Don't Break The Code
Plugin URI: http://www.strangerstudios.com/wp/dont-break-the-code/
Description: Disable the Visual editor on a per post basis.
Version: .2.3
Author: Stranger Studios (WordCamp Philly)
Author URI: http://www.strangerstudios.com
*/

/*
	Runs on the edit post page in wp-admin. Set the cookie for the HTML tab.
*/
function dbtc_load_post()
{
	$cookie_path = str_replace('/wp-admin/post.php','/',$_SERVER['PHP_SELF']);
	setcookie('wp-settings-1','m5=3&editor=html&m9=o&m1=o',0,$cookie_path);
		
	add_filter("format_to_edit", "dbtc_format_to_edit");
	add_action("admin_footer", "dbtc_admin_footer", 10);
}
add_action("load-post.php", "dbtc_load_post");

function dbtc_format_to_edit($content)
{
	if(isset($_GET['post']) && get_post_meta($_GET['post'], '_dbtc_disable_visual', true) != false){
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
				jQuery('#edButtonPreviewSpan').css('text-decoration', 'line-through');
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
				
				//disable/enable the visual editor
				if(jQuery('#dbtc_disable_visual').attr('checked'))
				{
					dbtc_disableVisualEditor();								
					var checked = '1';
				}
				else
				{
					dbtc_enableVisualEditor();
					var checked = '';
				}
					
				//update post meta
				data = 'action=dbtc_save&post=<?php echo $_GET["post"]; ?>&dbtc_disable_visual=' + checked;				
				jQuery.post(ajaxurl, data, function(response) {
					//alert('Got this from the server: ' + response);
				});
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
	Add checkbox to the screen settings.
*/
function dbtc_screen_settings($current, $screen)
{	
	//only for admins
	if(!current_user_can("manage_options"))
		return;
	
	if(in_array($screen->id, array("post", "page")))
	{
		$checked = "";
  		if(isset($_GET['post']) && get_post_meta($_GET['post'], '_dbtc_disable_visual', true) != false) $checked = ' checked="checked" ';
		
		$current .= "<h5>Don't Break The Code</h5>";
		$current .= '<input type="checkbox" id="dbtc_disable_visual" name="dbtc_disable_visual" '.$checked.'/>';
	    $current .= '<label for="dbtc_checkbox"> ';
		$current .=   __("Disable Visual Editor", 'dbtc_textbox' );
	    $current .= '</label> ';
	}
		
	return $current;
}
add_filter('screen_settings', 'dbtc_screen_settings', 10, 2);

/*
	Ajax called when saving options
*/
function dbtc_save_options()
{		
	$dbtc_disable_visual = $_POST['dbtc_disable_visual'];
	$post_id = $_POST['post'];
		
	if($post_id && $dbtc_disable_visual !== NULL)
	{											
		update_post_meta($post_id, "_dbtc_disable_visual", $dbtc_disable_visual);							
	}
	
	//this is called via Ajax, so just exit here
	exit;
}
add_action('wp_ajax_dbtc_save', 'dbtc_save_options');
?>

