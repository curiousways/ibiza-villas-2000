<?php get_header(); ?>

<div class="site-content">
    <div class="row">
        <h1><?php the_title(); ?></h1>
        <?php get_template_part('loop'); ?>
        
        <?php
        $args = array(
            'post_type' => 'custom_weeks',
            'posts_per_page' => -1,
        );
        $custom_week_query = new WP_Query($args);
        if ($custom_week_query->have_posts()) : ?>
            <ul class="property-grid">
                <?php while ($custom_week_query->have_posts()): $custom_week_query->the_post(); global $post; ?>
                    <li>
                        <div class="property">
                            <div class="thumb">
                                <a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>"><?php the_post_thumbnail('iv2000_420x280'); ?></a>
                            </div>
                            <div class="info">
                                <?php 
                                $villa_pretty_name = get_field('villa_pretty_name'); 
                                if ($villa_pretty_name) {
                                    echo '<h3><a href="' . get_permalink() . '" title="' . the_title() . '">' . the_title() . '</a></h3>';
                                    echo '<h4>' . $villa_pretty_name . '</h4>';
                                } else {
                                    echo '<h3><a href="' . get_permalink() . '" title="' . the_title() . '">' . the_title() . '</a></h3>';
                                }
                                ?>
                                <hr>
                                <?php 
                                if (!has_excerpt()) {
                                    $trimmed = content(25);
                                    echo wp_strip_all_tags($trimmed);
                                } else { 
                                    $excerpt = excerpt(25);
                                    echo $excerpt;
                                } 
                                ?>
                                <a href="<?php the_permalink(); ?>" class="button pull-right">Find out more</a>
                            </div>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
        <?php wp_reset_query(); ?>
    </div>
</div>

<?php get_footer(); ?>