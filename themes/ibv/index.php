<?php
/**
 * Fallback template — used only when nothing more specific matches.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="ibv-section">
	<div class="ibv-container">
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article class="ibv-stack">
					<h1><?php the_title(); ?></h1>
					<div class="ibv-prose">
						<?php the_content(); ?>
					</div>
				</article>
				<?php
			endwhile;
			?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing here yet.', 'ibv' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
