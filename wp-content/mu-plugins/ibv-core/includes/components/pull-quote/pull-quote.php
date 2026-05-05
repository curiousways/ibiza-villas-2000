<?php
/**
 * Component: Pull quote.
 *
 * Centred display quote + author attribution. Used by single posts
 * (sourced from ACF) and reusable elsewhere a centred quote treatment
 * is wanted.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render a pull quote.
 *
 * @param array $args {
 *     @type string $text         Required. The quoted body.
 *     @type string $author_name  Optional. Name of the speaker.
 *     @type string $author_title Optional. Role / title of the speaker.
 * }
 */
function ibv_core_pull_quote( $args = [] ) {
	$defaults = [
		'text'         => '',
		'author_name'  => '',
		'author_title' => '',
	];
	$args     = wp_parse_args( $args, $defaults );

	if ( ! $args['text'] ) {
		return;
	}

	wp_enqueue_style( 'ibv-pull-quote' );
	?>
	<figure class="ibv-pull-quote">
		<blockquote class="ibv-pull-quote__text ibv-font-display">
			<?php echo esc_html( $args['text'] ); ?>
		</blockquote>
		<?php if ( $args['author_name'] || $args['author_title'] ) : ?>
			<figcaption class="ibv-pull-quote__attribution">
				<?php if ( $args['author_name'] ) : ?>
					<span class="ibv-pull-quote__author-name"><?php echo esc_html( $args['author_name'] ); ?></span>
				<?php endif; ?>
				<?php if ( $args['author_name'] && $args['author_title'] ) : ?>
					<span class="ibv-meta-dot" aria-hidden="true"></span>
				<?php endif; ?>
				<?php if ( $args['author_title'] ) : ?>
					<span class="ibv-pull-quote__author-title"><?php echo esc_html( $args['author_title'] ); ?></span>
				<?php endif; ?>
			</figcaption>
		<?php endif; ?>
	</figure>
	<?php
}
