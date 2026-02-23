<style type="text/css">
	ul {
    margin: 0 0 1.5rem;
    padding: 0;
}

li {
    display: block;
}

svg {
    fill: currentColor;
    width: 1.5rem;
    height: 1.5rem;
}


main {
    max-width: 81.25rem;
    margin: auto;
}




.card-content{
        padding: 1.9375rem;
}


/********************************
* Carousel styles
*********************************/
.carousel-items {
    display: flex;
    overflow-x: scroll;
    padding: 1rem 0;
    scroll-snap-type: x mandatory;
}

.carousel-item {
    flex: 1 0 250px;
    margin-left: 1rem;
    margin-right: 1rem;
    scroll-snap-align: start;
}

::-webkit-scrollbar-track {
    background-color: #F5F5F5;
}

::-webkit-scrollbar {
    height: 6px;
    background-color: #F5F5F5;
}

::-webkit-scrollbar-thumb {
    background-color: #3d4852;
    border-radius: 3px;
}


/********************************
* Breakpoints
*********************************/
@media screen and (min-width: 0px) {
    .carousel-item {
        flex-basis: 520px;
    }
    .card {
    border-radius: 5px;
    background-color: #fff;
    min-height: 530px;
    display:none;
    }
    .hhnomads-01{
    font-size: 30px;
    letter-spacing: 2px;
    line-height: 1.0;
    text-align: center!important;
    }
    .hhnomads-02{
    font-size: 39px;
    letter-spacing: 2px;
    line-height: 1.0;
    text-align: center!important;
    }
}


@media screen and (min-width: 576px) {
    .carousel-item {
        flex-basis: 400px;
    }
    .card {
    border-radius: 5px;
    background-color: #fff;
    min-height: 530px;
     display:none;
}
.hhnomads-01{
    font-size: 29px;
    letter-spacing: 2px;
    line-height: 1.0;
    }
    .hhnomads-02{
    font-size: 37px;
    letter-spacing: 2px;
    line-height: 1.0;
    }
}

@media screen and (min-width: 992px) {


    .carousel-item {
        flex-basis: 380px;
    }
    .card {
    border-radius: 5px;
    background-color: #fff;
        min-height: 530px;
        display:block;
}
    .hhnomads-01{
    font-size: 19px;
    letter-spacing: 2px;
    line-height: 1.0;
    }
    .hhnomads-02{
    font-size: 24px;
    letter-spacing: 2px;
    line-height: 1.0;
    }

}

@media screen and (min-width: 1280px) {
    

    .carousel-item {
        flex-basis: 410px;
    }

    .hhnomads-01{
    font-size: 26px;
    letter-spacing: 2px;
    line-height: 1.0;
    }
    .hhnomads-02{
    font-size: 34px;
    letter-spacing: 2px;
    line-height: 1.0;
    }
    .card {
    border-radius: 5px;
    background-color: #fff;
        min-height: 500px;
        display:block;
}
}

@media screen and (min-width: 1600px) {

    .carousel-item {
        flex-basis: 410px;
    }

    .hhnomads-01{
    font-size: 26px;
    letter-spacing: 2px;
    line-height: 1.0;
    }
    .hhnomads-02{
    font-size: 34px;
    letter-spacing: 2px;
    line-height: 1.0;
    }
    .card {
    border-radius: 5px;
    background-color: #fff;
        min-height: 500px;
        display:block;
}


}
</style>
<?php

	$explore_1_image = get_field('explore_1_image', 'option');
	$explore_1_text = get_field('explore_1_text', 'option');
	$explore_1_url = get_field('explore_1_url', 'option');
	$explore_1_image_scale = wp_get_attachment_image_src( $explore_1_image['id'], 'iv2000_420x280' );

	$explore_2_image = get_field('explore_2_image', 'option');
	$explore_2_text = get_field('explore_2_text', 'option');
	$explore_2_url = get_field('explore_2_url', 'option');
	$explore_2_image_scale = wp_get_attachment_image_src( $explore_2_image['id'], 'iv2000_420x280' );

	$explore_3_image = get_field('explore_3_image', 'option');
	$explore_3_text = get_field('explore_3_text', 'option');
	$explore_3_url = get_field('explore_3_url', 'option');
	$explore_3_image_scale = wp_get_attachment_image_src( $explore_3_image['id'], 'iv2000_420x280' );

?>



<div class="explore-slider">


	<div class="explore" style="border-radius:5px;background-image: linear-gradient(to bottom, rgba(245, 246, 252, 0.52), rgba(82, 216, 163, 0.52)), url(<?php echo $explore_1_image_scale[0]; ?>);">
		<a href="<?php echo $explore_1_url; ?>"><span><?php echo $explore_1_text; ?></span></a>
	</div>

	<div class="explore" style="border-radius:5px;background-image: linear-gradient(to bottom, rgba(245, 246, 252, 0.52), rgba(82, 216, 163, 0.52)), url(<?php echo $explore_2_image_scale[0]; ?>);">
		<a href="<?php echo $explore_2_url; ?>"><span><?php echo $explore_2_text; ?></span></a>
	</div>

	<div class="explore" style="border-radius:5px;background-image: linear-gradient(to bottom, rgba(245, 246, 252, 0.52), rgba(82, 216, 163, 0.52)), url(<?php echo $explore_3_image_scale[0]; ?>);">
		<a href="<?php echo $explore_3_url; ?>"><span><?php echo $explore_3_text; ?></span></a>
	</div>


</div><!--/.explore-slider -->

<div style="background-color:orange;" class="explore-slider">



	 


</div>

<div style="border-radius:5px;background-color: #F6B91D;" class="card">
                      
                       <img style="border-top-right-radius: 5px;
    border-top-left-radius: 5px;" class="bord-nomads" alt="Ibiza Villas 2000 - Rental Insider Guide" title="Ibiza Villas 2000 - Ibiza Rental Insider Guide" src="https://ibizavillas2000.com/wp-content/themes/rudeibiza/services/IbizaVillas2000-IbizaRentalGuide.png" />

                        <div style="padding-top: 0px!important;" class="card-content">

                            
                            <h3><a class="hhnomads-01" style="color:#fff;" href="https://ibizavillas2000.com/ibiza-villa-rentals-insiders-guide-for-your-2025-holiday/">IBIZA VILLA RENTAL</a></h3>
                            <h3><a class="hhnomads-02" style="color:#fff;" href="https://ibizavillas2000.com/ibiza-villa-rentals-insiders-guide-for-your-2025-holiday/">INSIDERS GUIDE</a></h3>
                            <h3><a class="hhnomads-01" style="color:#fff;" href="https://ibizavillas2000.com/ibiza-villa-rentals-insiders-guide-for-your-2025-holiday/">FOR YOUR HOLIDAY</a></h3>
                        
                            
                            <a style="margin-top:20px;padding:10px;width: 100%!important;" href="https://ibizavillas2000.com/ibiza-villa-rentals-insiders-guide-for-your-2025-holiday/" class="button butds" data-hover="MORE INFO">IBIZA RENTAL GUIDE</a>
                        </div>
                    </div>

                    