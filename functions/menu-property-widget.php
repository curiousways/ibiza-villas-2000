<?php

// Creating the widget 
class menu_property_widget extends WP_Widget {

function __construct() {
parent::__construct(
// Base ID of your widget
'menu_property', 

// Widget name will appear in UI
__('Menu property widget', 'menu_property_widget'), 

// Widget description
array( 'description' => __( 'Widget to show the relevant properties based on tick boxes within the properties page', 'menu_property_widget' ), ) 
);
}

// Creating widget front-end
// This is where the action happens
public function widget( $args, $instance ) {
	
	$menu_property_type = $instance['menu_property_type'];
	$menu_property_area = $instance['menu_property_area'];

	$args = array(
	    'post_type' 		=> $menu_property_type,
	    'posts_per_page' 	=> 15,
	    'property_location' => $menu_property_area,
	    'orderby'			=> 'title',
	    'order'				=> 'ASC',
	    'meta_query'		=> array(
			array(
				'key'	 		=> 'property_show_in_menu',
				'value'	  		=> 'yes',
				'compare' 		=> 'LIKE',
			),
		),
	);

	$menu_property_query = new WP_Query( $args );

	if ($menu_property_query->have_posts()) : ?>
	<?php // We don't need opening UL because uber menu does that for us ?>
			<?php while ($menu_property_query->have_posts()) : $menu_property_query->the_post(); ?>
				<li>
					<a href="<?php the_permalink(); ?>" alt="<?php the_title(); ?>"><?php the_title(); ?> (Sleeps <?php the_field('property_sleeps'); ?>)</a>
				</li>
			<?php endwhile; ?>
 	<?php endif ;

	wp_reset_query();
}
		
// Widget Backend 
public function form( $instance ) {
	if ( isset( $instance[ 'title' ] ) ) {
		$title = $instance[ 'title' ];
	}
	else {
		$title = __( 'New title', 'menu_property_widget' );
	}

	//Check if menu_area exists, if its null, put "new menu_area" for use in the form
    if ( isset( $instance[ 'menu_property_type' ] ) ) {
        $menu_property_type = $instance[ 'menu_property_type' ];
    }
    else {
        $menu_property_type = __( 'new menu_property_type', 'menu_property_widget' );
    }

	//Check if menu_area exists, if its null, put "new menu_area" for use in the form
    if ( isset( $instance[ 'menu_property_area' ] ) ) {
        $menu_property_area = $instance[ 'menu_property_area' ];
    }
    else {
        $menu_property_area = __( 'new menu_property_area', 'menu_property_widget' );
    }

	// Widget admin form
	?>
	<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<p>
	<label for="<?php echo $this->get_field_id( 'menu_property_type' ); ?>"><?php _e( 'Property type:' ); ?></label> 
	
	<select class="widefat" id="<?php echo $this->get_field_id( 'menu_property_type' ); ?>" name="<?php echo $this->get_field_name( 'menu_property_type' ); ?>" type="text" value="<?php echo esc_attr( $menu_property_type ); ?>">
		<option value="villas">Villas</option>
		<option value="apartments">Apartments</option>
	</select>
	</p>
	<p>
	<label for="<?php echo $this->get_field_id( 'menu_property_area' ); ?>"><?php _e( 'Property location:' ); ?></label> 
	
	<select class="widefat" id="<?php echo $this->get_field_id( 'menu_property_area' ); ?>" name="<?php echo $this->get_field_name( 'menu_property_area' ); ?>" type="text" value="<?php echo esc_attr( $menu_property_area ); ?>">
		<option value="chamonix">Chamonix</option>
		<option value="avoriaz">Avoriaz</option>
		<option value="morzine">Morzine</option>
	</select>
	</p>
	<?php 
}
	
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['menu_property_type'] = ( ! empty( $new_instance['menu_property_type'] ) ) ? strip_tags( $new_instance['menu_property_type'] ) : '';
		$instance['menu_property_area'] = ( ! empty( $new_instance['menu_property_area'] ) ) ? strip_tags( $new_instance['menu_property_area'] ) : '';
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}	
} // Class wpb_widget ends here

// Register and load the widget
function menu_property_load_widget() {
	register_widget( 'menu_property_widget' );
}
add_action( 'widgets_init', 'menu_property_load_widget' );

?>