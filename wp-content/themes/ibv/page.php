<?php
/**
 * Default page template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article class="ibv-section">
		<div class="ibv-container ibv-container--narrow ibv-stack">
			<?php
			ibv_core_section_heading(
				[
					'title' => get_the_title(),
					'level' => 'h1',
				]
			);
			?>
			<div class="ibv-prose ibv-prose--longform">
				<?php the_content(); ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
