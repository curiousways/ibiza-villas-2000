<?php
/**
 * 404 template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="ibv-section">
	<div class="ibv-container ibv-container--narrow ibv-stack">
		<?php
		ibv_core_section_heading(
			[
				'eyebrow' => __( 'Page not found', 'ibv' ),
				'title'   => __( 'This page seems to have wandered off.', 'ibv' ),
				'level'   => 'h1',
			]
		);

		ibv_core_button(
			[
				'url'     => home_url( '/' ),
				'label'   => __( 'Back to home', 'ibv' ),
				'variant' => 'primary',
			]
		);
		?>
	</div>
</section>

<?php
get_footer();
