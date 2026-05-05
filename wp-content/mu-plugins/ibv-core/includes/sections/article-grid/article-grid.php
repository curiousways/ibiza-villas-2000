<?php
/**
 * Section: Article grid.
 *
 * Three-column grid of article cards from a WP_Query of published posts.
 * Generic enough for related-posts, category archives, etc. Used by the
 * Ibiza Guide page; brief 03 (FacetWP) wraps this for filter + pagination.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the article-grid section.
 *
 * @param array $args {
 *     @type int[]  $exclude        Post IDs to exclude from the query.
 *     @type int    $posts_per_page Posts to fetch. Default 9 (3 cols × 3 rows).
 *     @type string $surface        Surface modifier slug. Default 'bg'.
 * }
 */
function ibv_core_section_article_grid( $args = [] ) {
	$defaults = [
		'exclude'        => [],
		'posts_per_page' => 9,
		'surface'        => 'bg',
	];
	$args     = wp_parse_args( $args, $defaults );

	$q = new WP_Query(
		[
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => (int) $args['posts_per_page'],
			'orderby'        => 'date',
			'order'          => 'DESC',
			'post__not_in'   => array_map( 'intval', (array) $args['exclude'] ),
			'no_found_rows'  => true,
		]
	);

	if ( ! $q->have_posts() ) {
		return;
	}

	wp_enqueue_style( 'ibv-section-article-grid' );

	$valid_surfaces = [ 'bg', 'white', 'tint-teal', 'tint-gold', 'tint-blue', 'forest-green' ];
	$surface        = in_array( $args['surface'], $valid_surfaces, true ) ? $args['surface'] : 'bg';
	?>
	<section class="ibv-section-article-grid ibv-section ibv-section--surface-<?php echo esc_attr( $surface ); ?>">
		<div class="ibv-container">
			<div class="ibv-section-article-grid__grid">
				<?php
				foreach ( $q->posts as $post ) {
					ibv_core_article_card( [ 'post_id' => $post->ID ] );
				}
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
	<?php
}
