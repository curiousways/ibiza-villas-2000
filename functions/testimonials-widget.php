<?php

// Creating the widget
class testimonials_widget extends WP_Widget {

	function __construct() {

		parent::__construct(

			// Base ID of your widget
			'testimonials',

			// Widget name will appear in UI
			__('Testimonials widget', 'testimonials_widget'),

			// Widget description
			array( 'description' => __( 'Widget to show the testimonials', 'testimonials_widget' ), )

		);

	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {

		$args = array(
		    'post_type' => 'testimonials',
		    'posts_per_page' => 4
		);

		$testimonial_query = new WP_Query( $args );

		?>

		<?php if ($testimonial_query->have_posts()) : ?>
		<div class="testimonials">

			<div class="text-center">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/tripadvisor_x2.png" alt="TripAdvisor Testimonials" class="tripadvisor-logo">
				<div class="slider">
					<?php while ($testimonial_query->have_posts()) : $testimonial_query->the_post(); ?>
						<div>
							<div class="testimonial">
								<blockquote><?php the_title(); ?></blockquote>
								<small><?php the_field('testimonial_date'); ?></small>
							</div>
						</div>

					<?php endwhile; ?>
				</div>
			</div>

		</div><!-- ./testimonials -->
		<?php endif; ?>


		<?php wp_reset_query();

	}

	// Widget Backend
	public function form( $instance ) { ?>

		<p><?php _e('This widget has no options.' ); ?></p>

		<?php

	}

} // Class wpb_widget ends here

// Register and load the widget
function testimonials_load_widget() {
	register_widget( 'testimonials_widget' );
}
add_action( 'widgets_init', 'testimonials_load_widget' );

?>