<?php
/**
 * Section: Legal tabs.
 *
 * Horizontal nav of the legal pages — every Page assigned the `page-legal.php`
 * template, ordered by menu_order. Self-maintaining: add a legal page, assign
 * the template, and it appears here. The current page's tab is marked active
 * (Blue/500 underline + aria-current).
 *
 * Tab label comes from the page's `legal_tab_label` ACF field, falling back to
 * the page title.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the legal-pages tab nav.
 *
 * @param array $args {
 *     @type int    $current_id Page ID to mark active. Default: current queried page.
 *     @type string $surface    Surface modifier slug. Default 'bg'.
 * }
 */
function ibv_core_legal_tabs( array $args = [] ) {
	$defaults = [
		'current_id' => 0,
		'surface'    => 'bg',
	];
	$args = wp_parse_args( $args, $defaults );

	$current_id = $args['current_id'] ? (int) $args['current_id'] : (int) get_queried_object_id();

	$pages = get_posts(
		[
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'page-legal.php',
			'no_found_rows'  => true,
		]
	);

	if ( ! $pages ) {
		return;
	}

	wp_enqueue_style( 'ibv-legal-tabs' );

	$root_classes = [ 'ibv-legal-tabs', 'ibv-section' ];

	$valid_surfaces = [ 'bg', 'white', 'tint-teal', 'tint-gold', 'tint-blue', 'forest-green' ];
	if ( in_array( $args['surface'], $valid_surfaces, true ) ) {
		$root_classes[] = 'ibv-section--surface-' . $args['surface'];
	}
	?>
	<section class="<?php echo esc_attr( implode( ' ', $root_classes ) ); ?>">
		<div class="ibv-container">
			<nav class="ibv-legal-tabs__nav" aria-label="<?php esc_attr_e( 'Legal pages', 'ibv' ); ?>">
				<ul class="ibv-legal-tabs__list">
					<?php
					foreach ( $pages as $page ) :
						$label = (string) get_field( 'legal_tab_label', $page->ID );
						if ( '' === $label ) {
							$label = get_the_title( $page->ID );
						}
						$is_current   = ( (int) $page->ID === $current_id );
						$item_classes = 'ibv-legal-tabs__item' . ( $is_current ? ' ibv-legal-tabs__item--active' : '' );
						?>
						<li class="<?php echo esc_attr( $item_classes ); ?>">
							<a class="ibv-legal-tabs__link" href="<?php echo esc_url( get_permalink( $page->ID ) ); ?>"<?php echo $is_current ? ' aria-current="page"' : ''; ?>>
								<?php echo esc_html( $label ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>
		</div>
	</section>
	<?php
}
