<?php
/**
 * Template Name: Ibiza Guide
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$featured_id = (int) get_field( 'ig_featured_article' );

	ibv_core_section_hero( [ 'compact' => true ] );

	if ( $featured_id ) {
		ibv_core_section_featured_article( [ 'post_id' => $featured_id ] );
	}

	wp_enqueue_style( 'ibv-facetwp' );
	?>
	<section class="ibv-section ibv-section--surface-bg ibv-section--rhythm-sm">
		<div class="ibv-container">
			<?php echo facetwp_display( 'facet', 'category' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- FacetWP returns sanitised markup. ?>
		</div>
	</section>
	<?php

	ibv_core_section_article_grid(
		[
			'exclude' => $featured_id ? [ $featured_id ] : [],
			'facetwp' => true,
		]
	);
	?>
	<section class="ibv-section ibv-section--surface-bg ibv-section--rhythm-sm">
		<div class="ibv-container">
			<?php echo facetwp_display( 'facet', 'pager' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- FacetWP returns sanitised markup. ?>
		</div>
	</section>
	<?php

	$nl_title = (string) get_field( 'newsletter_title' );
	$nl_body  = (string) get_field( 'newsletter_body' );

	if ( $nl_title || $nl_body ) :
		?>
		<section class="ibv-section ibv-section--surface-blue">
			<div class="ibv-container">
				<?php
				ibv_core_newsletter_form(
					[
						'title'       => $nl_title,
						'description' => $nl_body,
						'variant'     => 'default',
					]
				);
				?>
			</div>
		</section>
		<?php
	endif;

endwhile;

get_footer();
