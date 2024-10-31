<?php
/*Template Name: Screen9 Video Default template
 * Nothing special
 */

get_header(); ?>

<div id="primary" class="site-content">
	<div id="content" role="main">
		<?php 
		$videoid = get_post_meta( get_the_ID(), 'videoid', true );
		$video_details = screen9_call("getMediaDetails", array($videoid), $videoid."_MediaDetails");
		while ( have_posts() ) : the_post(); 
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class($video_details['categoryname']); ?>> 
			<header class="entry-header"><h1><?php the_title(); ?></h1></header>
			<div class="entry-summary">
				<?php echo($video_details['description']); ?>
			</div>
			<?php 
				$options = array(
		    		'embedtype' => 'playertag'
				);
				$arguments = array(
					$videoid, 0, $options
				);
		
				$video_presentation_playertag = screen9_call("getPresentation", $arguments, $videoid."_Presentation");
			?>
			<div class="entry-content">
				<?php echo($video_presentation_playertag['playertag']); ?>
			</div>
			<nav id="nav-single">
				<h3 class="assistive-text">Navigering</h3>
				<span class="nav-previous"><?php previous_post_link( '%link', __( '<span class="meta-nav">&larr;</span> Previous') ); ?></span>
				<span class="nav-next"><?php next_post_link( '%link', __( 'Next <span class="meta-nav">&rarr;</span>') ); ?></span>
			</nav><!-- #nav-single --> <?php get_template_part( 'content-single', get_post_format() ); ?>
			
		</article>
		<?php endwhile; ?>
	</div>
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>