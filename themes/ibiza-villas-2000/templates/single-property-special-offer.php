
<!-- Single Property Special Offer -->

<?php 

$offer_text = get_field('property_special_offers_text');

if( !empty($offer_text) ): ?>

	<div class="row">
		<div class="small-12 columns">
			<div class="single-property-special-offer"><?php echo $offer_text; ?></div>
		</div>
	</div>

<?php endif; ?>

