<?php
/**
 * Section: Ibiza guide article preview.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Three article cards.
 */
function ibv_core_section_ibiza_guide_preview() {
	wp_enqueue_style( 'ibv-section-ibiza-guide-preview' );

	$intro      = get_field( 'guide_intro' );
	$descriptor = get_field( 'guide_descriptor' );
	$ids        = get_field( 'guide_articles' );
	$viewall    = get_field( 'guide_view_all_url' );

	if ( ! is_array( $ids ) ) {
		$ids = $ids ? [ $ids ] : [];
	}
	$ids = array_slice( array_filter( array_map( 'intval', $ids ) ), 0, 3 );

	if ( count( $ids ) < 1 ) {
		$q = new WP_Query(
			[
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => 3,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'fields'         => 'ids',
				'no_found_rows'  => true,
			]
		);
		$ids = $q->posts;
		wp_reset_postdata();
	}

	if ( count( $ids ) < 1 ) {
		return;
	}

	if ( ! $intro ) {
		$intro = __( 'Our Ibiza Guide', 'ibv' );
	}
	?>
	<section class="ibv-section-ibiza-guide-preview ibv-section ibv-section--surface-bg">
		<div class="ibv-container">

			<header class="ibv-section-ibiza-guide-preview__header">
				<div class="ibv-section-ibiza-guide-preview__heading">
					<h2 class="ibv-section-ibiza-guide-preview__title ibv-font-display">
						<?php echo esc_html( $intro ); ?>
					</h2>
					<?php if ( $descriptor ) : ?>
						<p class="ibv-section-ibiza-guide-preview__descriptor">
							<?php echo esc_html( $descriptor ); ?>
						</p>
					<?php endif; ?>
				</div>
				<?php
				if ( $viewall ) {
					ibv_core_button(
						[
							'url'     => esc_url( $viewall ),
							'label'   => __( 'View all Articles', 'ibv' ),
							'variant' => 'primary',
							'size'    => 'small',
						]
					);
				}
				?>
			</header>

			<hr class="ibv-section-ibiza-guide-preview__divider" aria-hidden="true">

			<div class="ibv-section-ibiza-guide-preview__grid">
				<?php
				foreach ( $ids as $pid ) {
					ibv_core_article_card( [ 'post_id' => $pid ] );
				}
				?>
			</div>
		</div>
	</section>
	<?php
}
