<?php
/**
 * Section: Trust strip.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Trust logos row.
 */
function ibv_core_section_trust_strip() {
	wp_enqueue_style( 'ibv-section-trust-strip' );

	$rows = get_field( 'trust_strip' );
	if ( ! is_array( $rows ) || ! count( $rows ) ) {
		return;
	}
	?>
	<section class="ibv-section-trust-strip ibv-section ibv-section--alt">
		<div class="ibv-container">
			<div class="ibv-section-trust-strip__row">
				<?php foreach ( $rows as $row ) : ?>
					<?php
					$logo = $row['logo'] ?? null;
					$url  = ! empty( $row['url'] ) ? esc_url( $row['url'] ) : '';
					if ( empty( $logo ) ) {
						continue;
					}
					?>
					<?php if ( $url ) : ?>
						<a class="ibv-trust-item ibv-trust-item--link" href="<?php echo esc_url( $url ); ?>">
					<?php else : ?>
						<div class="ibv-trust-item">
					<?php endif; ?>
						<span class="ibv-trust-item__logo">
							<?php
							ibv_core_image(
								$logo,
								'thumbnail',
								[
									'class'    => 'ibv-trust-item__logo-img',
									'loading'  => 'lazy',
								]
							);
							?>
						</span>
						<?php if ( $url ) : ?>
							<svg class="ibv-trust-item__arrow" width="11" height="10" viewBox="0 0 18 18" fill="none" aria-hidden="true" focusable="false">
								<path d="M3 9h12M11 5l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						<?php endif; ?>
					<?php if ( $url ) : ?>
						</a>
					<?php else : ?>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}
