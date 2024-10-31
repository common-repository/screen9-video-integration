<?php

add_action( 'add_meta_boxes', 'add_video_chooser' );
add_action( 'save_post', 'screen9_save_postdata' );

/* Adds a box to the main column on the Post and Page edit screens */
function add_video_chooser() {
       add_meta_box( 'screen9_video_chooser', __( 'Video' ), 'screen9_video_chooser_box', 'post', 'side', 'low' );
}

/* Prints the box content */
function screen9_video_chooser_box( $post ) {


  wp_nonce_field( plugin_basename( __FILE__ ), 'myplugin_noncename' );

  $value = get_post_meta( $post->ID, 'screen9_videoid', true );
  echo '<label for="screen9_videoid">';
  _e('Selected video', SCREEN9_TEXT_DOMAIN);
  echo '</label>';
  echo '<select name="screen9_videoid"><option value=""></option>';
  $img = get_latest_media_list($value);
  echo '</select>';
  if($img!=''){
  	echo(sprintf('<hr><img src="%s"/>',$img));
  }
}


function screen9_save_postdata( $post_id ) {
	
	$post_ID = $_POST['post_ID'];
	$data = sanitize_text_field( $_POST['screen9_videoid'] );

	update_post_meta($post_ID, 'screen9_videoid', $data);

}

function get_latest_media_list($current){
	$fields = array('mediaid','title','thumbnail');
	$properties = array(
    	'picsearch.moderation' => 'approved'
    );
	$filter = array(
    	'mediatype' => 'video',
    	'properties' => $properties
    );
	$arguments = array(
			$fields, $filter, 'posted', 50, 1
	);
	$videos = screen9_call("listMedia", $arguments);
	
	foreach ($videos as $video) {
		$selected="";
		if($current==$video['mediaid']){
			$selected = "selected";
			$ret_image = $video['thumbnail'];
		}
		echo(sprintf("<option value='%s' %s>%s</option>", $video['mediaid'], $selected, $video['title']));
		$i++;
	}
	
	return $ret_image;
}
?>