<style>
.footer-email-newsletter #mce-EMAIL{font-size:14px;width:240px;float:left;border:none;height:35px;margin:0}.footer-email-newsletter #subscribe-subit{float:right}.footer-email-newsletter .mce-responses .response{display:none}.footer-email-newsletter .honeypot{position:absolute;left:-5000px}.floatcontact .floatContent a{color:white}
</style>

<?php // Output search form but not on home page and only on mobile
if (!is_front_page()) : ?>
    <div class="row collapse full-width show-for-small-only">
        <?php if (!is_page_template('templates/property-results.php')) {
            get_template_part('templates/search-home-new');
        } else {
            get_template_part('templates/search-vertical');
        } ?>
    </div>
<?php endif; ?>

<footer>
    <div class="row">
        <div class="medium-12 columns text-center">
            <a href="/villa-scams/ibiza-villa-rental-company-safe-legal/">
                <img src="/wp-content/uploads/2019/01/avat-logo_2019.png" alt="AVAT" title="Ibiza Villa 2000">
            </a>
            <p>Central de Reservas RGE 2016018990 - Comercializador de Estancias Turísticas 12752</p>
        </div>
    </div>

    <div class="row">
        <div class="small-12 medium-6 large-4 columns">
            <?php dynamic_sidebar('footer-widget-1'); ?>
        </div>
        <div class="small-12 medium-6 large-4 columns">
            <?php dynamic_sidebar('footer-widget-2'); ?>
        </div>
        <div class="small-12 large-4 columns">
            <?php // dynamic_sidebar('footer-widget-3'); ?>
            <h3>SIGN UP FOR SPECIAL OFFERS</h3>
            
            <div id="mc_embed_signup" class="footer-email-newsletter">
                <form action="//ibizavillas2000.us16.list-manage.com/subscribe/post?u=235e8d22164eb3a4bdbb887dd&amp;id=6a7877668a" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                    <div id="mc_embed_signup_scroll">
                        <input placeholder="YOUR EMAIL ADDRESS" type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
                        <button class="icon-arrow-right" type="submit" id="subscribe-subit"></button>
                        
                        <div id="mce-responses" class="clear">
                            <div class="response" id="mce-error-response"></div>
                            <div class="response" id="mce-success-response"></div>
                        </div>
                        
                        <!-- Honeypot for bot protection -->
                        <div class="honeypot" aria-hidden="true">
                            <input type="text" name="b_235e8d22164eb3a4bdbb887dd_6a7877668a" tabindex="-1" value="">
                        </div>
                        
                        <div class="clear">
                            <!-- <input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"> -->
                        </div>
                    </div>
                </form>
            </div>
            
            <script src='<?php echo esc_url( get_template_directory_uri() ); ?>/js/mailchimp.js'></script>
            <script>
                (function($) {
                    window.fnames = new Array(); 
                    window.ftypes = new Array();
                    fnames[0]='EMAIL';ftypes[0]='email';
                    fnames[1]='FNAME';ftypes[1]='text';
                    fnames[2]='LNAME';ftypes[2]='text';
                    fnames[3]='BIRTHDAY';ftypes[3]='birthday';
                    fnames[4]='MMERGE4';ftypes[4]='date';
                }(jQuery));
                var $mcj = jQuery;
            </script>
            
            <a href="#" class="button revealBrands">Other Brands in the Rude Leisure Group</a>
        </div>
    </div>

    <div class="row">
        <div class="medium-12 columns">
            <!-- Footer Navigation -->
            <?php 
            $args = array(
                'theme_location' => 'footer',
                'menu_class' => 'footer-nav-bar',
                'container' => 'nav'
            );
            wp_nav_menu($args); 
            ?>
        </div>
    </div>
</footer>

</div><!--/.main -->

<div class="floatcontact">
    <div class="floatsocial">
        <a target="_blank" class="facebook" href="https://www.facebook.com/ibizavillas2000?fref=ts" title="Find Ibiza Villas 2000 On Facebook">
            <span class="icon-facebook"></span>
        </a>
        <a target="_blank" class="twitter" href="https://twitter.com/ibizavillas2k" title="Follow Ibiza Villas 2000 On Twitter">
            <span class="icon-twitter"></span>
        </a>
    </div>

    <span class="floatContent">
        <a href="/contact/"><?php _e('Email Us'); ?></a>
    </span>

    <!-- Google Translate (commented out)
    <div class="google-translate-wrap">
        <div id="google_translate_element"></div>
    </div>
    <script>
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en', 
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE, 
                gaTrack: true, 
                gaId: 'UA-28761746-2'
            }, 'google_translate_element');
        }
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    -->
</div>

<div class="brands revealJs">
    <?php the_widget('rude_brands_footer_widget'); ?>
</div>

<?php wp_footer(); ?>

<div id="myBooking" class="reveal-modal footer-form-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
    <h3 id="modalTitle">My Booking</h3>
    <p class="lead">Please enter your booking reference number..</p>
    <form method="get" action="//ibizavillas2000.co.uk/cgi-bin/LIVE/bookingForm.pl">
        <input name="bookingRef" required placeholder="Your booking ref">
        <button type="submit">Go</button>
    </form>
</div>

</body>
</html>