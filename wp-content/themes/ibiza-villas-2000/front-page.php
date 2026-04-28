<?php get_header('new'); ?>

<style>
body{background-color:#F8F8F8}.featured-section{background-color:#f7f7f7;padding-bottom:60px;padding-top:30px}.property-list{padding-top:40px}.property-item{background-color:#fff}.contact-details h2{letter-spacing:1px!important}.contact-details strong{font-weight:bold}.safety-section{padding-top:10px}.safety-img{height:auto}.safety-title{margin-top:0;font-size:0.8rem}.safety-text{font-size:12px}.why-book-section{background-image:url(https://ibizavillas2000.com/wp-ibiza/wp-content/themes/rudeibiza/images/IbizaVillas2000-WhyBookWIthUs.jpg);background-position:bottom;background-repeat:no-repeat;background-size:cover}.why-book-content{padding-top:100px;color:white}.why-book-title{color:white;font-size:200%;margin-bottom:40px}.why-book-subtitle{color:#FFC029;text-align:left;text-transform:uppercase;letter-spacing:1px;font-size:18px}.why-book-text{text-align:justify}.why-book-text span{font-weight:400}.why-book-bottom{padding-bottom:60px;padding-top:20px;color:white;margin-bottom:30px}.services-section{background-color:#f7f7f7;padding-top:80px}.partners-section{background-size:contain;background-position:bottom;background-image:url(https://ibizavillas2000.com/wp-ibiza/wp-content/themes/rudeibiza/images/IbizaVillas2000-Partners.jpg);background-repeat:no-repeat}
</style>

<div class="future small-12 medium-12 large-12 columns featured-section">
    <?php get_template_part('templates/featured-boxes'); ?>
</div>

<div class="row property-list">
    <?php
    // TODO pass 3: editorial exclusion via ACF
    $post__not_in = array();
    $args = array(
        'post_type' => 'villas',
        'posts_per_page' => -1,
        'post__not_in' => $post__not_in,
        'meta_key' => 'property_for_sale',
        'meta_value' => 0
    );

    $property_query = new WP_Query($args);

    if ($property_query->have_posts()) : ?>
        <?php while ($property_query->have_posts()): $property_query->the_post(); global $post; ?>
            <div id="property-<?php echo $post->ID; ?>" class="property-item">
                <?php get_template_part('templates/loop-grid-part-list'); ?>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
    <?php wp_reset_query(); ?>
</div>

<div class="page-intro">
    <div class="nomads-page-info row">
        <div class="small-12 medium-12 large-7 columns">
            <?php
            // TODO pass 3: move to ACF Site Options (replaces hard-coded options page ID).
            the_field('front_page_intro', 2);
            ?>
        </div>
        
        <div class="nomads-page-border column medium-12 large-5">
            <div class="home-contact-details contact-details">
                <h2>Sales and Equiries</h2>
                <strong>UK&nbsp;</strong>+43 203 700 1364<br>
                <strong>Ibiza&nbsp;</strong>+34 666 934 060<br>
                <strong>Opening hours:&nbsp;</strong>09.00 AM to 17.00 PM<br>
                <strong>Email:&nbsp;</strong><a href="mailto:bookings@ibizavillas2000.com">bookings@ibizavillas2000.com</a><br>
            </div>

            <div class="row safety-section">
                <div class="small-12 medium-3 large-3 columns">
                    <img class="imgrespcomo safety-img" href="https://ibizavillas2000.com/ozone-cleaning-system-health-risk-prevention-system-clean-and-safe/" title="Ibiza-Villas-2000-Ozone-Cleaning-System" src="https://ibizavillas2000.com/wp-content/uploads/2020/09/Ibiza-Villas-2000-Ozone-Cleaning-System.png">
                </div>
                <div class="column small-12 medium-9 large-9">
                    <h4 class="safety-title">We've taken additional cleaning measures to ensure your safety during your stay with us</h4>
                    <p class="hideunder safety-text">Implementing additional cleaning and disinfection protocols in accordance with official recommendations.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="small-12 medium-12 large-12 columns why-book-section">
    <div class="small-12 medium-12 large-12 columns why-book-content">
        <div>
            <div class="small-12 medium-4 large-4 columns">
                <h3 class="why-book-title">WHY BOOK WITH US?</h3>
                <h3 class="mt-lg why-book-subtitle">Save €£$</h3>
                <p class="why-book-text"><span>You'll save a heap of cash. Our down to earth prices offer you the best value Ibiza villas around.</span></p>
            </div>
            <div class="small-12 medium-4 large-4 columns">
                <h3 class="mt-lg why-book-subtitle">Your booking is safe</h3>
                <p class="why-book-text"><span>If you rent your holiday villa with us, you can relax and know your booking is safe and secure. We've organised thousands of villa holidays in Ibiza over 20 years. Members of APNEEF, we're a fully registered, legal and insured family business.</span></p>
            </div>
            <div class="small-12 medium-4 large-4 columns">
                <h3 class="mt-lg why-book-subtitle">Prices fixed in £ & €</h3>
                <p class="why-book-text"><span>You won't have to worry about any currency exchange fluctuations affecting the price of our rental villas. Prices are set in both Euros and Sterling. Your price of your stay will not change, even if the world around you does.</span></p>
            </div>
        </div>
    </div>

    <div class="small-12 medium-12 large-12 columns why-book-bottom">
        <div>
            <div class="small-12 medium-4 large-4 columns">
                <h3 class="mt-lg why-book-subtitle">We're here for you</h3>
                <p class="why-book-text"><span>You'll get fantastic customer service, from your initial enquiry until the end of your holiday for help, advice and to answer any questions you may have.</span></p>
            </div>
            <div class="small-12 medium-4 large-4 columns">
                <h3 class="mt-lg why-book-subtitle">No deposit on arrival</h3>
                <p class="why-book-text"><span>You won't have to pay a huge deposit on our rental villas when you arrive - instead, we organise accidental damage insurance for just a few euros so you don't have to worry about a thing.</span></p>
            </div>
            <div class="small-12 medium-4 large-4 columns">
                <h3 class="mt-lg why-book-subtitle">Concierge service</h3>
                <p class="why-book-text"><span>To make sure you can simply relax and have the time of your life, we can organise airport transfers, taxis, private chefs, club tickets, pre-arrival shopping, boat hire & more. We're here to help make your holiday in Ibiza the very best that it can be.</span></p>
            </div>
        </div>
    </div>
</div>

<div class="future small-12 medium-12 large-12 columns services-section">
    <?php get_template_part('templates/featured-services'); ?>
</div>

<div class="rude-sponsors-container partners-section">
    <div class="row collapse rude-sponsors">
        <div class="small-12 large-12 medium-12 text-center columns">
            <span><strong>OUR PARTNERS</strong></span>
            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/pimeef_2022.png" alt="PIMEEF" title="Ibiza Villas 2000" width="90">
            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/ibizarocks_2019.gif" alt="Ibiza Rocks" title="Ibiza Villas 2000" width="68">
            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/cafemambo_2019.png" alt="Cafe Mambo" title="Ibiza Villas 2000" width="99">
            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/motoluis_2019.png" alt="Motoluis" title="Ibiza Villas 2000" width="99">
            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/gardenfestival_2019.gif" alt="Garden Festival" title="Ibiza Villas 2000" width="50">
            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/Rude-Chalets_2019.jpg" alt="Rude Chalets" title="Ibiza Villas 2000" width="115">
            <a target="_blank" href="http://www.crispcateringibiza.com/">
                <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/crisp-logo_2019.png" alt="Crisp Catering Ibiza" title="Ibiza Villas 2000" width="99">
            </a>
        </div>
    </div>
</div>

<?php get_footer(); ?>
