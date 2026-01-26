
<style type="text/css">
	
.wpb-js-composer .vc_tta-color-grey.vc_tta-style-outline .vc_tta-panel .vc_tta-panel-heading {
    border-color: #e3e3e3!important;
    background-color: #EBEBEB!important;
}

.wpb-js-composer .vc_tta-color-grey.vc_tta-style-outline .vc_tta-panel .vc_tta-panel-title>a {
    color: #666!important;
}

.wpb-js-composer .vc_tta .vc_tta-controls-icon.vc_tta-controls-icon-plus::before {
    border-color: #666!important;
}


.wpb-js-composer .vc_tta .vc_tta-controls-icon.vc_tta-controls-icon-plus::after {
    border-color: #666!important;
}
</style>

<?php if (is_front_page()) { ?>
<div  style="margin: 0 auto;
max-width: 81.25rem;
padding-top:30px;
background-color: #F6F6F6;" class="search-properties">

	<div class="small-12 medium-12 large-12">

		<form class="small-12 medium-12 large-12" id="property-search-form" name="property-search-form" method="get" action="/property-results/">


			<div class="small-12 medium-4 large-3 columns">

				<div data-type="dropdown" class="facetwp-facet facetwp-type-dropdown">

					<select name="location" class="property-search-form-location facetwp-dropdown">
						<option value="all">CHOOSE YOUR LOCATION</option>

						<option value="san-antonio">San Antonio</option>
						<option value="ibiza-town">Ibiza Town</option>
						<option value="playa-den-bossa">Playa den Bossa</option>
						<option value="san-rafel">San Rafel</option>
						<option value="north-island">North Island</option>
						<option value="san-josep">San Josep</option>
					</select>

					<i class="facetwp-dropdown-arrow icon-arrow-down"></i>
				</div>

			</div>

			<div class="small-12 medium-4 large-3 columns">

				<div data-type="dropdown" class="facetwp-facet facetwp-type-dropdown">

					<select name="min" class="property-search-form-min facetwp-dropdown" >
						<option value="1">SLEEPS</option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
						<option value="10">10+</option>
					</select>

					<i class="facetwp-dropdown-arrow icon-arrow-down"></i>
				</div>

			</div>

			<input type="hidden" name="max" value="99" />

			<div class="small-12 medium-4 large-2 columns search">
				<button style="border-radius: 3px;" class="search-fwp" type="submit">Search</button>
			</div>

		</form>

	

		
		<?php 
			if (get_page_template() != '/var/www/vhosts/ibizavillas2000.com/httpdocs/wp-ibiza/wp-content/themes/rudeibiza/page-special-offers.php') {
				?>
					<div class="small-12 medium-12 large-4 columns buttons" style="">
						<a class="button " title="Special offers" href="/special-offers" tabindex="-1" style="background: #FEBF3E;width: 100%;"><?php the_field('offer_redbox', 3720); ?></a>
					</div>
				<?php
			}
		?>
				
		
		
	</div>
		<div class="small-12 medium-12 large-12 columns" >
		<?php echo do_shortcode( '[vc_tta_accordion style="outline" active_section="0" no_fill="true" collapsible_all="true"][vc_tta_section title="LOOKING FOR A LARGE VILLA (14+) HERE IN IBIZA?" tab_id="1652684979034-5c35c36b-3b33"][vc_column_text]<p style="color:#145267!important;"><strong>Please Note!</strong> For legal reasons the maximum occupancy of a villa in Ibiza is 12 people, <strong>if you are searching for a villa for more than 12 people, please <a href="https://ibizavillas2000.com/contact/">contact us</a>. </strong>We have many villas next door to each other plus our apartment hotel which can sleep up to 65 guests.</p>[/vc_column_text][/vc_tta_section][/vc_tta_accordion]' ); ?>

</div>


</div>


<?php } ?>

<?php if (!is_front_page()) { ?>


<style type="text/css">


@media only screen and (min-width:1080px) {

.search-nomads {
margin: 0 auto;
max-width: 81.25rem;
padding-top:180px;
background-color: #F6F6F6;
}
}

@media only screen and (max-width:1080px) {

.search-nomads {
margin: 0 auto;
max-width: 81.25rem;
padding-top:80px;
background-color: #F6F6F6;
}
}
</style>

<?php



// Checks if there is query strings to get and if not sets a default value

	if(empty($_GET['location'])) {
		$property_location = array('san-antonio','ibiza-town','playa-den-bossa', 'san-rafel', 'san-josep', 'north-island');
	} else if(($_GET['location']) == 'all' ) {
		$property_location = array('san-antonio','ibiza-town','playa-den-bossa', 'san-rafel', 'san-josep', 'north-island');
	} else {
		$property_location = htmlspecialchars($_GET['location'],ENT_QUOTES);
	}

	if(empty($_GET['min'])) {
		$sleeps_minimum = '1';
	} else {
		$sleeps_minimum = htmlspecialchars($_GET['min'],ENT_QUOTES);
	}

	if(empty($_GET['max'])) {
		$sleeps_maximum = '99';
	} else {
		$sleeps_maximum = htmlspecialchars($_GET['max'],ENT_QUOTES);
	}


?>







<div  style="" class="search-nomads search-properties">

	<div class="small-12 medium-12 large-12">

		<form class="small-12 medium-12 large-12" id="property-search-form" name="property-search-form" method="get" action="/property-results/">


			<div class="small-12 medium-4 large-3 columns">

				<div data-type="dropdown" class="facetwp-facet facetwp-type-dropdown">

					<select name="location" class="property-search-form-location facetwp-dropdown">
						<option value="all">CHOOSE YOUR LOCATION</option>

						<option value="san-antonio">San Antonio</option>
						<option value="ibiza-town">Ibiza Town</option>
						<option value="playa-den-bossa">Playa den Bossa</option>
						<option value="san-rafel">San Rafel</option>
						<option value="north-island">North Island</option>
						<option value="san-josep">San Josep</option>
					</select>

					<i class="facetwp-dropdown-arrow icon-arrow-down"></i>
				</div>

			</div>

			<div class="small-12 medium-4 large-3 columns">

				<div data-type="dropdown" class="facetwp-facet facetwp-type-dropdown">

					<select name="min" class="property-search-form-min facetwp-dropdown" >
						<option value="1">SLEEPS</option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
						<option value="10">10+</option>
					</select>

					<i class="facetwp-dropdown-arrow icon-arrow-down"></i>
				</div>

			</div>

			<input type="hidden" name="max" value="99" />

			<div class="small-12 medium-4 large-2 columns search">
				<button style="border-radius: 3px;" class="search-fwp" type="submit">Search</button>
			</div>

		</form>
		
		<?php 
			if (get_page_template() != '/var/www/vhosts/ibizavillas2000.com/httpdocs/wp-ibiza/wp-content/themes/rudeibiza/page-special-offers.php') {
				?>
					<div class="small-12 medium-12 large-4 columns buttons" style="">
						<a class="button " title="Special offers" href="/special-offers" tabindex="-1" style="background: #FEBF3E;width: 100%;"><?php the_field('offer_redbox', 3720); ?></a>
					</div>
				<?php
			}
		?>
				
		
		
	</div>

	<div class="small-12 medium-12 large-12 columns" >
		<?php echo do_shortcode( '[vc_tta_accordion style="outline" active_section="0" no_fill="true" collapsible_all="true"][vc_tta_section title="LOOKING FOR A LARGE VILLA (14+) HERE IN IBIZA?" tab_id="1652684979034-5c35c36b-3b33"][vc_column_text]<p style="color:#145267!important;"><strong>Please Note!</strong> For legal reasons the maximum occupancy of a villa in Ibiza is 12 people, <strong>if you are searching for a villa for more than 12 people, please <a href="https://ibizavillas2000.com/contact/">contact us</a>. </strong>We have many villas next door to each other plus our apartment hotel which can sleep up to 65 guests.</p>[/vc_column_text][/vc_tta_section][/vc_tta_accordion]' ); ?>

</div>

</div>



<?php } ?>