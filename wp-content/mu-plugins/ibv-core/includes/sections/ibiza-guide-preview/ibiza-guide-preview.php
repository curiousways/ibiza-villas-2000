<?php
/**
 * Section: Ibiza guide article preview.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Three article cards.
 */
function ibv_core_section_ibiza_guide_preview() {
	wp_enqueue_style( 'ibv-section-ibiza-guide-preview' );

	$intro   = get_field( 'guide_intro', 'option' );
	$ids     = get_field( 'guide_articles', 'option' );
	$viewall = get_field( 'guide_view_all_url', 'option' );

	if ( ! is_array( $ids ) ) {
		$ids = $ids ? [ $ids ] : [];
	}
	$ids = array_slice( array_filter( array_map( 'intval', $ids ) ), 0, 3 );

	if ( count( $ids ) < 1 ) {
		$q = new WP_Query(
			[
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => 3,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'fields'         => 'ids',
				'no_found_rows'  => true,
			]
		);
		$ids = $q->posts;
		wp_reset_postdata();
	}

	if ( count( $ids ) < 1 ) {
		return;
	}

	if ( ! $intro ) {
		$intro = __( 'Our Ibiza Guide', 'ibv' );
	}
	?>
	<section class="ibv-section-ibiza-guide-preview ibv-section">
		<div class="ibv-container">
			<div class="ibv-section-ibiza-guide-preview__head">
				<?php
				ibv_core_section_heading(
					[
						'title' => $intro,
						'level' => 'h2',
					]
				);
				if ( $viewall ) {
					ibv_core_button(
						[
							'url'     => esc_url( $viewall ),
							'label'   => __( 'View all articles', 'ibv' ),
							'variant' => 'ghost',
						]
					);
				}
				?>
			</div>
			<div class="ibv-section-ibiza-guide-preview__grid ibv-grid ibv-grid--3">
				<?php
				foreach ( $ids as $pid ) {
					$post = get_post( $pid );
					if ( ! $post ) {
						continue;
					}
					setup_postdata( $post );
					$cats    = get_the_category( $pid );
					$catname = ( $cats && isset( $cats[0] ) ) ? $cats[0]->name : '';
					$mins    = ibv_estimate_reading_minutes( $pid );
					$excerpt = get_the_excerpt( $post );
					if ( ! $excerpt ) {
						$excerpt = wp_trim_words( wp_strip_all_tags( $post->post_content ), 24, '…' );
					}
					?>
					<article class="ibv-article-card">
						<a href="<?php the_permalink( $pid ); ?>" class="ibv-article-card__media">
							<?php
							if ( has_post_thumbnail( $pid ) ) {
								ibv_core_image(
									get_post_thumbnail_id( $pid ),
									'ibv-card',
									[ 'class' => 'ibv-article-card__image' ]
								);
							}
							?>
						</a>
						<div class="ibv-article-card__body">
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
							<h3 class="ibv-article-card__title">
								<a href="<?php the_permalink( $pid ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a>
							</h3>
							<p class="ibv-article-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
							<?php
							ibv_core_button(
								[
									'url'     => get_permalink( $pid ),
									'label'   => __( 'Read more', 'ibv' ),
									'variant' => 'ghost',
									'size'    => 'small',
								]
							);
							?>
						</div>
					</article>
					<?php
				}
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
	<?php
}
