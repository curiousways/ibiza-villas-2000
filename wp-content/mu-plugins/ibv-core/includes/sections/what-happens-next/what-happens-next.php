<?php
/**
 * Section: What happens next (booking confirmation).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Static three-step cards.
 */
function ibv_core_section_what_happens_next() {
	wp_enqueue_style( 'ibv-section-what-happens-next' );
	wp_enqueue_style( 'ibv-section-heading' );
	?>
	<section class="ibv-what-happens-next ibv-section">
		<div class="ibv-container">
			<?php
			ibv_core_section_heading(
				[
					'title' => __( 'What happens next', 'ibv' ),
					'level' => 'h2',
					'align' => 'center',
				]
			);
			?>
			<ol class="ibv-what-happens-next__grid ibv-grid ibv-grid--3">
				<li class="ibv-what-happens-next__card">
					<span class="ibv-what-happens-next__step" aria-hidden="true">1</span>
					<h3 class="ibv-what-happens-next__card-title"><?php esc_html_e( 'We will call or email you', 'ibv' ); ?></h3>
					<p class="ibv-what-happens-next__card-text"><?php esc_html_e( 'Our villa specialists pick up booking requests quickly and will confirm availability and details with you.', 'ibv' ); ?></p>
				</li>
				<li class="ibv-what-happens-next__card">
					<span class="ibv-what-happens-next__step" aria-hidden="true">2</span>
					<h3 class="ibv-what-happens-next__card-title"><?php esc_html_e( 'You receive a confirmation email', 'ibv' ); ?></h3>
					<p class="ibv-what-happens-next__card-text"><?php esc_html_e( 'Once everything is agreed, we email your booking confirmation with next steps and payment information.', 'ibv' ); ?></p>
				</li>
				<li class="ibv-what-happens-next__card">
					<span class="ibv-what-happens-next__step" aria-hidden="true">3</span>
					<h3 class="ibv-what-happens-next__card-title"><?php esc_html_e( 'Your Ibiza stay is confirmed', 'ibv' ); ?></h3>
					<p class="ibv-what-happens-next__card-text"><?php esc_html_e( 'Relax — we are on hand from booking through to check-in and beyond.', 'ibv' ); ?></p>
				</li>
			</ol>
		</div>
	</section>
	<?php
}
