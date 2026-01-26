<?php

// Creating the widget 
class faq_categories_widget extends WP_Widget {

	function __construct() {
		
		parent::__construct(
			
			// Base ID of your widget
			'faq_categories', 

			// Widget name will appear in UI
			__('FAQ categories widget', 'faq_categories_widget'), 

			// Widget description
			array( 'description' => __( 'Widget to show the FAQ categories', 'faq_categories_widget' ), ) 

		);

	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) { 

			/* Loop through posts with same location but exclude current post */
			?>
			<h3><?php echo $instance['title']; ?></h3>

			<?php
			$post_type = 'faq';

			// Get all the taxonomies for this post type
			$taxonomies = get_object_taxonomies( (object) array( 'post_type' => $post_type ) );

			foreach( $taxonomies as $taxonomy ) : 

			    // Gets every "category" (term) in this taxonomy to get the respective posts
			    $terms = get_terms( $taxonomy ); ?>
				<ul>
			    <?php foreach( $terms as $term ) : ?>

			        <li><a href="<?php echo '/frequently-asked-questions?filter=' . $term->slug; ?>"><?php echo $term->name; ?></a></li>

			    <?php 
			    endforeach; ?>
			    </ul>


			<?php endforeach;

	}
			
	// Widget Backend 
	public function form( $instance ) {

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'New title', 'faq_categories_widget' );
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
function faq_categories_load_widget() {
	register_widget( 'faq_categories_widget' );
}
add_action( 'widgets_init', 'faq_categories_load_widget' );

?>