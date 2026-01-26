<?php

// Creating the widget 
class rude_brands_footer_widget extends WP_Widget {

	function __construct() {
		
		parent::__construct(
			
			// Base ID of your widget
			'rude_brands_footer', 

			// Widget name will appear in UI
			__('Rude Brands Footer widget', 'rude_brands_footer_widget'), 

			// Widget description
			array( 'description' => __( 'Widget to show the related content to the current page', 'rude_brands_footer_widget' ), ) 

		);

	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) { 
		$args = array(
			    'post_type' 	=> 'rude_brand',
				'showposts'		=> '-1',	    

			);

		$rude_brands_query = new WP_Query( $args );

			if ($rude_brands_query->have_posts()) : ?>
				<div class="row">
				<div class="footer-brands">
					<div class="row">
						<ul>
				

							<?php 
								// reset everything
								$link_category = "Nope";
								$current_link_category = "";
								$i = 0;
							 ?>
							<?php while ($rude_brands_query->have_posts()) : $rude_brands_query->the_post(); ?>


								
									<li>
										<a href="<?php the_permalink(); ?>" class="fc-tooltip" title='
										<div class="footer-brand-info <?php the_field("rude_brand_colour"); ?>">
											<?php 
											$attachment_id 	= get_field("rude_brand_more_info_image"); 
											$size			= "medium";
											?>
											<a href="<?php the_field("rude_brand_link"); ?>">
											<?php echo wp_get_attachment_image( $attachment_id, $size); ?>
											</a>
											<div class="footer-brand-detail">
												<h3><a href="<?php the_field("rude_brand_link"); ?>"><?php the_title(); ?></a></h3>
												<p><?php echo  wp_trim_words(get_field("rude_brand_about_the_brand"), 20); ?></p>
												
												<?php if( get_field('rude_brand_link') ): ?>
													<a href="<?php the_field("rude_brand_link"); ?>" class="button">Find out more</a>
												<?php endif; ?>
											</div>
										</div>'>
											<img src="<?php the_field('rude_brand_logo'); ?>" alt="<?php the_title(); ?>">

										</a>
										
									</li>

										

										
							<?php endwhile; ?>

						</ul>
					</div>
				</div>
				</div>
		 	<?php endif ; ?>

		<?php wp_reset_query();

	}
			
	// Widget Backend 
	public function form( $instance ) {

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'New title', 'rude_brands_footer_widget' );
		}

		// Widget admin form
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<?php 

	}
		
		// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}

} // Class wpb_widget ends here

// Register and load the widget
function rude_brands_footer_load_widget() {
	register_widget( 'rude_brands_footer_widget' );
}
add_action( 'widgets_init', 'rude_brands_footer_load_widget' );

?>