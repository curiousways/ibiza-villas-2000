<?php get_header(); ?>

<style>
.property-list img{width:100%;display:inline-block;vertical-align:middle;object-fit:cover;height:270px;border-radius:5px}.property-list .slider-detail{padding-top:20px;padding-left:40px;padding-right:40px}#property-11234{display:none}.sale-intro{padding:40px 0}.sale-contact-details{background:#f9f9f9;padding:30px;border-radius:8px}.sale-contact-details h3{margin-bottom:20px;color:#333}.sale-contact-details ul{list-style:none;padding:0}.sale-contact-details ul li{margin-bottom:10px;padding:5px 0}.sale-contact-details ul li strong{display:inline-block;min-width:150px}.property-item{background-color:#fff;margin-bottom:20px;border-radius:8px;overflow:hidden}
</style>

<div class="page-intro sale-intro">
    <div class="row">
        <div class="small-12 medium-6 large-7 columns">
            <p>We are all very proud to be part of the Ibiza Villas 2000 team, a local, trusted family business with a solid 20-year history on the island. As permanent, all-year-round Ibiza residents, we have over fifty years of experience of living and working in Ibiza between us. Yes, ladies and gents, we really know our island well. Go ahead, ask us anything!</p><br>
            <?php echo "Below you'll find a curated list of villas for sale in ibiza at spectacular prices. Get in touch with us to arrange a viewing at a time of your convenience."; ?>
        </div>

        <div class="column medium-6 large-5">
            <div class="home-contact-details sale-contact-details">
                <h3>Sales &amp; Enquiries</h3>
                <ul>
                    <li><strong>SALES TEAM UK</strong> <span>0044 203 700 1364</span></li>
                    <li><strong>SALES TEAM SPAIN</strong> <span>0034 666 934 060</span></li>
                </ul>
                
                <h3 class="mt-lg">On Island Contacts</h3>
                <ul>
                    <li><strong>Office hours</strong> <span>0034 971 300 890</span></li>
                    <li><strong>Out of hours &amp; transport</strong> <span>0034 617 427 427</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row property-list">
    <?php
    if ($spanish_law == 'yes') {
        $args = array(
            'post_type' => 'villas',
            'posts_per_page' => -1,
            'post__not_in' => array('5610','2928'),
            'meta_key' => 'property_for_sale',
            'meta_value' => true
        );
    } else {
        $args = array(
            'post_type' => 'villas',
            'posts_per_page' => -1,
            'meta_key' => 'property_for_sale',
            'meta_value' => true
        );
    }

    $property_query = new WP_Query($args);

    if ($property_query->have_posts()) : ?>
        <?php while ($property_query->have_posts()): $property_query->the_post(); global $post; ?>
            <div id="property-<?php echo $post->ID; ?>" class="property-item">
                <?php get_template_part('templates/loop-grid-part-list-sale'); ?>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
    <?php wp_reset_query(); ?>
</div>

<div class="row collapse featured-properties">
    <?php get_template_part('templates/featured-properties'); ?>
</div>

<?php get_template_part('templates/explore'); ?>

<?php get_template_part('templates/testimonials'); ?>

<div class="rude-sponsors-container">
    <div class="row collapse rude-sponsors">
        <div class="small-12 text-center columns">
            <span><strong>OUR PARTNERS</strong></span>
            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/pimeef.png" alt="PIMEEF" width="87">
            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/ibizarocks.png" alt="Ibiza Rocks" width="68">
            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/cafemambo.png" alt="Cafe Mambo" width="99">
            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/motoluis.png" alt="Motoluis" width="99">
            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/fox.png" alt="Fox" width="99">
            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/gardenfestival.jpg" alt="Garden Festival" width="50">
            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/rudechalets.png" alt="Rude Chalets" width="115">
            <a target="_blank" href="http://www.crispcateringibiza.com/">
                <img src="<?php bloginfo('stylesheet_directory'); ?>/images/sponsors/crisp-logo.png" alt="Crisp Catering Ibiza" width="99">
            </a>
        </div>
    </div>
</div>

<?php get_template_part('templates/explore-other'); ?>

<?php get_footer(); ?>