<?php

// Creating the widget 
class custom_week_widget extends WP_Widget {

function __construct() {
parent::__construct(
// Base ID of your widget
'custom_weeks', 

// Widget name will appear in UI
__('Custom week widget', 'custom_week_widget'), 

// Widget description
array( 'description' => __( 'Widget to show all the custom week posts', 'custom_week_widget' ), ) 
);
}

// Creating widget front-end
// This is where the action happens
public function widget( $args, $instance ) {

	$args = array(
	    'post_type' => array('custom_weeks'),
	    'posts_per_page' =>10 ,
	); ?>

	 <?php $custom_week_query = new WP_Query( $args ); ?>

	<?php if ($custom_week_query->have_posts()) : ?>
	<div class="custom-weeks-widget">
		<h3><?php echo $instance['title']; ?></h3>
		<ul>
			<?php while ($custom_week_query->have_posts()) : $custom_week_query->the_post(); ?>
				<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
			<?php endwhile; ?>
		</ul>
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
		$title = __( 'New title', 'custom_week_widget' );
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
function custom_week_load_widget() {
	register_widget( 'custom_week_widget' );
}
add_action( 'widgets_init', 'custom_week_load_widget' );

?>