<?php
/*
 * The default loop
 */
?>
<a id="content"></a>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <?php get_template_part('templates/content', get_post_format()); ?>
<?php endwhile; else: ?>
    <div class="alert-box error"><?php _e('Sorry, the information you requested was not found'); ?></div>
<?php endif; ?>

<div class="row">
    <div class="small-12 columns">
        <?php if (function_exists("emm_paginate")) {
            emm_paginate();
        } ?>
    </div>
</div>

<?php
wp_reset_query();
wp_reset_postdata(); 
?>