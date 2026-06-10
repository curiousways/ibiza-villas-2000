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

	// Main villa description — the post content (keyword-rich, carries the
	// internal links). Rendered through the_content filter so formatting,
	// links, and embeds resolve exactly as core would output them.
	$content_raw = get_the_content( null, false, $villa_id );
	$summary     = ( '' !== trim( $content_raw ) ) ? apply_filters( 'the_content', $content_raw ) : '';
	$indicative  = get_field( 'villa_indicative_from_price', $villa_id );

	$summary_id = wp_unique_id( 'ibv-vo-summary-' );
	$heading_id = wp_unique_id( 'ibv-vo-heading-' );
	$plain_len  = $summary ? mb_strlen( wp_strip_all_tags( (string) $summary ) ) : 0;
	$use_clamp  = $plain_len > 200;
	?>
	<section class="ibv-villa-overview" aria-labelledby="<?php echo esc_attr( $heading_id ); ?>">
		<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="ibv-villa-overview__heading">
			<?php esc_html_e( 'Villa Overview', 'ibv' ); ?>
		</h2>

		<?php if ( $summary ) : ?>
			<div class="ibv-villa-overview__summary-block">
				<div
					id="<?php echo esc_attr( $summary_id ); ?>"
					class="ibv-villa-overview__summary ibv-prose<?php echo $use_clamp ? ' ibv-villa-overview__summary--clamp' : ''; ?>"
				>
					<?php echo $summary; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the_content filter output, rendered as core does. ?>
				</div>
				<?php if ( $use_clamp ) : ?>
					<button
						type="button"
						class="ibv-villa-overview__read-more"
						data-ibv-readmore
						aria-expanded="false"
						aria-controls="<?php echo esc_attr( $summary_id ); ?>"
					>
						<?php esc_html_e( 'Read more', 'ibv' ); ?>
					</button>
					<?php
					wp_enqueue_script( 'ibv-villa-overview' );
					$read_less = esc_js( __( 'Read less', 'ibv' ) );
					$read_more = esc_js( __( 'Read more', 'ibv' ) );
					$inline    = 'document.addEventListener("DOMContentLoaded",function(){document.querySelectorAll("[data-ibv-readmore]").forEach(function(btn){var id=btn.getAttribute("aria-controls");var el=id?document.getElementById(id):null;if(!el)return;btn.addEventListener("click",function(){var open=el.classList.toggle("ibv-villa-overview__summary--open");btn.setAttribute("aria-expanded",open?"true":"false");btn.textContent=open?"' . $read_less . '":"' . $read_more . '";});});});';
					wp_add_inline_script( 'ibv-villa-overview', $inline );
					?>
				<?php endif; ?>
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
				<p class="ibv-villa-overview__price-note ibv-villa-overview__price-note--dated" hidden><?php esc_html_e( 'For your selected dates', 'ibv' ); ?></p>
			<?php else : ?>
				<p class="ibv-villa-overview__price-row">
					<?php /* <label for> the enquiry-panel date trigger — clicking the prompt opens the date picker. The panel JS drops the for attribute while a hydrated price is shown so the button keeps its own accessible name. */ ?>
					<label class="ibv-villa-overview__price-amount ibv-villa-overview__price-amount--on-request" for="ibv-ep-when" data-bob-from-price="<?php echo esc_attr( (string) $villa_id ); ?>">
						<?php esc_html_e( 'Select your dates to see pricing', 'ibv' ); ?>
					</label>
					<span class="ibv-villa-overview__price-unit" hidden><?php esc_html_e( '/ wk', 'ibv' ); ?></span>
				</p>
				<p class="ibv-villa-overview__price-note ibv-villa-overview__price-note--dated" hidden><?php esc_html_e( 'For your selected dates', 'ibv' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php
}
