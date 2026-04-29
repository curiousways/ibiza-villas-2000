<?php
/**
 * Template Name: Booking Confirmation
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<article class="ibv-booking-confirmation">

	<header class="ibv-booking-confirmation__hero">
		<div class="ibv-container">
			<div class="ibv-booking-confirmation__check" aria-hidden="true"><?php echo esc_html( '✓' ); ?></div>
			<h1 class="ibv-booking-confirmation__title"><?php esc_html_e( 'Booking Request Received', 'ibv' ); ?></h1>
		</div>
	</header>

	<?php
	ibv_core_section_what_happens_next();
	ibv_core_section_concierge_cross_sell( __( 'Explore concierge services', 'ibv' ) );

	$villa_slug = isset( $_GET['villa'] ) ? sanitize_title( wp_unslash( $_GET['villa'] ) ) : '';
	$context_villa_id = 0;
	if ( $villa_slug ) {
		$posts = get_posts(
			[
				'post_type'      => 'villas',
				'name'           => $villa_slug,
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			]
		);
		if ( ! empty( $posts ) ) {
			$context_villa_id = (int) $posts[0];
		}
	}

	ibv_core_section_villa_similar( $context_villa_id );
	?>

</article>

<?php
get_footer();
