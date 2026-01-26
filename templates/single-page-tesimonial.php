<?php

$currentPostId = 2;



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
<div class="testimonials">

		<div class="row">

			<div class="small-12 large-6 medium-centered text-center columns">

				<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/tripadvisor_x2.png" alt="TripAdvisor Testimonials" class="tripadvisor-logo">

				<div class="slider">

					<?php while ($testimonial_query->have_posts()) : $testimonial_query->the_post(); ?>
						<div>
							<div class="testimonial">
								<blockquote><?php the_title(); ?></blockquote>

								<small><?php the_field('testimonial_date'); ?></small>
							</div>
							<?php if( get_field('url') ): ?>
								<a target="_blank" href="<?php the_field('url'); ?>" class="button green">Read our reviews</a>
							<?php endif; ?>


						</div>

					<?php endwhile; ?>

				</div>

			</div>

		</div><!-- ./row -->

</div><!-- ./testimonials -->
<?php endif; ?>




<?php wp_reset_query(); ?>