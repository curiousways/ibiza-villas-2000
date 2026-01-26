<?php

$args = array(
    'post_type' => 'testimonials',
    'posts_per_page' => 10
);

$testimonial_query = new WP_Query( $args );

?>

<?php if ($testimonial_query->have_posts()) : ?>
<div class="testimonials">

	<div class="row">

		<div class="small-12 large-12 medium-centered text-center columns">
			<div class="slider">
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
			</div>
			</div>

		</div><!-- ./row -->

</div><!-- ./testimonials -->
<?php endif; ?>




<?php wp_reset_query(); ?>