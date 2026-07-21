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
 * Content defaults to the queried post's `hero_*` ACF fields. Each piece can be
 * overridden via args for contexts with no post to read from (e.g. the 404
 * template, which sources its copy from Site Options).
 *
 * @param array $args {
 *     @type callable|null $after_copy Optional. Invoked with no arguments; output appears below the copy block.
 *     @type bool          $compact    Optional. When true, the hero uses a reduced min-height (480px instead of 720px). All other styling unchanged. Default false.
 *     @type int|array|null $image     Optional. Attachment ID or ACF image array. Overrides the `hero_image` ACF lookup. Pass 0 to suppress the lookup and render the fallback background.
 *     @type string|null   $title      Optional. Overrides the `hero_title` ACF lookup.
 *     @type string|null   $subtitle   Optional. Overrides the `hero_subtitle` ACF lookup.
 *     @type array|null    $cta        Optional. Args array passed to ibv_core_button(); rendered inside the copy block, after the subtitle.
 * }
 */
function ibv_core_section_hero( $args = [] ) {
	$defaults = [
		'after_copy' => null,
		'compact'    => false,
		'image'      => null,
		'title'      => null,
		'subtitle'   => null,
		'cta'        => null,
	];
	$args = wp_parse_args( $args, $defaults );

	wp_enqueue_style( 'ibv-section-hero' );

	// `null` means "not overridden" — only then do we touch the post context.
	$image    = null !== $args['image'] ? $args['image'] : get_field( 'hero_image' );
	$title    = null !== $args['title'] ? $args['title'] : get_field( 'hero_title' );
	$subtitle = null !== $args['subtitle'] ? $args['subtitle'] : get_field( 'hero_subtitle' );

	// Accepts an ACF image array or a bare attachment ID (mirrors ibv_core_image()).
	$image_id = 0;
	if ( is_array( $image ) && ! empty( $image['ID'] ) ) {
		$image_id = (int) $image['ID'];
	} elseif ( is_numeric( $image ) ) {
		$image_id = (int) $image;
	}

	$bg = $image_id ? wp_get_attachment_image_url( $image_id, 'ibv-hero' ) : '';

	$style_attr = '';
	if ( $bg ) {
		$style_attr = sprintf( '--ibv-hero-image: url(%s)', esc_url_raw( $bg ) );
	}

	$classes = [ 'ibv-section-hero', 'ibv-section' ];
	if ( ! empty( $args['compact'] ) ) {
		$classes[] = 'ibv-section-hero--compact';
	}
	?>
	<section class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $style_attr ? ' style="' . esc_attr( $style_attr ) . '"' : ''; ?>>
		<div class="ibv-container ibv-section-hero__inner">
			<div class="ibv-section-hero__copy">
				<?php if ( $title ) : ?>
					<h1 class="ibv-section-hero__title ibv-font-display"><?php echo esc_html( $title ); ?></h1>
				<?php endif; ?>
				<span class="ibv-rule ibv-rule--gold" aria-hidden="true"></span>
				<?php if ( $subtitle ) : ?>
					<p class="ibv-section-hero__subtitle"><?php echo esc_html( $subtitle ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $args['cta'] ) && is_array( $args['cta'] ) ) : ?>
					<?php ibv_core_button( $args['cta'] ); ?>
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
