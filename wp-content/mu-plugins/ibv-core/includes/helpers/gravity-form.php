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

	// No GF datepicker stylesheet. 3.0 swapped jQuery UI for WhatSock and
	// we do not load GF's theme CSS, so visible date fields (Concierge)
	// are skinned in gravity-forms.css. Hidden villa/accommodation range
	// inputs are disarmed below so VanillaCalendarPro owns them.

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
 * Rewrite a Gravity Forms submit control into a design-system <button>
 * with an inline arrow. GF 2.6+ may already emit <button> (or <a> for
 * type=link); older / legacy markup emits <input type="submit">. The
 * previous input-only rewrite appended a second label after </button>.
 *
 * Preserves GF id / class / onclick so AJAX submit and the JS gate keep
 * working.
 *
 * @param string $button        Markup from gform_submit_button.
 * @param string $label         Visible label.
 * @param string $label_class   Class on the inner label span.
 * @return string
 */
function ibv_core_gform_submit_button_with_arrow( $button, $label, $label_class ) {
	if ( ! is_string( $button ) || '' === $button ) {
		return $button;
	}

	$arrow = ibv_core_icon(
		'arrow-right',
		[
			'class' => 'ibv-button__arrow',
			'size'  => 18,
		]
	);
	$inner = '<span class="' . esc_attr( $label_class ) . '">' . esc_html( $label ) . '</span>' . $arrow;

	$out = $button;
	if ( preg_match( '/\sclass=/', $out ) ) {
		$out = preg_replace(
			'/\sclass=("|\')/',
			' class=$1ibv-button ibv-button--primary ibv-button--medium ',
			$out,
			1
		);
	} else {
		$out = preg_replace(
			'/^(<(?:input|button|a)\b)/i',
			'$1 class="ibv-button ibv-button--primary ibv-button--medium"',
			$out,
			1
		);
	}

	if ( preg_match( '/^<input\b/i', $out ) ) {
		$out = preg_replace( '/^<input\b/i', '<button', $out, 1 );
		$out = preg_replace( '/\svalue=("|\').*?\1/', '', $out, 1 );
		$out = preg_replace( '#\s*/?>\s*$#', '>' . $inner . '</button>', $out, 1 );
		return $out ?? $button;
	}

	$out = preg_replace( '/\svalue=("|\').*?\1/', '', $out, 1 );
	$out = preg_replace(
		'/^(<(?:button|a)\b[^>]*>).*?(<\/(?:button|a)>)\s*$/is',
		'$1' . $inner . '$2',
		$out,
		1
	);

	return $out ?? $button;
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

/**
 * Stop Gravity Forms 3.0's WhatSock datepicker on hidden range inputs.
 *
 * Villa + accommodation keep `type=date` / `dateType=datepicker` so GF
 * stores one YYYY-MM-DD value. 3.0 still emits `.gform-datepicker` and a
 * toggle button on that type, and its JS will init a second calendar even
 * when the <li> is `display:none`. Strip the hook classes and the toggle
 * so only the grafted VanillaCalendarPro picker writes those inputs.
 *
 * @param string   $content Rendered field HTML.
 * @param GF_Field $field   Field object.
 * @return string Field HTML.
 */
function ibv_gf_disarm_hidden_range_datepicker( $content, $field ) {
	if ( ! is_object( $field ) || 'date' !== (string) $field->type ) {
		return $content;
	}
	$classes = preg_split( '/\s+/', (string) $field->cssClass, -1, PREG_SPLIT_NO_EMPTY );
	if ( ! in_array( 'ibv-drp-hidden', (array) $classes, true ) ) {
		return $content;
	}

	// Remove the toggle first, while its class name is still intact.
	// A global class-token strip would also match inside
	// `gform-datepicker-toggle` (`\b` treats `-` as a boundary).
	$out = preg_replace(
		'/<button\b[^>]*\bgform-datepicker-toggle\b[^>]*>.*?<\/button>/is',
		'',
		$content
	);
	$out = preg_replace(
		'/<kbd\b[^>]*\bid=(["\'])keyboardHint_[^"\']+\1[^>]*>.*?<\/kbd>/is',
		'',
		$out ?? $content
	);

	$work = $out ?? $content;

	// Only strip datepicker hook classes from the date <input>.
	$work = preg_replace_callback(
		'/<input\b[^>]*>/is',
		static function ( $m ) {
			$tag = preg_replace(
				'/\s*\b(?:gform-datepicker|datepicker_with_icon|gdatepicker_with_icon|datepicker)\b/',
				'',
				$m[0]
			);
			if ( ! is_string( $tag ) ) {
				return $m[0];
			}
			if ( ! preg_match( '/\sdata-initialized=/', $tag ) ) {
				$tag = preg_replace( '/^(<input\b)/i', '$1 data-initialized="true"', $tag, 1 ) ?? $tag;
			}
			return $tag;
		},
		$work
	);

	return $work ?? $content;
}
add_filter( 'gform_field_content', 'ibv_gf_disarm_hidden_range_datepicker', 10, 2 );
