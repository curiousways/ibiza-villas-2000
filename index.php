<?php get_header(); ?>

<style>
i.fas.fa-star{color:#FDBF3D!important}#ccc[light] #ccc-icon{fill:#3CA09A!important;margin-bottom:0!important}@media only screen and (min-width:1385px){.top-bar .top-bar-section>ul li:not(.has-form) a:not(.button){padding:0 10px;font-size:74%}}@media only screen and (max-width:1385px) and (min-width:1280px){.top-bar .top-bar-section>ul li:not(.has-form) a:not(.button){padding:0 10px;font-size:66%}}@media only screen and (max-width:1280px) and (min-width:1120px){.top-bar .top-bar-section>ul li:not(.has-form) a:not(.button){padding:0 10px;font-size:62%}}@media only screen and (max-width:1230px) and (min-width:1120px){.top-bar .top-bar-section>ul li:not(.has-form) a:not(.button){padding:0 5px;font-size:58%}}@media only screen and (max-width:1120px) and (min-width:1080px){.top-bar .top-bar-section>ul li:not(.has-form) a:not(.button){padding:0 5px;font-size:55%}}
</style>

<div class="site-content">
    <div class="row">
        <div class="large-8 columns">
            <h1><?php the_title(); ?></h1>
            <?php
            // Gallery for single pages
            $images = get_field('page_gallery'); 
            if($images):
            ?>
            <div class="page-slider">
                <?php foreach($images as $image): ?>
                    <div>
                        <?php echo wp_get_attachment_image($image['id'], 'property-gallery-image', false, array('class'=>'slick-loading')); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <?php get_template_part('loop'); ?>
        </div>
        <div class="large-4 columns">
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>