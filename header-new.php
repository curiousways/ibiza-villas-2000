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
			jQuery("#menu-item-13091").click(function() {
				jQuery('#myBooking').foundation('reveal', 'open');
			});
			jQuery("#menu-item-2523").click(function() {
				jQuery('#myBooking').foundation('reveal', 'open');
			});
		});
	</script>

	<script>
		// Détection de Safari
		if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
			document.body.classList.add('safari');
		}
	</script>

	<?php if (is_page('thanks-for-your-enquiry')): ?>
		<script>
			fbq('track', 'Lead', {
				content_name: 'thanks',
			});
		</script>
	<?php endif; ?>

	<style>
		.top-bar:before {
			position: fixed
		}

		@media only screen and (min-width:1080px) {
			.fixed .top-bar {
				padding-top: 120px;
				padding-left: 20px;
				padding-bottom: 0;
				margin-top: 30px
			}

			.top-bar-section {
				margin-top: 5px
			}

			.safari .fixed .top-bar {
				padding-top: 160px;
				padding-left: 20px;
				padding-bottom: 0
			}
		}

		article {
			max-width: 600px;
			margin: 1em auto;
			overflow: hidden;
			position: relative;
			min-height: 4em
		}

		.example-left {
			white-space: nowrap;
			position: absolute;
			text-transform: uppercase;
			line-height: 0;
			-webkit-animation: mymove 30s linear infinite;
			animation: mymove 30s linear infinite alternate
		}

		@-webkit-keyframes mymove {
			from {
				left: 0
			}

			to {
				left: -1140px
			}
		}

		@keyframes mymove {
			from {
				left: 0
			}

			to {
				left: -1140px
			}
		}

		@-webkit-keyframes urmove {
			from {
				right: 0
			}

			to {
				right: -140px
			}
		}

		@keyframes urmove {
			from {
				right: 0
			}

			to {
				right: -140px
			}
		}

		li#menu-item-2198 {
			padding-right: 20px !important;
			margin-top: 5px
		}

		@media screen and (max-width:640px) {
			.lobutton {
				padding-bottom: 50px;
				font-size: 14px
			}

			.hero-title img {
				height: 100%;
				position: relative;
				right: 0;
				z-index: 2;
				max-height: 110px;
				min-width: 110px
			}

			.col-lolo {
				padding-left: 0
			}

			.displo {
				display: none
			}

			.hero-title {
				margin: 50px 0 0 !important
			}

			.bouts {
				margin-top: 24px !important;
				border-radius: 3px !important;
				font-weight: bold !important;
				width: 100% !important;
				background-color: #FEBF3E !important;
				padding: 20px 12px !important;
				margin-left: -45px !important
			}
		}

		@media screen and (min-width:640px) and (max-width:1000px) {
			.lobutton {
				margin-top: 4%;
				margin-left: 0;
				padding-bottom: 50px
			}

			.hero-title img {
				height: 100%;
				position: relative;
				right: 0;
				z-index: 2;
				max-height: 110px;
				min-width: 110px
			}

			.col-lolo {
				padding-left: 0
			}

			.hero-title {
				margin: 50px 0 0 !important
			}

			.bouts {
				margin-top: 29px !important;
				border-radius: 3px !important;
				font-weight: bold !important;
				width: 90% !important;
				background-color: #FEBF3E !important;
				padding: 20px 10px !important;
				margin-left: -54px !important
			}
		}

		@media screen and (min-width:1000px) and (max-width:1120px) {
			.lobutton {
				margin-top: 18px;
				margin-left: -50px;
				padding-bottom: 50px
			}

			.hero-title img {
				height: 100%;
				position: relative;
				right: 0;
				z-index: 2;
				max-height: 120px;
				min-width: 120px
			}

			.col-lolo {
				margin-left: -14px;
				padding-left: 0
			}
		}

		@media screen and (min-width:1120px) {
			.lobutton {
				margin-top: 0;
				margin-left: 0;
				padding-bottom: 50px
			}

			.hero-title img {
				height: 100%;
				position: relative;
				right: 0;
				z-index: 2;
				max-height: 120px;
				min-width: 120px
			}

			.col-lolo {
				padding-left: 0;
				margin-left: -14px
			}
		}

		@media only screen and (min-width:992px) and (max-width:1080px) {
			.hero-title {
				margin: 40px 0 0 !important
			}
		}

		@media screen and (min-width:992px) {
			.bouts {
				margin-top: 29px !important;
				border-radius: 3px !important;
				font-weight: bold !important;
				width: 90% !important;
				background-color: #FEBF3E !important;
				padding: 20px 10px !important;
				margin-left: -54px !important
			}
		}

		i.fas.fa-star {
			color: #FDBF3D
		}

		.clean-nomads {
			height: auto !important;
			position: relative;
			right: 0;
			z-index: 2
		}

		.site-header {
			background-image: linear-gradient(90deg, rgba(96, 181, 155, 1) 0%, rgba(96, 181, 155, 0) 60%, rgba(96, 181, 155, 0) 100%), url('https://ibizavillas2000.com/wp-content/uploads/2020/07/Ibiza-Villas-2000-Villa-Paxti-2020.jpg');
			background-size: cover !important;
			background-position-y: 50%
		}

		.search-properties {
			margin-top: 0
		}

		.home .site-header {
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
			margin: 180px 0 0
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

		@media only screen and (max-width:1079px) {
			.f-topbar-fixed {
				padding-top: inherit !important
			}

			.preheader {
				display: none
			}
		}

		@media only screen and (max-width:1280px) and (min-width:1120px) {
			.top-bar .top-bar-section>ul li:not(.has-form) a:not(.button) {
				padding: 0 10px;
				font-size: 80%
			}
		}

		@media only screen and (max-width:1430px) and (min-width:1280px) {
			.top-bar .top-bar-section>ul li:not(.has-form) a:not(.button) {
				padding: 0 14px;
				font-size: 85%
			}
		}

		@media only screen and (max-width:1120px) and (min-width:1080px) {
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

			select.notranslate {
				top: 2px;
				background: #54D7B1;
				color: #fff
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
				display:
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

		@media only screen and (max-width:1200px) and (min-width:1020px) {
			.hideunder {
				display: none
			}
		}

		@media only screen and (max-width:1020px) and (min-width:0px) {
			.imgrespcomo {
				display: none;
				max-width: 70%;
				margin: 0;
				padding: 20px
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

	<header class="site-header-gradient">
		<div class="site-header">
			<!-- NAVIGATION -->
			<div class="row full-width collapse">
				<div class="small-12 columns">
					<?php get_template_part('templates/nav', 'topbarnew'); ?>
				</div>
			</div>

			<!-- HERO -->
			<div class="row">
				<!-- KEEP THIS -->
				<div class="small-12 medium-12 hero-title text-center columns">
					<h1 style="font-weight:bold;font-size"></h1>
				</div>

				<!-- TEXT -->
				<div style="margin-top:20px;padding-bottom:50px;" class="small-12 medium-12 large-6 hero-title columns">
					<h1 style="letter-spacing:2px!important;text-align:left;font-weight:bold;font-size;max-width:558px;">Best Value Holiday Rental Villas</h1>
					<h2 style="letter-spacing:2px!important;color:white;">ONLY 30% DEPOSIT</h2>
					<h2 style="letter-spacing:2px!important;color:white;">ESTABLISHED IN 2002</h2>
					<h2 style="letter-spacing:2px!important;color:white;">MEMBERS OF THE VILLA ASSOCIATION</h2>
					<h2 style="letter-spacing:2px!important;color:white;">SHORT BREAKS</h2>

					<!-- BUTTON -->
					<div style="margin-bottom:60px;margin-top:00px;margin-left:-30px;" class="small-12 medium-12 large-12 columns">
						<div style="display:none;z-index:2;position:relative;top:50%;transform:translateY(-50%);" class="small-2 medium-2 large-3 columns">
							<img class="" href="https://ibizavillas2000.com/ozone-cleaning-system-health-risk-prevention-system-clean-and-safe/" title="Ibiza-Villas-2000-Ozone-Cleaning-System" src="https://ibizavillas2000.com/wp-content/uploads/2020/09/Ibiza-Villas-2000-Ozone-Cleaning-System.png">
						</div>

						<div class="small-12 medium-12 large-12 columns" style="position:relative;">
							<section class="early">
								<article>
									<a href="/special-offers" class="lobutton" style="text-transform:uppercase;letter-spacing:1px;color:#ffffff;border-radius:3px;background:#F6B91D;padding:25px 20px 25px 20px;width:300px;display:inline-block;overflow:hidden;position:relative;">
										<p class="example-left" style="margin:0;">
											<?php the_field('offer_red_round_box_1', 3720); ?>
											<span><?php the_field('offer_red_round_box_line_2', 3720); ?></span>
											<?php the_field('offer_red_round_box_line_3', 3720); ?>

											<?php the_field('offer_red_round_box_1', 3720); ?>
											<span><?php the_field('offer_red_round_box_line_2', 3720); ?></span>
											<?php the_field('offer_red_round_box_line_3', 3720); ?>

											<?php the_field('offer_red_round_box_1', 3720); ?>
											<span><?php the_field('offer_red_round_box_line_2', 3720); ?></span>
											<?php the_field('offer_red_round_box_line_3', 3720); ?>

											<?php the_field('offer_red_round_box_1', 3720); ?>
											<span><?php the_field('offer_red_round_box_line_2', 3720); ?></span>
											<?php the_field('offer_red_round_box_line_3', 3720); ?>

											<?php the_field('offer_red_round_box_1', 3720); ?>
											<span><?php the_field('offer_red_round_box_line_2', 3720); ?></span>
											<?php the_field('offer_red_round_box_line_3', 3720); ?>
										</p>
									</a>

									<div data-badgeid="1" class="wprevpro_badge_container" data-onc="popup" data-oncurl="" data-oncurltarget="new" data-animatedir="" data-animatedelay="">
										<div style="padding-top:10px;" class="wprevpro_badge wppro_badge4_DIV_1 b4s1" id="wprev-badge-1">
											<span class="wppro_badge1_DIV_stars b4s2">
												<span class="svgicons svg-wprsp-star-full"></span>
												<span class="svgicons svg-wprsp-star-full"></span>
												<span class="svgicons svg-wprsp-star-full"></span>
												<span class="svgicons svg-wprsp-star-full"></span>
												<span class="svgicons svg-wprsp-star-full"></span>
											</span>
											<span style="color:white;" class="wppro_badge1_DIV_12 b4s12">
												<span style="color:white;font-weight:900;" class="wppro_badge1_SPAN_3 b3s13"><b>4.8</b></span>&nbsp;Stars&nbsp;
												<span class="wppro_badge1_A_14_span"><span class="wppro_badge1_SPAN_15">200+</span>&nbsp;Reviews</span>
											</span>
										</div>
									</div>
								</article>
							</section>
						</div>
					</div>
				</div>

				<div style="display:none;margin-top:20px;padding-bottom:50px;padding-top:20px;" class="small-12 medium-12 large-5 hero-title text-center columns">
					<div class="villa-home">

						<div style="display:;" class="row" style="padding:40px;display:;">
							<div class="small-0 medium-6 large-6 columns">
								<img class="imgrespcomo" style="height:auto;" href="https://ibizavillas2000.com/ozone-cleaning-system-health-risk-prevention-system-clean-and-safe/" title="Ibiza-Villas-2000-Ozone-Cleaning-System" src="https://ibizavillas2000.com/wp-content/uploads/2020/09/Ibiza-Villas-2000-Ozone-Cleaning-System.png">
							</div>

							<div class="column small-12 medium-6 large-6">
								<h4 style="margin-top:-5px;font-size:0.9rem;">We've taken additional cleaning measures to ensure your safety during your stay with us</h4>
								<p style="font-size:12px;" class="hideunder">Implementing additional cleaning and disinfection protocols in accordance with official recommendations.</p>
							</div>

							<div class="column small-12 medium-12 large-12" style="padding-top:20px;">
								<a style="padding:20px 10px;width:100%;box-shadow:0 8px 16px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);font-weight:bold;" href="https://ibizavillas2000.com/ozone-cleaning-system-health-risk-prevention-system-clean-and-safe/" class="button">LEARN MORE</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>

	<div class="row collapse full-width">
		<?php get_template_part( 'templates/search-home-new' ); ?>
	</div>