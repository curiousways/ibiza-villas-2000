<script>
function fwp_redirect() {
    var query = FWP.build_query_string();
    window.location.href = '/property-results/?' + query;
}
</script>

<div class="search-properties facetSearchTop">
	
	<div class=" show-for-small-only small-12 columns">
		<label>START YOUR SEARCH</label>
	</div>

	<div class="facet-row">
		<div class="facet-column">
			<?php echo facetwp_display( 'facet', 'property_types' ); ?>	
		</div>
		<div class="facet-column">
			<?php echo facetwp_display( 'facet', 'property_locations' ); ?>
		</div>
		<div class="facet-column large">
			<label>To sleep:</label>
			<?php echo facetwp_display( 'facet', 'property_sleeps' ); ?>

			<button class="search-fwp" onclick="fwp_redirect()">Search</button>
			<!-- <a class="reset-facet" onclick="FWP.reset()">Reset</a> -->
		</div>
	</div>

	<?php echo facetwp_display( 'template', 'homepage_property_search' ); ?>

	<div class="buttons" style="">
						<a class="button" title="Special offers" href="/special-offers" tabindex="-1" style="background: red;">Special offers 20199 &gt;</a>
					</div>
	
</div>
