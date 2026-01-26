<?php get_header(); ?>

<style>
	.explore-slider.slick-initialized.slick-slider {
    display: none!important;
}
.slider.featuredSidebar.slick-initialized.slick-slider {
    display: none!important;
}
.contact-form-section{padding-bottom:40px}.contact-heading{letter-spacing:1px!important}.contact-info-section{padding-top:20px!important}.contact-testimonials{margin-bottom:30px}.contact-testimonials img{padding-bottom:30px;max-width:190px}.contact-testimonials .button{padding:10px;width:100%!important}.partners-section{background-size:contain;background-position:bottom;background-image:url(https://ibizavillas2000.com/wp-ibiza/wp-content/themes/rudeibiza/images/IbizaVillas2000-Partners.jpg);background-repeat:no-repeat}
</style>

<div class="site-content">
    <div class="row">
        <div class="large-8 columns">
            <h1><?php the_title(); ?></h1>

            <div class="row mb-lg">
                <div class="small-12 medium-12 columns contact-form-section">
                    <?php get_template_part('loop'); ?>
                </div>

                <div class="column medium-12 large-7">
                    <div class="home-contact-details">
                        <h2 class="contact-heading">GET IN TOUCH</h2>
                        <?php echo do_shortcode('[gravityform id="3" title="false" description="false"]'); ?>
                    </div>
                </div>

                <div class="column medium-12 large-5">
                    <div class="home-contact-details">
                        <h2 class="contact-heading">UK</h2>
                        <p>86-90 Paul Street<br>
                        London EC2A 4NE</p>

                        <strong>Tel:&nbsp;</strong>+44 203 700 1364<br>
                        <strong>Opening hours:&nbsp;</strong>09.00 to 17.00<br>
                        <strong>Email:&nbsp;</strong><a href="mailto:bookings@ibizavillas2000.com">bookings@ibizavillas2000.com</a><br>
                        
                        <h2 class="mt-lg contact-heading contact-info-section">Spain</h2>
                        <p>59 Avenida Espagne<br>
                        Ibiza - Balearic Islands<br>
                        Spain 07800</p>

                        <h3 class="mt-lg">Sales</h3>
                        <strong>Tel:&nbsp;</strong>+34 666 934 060<br>
                        <strong>Opening hours:&nbsp;</strong>09.00 to 17.00<br>
                        <strong>Email:&nbsp;</strong><a href="mailto:bookings@ibizavillas2000.com">bookings@ibizavillas2000.com</a><br>

                        <h3 class="mt-lg contact-info-section">Operations &amp; Admin</h3>
                        <strong>Tel:&nbsp;</strong>+34 607 894 648<br>
                        <strong>Opening hours:&nbsp;</strong>09.00 to 17.00<br>
                        <strong>Email:&nbsp;</strong><a href="mailto:Operations@ibizavillas2000.com">Operations@ibizavillas2000.com</a><br>
                    </div>
                </div>
            </div>
        </div>

        <div class="large-4 columns">
            <div class="margnomads">
                <div class="nomads-page-border-contact column medium-12 large-12 contact-testimonials">
                    <div class="home-contact-details">
                        <img class="bord-nomads" alt="Ibiza Villas 2000 - Clients Testimonials" title="Clients Testimonials" src="https://ibizavillas2000.com/wp-ibiza/wp-content/themes/rudeibiza/images/IbizaVillas2000-OurGoogleReviews.png" />
                        <p>Customer satisfaction is a primary goal for our company.</p><br>
                        <a class="button butds" data-hover="ALL TESTIMONIALS" href="https://ibizavillas2000.com/testimonials/">READ REVIEWS</a>
                    </div>
                </div>
            </div>
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>

<div class="rude-sponsors-container partners-section">
    <div class="row collapse rude-sponsors">
        <div class="small-12 large-12 medium-12 text-center columns">
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

<?php get_footer(); ?>