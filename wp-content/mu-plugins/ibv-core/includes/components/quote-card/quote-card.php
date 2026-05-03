<?php
/**
 * Component: Quote card.
 *
 * Renders a guest testimonial as a card: gold star row, quote, then
 * attribution. Used on the homepage testimonials grid; available for
 * other surfaces (villa-page testimonial teaser may adopt it in a
 * future pass).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render a single quote card.
 *
 * @param array $args {
 *     @type string $quote       Required. The testimonial text.
 *     @type string $attribution Optional. Reviewer name + city.
 *     @type int    $stars       Star count, 1–5. Default 5.
 * }
 */
function ibv_core_quote_card( $args = [] ) {
	$defaults = [
		'quote'       => '',
		'attribution' => '',
		'stars'       => 5,
	];
	$args = wp_parse_args( $args, $defaults );

	if ( ! $args['quote'] ) {
		return;
	}

	$stars = max( 0, min( 5, (int) $args['stars'] ) );

	wp_enqueue_style( 'ibv-quote-card' );
	?>
	<blockquote class="ibv-quote-card">
		<?php if ( $stars > 0 ) : ?>
			<p class="ibv-quote-card__stars" aria-hidden="true"><?php echo esc_html( str_repeat( '★', $stars ) ); ?></p>
		<?php endif; ?>
		<p class="ibv-quote-card__quote"><?php echo esc_html( $args['quote'] ); ?></p>
		<?php if ( $args['attribution'] ) : ?>
			<footer class="ibv-quote-card__attr">— <?php echo esc_html( $args['attribution'] ); ?></footer>
		<?php endif; ?>
	</blockquote>
	<?php
}
