<?php get_header(); ?>

<style>
.faq-container{padding-bottom:100px}.faq-accordion{margin-top:20px}.faq-accordion h3{cursor:pointer;background:#f5f5f5;padding:15px;margin:0;border-bottom:1px solid #ddd}.faq-accordion .content{padding:20px;background:#fff;border-bottom:1px solid #ddd}.faq-no-results{text-align:center;color:#666;font-style:italic}
</style>

<div class="site-content">
    <div class="row">
        <div class="large-8 columns faq-container">
            <?php
            /* Loop through posts with same location but exclude current post */
            $post_types = array('faq');
            $filter = '';
            if (!empty($_GET['filter'])) {
                $filter = htmlspecialchars($_GET['filter'], ENT_QUOTES);
                // If there is a value in the URL, the pass it into the query
                $args = array(
                    'post_type' => $post_types,
                    'showposts' => '-1',
                    'taxonomy' => 'faq-category',
                    'term' => $filter,
                );
            } else {
                // If there isn't a value in the URL return all the FAQS
                $args = array(
                    'post_type' => $post_types,
                    'showposts' => '-1',
                );
            }
            $faq = new WP_Query($args);
            ?>
            
            <h1><?php the_title(); ?> <?php echo !empty($filter) ? ': ' . ucfirst($filter) : ""; ?></h1>
            <?php get_template_part('loop'); ?>
            
            <?php if ($faq->have_posts()) : ?>
                <div class="accordion faq-accordion">
                    <?php while ($faq->have_posts()): $faq->the_post(); global $post; $i++ ?>
                        <h3><?php the_title(); ?></h3>
                        <div class="content">
                            <?php the_content(); ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="faq-no-results">Sorry, there are no FAQs related to that subject at the moment.</p>
            <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </div>
        
        <div class="large-4 columns">
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>