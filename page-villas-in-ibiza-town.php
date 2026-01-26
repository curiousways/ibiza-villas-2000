<?php get_header(); ?>


<?php global $spanish_law; ?>



<div class="row">
	
	
	<div class="columns small-12 medium-12">
  	  	<h1><?php the_title(); ?></h1>
		<?php get_template_part( 'loop' ); ?>
	
		<?php		


		if ($spanish_law == 'yes') {
				$args = array(
				    'post_type' => 'villas',
					'property_location' => 'ibiza-town',
				    'posts_per_page' => -1,
					'post__not_in'  => array('5610','2928'),
					'meta_key'		=> 'property_for_sale', //filter out for sale properties, true is for sale, 0 not for sale (rental).
					'meta_value'	=> 0
				);
		} else {
				$args = array(
				    'post_type' => 'villas',
					'property_location' => 'ibiza-town',
				    'posts_per_page' => -1,
					'meta_key'		=> 'property_for_sale', //filter out for sale properties, true is for sale, 0 not for sale (rental).
					'meta_value'	=> 0
				);
		}


		$property_query = new WP_Query( $args );

		if ( $property_query->have_posts() ) : ?>
			<div class="row map-container"><div class="acf-map">
			<?php while ( $property_query->have_posts() ): $property_query->the_post(); global $post; ?>
			<?php 

				$location = get_field('property_map');

				if( !empty($location) ): ?>

				<div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng']; ?>">
					<div class="marker-info">
						<div class="info">
							<div class="thumb">
								<a href="<?php echo get_permalink(); ?>" title="<?php the_title(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>
							</div>
							        <?php 
          $villa_pretty_name = get_field('villa_pretty_name'); 
          
          if ($villa_pretty_name) {
           
        	echo '<h3><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
            echo '<h4>' . $villa_pretty_name . '</h4>';
          
          } else {
        	echo '<h3><a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a></h3>';
          }

        ?>
							<?php $location = wp_get_post_terms( $post->ID, 'property_location'); ?>
							<h4><?php echo $location[0]->name;?></h4>
							<p>
							<?php 
								if ( ! has_excerpt() ) {
									$trimmed = content(25);
								    echo wp_strip_all_tags($trimmed);
								} else { 
								    $excerpt = excerpt(25);
								    echo $excerpt;
								} 
							?>
							</p>
							<br><a href="<?php the_permalink(); ?>" class="button">Find out more</a>
						</div><!-- /.info -->
					</div>
				</div>

				<?php endif; ?>

			<?php endwhile; ?>

			</div></div>

		<?php endif; ?>

		<?php wp_reset_query(); ?>

	</div>

</div>


<div class="site-content">

  <div class="row">
  	  	
  	  	<!-- <h1><?php the_title(); ?></h1> -->
		
		<?php


			if ($spanish_law == 'yes') {
					$args = array(
					    'post_type' => 'villas',
						'property_location' => 'ibiza-town',
					    'posts_per_page' => -1,
						'post__not_in'  => array('5610','2928'),
						'meta_key'		=> 'property_for_sale', //filter out for sale properties, true is for sale, 0 not for sale (rental).
						'meta_value'	=> 0
					);
			} else {
					$args = array(
					    'post_type' => 'villas',
						'property_location' => 'ibiza-town',
					    'posts_per_page' => -1,
						'meta_key'		=> 'property_for_sale', //filter out for sale properties, true is for sale, 0 not for sale (rental).
						'meta_value'	=> 0
					);
			}

			$property_query = new WP_Query( $args );

			if ( $property_query->have_posts() ) : ?>
		
			<ul class="property-grid">

			<?php while ( $property_query->have_posts() ): $property_query->the_post(); global $post; ?>
				
				<li>

				<?php 
					// get the inside of the grid loop to make life easier 
					get_template_part( 'templates/loop-grid-part' ); 
				?>

				</li>
	

			<?php endwhile; ?>

			</ul>

		<?php endif; ?>

		<?php wp_reset_query(); ?>

	</div><!--/.row -->

</div>
<div style="background-size: contain;background-position: bottom; background-image: url(https://ibizavillas2000.com/wp-ibiza/wp-content/themes/rudeibiza/images/IbizaVillas2000-Partners.jpg);background-repeat: no-repeat;" class="rude-sponsors-container">
        <div class="row collapse rude-sponsors">
            <div class=" small-12 large-12 medium-12  text-center columns">
                <span><strong>OUR PARTNERS</strong></span>
                <img src="<?php bloginfo( 'stylesheet_directory' ); ?>/images/sponsors/pimeef_2022.png" alt="PIMEEF" title="Ibiza Villas 2000" width="90">
                <img src="<?php bloginfo( 'stylesheet_directory' ); ?>/images/sponsors/ibizarocks_2019.gif" alt="Ibiza Rocks" title="Ibiza Villas 2000" width="68">
                <img src="<?php bloginfo( 'stylesheet_directory' ); ?>/images/sponsors/cafemambo_2019.png" alt="Cafe Mambo" title="Ibiza Villas 2000" width="99">
                <img src="<?php bloginfo( 'stylesheet_directory' ); ?>/images/sponsors/motoluis_2019.png" alt="Motoluis" title="Ibiza Villas 2000" width="99">
                <img src="<?php bloginfo( 'stylesheet_directory' ); ?>/images/sponsors/fox_2019.png" alt="Fox" title="Ibiza Villas 2000" width="99">
                <img src="<?php bloginfo( 'stylesheet_directory' ); ?>/images/sponsors/gardenfestival_2019.gif" alt="Garden Festival" title="Ibiza Villas 2000" width="50">
                <img src="<?php bloginfo( 'stylesheet_directory' ); ?>/images/sponsors/Rude-Chalets_2019.jpg" alt="Rude Chalets" title="Ibiza Villas 2000" width="115">
                <a target="_blank" href="http://www.crispcateringibiza.com/"><img src="<?php bloginfo( 'stylesheet_directory' ); ?>/images/sponsors/crisp-logo_2019.png" alt="Crisp Catering Ibiza" title="Ibiza Villas 2000" width="99"></a>
            </div>
        </div>
    </div>

<?php get_footer(); ?>