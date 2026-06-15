<?php
/**
 * Gravity Forms helper — render a project-styled GF embed.
 *
 * Wraps the `[gravityform]` shortcode in `.ibv-gform` so the project's
 * base GF styles apply. Optional `--{variant}` modifier scopes context-
 * specific overrides (e.g. `card` for a panel-chrome form).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render a styled Gravity Forms embed.
 *
 * @param int|string $form_id Gravity Form ID. Empty / 0 renders nothing.
 * @param array      $args {
 *     @type string $variant Optional. `card` | `''`. Default `''` (base).
 *     @type bool   $ajax    Optional. AJAX submission. Default true.
 *     @type bool   $title   Optional. Show GF's own title. Default false.
 *     @type bool   $description Optional. Show GF's own description. Default false.
 *     @type string $class   Optional. Extra class on the wrapper.
 * }
 */
function ibv_core_gravity_form( $form_id, $args = [] ) {
	$form_id = (int) $form_id;
	if ( ! $form_id || ! function_exists( 'do_shortcode' ) ) {
		return;
	}

	$args = wp_parse_args(
		$args,
		[
			'variant'     => '',
			'ajax'        => true,
			'title'       => false,
			'description' => false,
			'class'       => '',
		]
	);

	wp_enqueue_style( 'ibv-gravity-forms' );

	$classes = [ 'ibv-gform' ];
	if ( ! empty( $args['variant'] ) ) {
		$classes[] = 'ibv-gform--' . sanitize_html_class( $args['variant'] );
	}
	if ( ! empty( $args['class'] ) ) {
		$classes[] = sanitize_html_class( $args['class'] );
	}

	$shortcode = sprintf(
		'[gravityform id="%d" title="%s" description="%s" ajax="%s"]',
		$form_id,
		$args['title'] ? 'true' : 'false',
		$args['description'] ? 'true' : 'false',
		$args['ajax'] ? 'true' : 'false'
	);
	?>
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<?php echo do_shortcode( $shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Gravity Forms output is server-rendered + sanitised by GF. ?>
	</div>
	<?php
}
