<?php
/**
 * Section: About — The Team.
 *
 * Two-column split at desktop: header (eyebrow + display title)
 * on the left, members grid on the right (2 sub-columns of
 * name cards; image, role and bio are optional). Closing
 * paragraph sits below the members grid in the right column.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ibv_core_section_about_team() {
	$members = get_field( 'about_team_members' );
	if ( ! is_array( $members ) || ! count( $members ) ) {
		return;
	}

	$members = array_values(
		array_filter(
			$members,
			static function ( $member ) {
				return is_array( $member ) && (string) ( $member['name'] ?? '' ) !== '';
			}
		)
	);
	if ( ! count( $members ) ) {
		return;
	}

	$eyebrow = (string) get_field( 'about_team_eyebrow' );
	$title   = (string) get_field( 'about_team_title' );
	$closing = (string) get_field( 'about_team_closing' );

	wp_enqueue_style( 'ibv-section-about-team' );
	?>
	<section class="ibv-section-about-team ibv-section ibv-section--surface-bg">
		<div class="ibv-container ibv-section-about-team__inner">

			<header class="ibv-section-about-team__header">
				<?php if ( $eyebrow ) : ?>
					<p class="ibv-section-about-team__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>
				<?php if ( $title ) : ?>
					<h2 class="ibv-section-about-team__title ibv-font-display"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
			</header>

			<ul class="ibv-section-about-team__grid">
				<?php foreach ( $members as $member ) :
					$image = $member['image'] ?? null;
					$name  = (string) ( $member['name'] ?? '' );
					$role  = (string) ( $member['role'] ?? '' );
					$bio   = (string) ( $member['bio'] ?? '' );
					$has_image = ( is_array( $image ) && ! empty( $image['ID'] ) ) || ( is_numeric( $image ) && (int) $image > 0 );
					?>
					<li class="ibv-section-about-team__member">
						<?php if ( $has_image ) : ?>
							<div class="ibv-section-about-team__media">
								<?php
								ibv_core_image(
									$image,
									'ibv-card',
									[ 'class' => 'ibv-section-about-team__image' ]
								);
								?>
							</div>
						<?php endif; ?>
						<p class="ibv-section-about-team__name-role">
							<strong class="ibv-section-about-team__name"><?php echo esc_html( $name ); ?></strong>
							<?php if ( $role ) : ?>
								<span class="ibv-section-about-team__role"><?php echo esc_html( $role ); ?></span>
							<?php endif; ?>
						</p>
						<?php if ( $bio ) : ?>
							<p class="ibv-section-about-team__bio"><?php echo esc_html( $bio ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php if ( $closing ) : ?>
				<p class="ibv-section-about-team__closing ibv-font-display"><?php echo esc_html( $closing ); ?></p>
			<?php endif; ?>

		</div>
	</section>
	<?php
}
