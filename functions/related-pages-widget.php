<?php

// Creating the widget 
class related_pages_widget extends WP_Widget {

	function __construct() {
		
		parent::__construct(
			
			// Base ID of your widget
			'related_pages', 

			// Widget name will appear in UI
			__('Related pages widget', 'related_pages_widget'), 

			// Widget description
			array( 'description' => __( 'Widget to show the related content to the current page', 'related_pages_widget' ), ) 

		);

	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {

		global $post;

		$current_type 		= get_post_type();
		$property_type 		= array();
		$property_location 	= array();
		$show_widget		= FALSE;

		// if we're on a property, show pages associated with type and location
		// if we're on a page, show pages with the same filters

		if( in_array( $current_type, array('villas', 'apartments' ) ) ) {

			// show a list of pages that match the taxonomy and type

			// get the current location from the terms
			$current_location 	= get_the_terms( $post->ID, 'property_location');

			$property_location[] 	= $current_location[0]->slug;

			$property_type[] 		= $current_type;

		} else {

			$property_location = get_field('related_to_location', $post->ID);

			$property_type = get_field('related_to_property_type', $post->ID);

			if(!empty($property_location) && !empty($property_type)) {
				$show_widget = TRUE;
			}
			// $weight 			= get_field('weight', $post->ID);
			
		}
		function orderbyreplace($orderby) {
		    return str_replace('menu_order', 'mt1.meta_value, mt2.meta_value', $orderby);
		}

		if($show_widget) :

			/* Loop through posts with same location but exclude current post */
			$args = array(
			    'post_type' 	=> 'page',
				'showposts'		=> '5',
				'post__not_in' 	=> array($post->ID),
				'meta_key'		=> 'related_link_category',
				'orderby'		=> array( 'meta_value' => 'ASC', 'title' => 'ASC' ),
				    

			);
			$args['meta_query']['relation'] = 'OR';
			foreach ($property_location as $location) {
				$args['meta_query'][] = array(
											'key'		=> 'related_to_location',
											'value'		=> $location,
											'compare'	=> 'LIKE'
										);
			}
			foreach ($property_type as $type) {
				$args['meta_query'][] = array(
											'key'		=> 'related_to_property_type',
											'value'		=> $type,
											'compare'	=> 'LIKE'
										);
			}

			$related_pages_query = new WP_Query( $args );

			if ($related_pages_query->have_posts()) : ?>

				<?php 
					// reset everything
					$link_category = "Nope";
					$current_link_category = "";
					$i = 0;
				 ?>
				<?php while ($related_pages_query->have_posts()) : $related_pages_query->the_post(); 

					// grab the ACF field for link category
					$current_link_category = get_field('related_link_category');

					// If this isn't the first loop, close the previous one if there's no link header
					if( empty($current_link_category) && $i != 0 && $link_category != "") { 
						echo '</ul>'; 
					} 

					// if we havent got a link category, spit these out in a list
					if( empty( $current_link_category ) && $link_category == "Nope" && $i == 0) { 
						echo '<h3>' . $instance['title'] . '</h3><ul>'; 
					// if we have, check to see if we are still within the same group, if not dont print the 
					// title again
					}
					elseif( !empty($current_link_category) && $current_link_category != $link_category) {
						
						if($i != 0) echo '</ul>'; 
						print '<h3>' . $current_link_category . '</h3><ul>';

					} ?>

					<li><a href="<?php the_permalink(); ?>"><?php echo the_title(); ?></a></li>

					<?php 
						$i++;
						$link_category = $current_link_category; 
					?>

					<?php endwhile; ?>

				</ul>

		 	<?php endif ; ?>
	 	<?php endif ; ?>

		<?php wp_reset_query();

	}
			
	// Widget Backend 
	public function form( $instance ) {

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'New title', 'related_pages_widget' );
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
function related_pages_load_widget() {
	register_widget( 'related_pages_widget' );
}
add_action( 'widgets_init', 'related_pages_load_widget' );

?>