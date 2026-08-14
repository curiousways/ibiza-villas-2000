<?php
/**
 * Section: Legal content column.
 *
 * The reading column for a legal page: the page title as the document <h1>,
 * followed by the editor content (`the_content()`) styled with the shared
 * `.ibv-prose` ruleset. Must be called inside the loop.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the legal content column.
 *
 * @param array $args {
 *     @type string $surface Surface modifier slug. Default 'bg'.
 * }
 */
function ibv_core_legal_content( array $args = [] ) {
	$defaults = [
		'surface' => 'bg',
	];
	$args = wp_parse_args( $args, $defaults );

	wp_enqueue_style( 'ibv-legal-content' );

	$root_classes = [ 'ibv-legal-content', 'ibv-section' ];

	$valid_surfaces = [ 'bg', 'white', 'tint-teal', 'tint-gold', 'tint-blue', 'forest-green' ];
	if ( in_array( $args['surface'], $valid_surfaces, true ) ) {
		$root_classes[] = 'ibv-section--surface-' . $args['surface'];
	}
	?>
	<section class="<?php echo esc_attr( implode( ' ', $root_classes ) ); ?>">
		<div class="ibv-container ibv-legal-content__inner">
			<h1 class="ibv-legal-content__title ibv-font-display"><?php the_title(); ?></h1>
			<div class="ibv-legal-content__body ibv-prose ibv-prose--longform">
				<?php the_content(); ?>
			</div>
		</div>
	</section>
	<?php
}
