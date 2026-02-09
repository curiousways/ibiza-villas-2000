<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title><?php wp_title(); ?></title>
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, width=device-width" />
	<link rel="shortcut icon" type="image/png" href="<?php bloginfo('stylesheet_directory'); ?>/images/favicon.png" />
	<?php wp_head(); ?>
	<?php get_template_part( 'templates/head', 'tracking' ); ?>

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

	<?php require_once(TEMPLATEPATH . '/templates/spanish_law.php'); ?>

	<script>
		(function(i, s, o, g, r, a, m) {
			i['GoogleAnalyticsObject'] = r;
			i[r] = i[r] || function() {
				(i[r].q = i[r].q || []).push(arguments)
			}, i[r].l = 1 * new Date();
			a = s.createElement(o),
				m = s.getElementsByTagName(o)[0];
			a.async = 1;
			a.src = g;
			m.parentNode.insertBefore(a, m)
		})(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
		ga('create', 'UA-28761746-2', {
			'siteSpeedSampleRate': 50
		});
		ga('create', 'UA-28761746-2', 'auto');
		ga('send', 'pageview');
	</script>

	<!-- <script>(function(){function f(){var e=document.createElement("script");e.type="text/javascript";e.async=true;e.src="//platform3.cloud-iq.com/cartrecovery/store.js?app_id=1758";var t = document.getElementsByTagName('head')[0];t.appendChild(e);}f();})();</script> -->

	<!-- Facebook Pixel Code -->
	<script>
		! function(f, b, e, v, n, t, s) {
			if (f.fbq) return;
			n = f.fbq = function() {
				n.callMethod ?
					n.callMethod.apply(n, arguments) : n.queue.push(arguments)
			};
			if (!f._fbq) f._fbq = n;
			n.push = n;
			n.loaded = !0;
			n.version = '2.0';
			n.queue = [];
			t = b.createElement(e);
			t.async = !0;
			t.src = v;
			s = b.getElementsByTagName(e)[0];
			s.parentNode.insertBefore(t, s)
		}(window,
			document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
		fbq('init', '442563459476640');
		fbq('track', 'PageView');
	</script>

	<script>
		jQuery(document).ready(function() {
			jQuery("#menu-item-2523").click(function() {
				jQuery('#myBooking').foundation('reveal', 'open');
			});
		});
	</script>

	<?php if (is_page('thanks-for-your-enquiry')): ?>
		<script>
			fbq('track', 'Lead', {
				content_name: 'thanks',
			});
		</script>
	<?php endif; ?>

	<style>
		i.fas.fa-star {
			color: #FDBF3D
		}

		.site-header {
			background: url(https://ibizavillas2000.com/wp-content/uploads/2020/07/Ibiza-Villas-2000-Villa-Paxti.jpg) !important;
			background-size: cover !important;
			background-position-y: 50%
		}

		.search-properties {
			margin-top: 0
		}

		h2.pn-section-0,
		h2.pn-section-1,
		h2.pn-section-2,
		h2.pn-section-3 {
			font-size: 1.4em
		}

		.home .site-header {
			background: url(../images/site-hero-5.jpg);
			min-height: 680px !important
		}

		.site-logo {
			margin-top: 10px !important;
			max-width: 170px !important
		}

		.offer-home {
			border-radius: 50%;
			width: 250px;
			height: 250px;
			background-color: #bb2e44;
			padding: 16px 8px 0;
			color: white;
			text-align: center;
			font-size: 25px;
			text-transform: uppercase;
			line-height: 50px;
			padding-top: 60px;
			left: -225px;
			top: 210px;
			position: absolute
		}

		.offer-home span {
			font-size: 40px;
			line-height: 40px;
			font-weight: bolder
		}

		.hero-title {
			margin: 80px 0 0
		}

		.villa-home {
			min-height: 400px;
			min-width: 80%;
			background-color: rgba(255, 255, 255, 0.8);
			float: left;
			position: relative;
			text-align: left
		}

		.hero-container {
			padding-top: 100px;
			width: 100% !important
		}

		.hero-main-title {
			font-weight: bold
		}

		.hero-left {
			left: 0;
			margin-top: 20px;
			padding-bottom: 50px
		}

		.hero-left h1 {
			text-align: left;
			font-weight: bold
		}

		.hero-left h2 {
			color: white
		}

		.hero-right {
			margin-top: 20px;
			padding-bottom: 50px
		}

		.hero-offer-btn {
			display: none;
			width: 100%;
			background-color: #B93047;
			padding: 0
		}

		.hero-center {
			margin-top: 0;
			padding-bottom: 0
		}

		@media only screen and (max-width:1079px) {
			.f-topbar-fixed {
				padding-top: inherit !important
			}

			.preheader {
				display: none
			}
		}

		@media only screen and (max-width:1430px) and (min-width:1280px) {
			.top-bar .top-bar-section>ul li:not(.has-form) a:not(.button) {
				padding: 0 10px;
				font-size: 90%
			}
		}

		@media only screen and (max-width:1280px) and (min-width:1120px) {
			.top-bar .top-bar-section>ul li:not(.has-form) a:not(.button) {
				padding: 0 10px;
				font-size: 80%
			}
		}

		@media only screen and (max-width:1080px) {
			select#gtranslate_selector {
				height: 2.75rem;
				color: #185366;
				font-size: 14px;
				margin-left: 6px
			}
		}

		@media only screen and (max-width:1120px) and (min-width:1080px) {
			.top-bar .top-bar-section>ul li:not(.has-form) a:not(.button) {
				padding: 0 10px;
				font-size: 70%
			}
		}

		@media only screen and (max-width:1020px) {
			.f-topbar-fixed {
				padding-top: inherit !important
			}

			select#gtranslate_selector {
				height: 2.75rem;
				color: #185366;
				font-size: 14px;
				margin-left: 6px
			}

			.preheader {
				display: none
			}

			.home-bullets {
				text-align: center
			}

			.villa-home {
				min-width: 100%;
				top: 128px
			}

			.offer-home {
				top: -77px;
				left: 35%;
				width: 125px;
				height: 125px;
				font-size: 12px;
				line-height: 15px;
				padding-top: 26px
			}

			.offer-home span {
				font-size: 20px;
				line-height: 32px
			}

			.hero-title h2 {
				display: none
			}
		}
	</style>
</head>

<body <?php body_class(); ?>>
	<div class="search-top">
		<div class="search-box">
			<div class="row">
				<div class="small-6 small-offset-3 end columns">
					<p class="mt-xl">Search the site</p>
					<?php get_search_form(); ?>
				</div>
			</div>
		</div>
	</div>

	<header class="site-header">
		<div class="row full-width collapse">
			<div class="small-12 columns">
				<?php get_template_part('templates/nav', 'topbarnew'); ?>
			</div>
		</div>

		<div class="row hero-container">
			<div class="small-12 medium-12 hero-title text-center columns">
				<h1 class="hero-main-title"></h1>
			</div>

			<div class="small-12 medium-12 large- hero-title columns hero-left">
				<h1>Best Value Holiday Rental Villas</h1>
				<h2>ONY 30% DEPOSIT IN 2023</h2>
				<h2>ESTABLISHED IN 2002</h2>
				<h2>MEMBERS OF THE VILLA ASSOCIATION</h2>
				<h2>SHORT BREAKS</h2>

				<div class="small-12 medium-12 large-12 hero-title columns hero-center">
					<a class="button hero-offer-btn" href="/special-offers">
						<?php the_field('offer_red_round_box_1', 3720); ?>
						<span><?php the_field('offer_red_round_box_line_2', 3720); ?></span>
						<?php the_field('offer_red_round_box_line_3', 3720); ?>
					</a>
				</div>
			</div>

			<div class="small-12 medium-12 large-5 hero-title text-center columns hero-right">
				<div class="villa-home">
					<?php get_template_part('templates/home-header-offer-como'); ?>
				</div>

				<!-- 
                <a href="/special-offers">
                    <div class="offer-home">
                        <?php the_field('offer_red_round_box_1', 3720); ?><br />
                        <span><?php the_field('offer_red_round_box_line_2', 3720); ?></span><br />
                        <?php the_field('offer_red_round_box_line_3', 3720); ?>
                    </div>
                </a>
                -->
			</div>
		</div>
	</header>

	<div class="row collapse full-width">
		<?php
		if (
			!is_page_template('templates/property-results.php') &&
			!is_page_template('templates/villas-small.php') &&
			!is_page_template('templates/villas-big.php')
		) {
			get_template_part('templates/search-home-new');
		}
		?>
	</div>