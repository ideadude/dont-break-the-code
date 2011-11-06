<?php
/*
Plugin Name: Don't Break The Code
Plugin URI: http://www.strangerstudios.com/wp/dont-break-the-code/
Description: Disable the Visual editor on a per post basis.
Version: .1
Author: Stranger Studios (WordCamp Philly)
Author URI: http://www.strangerstudios.com
*/

function dbtc_load_post()
{
	$cookie_path = str_replace('/wp-admin/post.php','/',$_SERVER['PHP_SELF']);
	setcookie('wp-settings-1','m5=3&editor=html&m9=o&m1=o',0,$cookie_path);
		
	add_filter("format_to_edit", "dbtc_format_to_edit");
}
add_action("load-post.php", "dbtc_load_post");

function dbtc_format_to_edit($content)
{
	if(isset($_GET['post']) && get_post_meta($_GET['post'], 'dbtc_checkbox') != false){
		$cookie_path = str_replace('/wp-admin/post.php','/',$_SERVER['PHP_SELF']);
		setcookie('wp-settings-1','m5=3&editor=html&m9=o&m1=o',0,$cookie_path);
		
		add_filter('admin_footer', 'dbtc_admin_footer');
	}
	
	return $content;
}

function dbtc_admin_footer()
{
?>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			switchEditors.go('content', 'html');
			jQuery('#edButtonPreview').attr('onclick', 'none');
			var edButtonPreviewHTML = jQuery('#edButtonPreview').html();
			jQuery('#edButtonPreview').html('<span style="text-decoration:line-through">'+edButtonPreviewHTML+'</span>');
		});
	</script>
<?php
}

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
       _e(" Disable Visual Editor", 'dbtc_textbox' );
  echo '</label> ';
}
add_action( 'admin_init', 'dbtc_add_custom_box', 1 );

function dbtc_save_post( $post_id ) {
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;

  if ( !wp_verify_nonce( $_POST['dbtc_noncename'], plugin_basename( __FILE__ ) ) )
      return;

  if ( ! current_user_can( 'edit_post' ) ) {
  	return;
  }

  if(isset($_POST['dbtc_checkbox'])){
	  if(!get_post_meta($post_id, 'dbtc_checkbox')) add_post_meta($post_id, 'dbtc_checkbox', 1);
  }else{
	  delete_post_meta($post_id, 'dbtc_checkbox');
  }
}
add_action( 'save_post', 'dbtc_save_post' );
?>

