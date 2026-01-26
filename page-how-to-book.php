<?php get_header(); ?>

<div class="site-content">
    <div class="row">
        <div class="large-8 columns">
            <?php
            // If there is a value in the URL, the pass it into the query
            $args = array(
                'post_type' => 'faq',
                'showposts' => '-1',
                'taxonomy' => 'faq-category',
                'term' => 'how-to-book',
            );
            $faq = new WP_Query($args);
            ?>
            
            <h1><?php the_title(); ?> <?php echo !empty($filter) ? ': ' . ucfirst($filter) : ""; ?></h1>
            <?php get_template_part('loop'); ?>
            
            <?php if ($faq->have_posts()) : ?>
                <div class="accordion">
                    <?php while ($faq->have_posts()): $faq->the_post(); global $post; $i++ ?>
                        <h3><?php the_title(); ?></h3>
                        <div class="content">
                            <?php the_content(); ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>Sorry, there are no FAQs related to that subject at the moment.</p>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </div>
        
        <div class="large-4 columns">
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>