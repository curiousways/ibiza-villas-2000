<?php
/** @var WP_Post $post */

$current_location = get_the_terms( $post->ID, 'property_location');
if ( empty( $current_location ) || is_wp_error( $current_location ) ) {
	return;
}
$current_slug = $current_location[0]->slug;
$current_location_title = $current_location[0]->name;
$current_property_type = 'villas';

$similar_args = array(
	'post_type' => $current_property_type,
	'tax_query' => array(
		array(
			'taxonomy' => 'property_location',
			'field'    => 'slug',
			'terms'    => $current_slug,
		),
	),
	'orderby' => 'asc',
	'posts_per_page'=> 10,
	'post__not_in' => array( $post->ID ),
	'meta_key'		=> 'property_for_sale',
	'meta_value'	=> 0
);

$similar = new WP_Query($similar_args);

if ( $similar->have_posts() ) : ?>

	<h4 class="text-center"><?php esc_html_e('Similar Properties in ', 'ibiza-villas-2000'); ?><?php echo esc_html( $current_location_title ); ?></h4>

	<div class="similar-properties">

		<?php while ( $similar->have_posts() ): $similar->the_post(); ?>

		<?php
  			get_template_part( 'templates/loop-grid-part' );
  		?>

		<?php endwhile; ?>

	</div>

<?php endif; ?>

<?php wp_reset_postdata(); ?>
