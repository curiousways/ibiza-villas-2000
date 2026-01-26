<?php
/**
 * Default template when no content type is set
 */
?>

<?php if ( is_single() ) : ?>

	<?php //get_template_part('templates/social-share'); ?>
	<?php get_template_part('templates/fb-like'); ?>

<?php endif; ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php /* Display the Posts Content in a div box. */ ?>
	<div class="entry-content">
		<?php the_content(); ?>
	</div>


</article>