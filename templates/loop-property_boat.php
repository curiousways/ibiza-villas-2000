<?php
/*
 * The property_boat loop
 */



?>



    <?php $images = get_field('property_images'); if( $images ): ?>
    

<?php

//if (is_single ($post = '12511')) {
      //get_template_part( 'templates/single-property-video' );}

?>

           <div class="show-for-large-up" style="padding:20px 0px;">
                <?php get_template_part( 'templates/single-property-video' ); ?>
            </div>




    <div id="property-slider" class="property-slider landscape">
          <?php foreach( $images as $image ): ?>
              <div>
                  <?php 
 //$image_attributes = wp_get_attachment_image_srcset( $image['id'] );
 $image_attributes = wp_get_attachment_image_src( $image['id'], array('840','560') );
?>
<img data-lazy="<?php echo $image_attributes[0] ?>"/>

              </div>
          <?php endforeach;  ?>
      </div>
    <?php endif; ?>


      <div id="property-map<?php if(! $images ) { echo ' force-display'; } ?>"  style="display:none;">
        <?php get_template_part( 'templates/single-property-map' ); ?>
      </div>


    <?php if( $villa_code = get_field('villa_manager_code') ): ?>
    

    
    <?php endif; ?>


    






    <div class="hide-for-large-up">


      



      <?php get_template_part( 'templates/single-property-features' ); ?>

      
      <?php get_template_part( 'templates/single-property-summary' ); ?>
    </div>


       

      

     
  <h2 style="padding-top:20px;">

  <?php _e('About'); ?> 
  <?php 
      
      $boat_pretty_name = get_field('boat_pretty_name'); 

    if ($boat_pretty_name) {
      echo $boat_pretty_name;
    } 

  ?>
  
  </h2>
 
  


  <?php 


            if ($spanish_law == 'yes') {
              $spanish_content = get_field('property_description_spanish');
              echo $spanish_content;
            } else {
              the_content();
            }


  ?>
     
        
