<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php wp_title(); ?></title>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, width=device-width"/>
    <link rel="shortcut icon" type="image/png" href="<?php bloginfo('stylesheet_directory'); ?>/images/favicon.png"/>
    <?php wp_head(); ?>
    
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <?php require_once(TEMPLATEPATH . '/templates/spanish_law.php'); ?>
  
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-28761746-2', {'siteSpeedSampleRate': 50});
        ga('create', 'UA-28761746-2', 'auto');
        ga('send', 'pageview');
    </script>

    <script>(function(){function f(){var e=document.createElement("script");e.type="text/javascript";e.async=true;e.src="//platform3.cloud-iq.com/cartrecovery/store.js?app_id=1758";var t = document.getElementsByTagName('head')[0];t.appendChild(e);}f();})();</script>

    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
        n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
        document,'script','https://connect.facebook.net/en_US/fbevents.js');
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
        li#menu-item-2198{padding-right:20px!important;margin-top:5px}.wpb-js-composer .vc_tta-color-grey.vc_tta-style-outline .vc_tta-panel .vc_tta-panel-heading{border-color:#e3e3e3!important;background-color:#EBEBEB!important}.wpb-js-composer .vc_tta-color-grey.vc_tta-style-outline .vc_tta-panel .vc_tta-panel-title>a{color:#666!important}.wpb-js-composer .vc_tta .vc_tta-controls-icon.vc_tta-controls-icon-plus::before{border-color:#666!important}.wpb-js-composer .vc_tta .vc_tta-controls-icon.vc_tta-controls-icon-plus::after{border-color:#666!important}i.fas.fa-star{color:#FDBF3D!important}.hide-como{display:none}.home .site-header{background:url(/wp-content/uploads/2017/06/villa-patxi-enchanced-ov.jpg);background-size:cover;background-position-y:50%}#ccc[light] #ccc-icon{fill:#3CA09A!important;margin-bottom:0!important}@media only screen and (max-width:1080px){select#gtranslate_selector{height:2.75rem;color:#EF2C44;font-size:14px;margin-left:6px}}
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
                <?php get_template_part('templates/nav', 'topbar'); ?>
            </div>
        </div>

        <?php if (is_front_page()) { ?>
            <div class="row">
                CLOCK
                <div class="medium-12 hero-title text-center columns">
                    <img class="hero-logo" src="<?php bloginfo('stylesheet_directory'); ?>/images/rudeibiza-logo-white.svg" alt="Ibiza Villas 2000">
                    <h1>Private Villas in Ibiza<br>Best Value Holiday Rental Villas</h1>
                </div>
            </div>
        <?php } ?>
    </header>

    <div class="row collapse full-width">
        <?php 
        if (!is_page_template('templates/property-results.php') && 
            !is_page_template('templates/villas-small.php') && 
            !is_page_template('templates/villas-big.php')) {
            get_template_part('templates/search-home-new');
        }
        ?>
    </div>