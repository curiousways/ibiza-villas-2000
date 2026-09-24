<?php
/**
 * Component: Floating WhatsApp button.
 *
 * Site-wide wa.me link. No script, no cookie. Number from Site Options.
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
	$url   = 'https://wa.me/' . $digits . '?text=' . rawurlencode( $text );

	wp_enqueue_style( 'ibv-whatsapp-button' );

	$icon = ibv_core_icon(
		'whatsapp',
		[
			'set'   => 'brands',
			'size'  => 24,
			'class' => 'ibv-whatsapp-button__icon',
		]
	);
	?>
	<a
		class="ibv-whatsapp-button"
		href="<?php echo esc_url( $url ); ?>"
		target="_blank"
		rel="noopener noreferrer"
		aria-label="<?php echo esc_attr( $label ); ?>"
	>
		<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- vendored brand SVG. ?>
		<span class="ibv-whatsapp-button__label"><?php echo esc_html( $label ); ?></span>
	</a>
	<?php
}
