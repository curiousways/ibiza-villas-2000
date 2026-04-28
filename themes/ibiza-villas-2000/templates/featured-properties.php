<?php
$post_types = array( 'villas' );
$post__not_in = array();

$args = array(
	'post_type' => $post_types,
	'orderby' => 'title',
	'posts_per_page'=> 100,
	'meta_key'		=> 'property_featured',
	'meta_value'	=> 1,
	'post__not_in'  => $post__not_in,
);

$featured_property = new WP_Query($args);

?>

<?php if ( $featured_property->have_posts() ) : ?>

	<h3 class="text-center"><?php esc_html_e( 'BROWSE OUR FEATURED ACCOMMODATION', 'ibiza-villas-2000' ); ?></h3>

	<div class="featured-properties">

		<?php while ( $featured_property->have_posts() ): $featured_property->the_post(); global $post; ?>

      		<?php
      			get_template_part( 'templates/loop-grid-part' );
      		?>

		<?php endwhile; ?>

	</div>

<?php endif; ?>

<?php wp_reset_postdata(); ?>
