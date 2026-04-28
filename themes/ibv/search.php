<?php
/**
 * Search results template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="ibv-section">
	<div class="ibv-container ibv-stack">
		<?php
		ibv_core_section_heading(
			[
				'eyebrow' => __( 'Search results', 'ibv' ),
				/* translators: %s: search query */
				'title'   => sprintf( __( 'Results for "%s"', 'ibv' ), get_search_query() ),
				'level'   => 'h1',
			]
		);
		?>

		<?php if ( have_posts() ) : ?>
			<ul class="ibv-stack" role="list">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<li>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html( get_the_excerpt() ); ?></p>
					</li>
					<?php
				endwhile;
				?>
			</ul>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No results.', 'ibv' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
