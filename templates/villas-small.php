<?php
/**
 * Template Name: Small Villas
 */
get_header(); ?>

<style>
.you-searched-for{background:#f9f9f9;padding:20px 0}.you-searched-for h1{padding-top:30px;margin-bottom:20px}.you-searched-for h2{padding-bottom:10px;letter-spacing:0!important;font-size:17px;font-weight:600}.toggle-buttons{display:none}.map-container{display:none}.marker-info{max-width:300px;padding:10px}.marker-info .thumb{width:80px;height:60px;overflow:hidden;float:left;margin-right:10px}.marker-info .thumb img{width:100%;height:100%;object-fit:cover}.marker-info h3{font-size:14px;margin:0 0 5px 0}.marker-info h4{font-size:12px;color:#666;margin:0 0 5px 0}.marker-info p{font-size:11px;margin:0 0 10px 0}.no-results{padding:40px 0}.no-results .alert-box{padding:20px;background:#f8d7da;border:1px solid #f5c6cb;border-radius:4px}.intro-text{padding-bottom:60px}.intro-text p{font-size:16px;line-height:1.6}.why-book-section{background-image:url(https://ibizavillas2000.com/wp-ibiza/wp-content/themes/rudeibiza/images/IbizaVillas2000-WhyBookWIthUs.jpg);background-position:bottom;background-repeat:no-repeat;background-size:cover}.why-book-content{padding-top:100px;color:white}.why-book-content h3{color:#FFC029;text-transform:uppercase;letter-spacing:2px;font-size:18px}.why-book-content p{text-align:justify}.why-book-content span{font-weight:400}.why-book-content a{color:#FFC029}.why-book-bottom{padding-top:20px;color:white;margin-bottom:60px}.final-intro{padding-top:40px}.final-intro p{font-size:16px;line-height:1.6}.property-grid{list-style:none;padding:0;margin:0}.site-content{background:linear-gradient(#F7F7F7,#ffffff)!important}.partners-section{background-size:contain;background-position:bottom;background-image:url(https://ibizavillas2000.com/wp-ibiza/wp-content/themes/rudeibiza/images/IbizaVillas2000-Partners.jpg);background-repeat:no-repeat}
</style>

<?php global $spanish_law; ?>

<?php
// small villas page, all locations and 6 to 12 guests
$property_location = array('san-antonio','ibiza-town','playa-den-bossa', 'san-rafel', 'san-josep', 'north-island');
$sleeps_minimum = '1';
$sleeps_maximum = '12';
?>

<div class="row collapse full-width">
    <?php get_template_part('templates/search-home-new'); ?>
</div>

<div class="row full-width you-searched-for">
    <div class="small-12 columns text-center">
        <h1>Villas to rent in Ibiza for small groups</h1>
        <div class="toggle-buttons results-map-toggle">
            <a class="button show-results-map" href="#">View Map</a><a class="button hide-results-map active" href="#">Hide Map</a>
        </div>
        <h2>Ideal for small to medium sized groups and families</h2>
    </div>
</div>

<?php get_template_part('loop'); ?>

<?php
if ($spanish_law == 'yes') {
    $args = array(
        'post_type' => $property_type,
        'posts_per_page' => -1,
        'property_location' => $property_location,
        'post__not_in' => array('5610','2928'),
        'meta_key' => 'property_for_sale',
        'meta_value' => 0
    );
} else {
    $args = array(
        'post_type' => $property_type,
        'posts_per_page' => -1,
        'property_location' => $property_location,
        'meta_key' => 'property_for_sale',
        'meta_value' => 0
    );
}

$property_query = new WP_Query($args);

if ($property_query->have_posts()) : ?>
    <div class="row map-container">
        <div class="acf-map">
            <?php while ($property_query->have_posts()): $property_query->the_post(); global $post; ?>
                <?php
                $location = get_field('property_map');

                if ($spanish_law == 'yes') {
                    $property_sleeps = get_field('property_sleeps_spanish');
                    $property_bedrooms = get_field('property_bedrooms_spanish');
                    $property_bathrooms = get_field('property_bathrooms_spanish');
                    $property_description = get_field('property_description_spanish');
                } else {
                    $property_sleeps = get_field('property_sleeps');
                    $property_bedrooms = get_field('property_bedrooms');
                    $property_bathrooms = get_field('property_bathrooms');
                }

                if (!empty($location) && ($property_sleeps >= $sleeps_minimum && $property_sleeps <= $sleeps_maximum)) : ?>
                    <div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng']; ?>">
                        <div class="marker-info">
                            <div class="info">
                                <div class="thumb">
                                    <a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
                                </div>
                                <h3><a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?>
                                    <?php echo "- Sleeps " . $property_sleeps; ?>
                                </a></h3>
                                <?php $location = wp_get_post_terms($post->ID, 'property_location'); ?>
                                <h4><?php echo $location[0]->name; ?></h4>
                                <?php
                                if (!has_excerpt()) {
                                    $trimmed = content(25);
                                    echo wp_strip_all_tags($trimmed);
                                } else {
                                    $excerpt = excerpt(25);
                                    echo $excerpt;
                                }
                                ?>
                                <br><a href="<?php the_permalink(); ?>" class="button">Find out more</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endwhile; ?>
        </div>
    </div>
<?php else : ?>
    <div class="row no-results">
        <div class="small-12 text-center columns">
            <div class="alert-box error"><h2><?php _e("Sorry, your search criteria didn't return any results"); ?></h2></div>
            <h4>Why not take a look at <a href="/property-results/">all our properties?</a></h4>
        </div>
    </div>
    <?php get_template_part('templates/explore'); ?>
<?php endif; ?>

<?php wp_reset_query(); ?>

<div class="row intro-text">
    <div class="small-12 columns text-center">
        <p>If you'd like to rent a villa in Ibiza for up to 12 guests, then this is the page for you. Whilst most of the holiday villas listed here sleep between 6-10 guests, it's sometimes possible to extend capacity by adding extra beds to particularly large rooms. If this is possible in the villa you're considering for your holiday, then we'll clearly state that in the villa page description. If you're unsure, then please <a href="/contact/">go ahead and ask</a>, we're here to help!</p>
    </div>
</div>

<div class="small-12 medium-12 large-12 columns why-book-section">
    <div class="small-12 medium-12 large-12 columns why-book-content">
        <div>
            <div class="small-12 medium-6 large-6 columns">
                <h3 class="mt-lg">Villas to rent in popular resorts</h3>
                <p><span>You'll find a great selection of the most popular Ibiza locations to choose from below. No matter whether you want to rent a villa in the busy holiday resorts of <a href="/villas-in-playa-den-bossa/">Playa d'en Bossa</a> & <a href="/villas-in-san-antonio/">San Antonio</a>, or close to the culturally rich capital of <a href="/villas-in-ibiza-town/">Ibiza Town</a>, we've got you covered.</span></p>
            </div>
            <div class="small-12 medium-6 large-6 columns">
                <h3 class="mt-lg">All our rental villas have pools</h3>
                <p><span>All of our rental villas have pools and nice outdoor spaces for that delicious Mediterranean al-fresco life & our down-to-earth prices mean that it doesn't have to break the bank to rent a villa in Ibiza. In fact, in many cases, it's much more affordable than a hotel.</span></p>
            </div>
        </div>
    </div>
    <div class="small-12 medium-12 large-12 columns why-book-bottom">
        <div>
            <div class="small-12 medium-6 large-6 columns">
                <h3 class="mt-lg">Authentic Ibiza villages</h3>
                <p><span>If you prefer to be a little bit further afield, perhaps close to the authentic Ibicencan villages of <a href="/villas-in-san-josep/">San Josep</a> (San Jose) or <a href="/villas-in-san-rafel/">San Rafael</a>, we can help there too! Just use the handy map above to see the locations of all small-medium sized rental villas at a glance.</span></p>
            </div>
            <div class="small-12 medium-6 large-6 columns">
                <h3 class="mt-lg">Recommended | check your villa booking is safe</h3>
                <p><span>Before you go ahead and rent a villa in Ibiza with any online company, we highly recommend that you make all the necessary checks first to make sure your <a href="/safe-ibiza-villa-rental/">booking will be safe</a>. With this is mind, we've put together an <a href="/safe-ibiza-villa-rental/">easy to follow, essential checklist</a>. We invite you to <a href="/safe-ibiza-villa-rental/">take a look</a>. If you follow the advice given, then you can relax, safe in the knowledge that your villa booking is as safe as it's possible to be.</span></p>
            </div>
        </div>
    </div>
</div>

<div class="row final-intro">
    <div class="small-12 columns text-center">
        <p>And now, without further ado, presenting our collection of small to medium-sized villas to rent in Ibiza.<br> Please go ahead and browse, your perfect holiday home is waiting!</p>
    </div>
</div>

<div class="site-content">
    <div class="row">
        <?php
        if ($spanish_law == 'yes') {
            $args = array(
                'post_type' => $property_type,
                'posts_per_page' => -1,
                'property_location' => $property_location,
                'post__not_in' => array('5610','2928'),
                'meta_key' => 'property_for_sale',
                'meta_value' => 0
            );
        } else {
            $args = array(
                'post_type' => $property_type,
                'posts_per_page' => -1,
                'property_location' => $property_location,
                'meta_key' => 'property_for_sale',
                'meta_value' => 0
            );
        }

        $property_query = new WP_Query($args);

        if ($property_query->have_posts()) : ?>
            <ul class="property-grid">
                <?php while ($property_query->have_posts()): $property_query->the_post(); global $post; ?>
                    <?php
                    $location = get_field('property_map');

                    if ($spanish_law == 'yes') {
                        $property_sleeps = get_field('property_sleeps_spanish');
                        $property_bedrooms = get_field('property_bedrooms_spanish');
                        $property_bathrooms = get_field('property_bathrooms_spanish');
                        $property_description = get_field('property_description_spanish');
                    } else {
                        $property_sleeps = get_field('property_sleeps');
                        $property_bedrooms = get_field('property_bedrooms');
                        $property_bathrooms = get_field('property_bathrooms');
                    }

                    if ($property_sleeps >= $sleeps_minimum && $property_sleeps <= $sleeps_maximum): ?>
                        <li>
                            <?php get_template_part('templates/loop-grid-part'); ?>
                        </li>
                    <?php endif; ?>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
        <?php wp_reset_query(); ?>
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