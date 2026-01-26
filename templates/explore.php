
<?php


	$explore_1_image = get_field('explore_1_image', 'option');
	$explore_1_text = get_field('explore_1_text', 'option');
	$explore_1_url = get_field('explore_1_url', 'option');
	$explore_1_image_scale = wp_get_attachment_image_src( $explore_1_image['id'], 'property-featured-image' );

	$explore_2_image = get_field('explore_2_image', 'option');
	$explore_2_text = get_field('explore_2_text', 'option');
	$explore_2_url = get_field('explore_2_url', 'option');
	$explore_2_image_scale = wp_get_attachment_image_src( $explore_2_image['id'], 'property-featured-image' );

	$explore_3_image = get_field('explore_3_image', 'option');
	$explore_3_text = get_field('explore_3_text', 'option');
	$explore_3_url = get_field('explore_3_url', 'option');
	$explore_3_image_scale = wp_get_attachment_image_src( $explore_3_image['id'], 'property-featured-image' );


?>
		<div class="row">

			<div class="small-12 medium-4 large-4 columns">

				<div class="explore" style="border-radius:5px;background-image: url(<?php echo $explore_1_image_scale[0]; ?>);">
					<a href="<?php echo $explore_1_url; ?>"><span><?php echo $explore_1_text; ?></span></a>
				</div>

			</div><!-- ./columns -->

			<div class="small-12 medium-4 large-4 columns">

				<div class="explore" style="border-radius:5px;background-image: url(<?php echo $explore_2_image_scale[0]; ?>);">
					<a href="<?php echo $explore_2_url; ?>"><span><?php echo $explore_2_text; ?></span></a>
				</div>

			</div><!-- ./columns -->


			<div class="small-12 medium-4 large-4 columns">


				<div class="explore" style="border-radius:5px;background-image: url(<?php echo $explore_3_image_scale[0]; ?>);">
					<a href="<?php echo $explore_3_url; ?>"><span><?php echo $explore_3_text; ?></span></a>
				</div>

			</div><!-- ./columns -->

		</div><!--/.row -->






