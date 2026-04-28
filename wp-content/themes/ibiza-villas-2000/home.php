<?php get_header(); ?>

<style>
.button-blog{padding:10px 60px!important}.nomage{display:inline-block;vertical-align:middle;object-fit:cover;height:270px;border-radius:5px}hr{box-sizing:content-box;border:solid #DDD;border-width:1px 0 0;height:0;margin:2.25rem 0 2.1875rem}.site-content{background:linear-gradient(#f7F7F7,#ffffff)}.partners-section{background-size:contain;background-position:bottom;background-image:url(https://ibizavillas2000.com/wp-ibiza/wp-content/themes/rudeibiza/images/IbizaVillas2000-Partners.jpg);background-repeat:no-repeat}
</style>

<div class="site-content">
    <div class="row">
        <div class="large-8 columns">
            <h1><?php echo get_the_title(get_option('page_for_posts', true)); ?></h1>
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <div class="row">
                    <article <?php post_class() ?> id="post-<?php the_ID(); ?>">
                        <?php if (has_post_thumbnail()) { ?>
                            <div class="small-12 medium-4 columns">
                                <?php the_post_thumbnail('large', array('class' => 'nomage')); ?>
                            </div>
                            <div class="small-12 medium-8 columns">
                        <?php } else { ?>
                            <div class="small-12 columns">
                        <?php } ?>
                            <h3><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
                            <p><strong><time datetime="<?php the_time('Y-m-d')?>"><?php the_time('F jS, Y') ?></time></strong></p>
                            <p><?php the_excerpt(); ?></p>
                            <a class="button button-blog" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php _e('Continue Reading'); ?></a>
                        </div>
                    </article>
                </div>
                <hr>
            <?php endwhile; ?>
            
            <div class="row">
                <div class="small-12 columns">
                    <?php numeric_posts_nav(); ?>
                </div>
            </div>
            
            <?php else : ?>
                <div class="row">
                    <div class="alert-box error">
                        <?php _e('Sorry, the information you requested was not found'); ?>
                        <?php get_search_form(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="large-4 columns">
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