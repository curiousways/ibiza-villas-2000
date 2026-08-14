<?php
/**
 * Site header.
 *
 * @package Ibiza_Villas_2000
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bookings_url = get_field( 'my_bookings_url', 'option' );
$villas_url   = ibv_get_search_villas_url();
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
	<div class="ibv-container">
		<div class="ibv-site-header__inner">
			<?php ibv_the_theme_logo( [ 'link_class' => 'ibv-site-header__brand custom-logo-link' ] ); ?>

			<button
				type="button"
				class="ibv-site-header__toggle"
				aria-expanded="false"
				aria-controls="ibv-site-menu"
				data-ibv-site-nav-toggle
			>
				<span class="ibv-u-visually-hidden"><?php esc_html_e( 'Menu', 'ibv' ); ?></span>
				<?php
				ibv_core_the_icon( 'menu', [ 'class' => 'ibv-site-header__toggle-icon ibv-site-header__toggle-icon--open' ] );
				ibv_core_the_icon( 'x', [ 'class' => 'ibv-site-header__toggle-icon ibv-site-header__toggle-icon--close' ] );
				?>
			</button>

			<div id="ibv-site-menu" class="ibv-site-header__menu">
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

				<div class="ibv-site-header__actions">
					<?php if ( $bookings_url ) : ?>
						<?php
						ibv_core_button(
							[
								'url'     => esc_url( $bookings_url ),
								'label'   => __( 'My Bookings', 'ibv' ),
								'variant' => 'secondary',
								'size'    => 'small',
								'target'  => '_blank',
							]
						);
						?>
					<?php endif; ?>
					<?php
					ibv_core_button(
						[
							'url'     => esc_url( $villas_url ),
							'label'   => __( 'Search Villas', 'ibv' ),
							'variant' => 'primary',
							'size'    => 'small',
						]
					);
					?>
				</div>
			</div>
		</div>
	</div>
</header>

<main id="ibv-main" class="ibv-site-main">
