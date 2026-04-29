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
$phone_uk = get_field( 'phone_uk', 'option' );
$phone_ib = get_field( 'phone_ibiza', 'option' );
?>
</main>

<footer class="ibv-site-footer">
	<div class="ibv-container">
		<div class="ibv-site-footer__grid">
			<div class="ibv-site-footer__brand-col">
				<div class="ibv-site-footer__brand">
					<?php if ( has_custom_logo() ) : ?>
						<?php the_custom_logo(); ?>
					<?php else : ?>
						<strong><?php echo esc_html( get_bloginfo( 'name', 'display' ) ); ?></strong>
					<?php endif; ?>
				</div>
				<?php if ( $tagline ) : ?>
					<p class="ibv-site-footer__tagline"><?php echo esc_html( $tagline ); ?></p>
				<?php endif; ?>
				<?php if ( $phone_uk || $phone_ib ) : ?>
					<p class="ibv-site-footer__phones">
						<?php if ( $phone_uk ) : ?>
							<span><?php esc_html_e( 'UK', 'ibv' ); ?> <?php echo esc_html( $phone_uk ); ?></span><br>
						<?php endif; ?>
						<?php if ( $phone_ib ) : ?>
							<span><?php esc_html_e( 'Ibiza', 'ibv' ); ?> <?php echo esc_html( $phone_ib ); ?></span>
						<?php endif; ?>
					</p>
				<?php endif; ?>
				<?php if ( is_array( $socials ) && count( $socials ) ) : ?>
					<ul class="ibv-socials">
						<?php foreach ( $socials as $row ) : ?>
							<?php
							if ( empty( $row['url'] ) ) {
								continue;
							}
							$net = ! empty( $row['network'] ) ? $row['network'] : 'link';
							?>
							<li>
								<a href="<?php echo esc_url( $row['url'] ); ?>" rel="noopener noreferrer" target="_blank">
									<?php echo esc_html( ucfirst( (string) $net ) ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div class="ibv-site-footer__col">
				<h4><?php esc_html_e( 'Quick Links', 'ibv' ); ?></h4>
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
				<h4><?php esc_html_e( 'Support', 'ibv' ); ?></h4>
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

			<div class="ibv-site-footer__col">
				<?php ibv_core_section_newsletter_cta(); ?>
			</div>
		</div>

		<?php if ( $legal ) : ?>
			<div class="ibv-site-footer__legal ibv-prose">
				<?php echo wp_kses_post( $legal ); ?>
			</div>
		<?php endif; ?>

		<div class="ibv-site-footer__badges" aria-label="<?php esc_attr_e( 'Accreditations', 'ibv' ); ?>">
			<span class="ibv-site-footer__badge"><?php esc_html_e( 'AVAT member', 'ibv' ); ?></span>
			<span class="ibv-site-footer__badge"><?php esc_html_e( 'Observatorio turístico', 'ibv' ); ?></span>
		</div>

		<p class="ibv-site-footer__meta">
			&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name', 'display' ) ); ?>.
		</p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
