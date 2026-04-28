<!-- Single Property Features List with Icons -->
<?php
$property_sleeps  = get_field('property_sleeps');
$property_bedrooms = get_field('property_bedrooms');
$property_bathrooms = get_field('property_bathrooms');


if ( have_rows('property_features') ) :

	echo '<div class="property-features"><h3>Property Facts</h3><ul>';

		if ($property_sleeps > 12) {
			echo '<li>Sleeps 12+</li>';
		} else {
			echo '<li>Sleeps ' . esc_html( $property_sleeps ) . '</li>';
		}

		if ($property_bathrooms) {
			echo '<li>' . esc_html( $property_bedrooms ) . ' Bedrooms</li>';
		}
		if ($property_bathrooms) {
			echo '<li>' . esc_html( $property_bathrooms ) . ' Bathrooms</li>';
		}

	while ( have_rows('property_features') ) : the_row();

		$icon = get_sub_field('property_feature_icon');
		$feature = get_sub_field('property_feature_text');

		echo '<li class="' . esc_attr( $icon ) . '">' . esc_html( $feature ) . '</li>';

	endwhile;

	echo '</ul></div>';

endif;

?>
