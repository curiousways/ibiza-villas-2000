<?php
/**
 * Section: About — Our Story.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ibv_core_section_about_story() {
	$entries = get_field( 'about_story_entries' );
	if ( ! is_array( $entries ) || ! count( $entries ) ) {
		return;
	}

	$eyebrow = (string) get_field( 'about_story_eyebrow' );
	$title   = (string) get_field( 'about_story_title' );
	$image   = get_field( 'about_story_image' );

	wp_enqueue_style( 'ibv-section-about-story' );
	?>
	<section class="ibv-section-about-story ibv-section ibv-section--surface-bg">
		<div class="ibv-container ibv-section-about-story__inner">
			<div class="ibv-section-about-story__copy">
				<header class="ibv-section-about-story__header">
					<?php if ( $eyebrow ) : ?>
						<p class="ibv-section-about-story__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
					<?php endif; ?>
					<?php if ( $title ) : ?>
						<h2 class="ibv-section-about-story__title ibv-font-display"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>
				</header>

				<dl class="ibv-section-about-story__entries">
					<?php foreach ( $entries as $entry ) :
						$label = (string) ( $entry['label'] ?? '' );
						$body  = (string) ( $entry['body'] ?? '' );
						if ( ! $body ) {
							continue;
						}
						?>
						<div class="ibv-section-about-story__entry">
							<?php if ( $label ) : ?>
								<dt class="ibv-section-about-story__label"><?php echo esc_html( $label ); ?></dt>
							<?php endif; ?>
							<dd class="ibv-section-about-story__body"><?php echo esc_html( $body ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			</div>

			<?php if ( ! empty( $image['ID'] ) ) : ?>
				<div class="ibv-section-about-story__media">
					<?php
					ibv_core_image(
						$image,
						'ibv-card',
						[ 'class' => 'ibv-section-about-story__image' ]
					);
					?>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}
