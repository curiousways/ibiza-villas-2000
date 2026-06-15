<?php
/**
 * Section: Concierge services grid.
 *
 * Renders the Concierge page's `concierge_services` repeater as a 3-up grid
 * of cards. Each card: 80px Lucide icon, serif title, 50px gold accent
 * rule, body description. Matches Figma node 1:6993 (frame mislabelled
 * "Our Ibiza Guide Section" — it's the services grid). Must be called
 * within the Concierge page loop so `get_field()` resolves to the page.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ibv_core_section_concierge_services() {
	$rows = get_field( 'concierge_services' );
	if ( ! is_array( $rows ) || ! count( $rows ) ) {
		return;
	}

	wp_enqueue_style( 'ibv-section-concierge-services' );
	?>
	<section class="ibv-section-concierge-services ibv-section ibv-section--surface-bg">
		<div class="ibv-container">
			<ul class="ibv-section-concierge-services__grid">
				<?php foreach ( $rows as $row ) :
					$icon  = isset( $row['icon'] ) ? (string) $row['icon'] : '';
					$title = isset( $row['title'] ) ? (string) $row['title'] : '';
					$desc  = isset( $row['description'] ) ? (string) $row['description'] : '';

					if ( '' === $title ) {
						continue;
					}
					?>
					<li class="ibv-concierge-card ibv-surface-card">
						<?php if ( '' !== $icon ) : ?>
							<?php ibv_core_the_icon( $icon, [ 'size' => 80, 'class' => 'ibv-concierge-card__icon' ] ); ?>
						<?php endif; ?>
						<h3 class="ibv-concierge-card__title ibv-font-display"><?php echo esc_html( $title ); ?></h3>
						<hr class="ibv-rule ibv-rule--gold" aria-hidden="true">
						<?php if ( '' !== $desc ) : ?>
							<p class="ibv-concierge-card__description"><?php echo esc_html( $desc ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
	<?php
}
