<?php
get_header();
?>
<div id="primary" class="site-content">
	<div id="content" role="main">
		<header class="page-header">
			<h1 class="page-title">Videor</h1>
		</header>
		<?php while ( have_posts() ) : the_post(); ?>
	
		<h2>
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?> </a>
		</h2>
	
		<hr />
		<?php endwhile; ?>

		
		 <?php global $wp_query;
        if ( isset( $wp_query->max_num_pages ) && $wp_query->max_num_pages > 1 ) { ?>
            <nav id="<?php echo $nav_id; ?>">
                <div class="nav-previous"><?php next_posts_link( '<span class="meta-nav">&larr;</span> Äldre videos'); ?></div>
                <div class="nav-next"><?php previous_posts_link( 'Nyare videos <span class= "meta-nav">&rarr;</span>' ); ?></div>
            </nav>
        <?php };?>
	</div>
</div>
<?php
get_sidebar();
get_footer();
?>