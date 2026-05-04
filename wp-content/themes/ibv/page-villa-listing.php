<?php
/**
 * Template Name: Villa Listing
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$page_id = get_the_ID();
	?>

	<article class="ibv-villa-listing">

		<header class="ibv-villa-listing__hero">
			<div class="ibv-container ibv-villa-listing__hero-grid">
				<div class="ibv-villa-listing__hero-text">
					<div class="ibv-villa-listing__hero-content">
						<?php
						ibv_core_section_heading(
							[
								'title' => get_the_title(),
								'level' => 'h1',
							]
						);
						?>
						<div class="ibv-prose">
							<?php the_content(); ?>
						</div>
						<p class="ibv-villa-listing__large-group-note">
							<?php
							$contact_page = get_field( 'listing_contact_page', $page_id );
							$contact_href = $contact_page ? $contact_page : ibv_get_contact_page_url();
							echo wp_kses_post(
								sprintf(
									/* translators: %s: contact link HTML */
									__( 'Looking for 12 or more guests? %s', 'ibv' ),
									'<a href="' . esc_url( $contact_href ) . '">' . esc_html__( 'Contact us', 'ibv' ) . '</a>'
								)
							);
							?>
						</p>
					</div>
					<?php ibv_core_hero_search(); ?>
				</div>
				<div class="ibv-villa-listing__hero-image">
					<?php
					$hero_img = get_field( 'listing_hero_image', $page_id );
					if ( ! empty( $hero_img['ID'] ) ) {
						ibv_core_image( $hero_img, 'ibv-card', [ 'class' => 'ibv-villa-listing__hero-img-el' ] );
					}
					?>
				</div>
			</div>
		</header>

		<?php
		ibv_core_section_villa_listing_grid();
		ibv_core_section_listing_empty_state();
		ibv_core_alternative_accommodation();
		?>

	</article>

	<?php
endwhile;

get_footer();
