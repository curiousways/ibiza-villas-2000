<?php

class similar_property_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
		'similar_property',
		__('Similar property widget', 'similar_property_widget'),
		array( 'description' => __( 'Widget to show the similar content to the current page', 'similar_property_widget' ), )
		);
	}

	public function widget( $args, $instance ) {
		global $post;

		if ( empty( $post ) ) {
			return;
		}

		$current_location = get_the_terms( $post->ID, 'property_location');
		if ( empty( $current_location ) || is_wp_error( $current_location ) ) {
			return;
		}

		$currentSlug = $current_location[0]->slug;

		$current_property_type = get_post_type();
		if ($current_property_type !== 'villas') {
			return;
		}

		$listings_url = home_url( '/all-villas/' );

		$wp_args = array(
		    'post_type' => 'villas',
			'tax_query' => array(
				array(
					'taxonomy' => 'property_location',
					'field'    => 'slug',
					'terms'    => $currentSlug,
				),
			),
			'orderby' => 'asc',
			'posts_per_page'=> 7,
			'post__not_in' => array($post->ID),
			'meta_key'		=> 'property_for_sale',
			'meta_value'	=> 0
		);

		$similar_property_query = new WP_Query( $wp_args );

		if ($similar_property_query->have_posts()) : ?>
			<h3><?php echo esc_html( $instance['title'] ); ?></h3>
			<ul>
			<?php while ($similar_property_query->have_posts()) : $similar_property_query->the_post(); ?>

				<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>

				<?php endwhile; ?>
			</ul>
			<p><a href="<?php echo esc_url( $listings_url ); ?>" class="button"><?php esc_html_e( 'Show all', 'ibiza-villas-2000' ); ?></a></p>
	 	<?php endif;

		wp_reset_postdata();
	}

	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'similar_property_widget' );
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

function similar_property_load_widget() {
	register_widget( 'similar_property_widget' );
}
add_action( 'widgets_init', 'similar_property_load_widget' );
