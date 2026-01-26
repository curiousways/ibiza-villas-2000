<?php global $spanish_law; ?>
<?php

// Creating the widget 
class featured_properties_widget extends WP_Widget {

	function __construct() {
		
		parent::__construct(
			
			// Base ID of your widget
			'featured_properties', 

			// Widget name will appear in UI
			__('Featured properties widget', 'featured_properties_widget'), 

			// Widget description
			array( 'description' => __( 'Widget to show the featured properties', 'featured_properties_widget' ), ) 

		);

	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {

			/* Loop through posts with same location but exclude current post */

			$post_types = array('villas', 'apartments');


	    if ($spanish_law == 'yes') {
			$args = array(
				'post_type' => $post_types,
				'orderby' => 'title',
				'showposts'=> '5',
				'meta_key'		=> 'property_featured',
				'meta_value'	=> 0,
		        'post__not_in'  => array('5610','2928'),
			);
	    } else {
			$args = array(
				'post_type' => $post_types,
				'orderby' => 'title',
				'showposts'=> '5',
				'meta_key'		=> 'property_featured',
				'meta_value'	=> 0,
			);
	    } 			

			$featured_property = new WP_Query($args);

		?>

		<?php if ( $featured_property->have_posts() ) : ?>

			<h3><?php _e('Featured Properties'); ?></h3>
			
			
			<div class="slider featuredSidebar">

				<?php while ( $featured_property->have_posts() ): $featured_property->the_post(); global $post; ?>

					<?php 
		      			// get the inside of the grid loop to make life easier 
		      			get_template_part( 'templates/loop-grid-part' ); 
		      		?>

				<?php endwhile; ?>

			</div>

		<?php endif; ?>

		<?php wp_reset_postdata(); 

	}
			
	// Widget Backend 
	public function form( $instance ) {

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'New title', 'featured_properties_widget' );
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
function featured_properties_load_widget() {
	register_widget( 'featured_properties_widget' );
}
add_action( 'widgets_init', 'featured_properties_load_widget' );

?>