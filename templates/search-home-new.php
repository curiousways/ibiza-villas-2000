<?php
$listing_url = home_url( '/all-villas/' );
$notice_block = '
<div class="search-properties-notice columns">
	<p style="color:#145267;"><strong>Please Note!</strong> For legal reasons the maximum occupancy of a villa in Ibiza is 12 people.
	<strong>If you are searching for a villa for more than 12 people, please <a href="' . esc_url( home_url( '/contact/' ) ) . '">contact us</a>.</strong></p>
</div>';

if ( is_front_page() ) : ?>
<div style="margin: 0 auto; max-width: 81.25rem; padding-top:30px; background-color: #F6F6F6;" class="search-properties">
	<div class="small-12 medium-12 large-12">
		<form class="small-12 medium-12 large-12" id="property-search-form" name="property-search-form" method="get" action="<?php echo esc_url( $listing_url ); ?>">
			<div class="small-12 medium-4 large-3 columns">
				<div class="property-search-field">
					<select name="location" class="property-search-form-location">
						<option value="all"><?php esc_html_e( 'CHOOSE YOUR LOCATION', 'ibiza-villas-2000' ); ?></option>
						<option value="san-antonio">San Antonio</option>
						<option value="ibiza-town">Ibiza Town</option>
						<option value="playa-den-bossa">Playa den Bossa</option>
						<option value="san-rafel">San Rafel</option>
						<option value="north-island">North Island</option>
						<option value="san-josep">San Josep</option>
					</select>
				</div>
			</div>
			<div class="small-12 medium-4 large-3 columns">
				<div class="property-search-field">
					<select name="min" class="property-search-form-min">
						<option value="1"><?php esc_html_e( 'SLEEPS', 'ibiza-villas-2000' ); ?></option>
						<?php for ( $i = 1; $i <= 9; $i++ ) : ?>
							<option value="<?php echo (int) $i; ?>"><?php echo (int) $i; ?></option>
						<?php endfor; ?>
						<option value="10">10+</option>
					</select>
				</div>
			</div>
			<input type="hidden" name="max" value="99" />
			<div class="small-12 medium-4 large-2 columns search">
				<button style="border-radius: 3px;" type="submit"><?php esc_html_e( 'Search', 'ibiza-villas-2000' ); ?></button>
			</div>
		</form>
		<?php
		if ( ! is_page_template( 'page-special-offers.php' ) ) {
			?>
					<div class="small-12 medium-12 large-4 columns buttons">
						<a class="button " title="<?php esc_attr_e( 'Special offers', 'ibiza-villas-2000' ); ?>" href="<?php echo esc_url( home_url( '/special-offers/' ) ); ?>" tabindex="-1" style="background: #FEBF3E;width: 100%;"><?php echo esc_html( get_field( 'offer_redbox', 3720 ) ); ?></a>
					</div>
			<?php
		}
		?>
	</div>
	<div class="small-12 medium-12 large-12 columns">
		<?php echo $notice_block; ?>
	</div>
</div>
<?php elseif ( ! is_front_page() ) : ?>
<style type="text/css">
@media only screen and (min-width:1080px) {
.search-nomads { margin: 0 auto; max-width: 81.25rem; padding-top:180px; background-color: #F6F6F6; }
}
@media only screen and (max-width:1080px) {
.search-nomads { margin: 0 auto; max-width: 81.25rem; padding-top:80px; background-color: #F6F6F6; }
}
</style>
<div class="search-nomads search-properties">
	<div class="small-12 medium-12 large-12">
		<form class="small-12 medium-12 large-12" id="property-search-form" name="property-search-form" method="get" action="<?php echo esc_url( $listing_url ); ?>">
			<div class="small-12 medium-4 large-3 columns">
				<div class="property-search-field">
					<select name="location" class="property-search-form-location">
						<option value="all"><?php esc_html_e( 'CHOOSE YOUR LOCATION', 'ibiza-villas-2000' ); ?></option>
						<option value="san-antonio">San Antonio</option>
						<option value="ibiza-town">Ibiza Town</option>
						<option value="playa-den-bossa">Playa den Bossa</option>
						<option value="san-rafel">San Rafel</option>
						<option value="north-island">North Island</option>
						<option value="san-josep">San Josep</option>
					</select>
				</div>
			</div>
			<div class="small-12 medium-4 large-3 columns">
				<div class="property-search-field">
					<select name="min" class="property-search-form-min">
						<option value="1"><?php esc_html_e( 'SLEEPS', 'ibiza-villas-2000' ); ?></option>
						<?php for ( $i = 1; $i <= 9; $i++ ) : ?>
							<option value="<?php echo (int) $i; ?>"><?php echo (int) $i; ?></option>
						<?php endfor; ?>
						<option value="10">10+</option>
					</select>
				</div>
			</div>
			<input type="hidden" name="max" value="99" />
			<div class="small-12 medium-4 large-2 columns search">
				<button style="border-radius: 3px;" type="submit"><?php esc_html_e( 'Search', 'ibiza-villas-2000' ); ?></button>
			</div>
		</form>
		<?php
		if ( ! is_page_template( 'page-special-offers.php' ) ) {
			?>
					<div class="small-12 medium-12 large-4 columns buttons">
						<a class="button " title="<?php esc_attr_e( 'Special offers', 'ibiza-villas-2000' ); ?>" href="<?php echo esc_url( home_url( '/special-offers/' ) ); ?>" tabindex="-1" style="background: #FEBF3E;width: 100%;"><?php echo esc_html( get_field( 'offer_redbox', 3720 ) ); ?></a>
					</div>
			<?php
		}
		?>
	</div>
	<div class="small-12 medium-12 large-12 columns">
		<?php echo $notice_block; ?>
	</div>
</div>
<?php endif; ?>
