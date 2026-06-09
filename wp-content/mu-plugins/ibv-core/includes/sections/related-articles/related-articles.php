<?php
/**
 * Section: Related articles.
 *
 * Renders below the single-post body on its own surface-blue band.
 * Sources, in priority order:
 *   1. The `related_articles` ACF picker on the current post.
 *   2. Fallback: most recent posts that share at least one category
 *      with the current post, current post excluded.
 * Returns silently if both sources yield nothing — orphan posts and
 * posts in single-post categories simply won't show the section.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
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

	if ( count( $ids ) < 1 ) {
		return;
	}

	wp_enqueue_style( 'ibv-section-related-articles' );
	?>
	<section class="ibv-section-related-articles ibv-section ibv-section--surface-blue">
		<div class="ibv-container">

			<header class="ibv-section-related-articles__header">
				<h2 class="ibv-section-related-articles__title ibv-font-display">
					<?php esc_html_e( 'Related Articles', 'ibv' ); ?>
				</h2>
				<hr class="ibv-rule ibv-rule--gold" aria-hidden="true">
			</header>

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
