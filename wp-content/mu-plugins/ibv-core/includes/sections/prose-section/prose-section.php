<?php
/**
 * Section: Prose (free-text reading column).
 *
 * The Page Builder's free-text layout, and deliberately the thinnest section
 * in the plugin: the standard `.ibv-section` rhythm/surface wrapper, the
 * existing `.ibv-container--narrow` reading column, and the shared
 * `.ibv-prose` ruleset from `assets/css/typography.css` — the same treatment
 * the default page template and the article body use.
 *
 * No stylesheet of its own, and no handle in `shared-assets.php`: every class
 * it emits already ships inside the globally-enqueued `ibv-base` bundle.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render a prose block.
 *
 * @param array $args {
 *     @type string $content Editor HTML. ACF's wysiwyg field has already run
 *                           this through the `the_content` filters, so it
 *                           arrives with paragraphs and shortcodes resolved.
 *     @type string $surface Surface modifier slug ('bg' | 'white' | 'tint-teal' | 'tint-gold' | 'tint-blue' | 'forest-green'). Default 'bg'.
 * }
 */
function ibv_core_prose_section( array $args = [] ) {
	$defaults = [
		'content' => '',
		'surface' => 'bg',
	];
	$args = wp_parse_args( $args, $defaults );

	$content = (string) $args['content'];

	if ( '' === trim( $content ) ) {
		return;
	}

	$root_classes = [ 'ibv-prose-section', 'ibv-section' ];

	$valid_surfaces = [ 'bg', 'white', 'tint-teal', 'tint-gold', 'tint-blue', 'forest-green' ];
	if ( in_array( $args['surface'], $valid_surfaces, true ) ) {
		$root_classes[] = 'ibv-section--surface-' . $args['surface'];
	}
	?>
	<section class="<?php echo esc_attr( implode( ' ', $root_classes ) ); ?>">
		<div class="ibv-container ibv-container--narrow">
			<div class="ibv-prose">
				<?php echo wp_kses_post( $content ); ?>
			</div>
		</div>
	</section>
	<?php
}
