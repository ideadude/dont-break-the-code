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
	$cookie_path = str_replace('/wp-admin/post.php','/',$_SERVER['PHP_SELF']);
	setcookie('wp-settings-1','m5=3&editor=html&m9=o&m1=o',0,$cookie_path);
		
	add_filter("format_to_edit", "dbtc_format_to_edit");
	add_action("admin_footer", "dbtc_admin_footer", 10);
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
			var edButtonPreviewHTML = jQuery('#edButtonPreview').html();
			jQuery('#edButtonPreview').html('<span style="text-decoration:line-through">'+edButtonPreviewHTML+'</span>');
		}
		
		jQuery(document).ready(function(){
			jQuery('#dbtc_disable_visual').click(function(){
				//disable the visual editor
				dbtc_disableVisualEditor();
				
				if(jQuery('#dbtc_disable_visual').attr('checked'))
					var checked = '1';
				else
					var checked = '0';
			
				//update post meta via our trigger
				jQuery.ajax({
					url: '<?php echo $_SERVER["PHP_SELF"]?>',
					data: 'post=<?php echo $_REQUEST["post"] ?>&dbtc_disable_visual=' + checked,					
					error: function(xml){alert('Error saving post meta [1]');},
                	success: function(text){alert('Success! ' + text)}
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
	This code adds a meta box to the sidebar of the edit post page with a checkbox to disable the visual editor.
*/
function dbtc_add_custom_box() {   
    add_meta_box( 
        'dbtc_sectionid',
        __( 'Visual Editor', 'dbtc_textbox' ),
        'dbtc_custom_box',
        'post',
		'side',
		'default'
    );
    add_meta_box(
        'dbtc_sectionid',
        __( 'Visual Editor', 'dbtc_textbox' ), 
        'dbtc_custom_box',
        'page',
		'side',
		'default'
    );
}


function dbtc_custom_box() {

  wp_nonce_field( plugin_basename( __FILE__ ), 'dbtc_noncename' );

  $checked = "";
  if(isset($_GET['post']) && get_post_meta($_GET['post'], 'dbtc_checkbox') != false) $checked = ' checked="checked" ';

  echo '<input type="checkbox" id="dbtc_checkbox" name="dbtc_checkbox" '.$checked.'/>';
  echo '<label for="dbtc_checkbox">';
       _e(" Disable", 'dbtc_textbox' );
  echo '</label> ';
}
//add_action( 'admin_init', 'dbtc_add_custom_box', 1 );

function dbtc_save_post( $post_id ) {
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;

  if ( !wp_verify_nonce( $_POST['dbtc_noncename'], plugin_basename( __FILE__ ) ) )
      return;

  if ( 'page' == $_POST['post_type'] ) 
  {
    if ( !current_user_can( 'edit_page', $post_id ) )
        return;
  }
  else
  {
    if ( !current_user_can( 'edit_post', $post_id ) )
        return;
  }

  if(isset($_POST['dbtc_checkbox'])){
	  if(!get_post_meta($post_id, 'dbtc_checkbox')) add_post_meta($post_id, 'dbtc_checkbox', 1);
  }else{
	  delete_post_meta($post_id, 'dbtc_checkbox');
  }
}
//add_action( 'save_post', 'dbtc_save_post' );

/*
	Add checkbox to the screen settings.
*/
function dbtc_screen_settings($current, $screen)
{
	$desired_screen = convert_to_screen('post.php');
	if ( $screen->id == $desired_screen->id )
	{
		$checked = "";
  		if(isset($_GET['post']) && get_post_meta($_GET['post'], 'dbtc_disable_visual') != false) $checked = ' checked="checked" ';
		
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
	update the dbtc_checkbox custom field if our "trigger" is passed
*/
function dbtc_template_redirect()
{	
	if(is_admin())
	{
		$dbtc_disable_visual = $_REQUEST['dbtc_disable_visual'];
		$post_id = $_REQUEST['post'];
		if($post_id && $dbtc_checkbox !== NULL)
		{														
			echo "(" . $dbtc_disable_visual . ", " . $post_id . ")";
			
			update_post_meta($post_id, "_dbtc_disable_visual", intval($dbtc_disable_visual));
			
			//this is called via Ajax, so just exit here
			exit;
		}
	}
}
add_action("init", "dbtc_template_redirect", 1);
?>

