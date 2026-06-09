<?php
/**
 * Section: Villa Listing Hero.
 *
 * Split layout: copy left (title, intro, optional note, search),
 * image right. Used only on the villa listing page template.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ibv_core_section_villa_listing_hero() {
	wp_enqueue_style( 'ibv-section-villa-listing-hero' );

	$page_id = get_the_ID();
	if ( ! $page_id ) {
		return;
	}

	$title           = get_the_title( $page_id );
	$hero_image      = get_field( 'listing_hero_image', $page_id );
	$description     = (string) get_field( 'listing_description', $page_id );
	$note_text       = (string) get_field( 'listing_note_text', $page_id );
	$note_link_url   = (string) get_field( 'listing_note_link_url', $page_id );
	$note_link_label = (string) get_field( 'listing_note_link_label', $page_id );

	$show_note = $note_text && $note_link_url && $note_link_label;
	?>
	<section class="ibv-section-villa-listing-hero ibv-section ibv-section--surface-bg">
		<div class="ibv-container ibv-section-villa-listing-hero__inner">
			<div class="ibv-section-villa-listing-hero__copy">
				<?php if ( $title ) : ?>
					<h1 class="ibv-section-villa-listing-hero__title ibv-font-display">
						<?php echo esc_html( $title ); ?>
					</h1>
				<?php endif; ?>

				<span class="ibv-rule ibv-rule--gold" aria-hidden="true"></span>

				<?php if ( $description ) : ?>
					<p class="ibv-section-villa-listing-hero__intro"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>

				<?php if ( $show_note ) : ?>
					<p class="ibv-section-villa-listing-hero__note">
						<?php echo esc_html( $note_text ); ?>
						<a class="ibv-section-villa-listing-hero__note-link" href="<?php echo esc_url( $note_link_url ); ?>">
							<?php echo esc_html( $note_link_label ); ?>
						</a>
					</p>
				<?php endif; ?>

				<div class="ibv-section-villa-listing-hero__search">
					<?php ibv_core_hero_search(); ?>
				</div>
			</div>

			<?php if ( ! empty( $hero_image['ID'] ) ) : ?>
				<div class="ibv-section-villa-listing-hero__media">
					<?php
					ibv_core_image(
						$hero_image,
						'ibv-card',
						[ 'class' => 'ibv-section-villa-listing-hero__image' ]
					);
					?>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}
