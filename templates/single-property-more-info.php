<?php


	 if ( is_single( 12511 ) ) {
                 $global_villa_features = get_field('global_villa_features_airstream', 'option');
            }
            else {
            	$global_villa_features = get_field('global_villa_features', 'option');
            }
            

	if( is_object_in_term( $post->ID, 'villa_type', 'sale' ) ) {
      	//maybe print a different global stuff for sale villas
	
	}else{
		echo $global_villa_features;
	}

	
// Property More Info - Show if has content

	$title = get_field('property_more_info_title'); 
	$intro = get_field('property_more_info_intro'); 
	$content = get_field('property_more_info_content'); 


	if( !empty($content) ): ?>

		<div class="property-more-info">

			<?php 
				echo '<h2>' . $title . '</h2>';
				if( !empty($intro) ) echo '<p>' . $intro . '</p>';
			?>
			<button style="    padding: 9px 40px;" >Read More...</button>
			<div class="revealJs">
				<?php echo $content; ?>
			</div>
		</div>

	<?php endif; ?>

