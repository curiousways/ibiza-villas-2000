<?php
$property_sleeps  = get_field('property_sleeps');
$sleeps = get_field('property_sleeps');

$locationsleeps = '';

$sleeps = (!empty($sleeps) ? 'Sleeps ' . $sleeps : '');

$location_terms = get_the_terms($post->ID,'property_location');
$location = (!empty($location_terms[0])) ? $location_terms[0]->name : '';
$location2 = (!empty($location_terms[1])) ? $location_terms[1]->name : '';

$postTypeObj = get_post_type_object( get_post_type() );
$type = $postTypeObj->labels->singular_name;

if(!empty($location) && !empty($location2) && !empty($sleeps)) {
	$locationsleeps = $location . ' - ' . $location2 . ' / ' . $sleeps;
} else if(!empty($location) && !empty($sleeps)) {
	$locationsleeps = $location . ' / ' . $sleeps;
} else {
	$locationsleeps = $location . $sleeps;
}

?>


<div class="slider-property slider-<?php echo esc_attr(strtolower($type)); ?>">

		<div class="slider-top">

			<?php if ( has_post_thumbnail() ) { ?>

				<?php $images = get_field('property_images'); ?>


						<?php if ( has_post_thumbnail() ) { ?>

							<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail('iv2000_420x280'); ?></a>

						<?php } else { ?>
							<a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php the_title_attribute(); ?>">
								<img src="<?php bloginfo('template_directory'); ?>/images/coming-soon.jpg" alt="Image Coming Soon" width="420" height="280" />
							</a>
						<?php } ?>


			<?php } else { ?>
				<a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php the_title_attribute(); ?>">
					<img src="<?php bloginfo('template_directory'); ?>/images/coming-soon.jpg" alt="Image Coming Soon" width="420" height="280" />

				</a>
			<?php } ?>


			<div class="slider-info">
				<?php get_template_part( 'templates/property-price-from-to' ); ?>
				<span class="slider-type"><?php echo esc_html( $type ); ?></span>
			</div>

		</div>

		<div class="slider-detail">

	        <?php
	          $villa_pretty_name = get_field('villa_pretty_name');

	          if ($villa_pretty_name) {

	        	echo '<h4>' . esc_html( $villa_pretty_name ) . '</h4>';
	        	echo '<h3><a href="' . esc_url( get_permalink() ) . '" title="' . esc_attr( get_the_title() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';

	          } else {
	        	echo '<h3><a href="' . esc_url( get_permalink() ) . '" title="' . esc_attr( get_the_title() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
	          }

	        ?>

			<p class="slider-location-sleeps">

				<?php echo esc_html( $locationsleeps ); ?>


			</p>

			<div class="special-offer-grid-text">
				<?php
					$offer_text = get_field('property_special_offers_text');
					echo wp_kses_post( $offer_text );
			 	?>
			</div>

			<a href="<?php the_permalink(); ?>" class="button">FIND OUT MORE</a>

		</div>

</div>
