<?php
/**
 * Section: Related articles.
 *
 * Renders below the single-post body on its own surface-blue band.
 * Sources, in priority order:
 *   1. Posts in `ibiza-villa-rentals-guide` skip the picker and list
 *      the other posts in that category (oldest first, up to 5), with
 *      an optional Guide PDF button from the Ibiza Guide page.
 *   2. The `related_articles` ACF picker on the current post.
 *   3. Fallback: most recent posts that share at least one category
 *      with the current post, current post excluded.
 * Returns silently if the chosen source yields nothing — orphan posts
 * and posts in single-post categories simply won't show the section.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ibiza Guide page ID (the page using page-ibiza-guide.php).
 *
 * @return int Page ID, or 0.
 */
function ibv_get_ibiza_guide_page_id() {
	$pages = get_posts(
		[
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'page-ibiza-guide.php',
		]
	);

	return ! empty( $pages ) ? (int) $pages[0] : 0;
}

/**
 * Render the related-articles section.
 *
 * @param array $args {
 *     @type int $post_id Optional. Defaults to the post in the loop.
 *     @type int $limit   Optional. Card count. Defaults to 3.
 * }
 */
function ibv_core_section_related_articles( $args = [] ) {
	$args = wp_parse_args(
		$args,
		[
			'post_id' => get_the_ID(),
			'limit'   => 3,
		]
	);

	$post_id = (int) $args['post_id'];
	$limit   = (int) $args['limit'];
	if ( ! $post_id || $limit < 1 ) {
		return;
	}

	$is_guide      = has_category( 'ibiza-villa-rentals-guide', $post_id );
	$guide_pdf_url = '';
	$ids           = [];

	if ( $is_guide ) {
		$term = get_term_by( 'slug', 'ibiza-villa-rentals-guide', 'category' );
		if ( $term && ! is_wp_error( $term ) ) {
			$q = new WP_Query(
				[
					'post_type'      => 'post',
					'post_status'    => 'publish',
					'posts_per_page' => 5,
					'orderby'        => 'date',
					'order'          => 'ASC',
					'category__in'   => [ (int) $term->term_id ],
					'post__not_in'   => [ $post_id ],
					'fields'         => 'ids',
					'no_found_rows'  => true,
				]
			);
			$ids = $q->posts;
			wp_reset_postdata();
		}

		$guide_page_id = ibv_get_ibiza_guide_page_id();
		if ( $guide_page_id && function_exists( 'get_field' ) ) {
			$pdf = get_field( 'ig_guide_pdf', $guide_page_id );
			if ( is_string( $pdf ) && $pdf ) {
				$guide_pdf_url = $pdf;
			}
		}
	} else {
		// 1. Editorial picker takes precedence.
		$picker = get_field( 'related_articles', $post_id );
		$picker = is_array( $picker ) ? $picker : ( $picker ? [ $picker ] : [] );
		$ids    = array_filter( array_map( 'intval', $picker ) );
		$ids    = array_values( array_diff( $ids, [ $post_id ] ) );
		$ids    = array_slice( $ids, 0, $limit );

		// 2. Fallback: same-category auto query.
		if ( count( $ids ) < 1 ) {
			$cats = wp_get_post_categories( $post_id );
			if ( ! empty( $cats ) ) {
				$q   = new WP_Query(
					[
						'post_type'      => 'post',
						'post_status'    => 'publish',
						'posts_per_page' => $limit,
						'orderby'        => 'date',
						'order'          => 'DESC',
						'category__in'   => array_map( 'intval', $cats ),
						'post__not_in'   => [ $post_id ],
						'fields'         => 'ids',
						'no_found_rows'  => true,
					]
				);
				$ids = $q->posts;
				wp_reset_postdata();
			}
		}
	}

	if ( count( $ids ) < 1 ) {
		return;
	}

	wp_enqueue_style( 'ibv-section-related-articles' );
	?>
	<section class="ibv-section-related-articles ibv-section ibv-section--surface-blue">
		<div class="ibv-container">

			<header class="ibv-section-related-articles__header">
				<h2 class="ibv-section-related-articles__title ibv-font-display">
					<?php
					echo esc_html(
						$is_guide
							? __( 'Ibiza villa rentals guide', 'ibv' )
							: __( 'Related Articles', 'ibv' )
					);
					?>
				</h2>
				<hr class="ibv-rule ibv-rule--gold" aria-hidden="true">
			</header>

			<?php if ( $guide_pdf_url ) : ?>
				<div class="ibv-section-related-articles__cta">
					<?php
					ibv_core_button(
						[
							'url'     => $guide_pdf_url,
							'label'   => __( 'Download the guide (PDF)', 'ibv' ),
							'variant' => 'primary',
							'size'    => 'small',
							'target'  => '_blank',
						]
					);
					?>
				</div>
			<?php endif; ?>

			<div class="ibv-section-related-articles__grid">
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
