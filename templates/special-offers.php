<?php


global $spanish_law;

if ($spanish_law == 'yes') {
	$args = array(
		'post_type' => 'post',
		'category'  => 5,
		'posts_per_page' => 3,
		'post__not_in'  => array('3091','3223','4732','2894','5610','2928')
		//'meta_key'		=> 'property_for_sale', //filter out for sale properties, true is for sale, 0 not for sale (rental).
		//'meta_value'	=> 0
	);
} else {
	$args = array(
		'post_type' => 'post',
		'category'  => 5,
		'posts_per_page' => 3
		//'meta_key'		=> 'property_for_sale', //filter out for sale properties, true is for sale, 0 not for sale (rental).
		//'meta_value'	=> 0
	);
} 	

$news_query = new WP_Query( $args );

?>

<?php if ($news_query->have_posts()) : ?>

<div class="special-offers-wrap">

	<div class="special-offers">

		<?php while ($news_query->have_posts()) : $news_query->the_post(); ?>

			<div>

				<article class="news">

					<h4><a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h4>

					<?php echo excerpt(100); ?>

				</article>
				<div class="buttons">
					<a class="button dark" title="<?php echo get_cat_name(5); ?>" href="/special-offers">All special offers</a>
					<a class="button" title="<?php the_title(); ?>" href="<?php echo get_permalink(); ?>">Continue reading...</a>
				</div>

			</div>

		<?php endwhile; ?>

	</div>

</div>

<?php endif; ?>

<?php wp_reset_query(); ?>