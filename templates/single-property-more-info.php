<?php
$global_villa_features = get_field('global_villa_features', 'option');

	if ( is_object_in_term( $post->ID, 'villa_type', 'sale' ) ) {
		//
	} else {
		echo $global_villa_features;
	}

	$title = get_field('property_more_info_title');
	$intro = get_field('property_more_info_intro');
	$content = get_field('property_more_info_content');


	if ( !empty($content) ) : ?>

		<div class="property-more-info">

			<?php
				echo '<h2>' . esc_html( $title ) . '</h2>';
				if ( !empty($intro) ) {
					echo '<p>' . wp_kses_post( $intro ) . '</p>';
				}
			?>
			<button style="    padding: 9px 40px;" >Read More...</button>
			<div class="revealJs">
				<?php echo wp_kses_post( $content ); ?>
			</div>
		</div>

<?php endif; ?>
