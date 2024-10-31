<?php
class Screen9VideoWidget extends WP_Widget{
	function __construct(){
		$widget_ops = array('classname' => 'Screen9VideoWidget' );
    	$this->WP_Widget('Screen9VideoWidget', 'Screen9 Video List Widget', $widget_ops);
	}
	function form($instance){
		$instance = wp_parse_args( (array) $instance, array( 'title' => '','count' => '') );
    	$title = $instance['title'];
    	$count = $instance['count'];
    
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', SCREEN9_TEXT_DOMAIN); ?><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label>
  <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Number of videos to display:', SCREEN9_TEXT_DOMAIN); ?><input type="number" class="widefat" id="<?php echo $this->get_field_id('rssFeed'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="url" value="<?php echo esc_attr($count); ?>" /></label></p>
<?php
	}
	function update($new_instance, $old_instance){
		$instance = $old_instance;
    	$instance['title'] = $new_instance['title'];
    	$instance['count'] = $new_instance['count'];
    	return $instance;
	}
	
	function widget($args, $instance){
	?>
	<h2><?php echo $instance['title']; ?></h2>
		<ul>
			<?php echo(listScreen9Videos($instance['count'])); ?>
		</ul>
	</aside>
	<?php
	}
}

function listScreen9Videos($noItems){
	$key = 'screen9-list-videos';
    $response = wp_cache_get( $key, SCREEN9_OBJ_CACHE_GRP );

    if ( false === $response ) {
	
		$args = array(
				'post_type' => SCREEN9_VIDEO_POST_TYPE,
				'posts_per_page'=> $noItems
		);
		
		$posts = get_posts($args);
		$i=0;
		foreach ( $posts as $p){
			$videoid = get_post_meta( $p->ID, 'videoid', true );
			$response.=('<li>');
			if($i<3){
				$video_detail = screen9_call("getMediaDetails", array($videoid));
				if($video_detail['thumbnail']!='F'){
					$response.=(sprintf("<img src='%s' alt='%s'/><br/>",$video_detail['thumbnail'], $p->post_title));
				}
			}
	    	$response.= sprintf("<a class=\"foot-link\" href=\"%s\">%s</a><time datetime=\"%s\">%s</time></li>",
	        get_permalink($p->ID), 
	        $p->post_title,
	        mysql2date("Y-m-d\TH:iP",$p->post_date),
	        mysql2date( get_option( 'date_format' ), $p->post_date ));
	        $i++;        
		}
		if ( !empty( $response ) ){
        	wp_cache_set( $key, $response, SCREEN9_OBJ_CACHE_GRP);
		}
    }
	return $response;
}

add_action( 'widgets_init', create_function('', 'return register_widget("Screen9VideoWidget");') );