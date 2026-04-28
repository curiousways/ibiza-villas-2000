<?php get_header(); ?>

<?php
// TODO pass 3: editorial exclusion via ACF
$post__not_in = array();
$base_args = array(
	'post_type' => 'villas',
	'posts_per_page' => -1,
	'post__not_in' => $post__not_in,
	'meta_key' => 'property_for_sale',
	'meta_value' => 0,
);

$property_query = new WP_Query($base_args);

if ($property_query->have_posts()) : ?>
    <div class="row map-container">
        <div class="acf-map">
            <?php while ($property_query->have_posts()) : $property_query->the_post(); global $post; ?>
                <?php
				$location = get_field('property_map');
                if (!empty($location)) : ?>
                    <div class="marker" data-lat="<?php echo esc_attr($location['lat']); ?>" data-lng="<?php echo esc_attr($location['lng']); ?>">
                        <div class="marker-info">
                            <div class="info">
                                <div class="thumb">
                                    <a href="<?php echo esc_url(get_permalink()); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
                                </div>
                                <?php
								$villa_pretty_name = get_field('villa_pretty_name');
								if ($villa_pretty_name) {
									echo '<h3><a href="' . esc_url(get_permalink()) . '" title="' . esc_attr(get_the_title()) . '">' . esc_html(get_the_title()) . '</a></h3>';
									echo '<h4>' . esc_html($villa_pretty_name) . '</h4>';
								} else {
									echo '<h3><a href="' . esc_url(get_permalink()) . '" title="' . esc_attr(get_the_title()) . '">' . esc_html(get_the_title()) . '</a></h3>';
								}
								?>
                                <?php $loc_terms = wp_get_post_terms($post->ID, 'property_location'); ?>
								<h4><?php echo ! empty($loc_terms[0]) ? esc_html($loc_terms[0]->name) : ''; ?></h4>
                                <p>
                                    <?php
                                    if (has_excerpt()) {
											echo esc_html(excerpt(50));
                                    } else {
											$trimmed = content(50);
											echo esc_html(wp_strip_all_tags($trimmed));
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

<?php wp_reset_postdata(); ?>

<div class="site-content">
    <div class="row">
        <div class="small-12 columns">
            <h1><?php the_title(); ?></h1>
        </div>

        <div class="small-12 medium-12 large-12 columns">
            <p>Below, we've curated our full collection of Ibiza villas for rent in all locations. All with pools &amp; all with fantastic value prices. If you're still unsure about the size of your group or preferred location and are exploring all options, then this is the right page for you to have a good browse and check out what's on offer.</p>
        </div>
        
        <?php
		$property_query = new WP_Query($base_args);

		if ($property_query->have_posts()) : ?>
            <ul class="property-grid">
                <?php while ($property_query->have_posts()) : $property_query->the_post(); ?>
                    <li id="property-<?php echo (int) get_the_ID(); ?>">
                        <?php get_template_part('templates/loop-grid-part'); ?>
                    </li>
				<?php endwhile; ?>
            </ul>
		<?php endif; ?>

        <?php wp_reset_postdata(); ?>
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
