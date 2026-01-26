<?php


if( have_rows('offer_table', "3720") ): ?>


    <?php while ( have_rows('offer_table', "3720") ) : the_row(); ?>


        <?php

        if(!empty(get_sub_field('home_feature_this_offer'))){

		
        $post_object = get_sub_field('villa');

        if( $post_object ):

            $post = $post_object;
            setup_postdata( $post ); ?>

            <?php
            //if ($current_user->ID == '3') {
            /*get all data to work through next*/

			
			
            $villaname = get_field('villa_pretty_name');

            $offers_new[$villaname]['name'] = $villaname;
            $offers_new[$villaname]['sleeps'] = $property_sleeps;

            $num = count($offers_new[$villaname]['offers']);

            $offers_new[$villaname]['offers'][$num]['dates'] = get_sub_field('dates');
            $offers_new[$villaname]['offers'][$num]['price'] = get_sub_field('special_offer_price');
            $offers_new[$villaname]['link'] = get_permalink();
            $offers_new[$villaname]['image'] = get_the_post_thumbnail();
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

 

            $property_sleeps = (!empty($property_sleeps) ? 'Sleeps ' . $property_sleeps : ''); // returns true

            $location 		= get_the_terms($post->ID,'property_location');
            $location 		= (!empty($location) ? $location[0]->name : ''); // returns true

            $location2 		= get_the_terms($post->ID,'property_location');
            $location2 		= (!empty($location2) ? $location2[1]->name : ''); // returns true

            $postTypeObj 	= get_post_type_object( get_post_type() );
            $type 			= $postTypeObj->labels->singular_name;

            // Location and sleeps, if either is empty don't print the /
             if(!empty($location) && !empty($location2) && !empty($property_sleeps)) {

                $locationsleeps = $location . ' - ' . $location2;

            } else if(!empty($location) && !empty($property_sleeps)) {

                $locationsleeps = $location;

            } else {

                $locationsleeps = $location . $property_sleeps;
}




            $offers_new[$villaname]['sleeps']=$locationsleeps;
            //}



            ?>


            <?php wp_reset_postdata(); ?>

        <?php endif; ?>



        <?php
         }else{
                
            }


        ?>

    <?php endwhile; ?>


<?php endif; ?>


<?php

foreach($offers_new as $offer_villa){
?>

<div class="row" style="padding: 40px;">
    <div class="small-12 medium-6 large-6 columns">
        <a href="<?php echo $offer_villa['link']; ?>" title="<?php echo $offer_villa['name']; ?>"><?php echo $offer_villa['image'];; ?></a>
    </div>

    <div class="column small-12 medium-6 large-6">
        <?php
        
        $villa_pretty_name = $offer_villa['name'];




        if ($villa_pretty_name) {
            echo '<h3><a href="' . $offer_villa['link'] . '" title="' . $offer_villa['name'] . '">' . $offer_villa['name'] . '</a></h3>';
            echo '<p style="font-size:12px;" class="slider-location-sleeps">' . $offer_villa['sleeps'] .  '</p>';
            echo '<h4 style="margin-top:-5px;">Dates on offer:</h4>';

      } foreach($offer_villa['offers'] as $offer_date){
                echo '<h5 style="line-height:30px;">' .$offer_date['dates']. ' for just <span style="font-size: 150%;color: #fff;font-weight: 800;padding: 5px;background: #ffc029;">&#163;' . $offer_date['price'] . '!</span></h5>';
            }


    



        ?>

        <!--<div class="column small-12 medium-2 large-4">
                    <div class="slider-info">
                        <?php echo "price goes here"; ?>

                    </div>
                </div>-->



    </div>


	<div class="column small-12 medium-12 large-12" style="padding-top: 20px;">
        <a style="padding:20px 10px; width: 100%;box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);font-weight: bold;" href="<?php echo $offer_villa['link']; ?>" class="button">GET THE OFFER!</a>
    </div>
<!-- BACK UP BPOUTON

<div class="column small-12 medium-12 large-12" style="padding-top: 20px;">
        <a style="width: 100%;box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);" href="<?php echo $offer_villa['link']; ?>" class="button">CHECK <?php echo $offer_villa['name'];?> & GET THE OFFER!</a>
    </div>
    
-->
    

   
</div>

<?php


}


