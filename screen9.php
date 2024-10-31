<?php
/*
 Plugin Name: Screen9 Video Integration
 Plugin URI: http://adeprimo.se
 Description: A simple plugin for uploading and displaying Screen9 video content
 Version: 0.1
 Author: Adeprimo
 Author URI: http://www.adeprimo.se
 License: GPL2
 Text Domain: screen9
 */

/*  Copyright 2013  ADEPRIMO  (email : OLA.LIDMARK-ERIKSSON@ADEPRIMO.SE)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once 'api.php';
require_once 'screen9_util.php';
require_once 'screen9_admin.php';
require_once 'screen9_article_meta_box.php';
require_once 'screen9_video_list_widget.php';

define("SCREEN9_OBJ_CACHE_GRP",     "screen9");
define("SCREEN9_VIDEO_POST_TYPE",     "screen9_video");
define("SCREEN9_TEXT_DOMAIN",     "screen9");

add_action( 'init', 'create_post_type' );

function create_post_type() {
	load_plugin_textdomain(SCREEN9_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages');
	
	register_post_type( SCREEN9_VIDEO_POST_TYPE,
	array(
			'labels' => array(
				'name' => __( 'Videos', SCREEN9_TEXT_DOMAIN ),
				'singular_name' => __( 'Video', SCREEN9_TEXT_DOMAIN ),
				'add_new' => __( 'Create new' , SCREEN9_TEXT_DOMAIN )
	),
		'public' => true,
		'has_archive' => true,
		'menu_icon' => plugins_url( 'images/video.png' , __FILE__ ),
		'supports' => array( 'title'),
	)
	);
}

add_action( 'admin_init', 'add_custom_metabox' );
add_action( 'admin_enqueue_scripts', 'init_admin_js' );
add_action( 'save_post', 'save_video' );
add_action( 'publish_post ', 'publish_video' );
add_action( 'admin_init ', 'screen9_internationalization');

add_filter( 'template_include', 'include_template_function', 1 );

function add_custom_metabox() {	
	add_meta_box( 'custom-metabox', __( 'Video data', SCREEN9_TEXT_DOMAIN ), 'custom_metabox', SCREEN9_VIDEO_POST_TYPE, 'normal', 'high' );
}

function custom_metabox() {
	global $post;	
	$videoid = get_post_meta( $post->ID, 'videoid', true );

	if(!isset($videoid)||$videoid===''){
		
		$staticfields = array(
		    'userid' => get_option('screen9_user_id'),
		    'mediatype' => 'video',
		    'returnmediaid' => 'true',
		);
		
		$mutablefields = array("failure_url", "success_url", "categoryid", "description");
		
		$arguments = array(
			3600, $staticfields, $mutablefields
		);
		$res = screen9_call("getUploadURL", $arguments);
		$videoid = $res['mediaid'];
		$action = sprintf("%s?uploadid=%s",$res['url'],$res['mediaid']);
	}else{
		$options = array(
		    'embedtype' => 'universal'
		);
		$arguments = array(
			$videoid, 0, $options
		);
		$video_presentation = screen9_call("getPresentation", $arguments);
		echo($video_presentation['universal']);		
		
		$options = array(
		    'embedtype' => 'playertag'
		);
		$arguments = array(
			$videoid, 0, $options
		);
		
		$video_presentation_playertag = screen9_call("getPresentation", $arguments);
		
		$video_details = screen9_call("getMediaDetails", array($videoid));
		$action = "/wp-content/plugins/Screen9/api.php?videoid=".$videoid;		
	}
		
	?>

	<p>
		<label for="videoid">Videoid:<br /> <input id="videoid" size="37"
			name="videoid" value="<?php echo($videoid);?>" /> </label>
	</p>
<?php if(isset($res)){?>
</form>
<form id="upload_form" action="<?=$action?>" method="POST" enctype="multipart/form-data">
<input type="hidden" name="auth" value="<?php echo($res['auth']) ?>">
<input type="hidden" value="<?php echo(get_bloginfo('url'));?>?success=true" name="success_url">
<input type="hidden" value="<?php echo(get_bloginfo('url'));?>?success=false" name="failure_url">
<?php }?>
	<p>
		<label for="video_description"><?php _e('Description:', SCREEN9_TEXT_DOMAIN);?><br /></label><textarea	id="video_description" name="description" cols="100" rows="10"><?php if( $video_details['description'] ) { echo $video_details['description']; } ?></textarea>
	</p>
	<p>
		<label for="categoryid"><?php _e('Category:', SCREEN9_TEXT_DOMAIN); ?><br /></label>
		<select name="categoryid" id="categoryid" class="select">
		<?php listCategories($video_details['categoryid']); ?>
		</select>
		
	</p>
		<?php if(isset($res)){?>
	<p>
		<input type="file" name="videofile" id="videofile" class="file">
		<input id="video_upload_btn" type="button"
			value="<?php _e('Upload', SCREEN9_TEXT_DOMAIN); ?>" />
	</p>
			<?php } ?>		
	<p>
		<label>Info:<br /></label>
		<b>Status: </b><span id="statustext"></span>
		<progress></progress>
		<br/>		
		<?php
		$seconds=round($video_details['duration']/1000);
		$interval = new DateInterval("PT{$seconds}S");
		echo(sprintf("<b>%s: </b>%02d:%02d:%02d<br/>",__("Length", SCREEN9_TEXT_DOMAIN), $interval->h,$interval->m,$interval->s));
		echo(sprintf("<b>%s: </b>%d<br/>",__("Displays", SCREEN9_TEXT_DOMAIN), $video_details['downloads_started']));
		echo(sprintf("<b>%s: </b>%s<br/>",__("File name", SCREEN9_TEXT_DOMAIN), $video_details['filename']));
		echo(sprintf("<b>Format: </b>%d*%d<br/>", $video_details['width'],$video_details['height']));
		?>
	</p>
		<?php if(! isset($res)){ ?>
	<p>
		<label><?php _e('Embed code:', SCREEN9_TEXT_DOMAIN); ?><br /></label>
		<input size="100" readonly="readonly" id="embed_embed" value="<?php echo(esc_attr($video_presentation_playertag['playertag'])); ?>" onclick="this.select()">
	</p>
	<p>
		<label onclick="jQuery('#screen9Thumbs').toggle();"><?php _e('Thumbnails', SCREEN9_TEXT_DOMAIN); ?> &#x25BC;<br /></label>
		<div id="screen9Thumbs" style="display:none;height: 370px;">
			<p><i><?php _e('Select thumbnail', SCREEN9_TEXT_DOMAIN); ?></i></p>
			<?php listThumbnails($video_details['thumbnails'],$video_details['thumbnail'],$video_details['images']); ?>
		</div>
	</p>
	<?php
		} 
}

function save_video( $post_id ) {
	global $post;

	if( $_POST && get_post_type( $post ) == SCREEN9_VIDEO_POST_TYPE) {
		
		$title = $_POST['post_title'];
		$videoid = $_POST['videoid'];
		$description=$_POST['description'];
		$category=$_POST['categoryid'];
		$images = explode(",", $_POST['screen9_image']);
	

		update_post_meta( $post->ID, 'videoid', $videoid );
		
		$arguments = array(
			$videoid, "title", $title
		);

		screen9_call("setMediaDetails", $arguments);

		if(isset($description)){
			$descriptionArguments = array(
				$videoid, "description", $description
			);
	
			screen9_call("setMediaDetails", $descriptionArguments);
		}
		
		if(isset($category)){
			$categoryArguments = array(
				$videoid, $category, ''
			);
		
			screen9_call("moveMedia", $categoryArguments);
		}
	
		if(isset($_POST['screen9_image'])){
			$thumbnailArguments = array(
				$videoid, "picsearch.thumbnail", $images[0], ''
			);
	
			screen9_call("setProperty", $thumbnailArguments);
	
			$titleImageArguments = array(
				$videoid, "picsearch.titleimage", $images[1], ''
			);
	
			screen9_call("setProperty", $titleImageArguments);
			
		}
		
		$post_status_from_request = $_POST['post_status'];

		if($post_status_from_request=='future'){
			$moderation_state = "approved";			
		}

		else if($post_status_from_request=='publish'){
			$moderation_state = "approved";
		}

		else if($post_status_from_request=='pending'){
			$moderation_state = "unmoderated";
		}

		else {
			$moderation_state = "non-approved";
		}		

		$moderationArguments = array(
			$videoid, "picsearch.moderation", $moderation_state, ''
		);

		screen9_call("setProperty", $moderationArguments);
		
		setPubDate($videoid);

	}
}

function init_admin_js() {
	/* Register our script. */
	wp_enqueue_script( 'screen9_js', plugins_url( 'scripts/admin.js', __FILE__ ), array('jquery') );
	wp_localize_script( 'screen9_js', 'ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ),
            		'error_msg_upload' => __('You have not uploaded a file', SCREEN9_TEXT_DOMAIN)));
}

function include_template_function( $template_path ) {
    if ( get_post_type() == SCREEN9_VIDEO_POST_TYPE ) {
        if ( is_single() ) {
            if ( $theme_file = locate_template( array ( 'single-screen9_video.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/single-screen9_video.php';
            }
        }
    	elseif ( is_archive() ) {
            if ( $theme_file = locate_template( array ( 'archive-screen9_video.php' ) ) ) {
                $template_path = $theme_file;
            } else { $template_path = plugin_dir_path( __FILE__ ) . '/archive-screen9_video.php';
 
            }
    	}
    }
    return $template_path;
}

function screen9_display_video($videoid){
	$options = array(
	    'embedtype' => 'universal'
	);
	$arguments = array(
		$videoid, 0, $options
	);
	if(isset($videoid) && $videoid!=""){
		$video_presentation = screen9_call("getPresentation", $arguments, $videoid."_Presentation");
		echo($video_presentation['universal']);
	}		
}

add_action('wp_ajax_screen9_video_status', 'screen9_video_status_callback');

function screen9_video_status_callback()
{
	$videoid=$_POST['videoid'];
	$video_details = screen9_call("getMediaDetails", array($videoid));
	header('Content-type: application/json');
	echo json_encode($video_details);
	die();
}
?>