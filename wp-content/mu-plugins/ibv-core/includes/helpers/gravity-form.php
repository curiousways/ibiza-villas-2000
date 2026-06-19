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

	// Force-load GF's legacy datepicker stylesheet — already registered by
	// GF (gravityforms.php:3155) but GF only enqueues it on forms using
	// legacy markup (form_display.php:3145). Modern-markup forms with a
	// Date Picker field render the jQuery UI calendar with no styles
	// otherwise. Cheap (~5KB) belt-and-braces; harmless if the form has
	// no date field. Drop this line once GF ships a modern-markup
	// datepicker theme of its own.
	if ( wp_style_is( 'gforms_datepicker_css', 'registered' ) ) {
		wp_enqueue_style( 'gforms_datepicker_css' );
	}

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

/**
 * Make the `ibv-pax` guest-count select's GF placeholder a true placeholder.
 *
 * GF renders a field placeholder as a normal empty-value <option>, so "Guests"
 * shows up as a selectable row in the open dropdown. Adding `disabled hidden`
 * drops it from the list (modern browsers) and blocks re-selection, while
 * `selected` keeps it as the collapsed-state label. A real preserved value
 * still wins (single-select honours the last `selected` option in source
 * order), so error re-renders show the chosen guest count, not the placeholder.
 *
 * @param string   $content Rendered field HTML.
 * @param GF_Field $field   Field object.
 * @return string Field HTML.
 */
function ibv_gf_pax_placeholder_not_selectable( $content, $field ) {
	if ( ! is_object( $field ) || 'select' !== (string) $field->type ) {
		return $content;
	}
	$classes = preg_split( '/\s+/', (string) $field->cssClass, -1, PREG_SPLIT_NO_EMPTY );
	if ( ! in_array( 'ibv-pax', (array) $classes, true ) ) {
		return $content;
	}

	// Inject `selected disabled hidden` into the placeholder <option> — the only
	// empty-value option, since guest counts are 1–12. The lookahead matches the
	// empty `value` attribute wherever GF places it in the tag, so a future GF
	// markup change that emits other attributes before `value=` won't silently
	// no-op (the old anchored pattern assumed `value` came first). `?? $content`
	// keeps the field intact if PCRE ever fails — preg_replace returns null on
	// error, which would otherwise blank the whole select.
	return preg_replace(
		'/<option\b(?=[^>]*\svalue=([\'"])\1)/',
		'<option selected disabled hidden',
		$content,
		1
	) ?? $content;
}
add_filter( 'gform_field_content', 'ibv_gf_pax_placeholder_not_selectable', 10, 2 );
