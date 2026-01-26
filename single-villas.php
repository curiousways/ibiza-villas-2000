<?php get_header(); ?>

<style>
.gform_wrapper ul li.gfield,.slick-track:after,hr{clear:both;min-width:100%}.bord-nomads{border-top-right-radius:5px;border-top-left-radius:5px}ul{margin:0 0 1.5rem;padding:0}li{display:block}svg{fill:currentColor;width:1.5rem;height:1.5rem}main{max-width:81.25rem;margin:auto}.card-content{padding:1.9375rem}.carousel-items{display:flex;overflow-x:scroll;padding:1rem 0;scroll-snap-type:x mandatory}.carousel-item{flex:1 0 250px;margin-left:1rem;margin-right:1rem;scroll-snap-align:start}::-webkit-scrollbar-track{background-color:#f5f5f5}::-webkit-scrollbar{height:6px;background-color:#f5f5f5}::-webkit-scrollbar-thumb{background-color:#3d4852;border-radius:3px}@media screen and (min-width:0px){.hhnomads-01,.hhnomads-02{letter-spacing:2px;line-height:1;text-align:center!important}.carousel-item{flex-basis:520px}.card{border-radius:5px;background-color:#fff;min-height:530px;display:none}.hhnomads-01{font-size:30px}.hhnomads-02{font-size:39px}}@media screen and (min-width:576px){.hhnomads-01,.hhnomads-02{letter-spacing:2px;line-height:1}.carousel-item{flex-basis:400px}.card{border-radius:5px;background-color:#fff;min-height:530px;display:none}.hhnomads-01{font-size:29px}.hhnomads-02{font-size:37px}}@media screen and (min-width:992px){.hhnomads-01,.hhnomads-02{letter-spacing:2px;line-height:1}.carousel-item{flex-basis:380px}.card{border-radius:5px;background-color:#fff;min-height:530px;display:block}.hhnomads-01{font-size:19px}.hhnomads-02{font-size:24px}}@media screen and (min-width:1280px){.hhnomads-01,.hhnomads-02{letter-spacing:2px;line-height:1}.carousel-item{flex-basis:410px}.hhnomads-01{font-size:26px}.hhnomads-02{font-size:34px}.card{border-radius:5px;background-color:#fff;min-height:580px;display:block}}@media screen and (min-width:1600px){.hhnomads-01,.hhnomads-02{letter-spacing:2px;line-height:1}.carousel-item{flex-basis:410px}.hhnomads-01{font-size:27px}.hhnomads-02{font-size:35px}.card{border-radius:5px;background-color:#fff;min-height:580px;display:block}}.ui-datepicker-prev{float:left;color:#FFF;background:none;padding:8px}.ui-datepicker-next{background:none;padding:8px}.ui-datepicker-title{background:#3EB89A!important;border-radius:0!important}.ui-widget select{font-size:0.7em!important;margin:10px 5px!important}.ui-corner-all{border-bottom-right-radius:0!important;border-bottom-left-radius:0!important;border-top-right-radius:0!important;border-top-left-radius:0!important}ui-button,.ui-state-default,.ui-widget-content .ui-state-default,.ui-widget-header .ui-state-default,html .ui-button.ui-state-disabled:active,html .ui-button.ui-state-disabled:hover{border:0 solid #c5c5c5!important;background:transparent!important}.ginput_container_date span{display:none!important}span#input_2_13_date_format{display:none!important}span#input_2_11_date_format{display:none!important}.mobile-sticky-footer{display:none;position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #eaeaea;padding:12px 16px;z-index:9999;box-shadow:0 -4px 12px rgba(0,0,0,0.1);align-items:center;-webkit-box-align:center;-ms-flex-align:center}.sticky-price-container{-webkit-box-flex:1;-ms-flex-positive:1;flex-grow:1;padding-right:15px}.sticky-price-label{font-size:12px;color:#717171;margin-bottom:-2px;line-height:1.2;display:block}.sticky-price-amount{font-size:18px;font-weight:800;color:#ffc029;line-height:1.3;margin-bottom:-3px;display:block;-webkit-font-smoothing:antialiased}.sticky-price-period{font-size:12px;color:#717171;display:block;line-height:1.4}.sticky-book-btn{background:#37B89A;color:white;border:none;border-radius:4px;padding:12px 20px;font-weight:400;font-size:15px;cursor:pointer;white-space:nowrap;-webkit-transition:background 0.2s;transition:background 0.2s;margin:0;-ms-flex-negative:0;flex-shrink:0;-webkit-appearance:none;-moz-appearance:none;appearance:none;min-width:120px;text-align:center}.sticky-book-btn:hover,.sticky-book-btn:focus{background:#2da58a;outline:none}@media screen and (-webkit-min-device-pixel-ratio:0){.sticky-book-btn{-webkit-transform:translateZ(0)}}@media (max-width:767px){.wptwa-toggle{display:none!important;visibility:hidden!important;opacity:0!important;pointer-events:none!important}.search-nomads{display:none}.property-title{margin:80px auto 15px}.mobile-sticky-footer{display:-webkit-box;display:-ms-flexbox;display:flex}body{padding-bottom:72px}}@media all and (-ms-high-contrast:none),(-ms-high-contrast:active){.mobile-sticky-footer{display:-ms-flexbox}}
</style>

<?php global $spanish_law; ?>

<?php
if ($spanish_law == 'yes') {
    $property_sleeps = get_field('property_sleeps_spanish');
    $property_bedrooms = get_field('property_bedrooms_spanish');
    $sleeps = get_field('property_sleeps_spanish');
    $property_bathrooms = get_field('property_bathrooms_spanish');
    $property_description = get_field('property_description_spanish');
} else {
    $property_sleeps = get_field('property_sleeps');
    $property_bedrooms = get_field('property_bedrooms');
    $property_bathrooms = get_field('property_bathrooms');
    $notice_l = get_field('notice_l');
    $sleeps_l = get_field('sleeps_l');
    $sleeps = get_field('property_sleeps');
}

$sleeps = (!empty($sleeps) ? 'Sleeps ' . $sleeps : '');
$location = get_the_terms($post->ID, 'property_location');
$location = (!empty($location) ? $location[0]->name : '');
$location2 = get_the_terms($post->ID, 'property_location');
$location2 = (!empty($location2) ? $location2[1]->name : '');
$postTypeObj = get_post_type_object(get_post_type());
$type = $postTypeObj->labels->singular_name;

// Location and sleeps, if either is empty don't print the /
if (!empty($location) && !empty($location2)) {
    $locationsleeps = $location . ' - ' . $location2 . ' ';
} else if (!empty($location)) {
    $locationsleeps = $location . '  ';
} else {
    $locationsleeps = $location . $sleeps;
}
?>

<div class="row property-title">
    <div class="large-12 columns">
        <?php if (stripos($_SERVER['REQUEST_URI'], '/sale/') !== false) {
            $villa_pretty_name = get_field('villa_pretty_name');
            $additional_title_keyword = get_field('additional_title_keyword');

            if ($villa_pretty_name) {
                if ($additional_title_keyword) {
                    $villa_pretty_name = $additional_title_keyword;
                }
                echo '<h1 class="pretty-name">' . $villa_pretty_name . '</h1>';
                the_title('<h2>', '</h2>');
            } else {
                the_title('<h1>', '</h1>');
            }
        } else {
            $villa_pretty_name = get_field('villa_pretty_name');
            $additional_title_keyword = get_field('additional_title_keyword');

            if ($villa_pretty_name) {
                if ($additional_title_keyword) {
                    $villa_pretty_name = $villa_pretty_name . ' ' . $additional_title_keyword;
                }
                echo '<h1 class="pretty-name">' . $villa_pretty_name . '</h1>';
                the_title('<h2>', '</h2>');
                echo '<p style="color:#37B89A;" class="slider-location-sleeps"><i style="color:#37B89A;" class="fas fa-map-marker-alt"></i>' . $locationsleeps . '</p>';
            } else {
                the_title('<h1>', '</h1>');
            }
        } ?>
        <?php get_template_part('templates/breadcrumbs'); ?>
    </div>
</div>

<div class="row">
    <div class="small-12 large-8 columns">
        <?php get_template_part('templates/single-property-special-offer'); ?>
        <?php get_template_part('templates/loop', 'property'); ?>
        <?php get_template_part('templates/single-property-more-info'); ?>
        
        <div class="hide-for-large-up">
            <?php get_template_part('templates/single-property-video'); ?>
        </div>
    </div>

    <div id="enquire-now" class="stuck small-12 large-4 property-sidebar columns">
        <h3 id="enquire-now stuck" style="text-align: center;">ENQUIRE NOW</h3>
        
        <div class="sidebar-quick-enquiry">
            <p style="padding-top: 20px;padding-left: 20px;padding-right: 20px;padding-bottom: 0px;">Simply fill in the enquiry form below and one of our friendly staff members will get in touch to start the booking process.</p>
            
            <?php if (stripos($_SERVER['REQUEST_URI'], '/rental/') !== false) {
                echo do_shortcode('[gravityform id="2" title="false" description="false" ajax="true"]');
            } else {
                echo do_shortcode('[gravityform id="14" title="false" description="false" ajax="true"]');
            } ?>
            
            <p style="text-align:center;padding: 20px;">Or contact us: <b style="color:#4cd8b0;">+44 203 700 1364</b></p>
        </div>
        
        <div><?php echo do_shortcode('[whatsapp_button id="17339"]'); ?></div>
        
        <?php get_template_part('templates/single-property-price'); ?>
        
        <div class="show-for-large-up">
            <?php
            if (is_single(12511)) {
                echo '<div class="single-property-sleeps">';
                echo "<h3>This Sovereign Airstream sleeps " . $property_sleeps;
                echo '</div>';
            } elseif ($property_sleeps >= 12) {
                echo '<div class="single-property-sleeps">';
                echo "<h3>This Villa sleeps 12</h3>";
                echo "<p><b>Please note:</b><br>If you are a large group of 12 or more people<br>";
                echo "please email us at<br>";
                echo '<a href="mailto:bookings@ibizavillas2000.com">bookings@ibizavillas2000.com</a></p>';
                echo '</div>';
            } else {
                echo '<div class="single-property-sleeps">';
                echo "<h3>This Villa sleeps " . $property_sleeps;
                echo '</div>';
            }
            ?>
            
            <?php get_template_part('templates/single-property-features'); ?>
            <?php get_template_part('templates/single-property-summary'); ?>
        </div>
        
        <div style="background-color: #F6B91D;" class="card">
            <img class="bord-nomads" alt="Ibiza Villas 2000 - Rental Insider Guide" title="Ibiza Villas 2000 - Ibiza Rental Insider Guide" src="https://ibizavillas2000.com/wp-content/themes/rudeibiza/services/IbizaVillas2000-IbizaRentalGuide.png" />
            <div style="padding-top: 0px!important;" class="card-content">
                <h3><a class="hhnomads-01" style="color:#fff;" href="https://ibizavillas2000.com/ibiza-villa-rentals-insiders-guide-for-your-2025-holiday/">IBIZA VILLA RENTAL</a></h3>
                <h3><a class="hhnomads-02" style="color:#fff;" href="https://ibizavillas2000.com/ibiza-villa-rentals-insiders-guide-for-your-2025-holiday/">INSIDERS GUIDE</a></h3>
                <h3><a class="hhnomads-01" style="color:#fff;" href="https://ibizavillas2000.com/ibiza-villa-rentals-insiders-guide-for-your-2025-holiday/">FOR YOUR HOLIDAY</a></h3>
                <a style="margin-top:20px;padding:10px;width: 100%!important;" href="https://ibizavillas2000.com/ibiza-villa-rentals-insiders-guide-for-your-2025-holiday/" class="button butds" data-hover="MORE INFO">IBIZA RENTAL GUIDE</a>
            </div>
        </div>
    </div>
</div>

<div class="row"><?php get_template_part('templates/similar-properties'); ?></div>

<div style="background-size: contain;background-position: bottom; background-image: url(https://ibizavillas2000.com/wp-content/themes/rudeibiza/images/IbizaVillas2000-Partners.jpg);background-repeat: no-repeat;" class="rude-sponsors-container">
    <div class="row collapse rude-sponsors">
        <div class=" small-12 large-12 medium-12  text-center columns">
            <span><strong>OUR PARTNERS</strong></span>
            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/pimeef_2022.png" alt="PIMEEF" title="Ibiza Villas 2000" width="90">
            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/ibizarocks_2019.gif" alt="Ibiza Rocks" title="Ibiza Villas 2000" width="68">
            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/cafemambo_2019.png" alt="Cafe Mambo" title="Ibiza Villas 2000" width="99">
            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/motoluis_2019.png" alt="Motoluis" title="Ibiza Villas 2000" width="99">
            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/fox_2019.png" alt="Fox" title="Ibiza Villas 2000" width="99">
            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/gardenfestival_2019.gif" alt="Garden Festival" title="Ibiza Villas 2000" width="50">
            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/Rude-Chalets_2019.jpg" alt="Rude Chalets" title="Ibiza Villas 2000" width="115">
            <a target="_blank" href="http://www.crispcateringibiza.com/">
                <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/crisp-logo_2019.png" alt="Crisp Catering Ibiza" title="Ibiza Villas 2000" width="99">
            </a>
        </div>
    </div>
</div>

<div class="mobile-sticky-footer">
    <div class="sticky-price-container">
        <span class="sticky-price-label">From</span>
        <span class="sticky-price-amount">
            <?php 
            $price_from_euros = get_field('property_price_from_euros');
            $price_from_pounds = get_field('property_price_from_pounds');
            
            if ($price_from_euros) {
                $price_from_euros_day = floor($price_from_euros/7);
                $price_from_pounds_day = floor($price_from_pounds/7);
                echo '€' . number_format($price_from_euros_day) . ' / £' . number_format($price_from_pounds_day);
            } else {
                echo 'Price upon request';
            }
            ?>
        </span>
        <span class="sticky-price-period">per night</span>
    </div>
    <button class="sticky-book-btn" onclick="document.getElementById('enquire-now').scrollIntoView({ behavior: 'smooth', block: 'start' })">
        Book Now
    </button>
</div>

<?php get_footer(); ?>