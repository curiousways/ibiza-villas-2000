<?php
/**
 * Section: Featured article.
 *
 * Two-column block surfacing a single editorial post — image on one side,
 * content (category pill, title, read-time meta, excerpt, primary CTA) on
 * the other. Used on the Ibiza Guide page; can be reused anywhere a single
 * post needs prominent treatment.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the featured-article section.
 *
 * @param array $args {
 *     @type int    $post_id    Required. Post ID to feature.
 *     @type string $image_side 'left' | 'right'. Default 'left'.
 *     @type string $surface    Surface modifier slug. Default 'bg'.
 * }
 */
function ibv_core_section_featured_article( $args = [] ) {
	$defaults = [
		'post_id'    => 0,
		'image_side' => 'left',
		'surface'    => 'bg',
	];
	$args     = wp_parse_args( $args, $defaults );

	$post_id = (int) $args['post_id'];
	if ( ! $post_id ) {
		return;
	}

	$post = get_post( $post_id );
	if ( ! $post ) {
		return;
	}

	wp_enqueue_style( 'ibv-section-featured-article' );

	setup_postdata( $post );

	$cats    = get_the_category( $post_id );
	$catname = ( $cats && isset( $cats[0] ) ) ? $cats[0]->name : '';
	$mins    = ibv_estimate_reading_minutes( $post_id );
	$excerpt = get_the_excerpt( $post );
	if ( ! $excerpt ) {
		$excerpt = wp_trim_words( wp_strip_all_tags( $post->post_content ), 32, '…' );
	}
	$permalink = get_permalink( $post_id );

	$image_side = in_array( $args['image_side'], [ 'left', 'right' ], true ) ? $args['image_side'] : 'left';

	$valid_surfaces = [ 'bg', 'white', 'tint-teal', 'tint-gold', 'tint-blue', 'forest-green' ];
	$surface        = in_array( $args['surface'], $valid_surfaces, true ) ? $args['surface'] : 'bg';

	$root_classes = [
		'ibv-section-featured-article',
		'ibv-section',
		'ibv-section-featured-article--image-' . $image_side,
		'ibv-section--surface-' . $surface,
	];
	?>
	<section class="<?php echo esc_attr( implode( ' ', $root_classes ) ); ?>">
		<div class="ibv-container ibv-section-featured-article__inner">

			<?php if ( has_post_thumbnail( $post_id ) ) : ?>
				<a href="<?php echo esc_url( $permalink ); ?>" class="ibv-section-featured-article__media">
					<?php
					ibv_core_image(
						get_post_thumbnail_id( $post_id ),
						'ibv-card',
						[
							'class'    => 'ibv-section-featured-article__image',
							'loading'  => 'lazy',
							'decoding' => 'async',
						]
					);
					?>
				</a>
			<?php endif; ?>

			<div class="ibv-section-featured-article__content">
				<?php if ( $catname ) : ?>
					<span class="ibv-section-featured-article__pill"><?php echo esc_html( $catname ); ?></span>
				<?php endif; ?>

				<h2 class="ibv-section-featured-article__title ibv-font-display">
					<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a>
				</h2>

				<p class="ibv-section-featured-article__meta">
					<?php
					echo ibv_core_icon( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- vendored SVG.
						'clock',
						[
							'class' => 'ibv-section-featured-article__meta-icon',
							'size'  => 16,
						]
					);
					?>
					<span>
						<?php
						printf(
							/* translators: %d minute count */
							esc_html( _n( '%d min read', '%d min read', $mins, 'ibv' ) ),
							(int) $mins
						);
						?>
					</span>
				</p>

				<p class="ibv-section-featured-article__description"><?php echo esc_html( $excerpt ); ?></p>

				<?php
				ibv_core_button(
					[
						'url'     => $permalink,
						'label'   => __( 'Read More', 'ibv' ),
						'variant' => 'primary',
						'size'    => 'small',
					]
				);
				?>
			</div>

		</div>
	</section>
	<?php

	wp_reset_postdata();
}
