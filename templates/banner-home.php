
<?php if( have_rows('hero_slider') ): // check if the repeater field has rows of data ?>
	<div class="home-banner" role="banner">

 	<?php // loop through the rows of data
    while ( have_rows('hero_slider') ) : the_row();

        // display a sub field value
        $image = get_sub_field('hero_image');
        $title = get_sub_field('hero_title');
        $button = get_sub_field('hero_button_text');
        $link = get_sub_field('hero_button_link');

        ?>



                <div class="slideBG" style="background-image: url(<?php echo $image ?>);">

                    <?php if( !empty($title) || !empty($button) ): ?>

                        <div class="slide-content">
                        	
                            <?php if( !empty($title) ) :?>
                                <h3><?php echo $title ?></h3>
                            <?php endif; ?>
                        	
                            <?php if( !empty($button) ) :?>
                                <a href="<?php echo $link ?>" class="button"><?php echo $button ?></a>
                            <?php endif; ?>
                            
                        </div>

                    <?php endif; ?>

                    

                </div>


        <?php endwhile ; ?>
        </div>
        

<?php endif; ?>




