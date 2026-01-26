<?php

$args = array(
    'post_type' => 'post',
    'posts_per_page' => 3,
    'cat' => 4

);

$news_query = new WP_Query( $args );

?>

<?php if ($news_query->have_posts()) : ?>

	<div class="latest-news">

		<?php while ($news_query->have_posts()) : $news_query->the_post(); ?>

			<div>

				<article class="news">

					<h4><a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h4>

					<?php echo excerpt(100); ?>

				</article>
				<div class="buttons">
					<a class="button dark" title="<?php echo get_cat_name(4); ?>" href="<?php echo get_category_link( 4 ); ?>">Latest news</a>
					<a class="button" title="<?php the_title(); ?>" href="<?php echo get_permalink(); ?>">Continue reading...</a>
				</div>

			</div>

		<?php endwhile; ?>

	</div>

<?php endif; ?>

<?php wp_reset_query(); ?>