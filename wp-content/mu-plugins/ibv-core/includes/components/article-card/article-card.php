<?php
/**
 * Component: Article card.
 *
 * Vertical card for a post — image, title, category + read-time meta,
 * gold rule, excerpt, ghost "Read more" link. Used by the Ibiza Guide
 * preview on the homepage and the article grid on the Ibiza Guide page.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render an article card for a given post.
 *
 * @param array $args {
 *     @type int $post_id Required. Post ID to render.
 * }
 */
function ibv_core_article_card( $args = [] ) {
	$defaults = [ 'post_id' => 0 ];
	$args     = wp_parse_args( $args, $defaults );

	$post_id = (int) $args['post_id'];
	if ( ! $post_id ) {
		return;
	}

	$post = get_post( $post_id );
	if ( ! $post ) {
		return;
	}

	wp_enqueue_style( 'ibv-article-card' );

	setup_postdata( $post );

	$cats    = get_the_category( $post_id );
	$catname = ( $cats && isset( $cats[0] ) ) ? $cats[0]->name : '';
	$mins    = ibv_estimate_reading_minutes( $post_id );
	$excerpt = get_the_excerpt( $post );
	if ( ! $excerpt ) {
		$excerpt = wp_trim_words( wp_strip_all_tags( $post->post_content ), 24, '…' );
	}
	$permalink = get_permalink( $post_id );
	?>
	<article class="ibv-article-card">
		<a href="<?php echo esc_url( $permalink ); ?>" class="ibv-article-card__media" aria-label="<?php echo esc_attr( get_the_title( $post ) ); ?>">
			<?php
			if ( has_post_thumbnail( $post_id ) ) {
				ibv_core_image(
					get_post_thumbnail_id( $post_id ),
					'ibv-card',
					[ 'class' => 'ibv-article-card__image' ]
				);
			}
			?>
		</a>
		<div class="ibv-article-card__body">

			<h3 class="ibv-article-card__title ibv-font-display">
				<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a>
			</h3>

			<p class="ibv-article-card__meta">
				<?php if ( $catname ) : ?>
					<span class="ibv-article-card__cat"><?php echo esc_html( $catname ); ?></span>
				<?php endif; ?>
				<span class="ibv-article-card__time">
					<?php
					printf(
						/* translators: %d minute count */
						esc_html( _n( '%d min read', '%d min read', $mins, 'ibv' ) ),
						(int) $mins
					);
					?>
				</span>
			</p>

			<hr class="ibv-article-card__rule" aria-hidden="true">

			<p class="ibv-article-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>

			<?php
			ibv_core_button(
				[
					'url'     => $permalink,
					'label'   => __( 'Read more', 'ibv' ),
					'variant' => 'ghost',
					'size'    => 'small',
				]
			);
			?>
		</div>
	</article>
	<?php

	wp_reset_postdata();
}
