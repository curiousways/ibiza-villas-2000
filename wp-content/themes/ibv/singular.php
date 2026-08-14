<?php
/**
 * Default singular template — fallback for CPTs without a dedicated template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article class="ibv-section">
		<div class="ibv-container ibv-stack">
			<?php
			$post_type_obj = get_post_type_object( get_post_type() );
			ibv_core_section_heading(
				[
					'eyebrow' => $post_type_obj ? $post_type_obj->labels->singular_name : '',
					'title'   => get_the_title(),
					'level'   => 'h1',
				]
			);
			?>

			<?php if ( has_post_thumbnail() ) : ?>
				<?php ibv_core_image( get_post_thumbnail_id(), 'ibv-hero' ); ?>
			<?php endif; ?>

			<div class="ibv-prose ibv-prose--longform">
				<?php the_content(); ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
