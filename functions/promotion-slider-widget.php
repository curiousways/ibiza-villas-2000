<?php

// Creating the widget 
class promotion_slide_widget extends WP_Widget {

function __construct() {
parent::__construct(
// Base ID of your widget
'promotion_slide', 

// Widget name will appear in UI
__('Promotion slider widget', 'promotion_slide_widget'), 

// Widget description
array( 'description' => __( 'Widget to show the related slide to the current menu', 'promotion_slide_widget' ), ) 
);
}

// Creating widget front-end
// This is where the action happens
public function widget( $args, $instance ) {

	$menu_area = $instance['menu_area'];



	
	$args = array(
	    'post_type' => 'promotion_slider',
	    'posts_per_page' => 3,
		'meta_query' => array(
		    'relation' => 'OR',
		    array(
		        'key' => 'menu_area',
		        'value' => $menu_area,
		        'compare' => 'LIKE'
		    ),
	    )
	);

	$promotion_slide_query = new WP_Query( $args );

	if ($promotion_slide_query->have_posts()) : ?>
		<div class="slider menu promotion-slider">

			<?php while ($promotion_slide_query->have_posts()) : $promotion_slide_query->the_post(); ?>
			<?php 

				$BGimage = get_field('background_image');
				$BGurl = $BGimage['url'];
				
				$mainImage = get_field('main_image');
				$mainImageurl = $mainImage['url'];
				
				$btnColour = get_field('button_colour');

				$link = get_field('link');
				if( $link ) {				    
					$link = get_permalink( $link->ID  );
				}
				?>

				<div style="background: url(<?php echo $BGurl; ?>);">

					<div class="slider-top">

						<img src="<?php echo $mainImageurl; ?>">
					</div>

					<div class="slider-detail">

						<h3><?php the_field('strap_line'); ?></h3>
						<a href="<?php echo $link; ?>" class="button <?php echo $btnColour; ?>">
							Find out more
						</a>

					</div>

				</div>

			<?php endwhile; ?>

		</div>
 	<?php endif ;

	wp_reset_query();
}
		
// Widget Backend 
public function form( $instance ) {
	if ( isset( $instance[ 'title' ] ) ) {
		$title = $instance[ 'title' ];
	}
	else {
		$title = __( 'New title', 'promotion_slide_widget' );
	}

	//Check if menu_area exists, if its null, put "new menu_area" for use in the form
    if ( isset( $instance[ 'menu_area' ] ) ) {
        $menu_area = $instance[ 'menu_area' ];
    }
    else {
        $menu_area = __( 'new menu_area', 'wpb_widget_domain' );
    }


	// Widget admin form
	?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'menu_area' ); ?>"><?php _e( 'Menu area / sidebar:' ); ?></label> 
		
		<select class="widefat" id="<?php echo $this->get_field_id( 'menu_area' ); ?>" name="<?php echo $this->get_field_name( 'menu_area' ); ?>" type="text" value="<?php echo esc_attr( $menu_area ); ?>">
			<option value="villas" 	<?php echo ($menu_area=='villas')?'selected':''; ?>>Villas</option>
			<option value="apartments"	<?php echo ($menu_area=='apartments')?'selected':''; ?>>Apartments</option>
			<option value="brands"		<?php echo ($menu_area=='brands')?'selected':''; ?>>Brands</option>
			<option value="sidebar"		<?php echo ($menu_area=='sidebar')?'selected':''; ?>>Sidebar</option>
		</select>
	</p>
	<?php 
}
	
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['menu_area'] = ( ! empty( $new_instance['menu_area'] ) ) ? strip_tags( $new_instance['menu_area'] ) : '';
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
}
} // Class wpb_widget ends here

// Register and load the widget
function promotion_slide_load_widget() {
	register_widget( 'promotion_slide_widget' );
}
add_action( 'widgets_init', 'promotion_slide_load_widget' );

?>