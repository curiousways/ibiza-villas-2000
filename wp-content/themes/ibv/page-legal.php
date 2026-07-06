<?php
/**
 * Template Name: Legal
 *
 * Shared template for the four site-map legal pages (Website T&Cs, Privacy
 * Policy, Cookie Policy, Accessibility Statement). Composition only:
 *
 *   title-band ("Legals" + last-updated)
 *   → legal-tabs (self-maintaining nav of page-legal.php pages)
 *   → legal-content (page title as <h1> + the_content in .ibv-prose)
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	ibv_core_title_band(
		[
			'title'   => __( 'Legals', 'ibv' ),
			/* translators: %s: month and year the page was last updated. */
			'meta'    => sprintf( __( 'Last updated: %s', 'ibv' ), get_the_modified_date( 'F Y' ) ),
			'surface' => 'bg',
		]
	);

	ibv_core_legal_tabs();

	ibv_core_legal_content();

endwhile;

get_footer();
