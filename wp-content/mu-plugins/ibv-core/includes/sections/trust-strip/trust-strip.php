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

	$rows = get_field( 'trust_strip', 'option' );
	if ( ! is_array( $rows ) || ! count( $rows ) ) {
		return;
	}
	?>
	<section class="ibv-section-trust-strip ibv-section">
		<div class="ibv-container">
			<div class="ibv-section-trust-strip__row">
				<?php foreach ( $rows as $row ) : ?>
					<div class="ibv-trust-item">
						<?php
						$logo = $row['logo'] ?? null;
						$url  = ! empty( $row['url'] ) ? esc_url( $row['url'] ) : '';
						if ( ! empty( $logo ) ) {
							if ( $url ) {
								echo '<a class="ibv-trust-item__link" href="' . esc_url( $url ) . '">';
							}
							echo '<span class="ibv-trust-item__logo">';
							ibv_core_image( $logo, 'thumbnail', [ 'class' => 'ibv-trust-item__logo-img' ] );
							echo '</span>';
							if ( $url ) {
								echo '</a>';
							}
						}
						?>
						<?php if ( ! empty( $row['label'] ) ) : ?>
							<p class="ibv-trust-item__label"><?php echo esc_html( $row['label'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $row['subtext'] ) ) : ?>
							<p class="ibv-trust-item__sub"><?php echo esc_html( $row['subtext'] ); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}
