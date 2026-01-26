<?php

// Creating the widget
class explore_widget extends WP_Widget {

	function __construct() {

		parent::__construct(

			// Base ID of your widget
			'explore',

			// Widget name will appear in UI
			__('Explore widget', 'explore_widget'),

			// Widget description
			array( 'description' => __( 'Widget to show the explore guide', 'explore_widget' ), )

		);

	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {

		get_template_part( 'templates/explore-carousel' );

	}

	// Widget Backend
	public function form( $instance ) { ?>

		<p><?php _e('This widget has no options.' ); ?></p>

		<?php

	}

} // Class wpb_widget ends here

// Register and load the widget
function explore_load_widget() {
	register_widget( 'explore_widget' );
}
add_action( 'widgets_init', 'explore_load_widget' );

?>