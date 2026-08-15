<?php
/**
 * Section: Villa overview (heading, summary, amenities, price).
 *
 * Matches Figma node 1:5973 (02b | Villa Detail → Overview container).
 * Title / rating / location / facts live in `villa-header`.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param int $villa_id Post ID.
 */
function ibv_core_section_villa_overview( $villa_id ) {
	$villa_id = (int) $villa_id;
	if ( ! $villa_id ) {
		return;
	}

	wp_enqueue_style( 'ibv-villa-detail' );
	wp_enqueue_style( 'ibv-section-villa-overview' );
	wp_enqueue_style( 'ibv-section-heading' );

	// Two-part copy: villa_summary (plain-text ACF field, also the card
	// description) always shows in full; the post content — the long
	// description, keyword-rich, carries the internal links — collapses
	// behind Read more below it. Rendered through the_content filter so
	// formatting, links, and embeds resolve exactly as core would output.
	$summary     = trim( (string) get_field( 'villa_summary', $villa_id ) );
	$content_raw = get_the_content( null, false, $villa_id );
	$description = ( '' !== trim( $content_raw ) ) ? apply_filters( 'the_content', $content_raw ) : '';
	$indicative  = get_field( 'villa_indicative_from_price', $villa_id );

	$description_id = wp_unique_id( 'ibv-vo-description-' );
	$heading_id     = wp_unique_id( 'ibv-vo-heading-' );
	?>
	<section class="ibv-villa-overview" aria-labelledby="<?php echo esc_attr( $heading_id ); ?>">
		<?php // .ibv-h3 utility: same treatment as the h3s inside the prose description below (semantically it stays the section's h2). ?>
		<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="ibv-villa-overview__heading ibv-h3">
			<?php esc_html_e( 'Villa Overview', 'ibv' ); ?>
		</h2>

		<?php if ( $summary ) : ?>
			<p class="ibv-villa-overview__summary"><?php echo esc_html( $summary ); ?></p>
		<?php endif; ?>

		<?php if ( $description ) : ?>
			<?php
			// Shared "Read more" toggle (assets/js|css/readmore.*): the button
			// names the open-class it toggles on the controlled element. It
			// sits directly under the summary; the full description below is
			// completely hidden (--collapse variant, no teaser) until opened.
			wp_enqueue_style( 'ibv-readmore' );
			wp_enqueue_script( 'ibv-readmore' );
			?>
			<button
				type="button"
				class="ibv-readmore__toggle"
				data-ibv-readmore="is-open"
				data-ibv-readmore-less="<?php esc_attr_e( 'Read less', 'ibv' ); ?>"
				aria-expanded="false"
				aria-controls="<?php echo esc_attr( $description_id ); ?>"
			>
				<?php esc_html_e( 'Read more', 'ibv' ); ?>
			</button>
			<div
				id="<?php echo esc_attr( $description_id ); ?>"
				class="ibv-villa-overview__description ibv-prose ibv-readmore__content ibv-readmore__content--collapse"
			>
				<?php echo $description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the_content filter output, rendered as core does. ?>
			</div>
		<?php endif; ?>

		<?php ibv_core_amenity_ticks( $villa_id ); ?>

		<div class="ibv-villa-overview__price">
			<?php
			/* Bob shell: ACF villa_indicative_from_price is the static fallback; empty
			   field shows a date prompt (mirrors villa-card). The enquiry-panel JS swaps
			   in the dated average weekly rate while a priced search is active — it
			   toggles the from/unit spans and the two note lines, and restores the
			   static state when dates clear or the villa is unavailable. */
			?>
			<?php if ( $indicative ) : ?>
				<p class="ibv-villa-overview__price-row">
					<span class="ibv-villa-overview__price-from"><?php esc_html_e( 'From', 'ibv' ); ?></span>
					<span class="ibv-villa-overview__price-amount" data-bob-from-price="<?php echo esc_attr( (string) $villa_id ); ?>">
						<?php printf( '€%s', esc_html( number_format_i18n( (float) $indicative ) ) ); ?>
					</span>
					<span class="ibv-villa-overview__price-unit"><?php esc_html_e( '/ wk', 'ibv' ); ?></span>
				</p>
				<p class="ibv-villa-overview__price-note ibv-villa-overview__price-note--season"><?php esc_html_e( 'Price varies by season', 'ibv' ); ?></p>
				<p class="ibv-villa-overview__price-note ibv-villa-overview__price-note--dated" hidden><?php esc_html_e( 'Plus cleaning and damage waiver', 'ibv' ); ?></p>
			<?php else : ?>
				<?php /* No indicative price: show nothing rather than a date prompt (mirrors villa-card). The empty amount and hidden unit stay in the DOM so the enquiry-panel JS can still swap in a dated weekly rate while a priced search is active. No "From" prefix here — a dated rate is exact, and the JS reset would otherwise un-hide it next to an empty amount. */ ?>
				<p class="ibv-villa-overview__price-row">
					<span class="ibv-villa-overview__price-amount" data-bob-from-price="<?php echo esc_attr( (string) $villa_id ); ?>"></span>
					<span class="ibv-villa-overview__price-unit" hidden><?php esc_html_e( '/ wk', 'ibv' ); ?></span>
				</p>
				<p class="ibv-villa-overview__price-note ibv-villa-overview__price-note--dated" hidden><?php esc_html_e( 'Plus cleaning and damage waiver', 'ibv' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php
}
