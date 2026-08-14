<?php
/**
 * Site footer.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tagline = get_field( 'footer_tagline', 'option' );
$legal   = get_field( 'footer_legal_html', 'option' );
$socials = get_field( 'socials', 'option' );
?>
</main>

<footer class="ibv-site-footer">
	<div class="ibv-container">

		<div class="ibv-site-footer__grid">

			<div class="ibv-site-footer__brand-col">
				<div class="ibv-site-footer__brand">
					<?php ibv_the_theme_logo( [ 'variant' => 'on-dark' ] ); ?>
				</div>
				<?php if ( $tagline ) : ?>
					<p class="ibv-site-footer__tagline"><?php echo esc_html( $tagline ); ?></p>
				<?php endif; ?>
				<?php if ( is_array( $socials ) && count( $socials ) ) : ?>
					<ul class="ibv-site-footer__socials">
						<?php
						foreach ( $socials as $row ) :
							if ( empty( $row['url'] ) ) {
								continue;
							}
							$net   = ! empty( $row['network'] ) ? strtolower( (string) $row['network'] ) : 'link';
							$label = ! empty( $row['network'] ) ? ucfirst( (string) $row['network'] ) : __( 'Social link', 'ibv' );
							$slug  = ( 'twitter' === $net ) ? 'x' : $net;
							$icon  = ibv_core_icon(
								$slug,
								[
									'set'  => 'brands',
									'size' => 18,
								]
							);
							?>
							<li>
								<a href="<?php echo esc_url( $row['url'] ); ?>" class="ibv-site-footer__social-link" rel="noopener noreferrer" target="_blank" aria-label="<?php echo esc_attr( $label ); ?>">
									<?php
									if ( $icon ) {
										echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- vendored brand SVG.
									} else {
										echo '<span class="ibv-site-footer__social-fallback">' . esc_html( strtoupper( function_exists( 'mb_substr' ) ? mb_substr( $net, 0, 1 ) : substr( $net, 0, 1 ) ) ) . '</span>';
									}
									?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div class="ibv-site-footer__col">
				<h3 class="ibv-site-footer__col-title ibv-font-display"><?php esc_html_e( 'Quick Links', 'ibv' ); ?></h3>
				<?php if ( has_nav_menu( 'footer_quick_links' ) ) : ?>
					<nav aria-label="<?php esc_attr_e( 'Quick links', 'ibv' ); ?>">
						<?php
						wp_nav_menu(
							[
								'theme_location' => 'footer_quick_links',
								'container'      => false,
								'menu_class'     => 'ibv-nav-list',
								'depth'          => 1,
								'fallback_cb'    => false,
							]
						);
						?>
					</nav>
				<?php endif; ?>
			</div>

			<div class="ibv-site-footer__col">
				<h3 class="ibv-site-footer__col-title ibv-font-display"><?php esc_html_e( 'Support', 'ibv' ); ?></h3>
				<?php if ( has_nav_menu( 'footer_support' ) ) : ?>
					<nav aria-label="<?php esc_attr_e( 'Support', 'ibv' ); ?>">
						<?php
						wp_nav_menu(
							[
								'theme_location' => 'footer_support',
								'container'      => false,
								'menu_class'     => 'ibv-nav-list',
								'depth'          => 1,
								'fallback_cb'    => false,
							]
						);
						?>
					</nav>
				<?php endif; ?>
			</div>

			<div class="ibv-site-footer__col ibv-site-footer__newsletter-col">
				<?php ibv_core_section_newsletter_cta(); ?>

				<?php
				$accreditations = get_field( 'accreditations', 'option' );
				$accreditation_items = array();
				if ( is_array( $accreditations ) ) {
					foreach ( $accreditations as $row ) {
						$img = isset( $row['image'] ) && is_array( $row['image'] ) ? $row['image'] : null;
						if ( $img && ! empty( $img['url'] ) ) {
							$accreditation_items[] = array(
								'img' => $img,
								'url' => isset( $row['url'] ) ? trim( (string) $row['url'] ) : '',
							);
						}
					}
				}
				if ( count( $accreditation_items ) ) :
					?>
					<ul class="ibv-site-footer__accreditations">
						<?php foreach ( $accreditation_items as $item ) : ?>
							<?php
							$img = $item['img'];
							$url = $item['url'];
							$alt = ! empty( $img['alt'] ) ? $img['alt'] : '';
							$w   = isset( $img['width'] ) ? (int) $img['width'] : 0;
							$h   = isset( $img['height'] ) ? (int) $img['height'] : 0;
							?>
							<li class="ibv-site-footer__accreditation">
								<?php if ( $url ) : ?>
									<a href="<?php echo esc_url( $url ); ?>" rel="noopener noreferrer" target="_blank">
								<?php endif; ?>
								<img
									src="<?php echo esc_url( $img['url'] ); ?>"
									alt="<?php echo esc_attr( $alt ); ?>"
									<?php if ( $w && $h ) : ?>
										width="<?php echo esc_attr( (string) $w ); ?>"
										height="<?php echo esc_attr( (string) $h ); ?>"
									<?php endif; ?>
									loading="lazy"
									decoding="async"
								>
								<?php if ( $url ) : ?>
									</a>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
					<?php
				endif;
				?>
			</div>

		</div>

		<hr class="ibv-site-footer__divider" aria-hidden="true">

		<div class="ibv-site-footer__legal">
			<p class="ibv-site-footer__copyright">
				&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name', 'display' ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'ibv' ); ?>
			</p>
			<?php if ( $legal ) : ?>
				<div class="ibv-site-footer__legal-detail ibv-prose ibv-prose--longform">
					<?php echo wp_kses_post( $legal ); ?>
				</div>
			<?php endif; ?>
			<?php // Reopens the cookie consent modal — code, not content, so it lives here rather than in the legal WYSIWYG. ?>
			<a href="#" class="ibv-site-footer__cc-link" data-ibv-cc-preferences><?php esc_html_e( 'Cookie preferences', 'ibv' ); ?></a>
		</div>

	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
