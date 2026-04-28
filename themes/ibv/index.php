<?php
/**
 * Fallback template — scaffold only.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="ibv-site-main" role="main">
	<p class="ibv-site-main__message">
		<?php esc_html_e( 'Ibiza Villas 2000 — scaffold theme is inactive until pass 3b.', 'ibv' ); ?>
	</p>
</main>

<?php
get_footer();
