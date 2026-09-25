<?php
/**
 * Component: Floating WhatsApp button.
 *
 * Site-wide click-to-chat link. No script, no cookie. Number from Site Options.
 * Desktop uses web.whatsapp.com/send (same as the old plugin); smaller viewports
 * use api.whatsapp.com/send so the app can open in-place.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the fixed WhatsApp chat button, or nothing if no number is set.
 */
function ibv_core_whatsapp_button() {
	$whatsapp = trim( (string) get_field( 'whatsapp_number', 'option' ) );
	$digits   = $whatsapp ? preg_replace( '/[^0-9]/', '', $whatsapp ) : '';

	if ( ! $digits ) {
		return;
	}

	$label = __( 'Chat with us on WhatsApp', 'ibv' );
	$text  = __( "Hi, I'm looking at villas on ibizavillas2000.com and have a question.", 'ibv' );
	$query = 'phone=' . rawurlencode( $digits ) . '&text=' . rawurlencode( $text );

	wp_enqueue_style( 'ibv-whatsapp-button' );

	$icon = ibv_core_icon(
		'whatsapp',
		[
			'set'   => 'brands',
			'size'  => 24,
			'class' => 'ibv-whatsapp-button__icon',
		]
	);

	ibv_core_whatsapp_button_link(
		'https://api.whatsapp.com/send?' . $query,
		$label,
		$icon,
		'app',
		false
	);
	ibv_core_whatsapp_button_link(
		'https://web.whatsapp.com/send?' . $query,
		$label,
		$icon,
		'web',
		true
	);
}

/**
 * @param string $url      Destination.
 * @param string $label    Visible text and aria-label.
 * @param string $icon     Inline SVG.
 * @param string $modifier 'app' | 'web'.
 * @param bool   $new_tab  Whether to open in a new tab.
 */
function ibv_core_whatsapp_button_link( $url, $label, $icon, $modifier, $new_tab ) {
	?>
	<a
		class="ibv-whatsapp-button ibv-whatsapp-button--<?php echo esc_attr( $modifier ); ?>"
		href="<?php echo esc_url( $url ); ?>"
		<?php if ( $new_tab ) : ?>
			target="_blank"
			rel="noopener noreferrer"
		<?php endif; ?>
		aria-label="<?php echo esc_attr( $label ); ?>"
	>
		<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- vendored brand SVG. ?>
		<span class="ibv-whatsapp-button__label"><?php echo esc_html( $label ); ?></span>
	</a>
	<?php
}
