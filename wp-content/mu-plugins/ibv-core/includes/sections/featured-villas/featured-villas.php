<?php
/**
 * Section: Featured villas grid.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Homepage "Our Villas" grid.
 */
function ibv_core_section_featured_villas() {
	wp_enqueue_style( 'ibv-section-featured-villas' );

	$ids = get_field( 'featured_villas', 'option' );
	if ( ! is_array( $ids ) ) {
		$ids = $ids ? [ $ids ] : [];
	}
	$ids = array_slice( array_filter( array_map( 'intval', $ids ) ), 0, 8 );

	if ( count( $ids ) < 1 ) {
		$q = new WP_Query(
			[
				'post_type'      => 'villas',
				'post_status'    => 'publish',
				'posts_per_page' => 4,
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
	?>
	<section class="ibv-section-featured-villas ibv-section ibv-section--alt">
		<div class="ibv-container">
			<?php
			ibv_core_section_heading(
				[
					'title' => __( 'Our Villas', 'ibv' ),
					'level' => 'h2',
				]
			);
			?>
			<div class="ibv-section-featured-villas__grid ibv-grid ibv-grid--4">
				<?php
				foreach ( $ids as $vid ) {
					ibv_core_villa_card( [ 'villa' => $vid ] );
				}
				?>
			</div>
			<p class="ibv-section-featured-villas__cta">
				<?php
				ibv_core_button(
					[
						'url'     => ibv_get_search_villas_url(),
						'label'   => __( 'Search all villas', 'ibv' ),
						'variant' => 'primary',
					]
				);
				?>
			</p>
		</div>
	</section>
	<?php
}
