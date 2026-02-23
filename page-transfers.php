<?php get_header(); ?>

<style>
#transfer-iframe{height:290px}.transfer-quote-section{text-align:center;margin-bottom:2rem}.transfer-quote-section h2{margin-bottom:1rem}.transfer-quote-section .button{display:inline-block;padding:10px 20px}
</style>

<div class="site-content">
    <div class="row">
        <div class="large-8 columns">
            <h1><?php the_title(); ?></h1>
            
            <?php
            // Gallery for single pages
            $images = get_field('page_gallery'); 
            if ($images):
            ?>
            <div class="page-slider">
                <?php foreach ($images as $image): ?>
                    <div>
                        <?php echo wp_get_attachment_image($image['id'], 'iv2000_1024x685', false, array('class'=>'slick-loading')); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Transfer iFrame -->
            <iframe id="transfer-iframe" src='https://airporttransfersibiza.com/cgi-bin/en/villatransfer.pl' frameborder='0'></iframe>
            
            <script>
                iFrameResize({
                    log: true,
                    enablePublicMethods: true,
                    resizedCallback: function(messageData){
                    },
                    messageCallback: function(messageData){
                    },
                    closedCallback: function(id){
                    }
                });
            </script>
            
            <?php get_template_part('loop'); ?>
            
            <div class="transfer-quote-section">
                <h2>Get a quote &amp; book your<br>Ibiza Transfer with us online</h2>
                <a class="button" target="_blank" href="http://airporttransfersibiza.com/cgi-bin/en/home.pl">Transfer Quote</a>
            </div>
        </div>
        
        <div class="large-4 columns">
            <?php get_sidebar(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>