<?php
/**
 * Site footer.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main>

<footer class="ibv-site-footer">
	<div class="ibv-container ibv-site-footer__inner">
		<?php if ( has_nav_menu( 'footer' ) ) : ?>
			<nav class="ibv-site-footer__nav" aria-label="<?php esc_attr_e( 'Footer', 'ibv' ); ?>">
				<?php
				wp_nav_menu(
					[
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'ibv-nav-list',
						'depth'          => 1,
						'fallback_cb'    => false,
					]
				);
				?>
			</nav>
		<?php endif; ?>

		<p class="ibv-site-footer__meta">
			&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name', 'display' ) ); ?>.
		</p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
