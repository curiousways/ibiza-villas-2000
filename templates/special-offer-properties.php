<?php
$post_types = array( 'villas' );
$post__not_in = array();

$args = array(
	'post_type' => $post_types,
	'orderby' => 'title',
	'posts_per_page'=> 99,
	'meta_key'		=> 'property_special_offer_display',
	'meta_value'	=> 1,
	'post__not_in'  => $post__not_in,
);

$special_offer_properties = new WP_Query($args);

?>

<?php if ( $special_offer_properties->have_posts() ) : ?>

	<div class="special-offer-properties">

		<div class="row">
			<h3 class="text-center"><?php esc_html_e( 'BROWSE OUR SPECIAL OFFERS', 'ibiza-villas-2000' ); ?></h3>

			<div class="special-offer-properties-grid">

				<?php while ( $special_offer_properties->have_posts() ): $special_offer_properties->the_post(); global $post; ?>

		      		<?php
		      			get_template_part( 'templates/loop-grid-special-offers-part' );
		      		?>

				<?php endwhile; ?>

			</div>
		</div>
	</div>
<?php endif; ?>

<?php wp_reset_postdata(); ?>
