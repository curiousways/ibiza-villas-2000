<?php
/* Get location and property type of current post */ ?>

<?php

// get the current location from the terms
$current_location = get_the_terms( $post->ID, 'property_location');
// grab the slug
$current_slug = $current_location[0]->slug;
$current_location_title = $current_location[0]->name;
// is it a villa or an apartment?! WHO KNOWS?!
// This guy does \/
$current_property_type = get_post_type();




/* Override to display morzine villas if the current property is an apartment in avoriaz as it's one of a kind */

if ( $current_slug == "avoriaz" && $current_property_type == 'apartments' ) {
	$avo_flag = true;
    $current_slug = 'morzine';
    $current_property_type = 'villas';
}



/* Loop through posts with same location but exclude current post */

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
	'showposts'=> '10',
	'post__not_in' => array($post->ID),
	'meta_key'		=> 'property_for_sale', //filter out for sale properties, true is for sale, 0 not for sale (rental).
	'meta_value'	=> 0
);

$similar = new WP_Query($similar_args);

if ( $similar->have_posts() ) : ?>


	<h4 class="text-center"><?php if ( $avo_flag == true ) { _e('Similar Properties around '); } else { _e('Similar Properties in '); } echo $current_location_title; ?></h4>


	<div class="slider similar-properties">

		<?php while ( $similar->have_posts() ): $similar->the_post(); global $post; ?>

		<?php
  			// get the inside of the grid loop to make life easier
  			get_template_part( 'templates/loop-grid-part' );
  		?>

		<?php endwhile; ?>

	</div>

<?php endif; ?>

<?php wp_reset_postdata(); ?>


