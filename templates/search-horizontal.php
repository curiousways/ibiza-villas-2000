<script>
function fwp_redirect() {
    var query = FWP.build_query_string();
    window.location.href = '/property-results/?' + query;
}
</script>
<div class="piste-bg text-center">
	<div class="row">
		<div class="search-horizontal facetSearchTop">

			
			<div class="form-group">
				<label>I am looking for:</label>
				<?php echo facetwp_display( 'facet', 'property_types' ); ?>
			</div>
			
			<div class="form-group">
				<label>Located in:</label>
				<?php echo facetwp_display( 'facet', 'property_locations' ); ?>
			</div>
			
			<div class="form-group narrow">
				<label>To sleep:</label>
				<?php echo facetwp_display( 'facet', 'property_sleeps' ); ?>
			</div>
			
	<!-- 		<div class="form-group wide">
				<label>Between these dates:</label> -->
				<?php //echo facetwp_display( 'facet', 'property_dates' ); ?>
			<!-- </div> -->

			<div class="form-group">
				<button class="search-fwp" onclick="fwp_redirect()">Find my accommodation</button>
				<a class="reset-facet" onclick="FWP.reset()">Reset Search</a>
			</div>


		</div>
	</div>
</div>




	<?php 
	if(is_home()) {
		echo facetwp_display( 'template', 'homepage_property_search' ); 
	} else {
		echo facetwp_display( 'template', 'property_search' ); 
	} ?>
