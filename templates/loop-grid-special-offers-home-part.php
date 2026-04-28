<?php
$property_sleeps = get_field('property_sleeps');
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

<div class="slider-property slider-<?php echo esc_attr(strtolower($type)); ?>" style="margin: 0px 0px;width: 100%;object-fit: cover;">

<?php

    $bg_id = get_post_thumbnail_id( $post->ID );
    $backgroundImg = $bg_id ? wp_get_attachment_image_src( $bg_id, 'full' ) : null;
    $bg_url = ( ! empty( $backgroundImg[0] ) ) ? $backgroundImg[0] : '';

?>

		<div class="slider-top home" style="<?php echo $bg_url ? 'display: table; width: 100%; background: url(' . esc_url( $bg_url ) . '); background-repeat: no-repeat; background-size: cover; background-position-y: 50%;' : ''; ?>">

			<?php if ( has_post_thumbnail() ) { ?>

				<?php $images = get_field('property_images'); ?>

						<?php if ( has_post_thumbnail() ) { ?>

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

		<div class="medium-12 hero-title text-center columns">

 			<img class="hero-logo" src="<?php echo esc_url( get_template_directory_uri() . '/images/rudeibiza-logo-white.svg' ); ?>" alt="Ibiza Villas 2000">

			<?php
	          $villa_pretty_name = get_field('villa_pretty_name');
	          $property_link = get_permalink();

	          if ($villa_pretty_name) {

	        	echo '<span class="sliderh1 homeslider-villaname">' . esc_html( $villa_pretty_name ) . '</span>';

	        	echo '<span class="sliderh2"><a href="' . esc_url( $property_link ) . '" title="' . esc_attr( get_the_title() ) . '">' . esc_html( get_the_title() ) . '</a></span>';
	        	echo '<a href="' . esc_url( $property_link ) . '" class="button" tabindex="0">FIND OUT MORE</a>';

	          } else {
	        	echo '<h1><a href="' . esc_url( $property_link ) . '" title="' . esc_attr( get_the_title() ) . '">' . esc_html( get_the_title() ) . '</a></h1>';
	          }

	        ?>
	        </div>

		</div>

</div>
