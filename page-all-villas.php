<?php get_header(); ?>

<?php global $spanish_law; ?>

<?php		
if ($spanish_law == 'yes') {
    $args = array(
        'post_type' => 'villas',
        'posts_per_page' => -1,
        'post__not_in' => array('5610','2928'),
        'meta_key' => 'property_for_sale',
        'meta_value' => 0
    );
} else {
    $args = array(
        'post_type' => 'villas',
        'posts_per_page' => -1,
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
                if (!empty($location)) : ?>
                    <div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng']; ?>">
                        <div class="marker-info">
                            <div class="info">
                                <div class="thumb">
                                    <a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
                                </div>
                                <?php 
                                $villa_pretty_name = get_field('villa_pretty_name'); 
                                if ($villa_pretty_name) {
                                    echo '<h3><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
                                    echo '<h4>' . $villa_pretty_name . '</h4>';
                                } else {
                                    echo '<h3><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
                                }
                                ?>
                                <?php $location = wp_get_post_terms($post->ID, 'property_location'); ?>
                                <h4><?php echo $location[0]->name; ?></h4>
                                <p>
                                    <?php 
                                    if ($spanish_law == 'yes') {
                                        echo wp_trim_words($property_description, 50);
                                    } else {
                                        if (has_excerpt()) {
                                            $excerpt = excerpt(50);
                                            echo $excerpt;					
                                        } else {
                                            $trimmed = content(50);
                                            echo wp_strip_all_tags($trimmed);
                                        }
                                    }
                                    ?>
                                </p>
                                <br><a href="<?php the_permalink(); ?>" class="button">Find out more</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endwhile; ?>
        </div>
    </div>
<?php endif; ?>

<?php wp_reset_query(); ?>

<div class="site-content">
    <div class="row">
        <div class="small-12 columns">
            <h1><?php the_title(); ?></h1>
        </div>

        <div style="display:none;" class="small-12 medium-12 large-12 columns">
            <?php echo do_shortcode('[vc_row gap="35"][vc_column][vc_tta_accordion style="outline" active_section="1" no_fill="true" collapsible_all="true"][vc_tta_section title="LOOKING FOR A LARGE VILLA (14+) HERE IN IBIZA?" tab_id="1652684979034-5c35c36b-3b33"][vc_column_text]<strong>Please Note!</strong> For legal reasons the maximum occupancy of a villa in Ibiza is 12 people, <strong>if you are searching for a villa for more than 12 people, please <a href="https://ibizavillas2000.com/contact/">contact us</a>. </strong>We have many villas next door to each other plus our apartment hotel which can sleep up to 66 guests.[/vc_column_text][/vc_tta_section][/vc_tta_accordion][/vc_column][/vc_row]'); ?>
        </div>

        <div class="small-12 medium-12 large-12 columns">
            <p>Below, we've curated our full collection of Ibiza villas for rent in all locations. All with pools & all with fantastic value prices. If you're still unsure about the size of your group or preferred location and are exploring all options, then this is the right page for you to have a good browse and check out what's on offer.</p>
        </div>
        
        <?php
        if ($spanish_law == 'yes') {
            $args = array(
                'post_type' => 'villas',
                'posts_per_page' => -1,
                'post__not_in' => array('5610','2928'),
                'meta_key' => 'property_for_sale',
                'meta_value' => 0
            );
        } else {
            $args = array(
                'post_type' => 'villas',
                'posts_per_page' => -1,
                'meta_key' => 'property_for_sale',
                'meta_value' => 0
            );
        }

        $property_query = new WP_Query($args);

        if ($property_query->have_posts()) : ?>
            <ul class="property-grid">
                <?php while ($property_query->have_posts()): $property_query->the_post(); global $post; ?>
                    <li id="property-<?php echo $post->ID; ?>">
                        <?php get_template_part('templates/loop-grid-part'); ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>

        <?php wp_reset_query(); ?>
        <?php the_content(); ?>
    </div>
</div>

<div class="future small-12 medium-12 large-12 columns" style="background-color: #f7f7f7;display:none;padding-top: 80px;">
    <?php get_template_part('templates/featured-services'); ?>
</div>

<div style="background-size: contain;background-position: bottom; background-image: url(https://ibizavillas2000.com/wp-ibiza/wp-content/themes/rudeibiza/images/IbizaVillas2000-Partners.jpg);background-repeat: no-repeat;" class="rude-sponsors-container">
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

<?php get_footer(); ?>