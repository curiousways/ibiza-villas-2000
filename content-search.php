<?php
/**
 * Default template when no content type is set
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php /* Display the Posts Content in a div box. */ ?>
	<div class="entry-content">

		<?php if ( has_excerpt( $post->ID ) ) : ?>

			<h4><a href="<?php the_permalink() ?>"><?php the_title()?></a></h4>

		    <?php the_excerpt(); ?>


			<p style="padding-top:20px;"><a href="<?php the_permalink() ?>"" class="button gold">Read more</a></p>

		<?php else :  ?>

			<h4><a href="<?php the_permalink() ?>"><?php the_title()?></a></h4>

			<p><?php echo wp_trim_words( get_the_content(), 40, '...'); ?></p>

			<p><a href="<?php the_permalink() ?>"" class="button gold">Read more</a></p>

		<?php endif ; ?>


	</div>


</article>