<?php get_header(); ?>


<div class="bg-middle">

	<div class="row">
		<div class="content-wrap main" role="main">

			<div class="row">

				<div class="small-12 medium-8 large-8 columns">

					<h2>Sorry...we can't find that page</h2>
					
					<p>...why not try searching for what you were looking for:</p>
					
					<form role="search" method="get" class="error-page search-form" action="<?php echo home_url( '/' ); ?>">
						<div class="input-group">
							<input type="search" class="search-field" placeholder="Search" value="" name="s" title="Search for:" />
							<button type="submit" class="search-submit hidden"><span class="icon-arrow-right"></span></button>
						</div>
					</form>


					<?php get_template_part( 'templates/latest-news' ); ?>


				</div>
				<div class="large-4 columns">
					<?php get_sidebar('primary-sidebar'); ?>
					<?php get_sidebar(); ?>
				</div>
			</div><!--/.row -->

		</div><!-- ./columns -->
	</div><!--/.row -->

</div><!--/.bg-middle -->


<?php get_footer(); ?>