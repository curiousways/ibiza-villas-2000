<?php
/**
 * Single post template — Ibiza Guide article.
 *
 * Ranks above singular.php in WP's template hierarchy for the `post`
 * post type. CPTs without their own template still fall through to
 * singular.php.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$post_id    = get_the_ID();
	$subtitle   = get_post_field( 'post_excerpt', $post_id );
	$cats       = get_the_category( $post_id );
	$cat_name   = ( $cats && isset( $cats[0] ) ) ? $cats[0]->name : '';
	$pull_quote = get_field( 'pull_quote' );

	wp_enqueue_style( 'ibv-article-shell' );
	?>

	<article class="ibv-article ibv-section ibv-section--surface-bg">
		<div class="ibv-container">

			<header class="ibv-article__header">
				<p class="ibv-article__meta">
					<span class="ibv-article__date"><?php echo esc_html( get_the_date() ); ?></span>
					<?php if ( $cat_name ) : ?>
						<span class="ibv-meta-dot" aria-hidden="true"></span>
						<span class="ibv-article__cat"><?php echo esc_html( $cat_name ); ?></span>
					<?php endif; ?>
				</p>
				<h1 class="ibv-article__title ibv-font-display"><?php the_title(); ?></h1>
				<?php if ( $subtitle ) : ?>
					<p class="ibv-article__subtitle"><?php echo esc_html( $subtitle ); ?></p>
				<?php endif; ?>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="ibv-article__featured">
					<?php
					ibv_core_image(
						get_post_thumbnail_id(),
						'full',
						[
							'class'   => 'ibv-article__featured-image',
							'loading' => 'eager',
						]
					);
					?>
				</figure>
			<?php endif; ?>

			<?php
			if ( is_array( $pull_quote ) && ! empty( $pull_quote['quote_text'] ) ) {
				ibv_core_pull_quote(
					[
						'text'         => $pull_quote['quote_text'],
						'author_name'  => $pull_quote['author_name'] ?? '',
						'author_title' => $pull_quote['author_title'] ?? '',
					]
				);
			}
			?>

			<div class="ibv-prose ibv-article__body">
				<?php the_content(); ?>
			</div>

		</div>
	</article>

	<?php
	/*
	 * TODO brief 02: Related articles section here.
	 * Reads ACF related_articles, falls back to category-related auto query,
	 * renders 3-up grid via ibv_core_article_card.
	 */

endwhile;

get_footer();
