<?php
/**
 * Section: Similar villas.
 *
 * Mirrors the canonical villa-card grid pattern from `featured-villas` —
 * bespoke header (title + button) + .ibv-grid of default villa cards.
 * Matches Figma node 1:6062 (header container 1:6064).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param int $villa_id Current villa (0 = no context).
 */
function ibv_core_section_villa_similar( $villa_id ) {
	$villa_id = (int) $villa_id;

	wp_enqueue_style( 'ibv-section-villa-similar' );
	wp_enqueue_style( 'ibv-villa-card' );
	wp_enqueue_style( 'ibv-button' );

	$similar_ids = [];
	if ( $villa_id ) {
		$raw = get_field( 'villa_similar', $villa_id );
		if ( is_array( $raw ) && count( $raw ) ) {
			$similar_ids = array_values( array_filter( array_map( 'intval', $raw ) ) );
		}
	}

	// post_status explicit on both queries: hand-picked villa_similar IDs
	// especially can point at villas since unpublished, and without it
	// WP_Query adds private posts for logged-in users who can read them.
	if ( count( $similar_ids ) ) {
		$query = new WP_Query(
			[
				'post_type'           => 'villas',
				'post_status'         => 'publish',
				'post__in'            => $similar_ids,
				'orderby'             => 'post__in',
				'posts_per_page'      => 3,
				'no_found_rows'       => true,
				'ignore_sticky_posts' => true,
			]
		);
	} else {
		$not_in = $villa_id ? [ $villa_id ] : [];
		$query  = new WP_Query(
			[
				'post_type'           => 'villas',
				'post_status'         => 'publish',
				'posts_per_page'      => 3,
				'post__not_in'        => $not_in,
				'orderby'             => 'date',
				'order'               => 'DESC',
				'no_found_rows'       => true,
				'ignore_sticky_posts' => true,
			]
		);
	}

	if ( ! $query->have_posts() ) {
		wp_reset_postdata();
		return;
	}
	?>
	<section class="ibv-villa-similar ibv-section ibv-section--surface-bg" aria-labelledby="ibv-villa-similar-title">
		<div class="ibv-container">
			<header class="ibv-villa-similar__header">
				<h2 id="ibv-villa-similar-title" class="ibv-villa-similar__title ibv-font-display"><?php esc_html_e( 'Similar Villas', 'ibv' ); ?></h2>
				<?php
				ibv_core_button(
					[
						'url'     => ibv_get_search_villas_url(),
						'label'   => __( 'View all villas', 'ibv' ),
						'variant' => 'primary',
						'size'    => 'small',
					]
				);
				?>
			</header>
			<div class="ibv-villa-similar__grid ibv-grid ibv-grid--3">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					ibv_core_villa_card(
						[
							'villa'        => get_the_ID(),
							'show_preview' => false,
						]
					);
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
	<?php
}
