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
			background: url(https://ibizavillas2000.com/wp-content/uploads/2020/07/Ibiza-Villas-2000-Villa-Paxti-2020.jpg) !important;
			background-size: cover !important;
			background-position-y: 50%
		}

		.search-properties {
			margin-top: 0
		}

		.home .site-header {
			background: url(../images/site-hero-5.jpg);
			background-size: cover;
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

		.hero-title h1 {
			font-size: 35px
		}

		.villa-home {
			min-width: 80%;
			background-color: rgba(255, 255, 255, 0.8);
			float: left;
			position: relative;
			text-align: left
		}

		.new-hero-container {
			margin-top: 20px;
			padding-bottom: 50px
		}

		.new-hero-left {
			text-align: left
		}

		.new-hero-left h1 {
			font-weight: bold
		}

		.new-hero-left h2 {
			color: white
		}

		.new-hero-button {
			font-weight: bold;
			width: 70%;
			background-color: #B93047;
			padding: 20px 10px;
			margin-left: -12px
		}

		.new-hero-right {
			margin-top: 20px;
			padding-bottom: 50px;
			padding-top: 20px
		}

		.new-hero-offer-btn {
			padding: 20px 10px;
			width: 100%;
			box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
			font-weight: bold
		}

		.new-hero-offer-container {
			padding-top: 20px
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

			.preheader {
				display: none
			}

			.home-bullets {
				text-align: center
			}

			.villa-home {
				min-width: 100%
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

			.small-12.medium-12.large-7.hero-title.columns {
				padding: 0 30px
			}
		}

		@media only screen and (min-width:40.0625em) {
			h2 {
				font-size: 1.425rem
			}

			.hero-title h1 {
				font-size: 2.7rem;
				line-height: 3.4rem
			}
		}

		@media only screen and (max-width:40em) {
			.hero-title img {
				height: 100%;
				margin-top: 10px
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

		<div class="row">
			<div class="small-12 medium-12 hero-title text-center columns">
				<h1 style="font-weight:bold;"></h1>
			</div>

			<div class="small-12 medium-12 large-7 hero-title columns new-hero-left new-hero-container">
				<h1 class="new-hero-left">Best Value Holiday Rental Villas</h1>
				<h2>ONLY 30% DEPOSIT IN 2023</h2>
				<h2>ESTABLISHED IN 2002</h2>
				<h2>MEMBERS OF THE VILLA ASSOCIATION</h2>
				<h2>SHORT BREAKS</h2>

				<div class="small-12 medium-12 large-12 hero-title columns new-hero-container">
					<a class="button new-hero-button" href="/special-offers">
						<?php the_field('offer_red_round_box_1', 3720); ?>
						<span><?php the_field('offer_red_round_box_line_2', 3720); ?></span>
						<?php the_field('offer_red_round_box_line_3', 3720); ?>
					</a>
				</div>
			</div>

			<div class="small-12 medium-12 large-5 hero-title text-center columns new-hero-right">
				<div class="villa-home">
					NEWOLD
					<?php get_template_part('templates/home-header-offer-como'); ?>

					<div class="column small-12 medium-12 large-12 new-hero-offer-container">
						<a class="button new-hero-offer-btn" href="<?php echo $offer_villa['link']; ?>">GET THE OFFER!</a>
					</div>
				</div>

				<!-- ROUND
                <a href="/special-offers">
                    <div class="offer-home">
                        <?php the_field('offer_red_round_box_1', 3720); ?><br />
                        <span><?php the_field('offer_red_round_box_line_2', 3720); ?></span><br />
                        <?php the_field('offer_red_round_box_line_3', 3720); ?>
                    </div>BBBB
                </a>
                -->

				<div class="text-center"></div>
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