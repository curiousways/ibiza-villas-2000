<?php


$args = array(
    'post_type' => 'testimonials',
    'posts_per_page' => 10,
    'meta_query'		=> array(
	array(
		'key' => 'related_property',
		'value' => '"' . get_the_ID() . '"',
		'compare' => 'LIKE'
		)
	)
);

$testimonial_query = new WP_Query( $args );

?>

<?php if ($testimonial_query->have_posts()) : ?>


<div class="testimonials single-property-testimonial">

		<div class="row">

			<div class="small-12 text-center columns">


		<div class="small-10 large-10 medium-centered large-centered small-centered text-center columns">
				<h3 class="text-center">TESTIMONIALS</h3>
				<?php echo do_shortcode('[google-reviews-pro place_photo=https://lh3.googleusercontent.com/p/AF1QipPre9XldywoVbblw3AtZQ9h5NeG28VMNcYsxGd9=s1600-w300-h300 place_name="Ibiza Villas 2000" place_id=ChIJ7Z5Sp9xAmRIRxs1jmPMO3Mo auto_load=true rating_snippet=true min_filter=4 write_review=true view_mode=slider open_link=true sort=1 hide_float_badge=true lazy_load_img=true]');?>
		</div>

	
				<!--- <div class="slider">ic

					<?php while ($testimonial_query->have_posts()) : $testimonial_query->the_post(); ?>
						<div>
							<div class="testimonial">
								<blockquote><?php the_title(); ?></blockquote>

								<small><?php the_field('testimonial_date'); ?></small>
							</div>
						<?php if( get_field('url') ): ?>
							<a target="_blank" href="<?php the_field('url'); ?>" class="button">Read our reviews</a>
						<?php endif; ?>
						</div>

					<?php endwhile; ?>

				</div> -->

			</div>

		</div><!-- ./row -->

</div><!-- ./testimonials -->
<?php endif; ?>




<?php wp_reset_query(); ?>