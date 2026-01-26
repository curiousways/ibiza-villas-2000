<?php
/*
 * The property loop
 */



?>

    <?php  include( TEMPLATEPATH . '/templates/spanish_law.php'); 


            if ($spanish_law == 'yes') {
              $property_sleeps  = get_field('property_sleeps_spanish');
              $property_bedrooms = get_field('property_bedrooms_spanish');
              $property_bathrooms = get_field('property_bathrooms_spanish');
              $property_description = get_field('property_description_spanish');
            } else {
              $property_sleeps  = get_field('property_sleeps');
              $property_bedrooms = get_field('property_bedrooms');
              $property_bathrooms = get_field('property_bathrooms');
              
            }


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
    <div style="display:none;" class="row villa-manager-code" id="property-availability" style="display:none;">
      <div class="small-12 columns">
          <h2>Availability</h2>
          <?php echo $villa_code; ?>
      </div>
    </div>

    
    <?php endif; ?>


      <div class="toggle-buttons">
        <?php if( $images ): ?>
          <a class="button toggle-property-images active" href="#">View Images</a>
        <?php endif; ?>

        <a class="button toggle-property-map <?php if(! $images ) { echo ' active'; } ?>" href="#">View Map</a>


       <?php $villa_code = get_field('villa_manager_code'); if( $villa_code ): ?>
          <a class="button toggle-property-availability" href="#">Show Availability</a>
       <?php endif; ?>
      </div>




      <?php




  $location     = get_the_terms($post->ID,'property_location');
  $location     = (!empty($location) ? $location[0]->name : ''); // returns true

  $location2    = get_the_terms($post->ID,'property_location');
  $location2    = (!empty($location2) ? $location2[1]->name : ''); // returns true



  // Locations
  if(!empty($location) && !empty($location2)) {

    $locations = $location . ' / ' . $location2;

  } else {

    $locations = $location;

  }





          $property_type = get_post_type($post->ID);
          if ($property_type == "villas") {
        $property_type = 'villa';
      }
      if ($property_type == "apartments") {
        $property_type = 'apartment';
      }




            

if ( is_single( 12511 ) ) {
         echo '<blockquote class="property-desc"><p>This Sovereign Airstream  ';
              echo $property_type;
              echo ' sleeps ' .  $property_sleeps;
              echo ' guests.';

                echo '</blockquote>';
            }
            

    /* ... */


          elseif($property_sleeps >= 12) {
              echo '<blockquote class="property-desc"><p>This ' . $locations . ' ';
              echo $property_type;
              echo ' comfortably sleeps 12';
              echo ' guests.';



              echo '</blockquote>';
            }
            else {echo '<blockquote class="property-desc"><p>This ' . $locations . ' ';
              echo $property_type;
              echo ' comfortably sleeps ' .  $property_sleeps;
              echo ' guests.';



              echo '</blockquote>';
            }






      ?>


    <div class="hide-for-large-up">


      <?php 

          if ( is_single( 12511 ) ) {
                 echo '<div class="single-property-sleeps">';
                    echo "<h3>This Sovereign Airstream sleeps " . $property_sleeps;
                      echo '</h3>';
                    echo '</div>';
            }
            

 


            elseif ($property_sleeps >= 12) {
                    
                    echo '<div class="single-property-sleeps">';
                    echo "<h3>This Villa sleeps 12</h3>" ;
                    echo "<p><b>Please note:</b><br>If you are a large group of 12 or more people<br>" ;
                    echo "please email us at<br>" ;
                    echo '<a href="mailto:bookings@ibizavillas2000.com">bookings@ibizavillas2000.com</a></p>';
                    echo '</div>';
                    
                
                }

                else {

                  echo '<div class="single-property-sleeps">';
            echo "<h3>This Villa sleeps " . $property_sleeps;
            echo '</h3>';
            echo '</div>';

   
    
}
          
      ?>



      <?php get_template_part( 'templates/single-property-features' ); ?>

      <div class="property-sidebar">

         <p><a class="button" href="#enquire-now">BOOK / ENQUIRE NOW</a></p> 

      </div>


           <div style="display: none;margin-top: -20px;margin-bottom:20px;"><?php echo do_shortcode('[whatsapp_button id="17339"]'); ?>
</div>

      <?php get_template_part( 'templates/single-property-summary' ); ?>
    </div>


      <?php 
// Get the current URL 
$current_url = $_SERVER['REQUEST_URI']; 

// Check if the URL contains "/sales/" 
if (strpos($current_url, '/sale/') !== false || strpos($_SERVER['REQUEST_URI'], 'airstream') !== false || strpos($_SERVER['REQUEST_URI'], 'villa-can-vicente') !== false || strpos($_SERVER['REQUEST_URI'], 'villa-omni') !== false) { 
    // Change the CSS class 
    $css_style = 'display:none;'; 
} else { 
    // Use the default class 
    $css_style = 'display:block;'; 
} 
?> 

<div style="<?php echo $css_style; ?>;display:none ;"> 



        <h2 style="padding-top:40px;padding-bottom:20px;">Availability</h2>

    
  
    

         <?php
          $villa_pretty_name = get_field('villa_pretty_name');
          $clean_name = str_replace(' ', '',$villa_pretty_name);
          if($clean_name=='VillaTunicu'){
           echo do_shortcode( "[dd_show_calendar villa='tunicu']");
          }
          if($clean_name=='VillaSavines'){
           echo do_shortcode( "[dd_show_calendar villa='savinas']");
          }
          if($clean_name=='VillaCasaLaCabana'){
           echo do_shortcode( "[dd_show_calendar villa='lacabana']");
          }
          if($clean_name=='CasaRoig'){
           echo do_shortcode( "[dd_show_calendar villa='roig']");
          }
          if($clean_name=='VillaTom'){
           echo do_shortcode( "[dd_show_calendar villa='tom']");
          }
          if($clean_name=='VillaKM2(CasaMaria)'){
           echo do_shortcode( "[dd_show_calendar villa='km2']");
          }
          if($clean_name=='VillaTegui'){
           echo do_shortcode( "[dd_show_calendar villa='tegui']");
          }
          if($clean_name=='VillaCanReiet(Miguel)'){
           echo do_shortcode( "[dd_show_calendar villa='reiet']");
          }
          if($clean_name=='VillaPepLuis'){
           echo do_shortcode( "[dd_show_calendar villa='luis']");
          }
          if($clean_name=='VillaPatxi'){
           echo do_shortcode( "[dd_show_calendar villa='patxi']");
          }
          if($clean_name=='VillaAlexa'){
           echo do_shortcode( "[dd_show_calendar villa='alexa']");
          }
          if($clean_name=='VillaAlberto'){
           echo do_shortcode( "[dd_show_calendar villa='alberto']");
          }
          if($clean_name=='VillaNieves'){
           echo do_shortcode( "[dd_show_calendar villa='nieves']");
          }
          if($clean_name=='VillaOasis'){
           echo do_shortcode( "[dd_show_calendar villa='oasis']");
          }
          if($clean_name=='VillaBellaVista'){
           echo do_shortcode( "[dd_show_calendar villa='bellevista']");
          }
          if($clean_name=='CasaMaymo'){
           echo do_shortcode( "[dd_show_calendar villa='maymo']");
          }
          if($clean_name=='VillaLosOlivos'){
           echo do_shortcode( "[dd_show_calendar villa='olivos']");
          }
          if($clean_name=='VillaCarlos'){
           echo do_shortcode( "[dd_show_calendar villa='carlos']");
          }
          if($clean_name=='CasaGalop'){
           echo do_shortcode( "[dd_show_calendar villa='galop']");
          }
          if($clean_name=='VillaMaria'){
           echo do_shortcode( "[dd_show_calendar villa='maria']");
          }
          if($clean_name=='VillaEvie'){
           echo do_shortcode( "[dd_show_calendar villa='evie']");
          }
          if($clean_name=='CasaCarolle(VillaCarol)'){
           echo do_shortcode( "[dd_show_calendar villa='carolle']");
          }
          if($clean_name=='CanMestreastunningIbizaVilla'){
           echo do_shortcode( "[dd_show_calendar villa='mestre']");
          }
          if($clean_name=='VillaDaniel(CanNebot)'){
           echo do_shortcode( "[dd_show_calendar villa='daniel']");
          }
          if($clean_name=='VillaTorres'){
           echo do_shortcode( "[dd_show_calendar villa='torres']");
          }
          if($clean_name=='CasaPeppe'){
           echo do_shortcode( "[dd_show_calendar villa='peppe']");
          }
 
    ?>   

        <div class="row small-12 medium-12 large-12">

    <div class="columns small-6 medium-2 large-2">
      <span style="    background-color: #BDFFC6;
    padding: 4px 10px;
    color: #BDFFC6;
    margin-right: 10px;">.</span>Available</p> 


    </div>
    <div class="columns small-6 medium-10 large-10">
      <p><span style="    background-color: #FDE1E6;
    padding: 4px 10px;
    color: #FDE1E6;
    margin-right: 10px;">.</span>Booked</p> 


    </div>
   </div>
    
</div>
     
  <h2 style="padding-top:20px;">

  <?php _e('About'); ?> 
  <?php 
      
      $villa_pretty_name = get_field('villa_pretty_name'); 

    if ($villa_pretty_name) {
      echo $villa_pretty_name;
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
     
        

  <?php get_template_part('templates/social-share'); ?>