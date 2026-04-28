<?php
/**
 * Site header.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="ibv-u-visually-hidden" href="#ibv-main"><?php esc_html_e( 'Skip to content', 'ibv' ); ?></a>

<header class="ibv-site-header">
	<div class="ibv-container ibv-site-header__inner">
		<a class="ibv-site-header__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				echo esc_html( get_bloginfo( 'name', 'display' ) );
			}
			?>
		</a>

		<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<nav class="ibv-site-header__nav" aria-label="<?php esc_attr_e( 'Primary', 'ibv' ); ?>">
				<?php
				wp_nav_menu(
					[
						'theme_location' => 'primary',
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
</header>

<main id="ibv-main" class="ibv-site-main">
