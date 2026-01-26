
<!-- Single Property Maps -->

<?php 

$location = get_field('property_map');

if( !empty($location) ): ?>

<div class="single-map-container"><div class="acf-map">
	<div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng']; ?>"></div>
</div></div>

<?php endif; ?>

