
<?php
$args = array(
	'post_type' => 'post',
	'category'  => 5,
	'posts_per_page' => 3,
	'post__not_in'  => array(),
);

$news_query = new WP_Query( $args );

?>

<?php if ($news_query->have_posts()) : ?>

<div class="special-offers-wrap">

	<div class="special-offers">

		<?php while ($news_query->have_posts()) : $news_query->the_post(); ?>

			<div>

				<article class="news">

					<h4><a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>

					<?php echo esc_html( excerpt(100) ); ?>

				</article>
				<div class="buttons">
					<a class="button dark" title="<?php echo esc_attr( get_cat_name(5) ); ?>" href="<?php echo esc_url( home_url( '/special-offers/' ) ); ?>"><?php esc_html_e( 'All special offers', 'ibiza-villas-2000' ); ?></a>
					<a class="button" title="<?php the_title_attribute(); ?>" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'Continue reading...', 'ibiza-villas-2000' ); ?></a>
				</div>

			</div>

		<?php endwhile; ?>

	</div>

</div>

<?php endif; ?>

<?php wp_reset_postdata(); ?>
