
<?php


	$explore_other_1_image = get_field('explore_other_1_image', 'option');
	$explore_other_1_text = get_field('explore_other_1_text', 'option');
	$explore_other_1_url = get_field('explore_other_1_url', 'option');
	$explore_other_1_image_scale = wp_get_attachment_image_src( $explore_other_1_image['id'], 'property-featured-image' );

	$explore_other_2_image = get_field('explore_other_2_image', 'option');
	$explore_other_2_text = get_field('explore_other_2_text', 'option');
	$explore_other_2_url = get_field('explore_other_2_url', 'option');
	$explore_other_2_image_scale = wp_get_attachment_image_src( $explore_other_2_image['id'], 'property-featured-image' );

?>
		<div class="row explore-other-container">

			<div class="small-12 medium-6 columns">
				<div class="explore" style="background-image: url(<?php echo $explore_other_1_image_scale[0]; ?>);">
					<a href="<?php echo $explore_other_1_url; ?>"><span><?php echo $explore_other_1_text; ?></span></a>
				</div>

			</div><!-- ./columns -->

			<div class="small-12 medium-6 columns">

				<div class="explore" style="background-image: url(<?php echo $explore_other_2_image_scale[0]; ?>);">
					<a href="<?php echo $explore_other_2_url; ?>"><span><?php echo $explore_other_2_text; ?></span></a>
				</div>

			</div><!-- ./columns -->




		</div><!--/.row -->






