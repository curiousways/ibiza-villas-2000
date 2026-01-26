<?php
// Embed youtube or vimeo with acf url field and default wordpress embedding

	$video_url = get_field('property_video');
	$video = wp_oembed_get($video_url, array('width'=>'600'));



	if( !empty($video_url) ): ?>
		<div class="videoWrapper">
			<?php echo $video; ?>
		</div>
	<?php endif; ?>
