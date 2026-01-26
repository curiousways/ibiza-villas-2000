<?php

// Creating the widget 
class similar_property_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
		// Base ID of your widget
		'similar_property', 

		// Widget name will appear in UI
		__('Similar property widget', 'similar_property_widget'), 

		// Widget description
		array( 'description' => __( 'Widget to show the similar content to the current page', 'similar_property_widget' ), ) 
		);
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {
		global $post;


		// get the current location from the terms
		$current_location = get_the_terms( $post->ID, 'property_location');
		// grab the slug
		$currentSlug = $current_location[0]->slug;
		// is it a villa or an apartment?! WHO KNOWS?! 
		// This guy does \/
		$current_property_type = get_post_type();

		$propertyTypeQuery = (!empty($current_property_type)) ? 'fwp_property_types=' . $current_property_type : "";
		$propertyLocationQuery = (!empty($currentSlug)) ? 'fwp_property_locations=' . $currentSlug : "";

		//  Construct the query string so that we can return all the similar properties
		$queryString = "/property-results/";
		if(!empty($propertyTypeQuery) && !empty($propertyLocationQuery)) {
			$queryString .= '?'. $propertyTypeQuery . '&' . $propertyLocationQuery;
		} elseif( !empty($propertyTypeQuery) && empty($propertyLocationQuery) ) {
			$queryString .= '?'. $propertyTypeQuery;
		} elseif( empty($propertyTypeQuery) && !empty($propertyLocationQuery) ) {
			$queryString .= '?'. $propertyLocationQuery;
		}





		/* Loop through posts with same location but exclude current post */
		$args = array(
		    'post_type' => $current_property_type,
			'tax_query' => array(
				array(
					'taxonomy' => 'property_location',
					'field'    => 'slug',
					'terms'    => $currentSlug,
				),
			),
			'orderby' => 'asc',
			'showposts'=> '7',
			'post__not_in' => array($post->ID),
			'meta_key'		=> 'property_for_sale', //filter out for sale properties, true is for sale, 0 not for sale (rental).
			'meta_value'	=> 0
		);

		$similar_property_query = new WP_Query( $args );

		if ($similar_property_query->have_posts()) : ?>
			<h3><?php echo $instance['title']; ?></h3>
			<ul>
			<?php while ($similar_property_query->have_posts()) : $similar_property_query->the_post(); ?>
				
				<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>

				<?php endwhile; ?>
			</ul>
			<p><a href="<?php echo $queryString; ?>" class="button">Show all</a></p>
	 	<?php endif ;

		wp_reset_query();
	}
			
	// Widget Backend 
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'similar_property_widget' );
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
function similar_property_load_widget() {
	register_widget( 'similar_property_widget' );
}
add_action( 'widgets_init', 'similar_property_load_widget' );

?>