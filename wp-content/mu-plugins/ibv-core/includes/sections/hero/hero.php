<?php
/**
 * Section: Hero (presentational; optional slot below copy).
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render hero section.
 *
 * @param array $args {
 *     @type callable|null $after_copy Optional. Invoked with no arguments; output appears below the copy block.
 * }
 */
function ibv_core_section_hero( $args = [] ) {
	$defaults = [
		'after_copy' => null,
	];
	$args = wp_parse_args( $args, $defaults );

	wp_enqueue_style( 'ibv-section-hero' );

	$image    = get_field( 'hero_image' );
	$title    = get_field( 'hero_title' );
	$subtitle = get_field( 'hero_subtitle' );

	$bg = '';
	if ( is_array( $image ) && ! empty( $image['ID'] ) ) {
		$bg = wp_get_attachment_image_url( (int) $image['ID'], 'ibv-hero' );
	}

	$style_attr = '';
	if ( $bg ) {
		$style_attr = sprintf( '--ibv-hero-image: url(%s)', esc_url_raw( $bg ) );
	}
	?>
	<section class="ibv-section-hero ibv-section"<?php echo $style_attr ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
		<div class="ibv-container ibv-section-hero__inner">
			<div class="ibv-section-hero__copy">
				<?php if ( $title ) : ?>
					<h1 class="ibv-section-hero__title ibv-font-display"><?php echo esc_html( $title ); ?></h1>
				<?php endif; ?>
				<span class="ibv-section-hero__rule" aria-hidden="true"></span>
				<?php if ( $subtitle ) : ?>
					<p class="ibv-section-hero__subtitle"><?php echo esc_html( $subtitle ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( is_callable( $args['after_copy'] ) ) : ?>
				<div class="ibv-section-hero__after-copy">
					<?php call_user_func( $args['after_copy'] ); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}
