    <?php
/**
 * Template Name: Property Wishlist
**/

get_header(); ?>



<?php

  // GET THE COOKIE VALUE FOR PROPERTIES SET BY USER
  if(!isset($_COOKIE['property'])) {
    // cookie doesnt exist
  } else {
      $property_cookie = $_COOKIE['property'];
      $property_post_id_array = json_decode(stripslashes($_COOKIE['property']));
  }

?>




<div class="site-content">

  <div class="row">
    <div class="large-12 columns">
        <h1><?php the_title(); ?></h1>
        <?php the_content(); ?>

        <?php

        $args = array(
            'post__in' => $property_post_id_array,
            'posts_per_page' => -1,
            'post_type' => 'villas',
			'meta_key'		=> 'property_for_sale', //filter out for sale properties, true is for sale, 0 not for sale (rental).
			'meta_value'	=> 0
        );

        

        $property_query = new WP_Query( $args );

        if ( $property_query->have_posts() ) : ?>

         <ul class="property-interest-title-list">

          <?php while ( $property_query->have_posts() ): $property_query->the_post(); global $post; ?>


            <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>


          <?php endwhile; ?>

        </ul>

        <?php

        else:
          echo "<blockquote><h3>It looks like you haven't added any properties to your wishlist yet.</h3>";
          echo '<a class="button" href="/villas/">Browse All Villas</a></blockquote>';
        endif; ?>

        <?php wp_reset_query(); ?>

    </div>


  </div>


<div class="row">


      <div class="large-12 columns">
        
        
        
        <div class="row">
          <div class="medium-6 columns">
            <div class="wishlist-container">
              <h2>Send your wishlist to us</h2>
              <p>We can help check availability and answer any questions you have relating to these villas and your holiday.<br><a href="#" class="button wishlist-reveal-ibiza">Send wishlist to Ibiza Villas</a></p>
              <div class="wishlist-ibiza-form" style="display:none;">
                 <?php
                    // email wishlist to ibiza
                    echo do_shortcode('[gravityform id="4" title="false" description="false"]');
                 ?>
             </div>
            </div>
          </div>

          <div class="medium-6 columns">
            <div class="wishlist-container">
             <h2>Send your wishlist to a friend!</h2>
              <p>Send your friend the wishlist so they can see the properties you are interested in!<br><a href="#" class="button wishlist-reveal-friend">Send wishlist to your friend</a></p>
              <div class="wishlist-friend-form" style="display:none;">
                 <?php 
                   // send to friend
                   echo do_shortcode('[gravityform id="5" title="false" description="false" ajax="true"]');
                 ?>
               </div>
           </div>
          </div>

        </div>

       



      </div>



</div>








    <div class="row">

    <?php

    $args = array(
        'post__in' => $property_post_id_array,
        'posts_per_page' => -1,
        'post_type' => 'villas',
		'meta_key'		=> 'property_for_sale', //filter out for sale properties, true is for sale, 0 not for sale (rental).
		'meta_value'	=> 0
    );

    $property_query = new WP_Query( $args );

    if ( $property_query->have_posts() ) : ?>

      <h3>Property Wishlist</h3>

      <ul class="property-grid">

      <?php while ( $property_query->have_posts() ): $property_query->the_post(); global $post; ?>


          <li>

            <?php
              get_template_part( 'templates/loop-grid-part' );
            ?>

          </li>

        <?php //endif; ?>

      <?php endwhile; ?>

      </ul>

    <?php endif; ?>

    <?php wp_reset_query(); ?>


    </div><!--/.row -->

</div> <!-- site content -->



<?php get_footer(); ?>
