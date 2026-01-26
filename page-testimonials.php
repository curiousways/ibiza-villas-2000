<?php get_header(); ?>

<style>
.grw-slider .wp-google-info{display:none!important}
</style>

<div class="site-content page-testimonials">
    <div class="row">
        <div class="large-8 columns">
            <h1><?php the_title(); ?></h1>
            
            <?php get_template_part('loop'); ?>
            
            <?php
            $args = array(
                'post_type' => 'testimonials',
                'posts_per_page' => -1,
                'meta_key' => 'testimonial_date',
                'orderby' => 'meta_value_num',
                'order' => 'DESC'
            );
            $testimonial_query = new WP_Query($args);
            ?>
            
            <!--
            <?php if ($testimonial_query->have_posts()) : ?>
                <div class="testimonials">
                    <div class="row">
                        <div class="columns">
                            <?php while ($testimonial_query->have_posts()) : $testimonial_query->the_post(); ?>
                                <div class="testimonial">
                                    <blockquote>
                                    <?php the_title(); ?>
                                    <small><?php the_field('testimonial_date'); ?></small>
                                    </blockquote>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            -->
            
            <?php wp_reset_query(); ?>
        </div>
        
        <div class="large-4 columns">
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>