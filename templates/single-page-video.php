<?php
// Embed youtube or vimeo with acf url field and default wordpress embedding

	$video_url = get_field('property_video');
	$video = wp_oembed_get( $video_url );


	if( !empty($video_url) ): ?>

		TEST
		<?php echo $video; ?>

	<?php endif; ?>
