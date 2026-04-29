<?php
/**
 * Section: Similar villas.
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
	wp_enqueue_style( 'ibv-section-heading' );

	$similar_ids = [];
	if ( $villa_id ) {
		$raw = get_field( 'villa_similar', $villa_id );
		if ( is_array( $raw ) && count( $raw ) ) {
			$similar_ids = array_values( array_filter( array_map( 'intval', $raw ) ) );
		}
	}

	if ( count( $similar_ids ) ) {
		$query = new WP_Query(
			[
				'post_type'              => 'villas',
				'post__in'               => $similar_ids,
				'orderby'                => 'post__in',
				'posts_per_page'         => 3,
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
			]
		);
	} else {
		$not_in = $villa_id ? [ $villa_id ] : [];
		$query  = new WP_Query(
			[
				'post_type'           => 'villas',
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
	<section class="ibv-villa-similar ibv-section">
		<div class="ibv-container">
			<?php
			ibv_core_section_heading(
				[
					'title' => __( 'Similar Villas', 'ibv' ),
					'level' => 'h2',
				]
			);
			?>
			<div class="ibv-villa-similar__grid ibv-grid ibv-grid--3">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					ibv_core_villa_card(
						[
							'villa'   => get_the_ID(),
							'variant' => 'similar',
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
