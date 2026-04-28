<?php
/**
 * Default archive template — fallback for CPT/taxonomy archives without a dedicated template.
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
				'title' => get_the_archive_title(),
				'lead'  => get_the_archive_description(),
				'level' => 'h1',
			]
		);
		?>

		<?php if ( have_posts() ) : ?>
			<ul class="ibv-grid ibv-grid--3" role="list">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<li>
						<a href="<?php the_permalink(); ?>">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php ibv_core_image( get_post_thumbnail_id(), 'ibv-card' ); ?>
							<?php endif; ?>
							<h2><?php the_title(); ?></h2>
						</a>
					</li>
					<?php
				endwhile;
				?>
			</ul>

			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing here yet.', 'ibv' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
