<?php

class featured_properties_widget extends WP_Widget {

	function __construct() {

		parent::__construct(
			'featured_properties',
			__('Featured properties widget', 'featured_properties_widget'),
			array( 'description' => __( 'Widget to show the featured properties', 'featured_properties_widget' ), )

		);

	}

	public function widget( $args, $instance ) {

			$post__not_in = array();
			$wp_args = array(
				'post_type' => 'villas',
				'orderby' => 'title',
				'posts_per_page'=> 5,
				'meta_key'		=> 'property_featured',
				'meta_value'	=> 0,
				'post__not_in'  => $post__not_in,
			);

			$featured_property = new WP_Query($wp_args);

		if ( $featured_property->have_posts() ) : ?>

			<h3><?php esc_html_e('Featured Properties'); ?></h3>

			<div class="featuredSidebar">

				<?php while ( $featured_property->have_posts() ): $featured_property->the_post(); global $post; ?>

					<?php
		      			get_template_part( 'templates/loop-grid-part' );
		      		?>

				<?php endwhile; ?>

			</div>

		<?php endif;

		wp_reset_postdata();

	}

	public function form( $instance ) {

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'New title', 'featured_properties_widget' );
		}

		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<?php

	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}

}

function featured_properties_load_widget() {
	register_widget( 'featured_properties_widget' );
}
add_action( 'widgets_init', 'featured_properties_load_widget' );
