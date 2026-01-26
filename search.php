<?php
get_header();
the_post();
?>

<!-- SEARCH -->
	<div class="row">

		<div class="main">

			<div class="row">

						<div class="small-12 medium-12 large-8 columns">

							<?php if ( have_posts() ) : ?>
 
				                <header class="page-header">
									<h2><?php echo $wp_query->found_posts; ?> <?php _e( 'Search Results Found For', 'locale' ); ?>: "<?php the_search_query(); ?>"</h2>

				                </header><!-- .page-header -->
				
				 
				                <?php /* Start the Loop */ ?>
				                <?php while ( have_posts() ) : the_post(); ?>
				                    <?php get_template_part( 'content', 'search' ); ?>
			 
				                <?php endwhile; ?>
				
				 
				            <?php else : ?>
				 
				            	<p>We couldn't find anything matching that term, please try another.</p>

				            <?php endif; ?>

						</div>
						
						<div class="large-4 medium-12 columns">
							<?php get_sidebar('primary-sidebar'); ?><?php get_sidebar(); ?>
						</div>
			</div><!--/.row -->

		</div><!--/.main -->

	</div><!--/.row -->

<?php get_footer(); ?>


