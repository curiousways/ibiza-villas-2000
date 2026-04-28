<style type="text/css">
@media only screen and (min-width:1080px) {

    .taptap-menu-button-wrapper {
        display: none!important;
    }

    .taptap-logo-image img {
        display: none!important;
    }

    .taptap-search-button {
        display: none!important;
    }

    .taptap-search-button-wrapper, .taptap-woo-button-wrapper {
        display: none!important;
    }
}

.preheader p.right {
    position: relative;
    z-index: 99999!important;
}

.balanceh {
    position: relative;
}

.balanceh::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 0.5s ease;
    background-color: rgba(0, 0, 0, 0.5);
}

.balanceh:hover::before {
    opacity: 0.5;
}

@media screen and (max-width: 1520px) {
    p#double {
        display: none;
    }
}

</style>
<div class="fixed">

	<div class="preheader">
     	
     	<div style="margin-top:6px; z-index:99998; background-color:#37A09B;" class="small-12 medium-12 large-12 columns">

				
				<div class="small-12 medium-4 large-2 columns" style="height: 140px;">
					
					<a class="site-logo-como" href="<?php echo site_url(); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a>
		
				</div>

<div class="small-12 medium-8 large-10 columns" style="height: 90px;">
		
		
					<p class="right" style="color:#fff!important;">
					
						<a style="color: #fff!important;" href="tel:00442037001364"><strong>UK:</strong> <span>0044 203 700 1364</span></a>
					
						<br />
					
						<a style="color: #fff!important;" href="tel:0034666934060"><strong>Ibiza:</strong> <span>0034 666 934 060</span></a>
					
					</p>
					
					<p class="right" style="padding-right:30px!important;">
						
						<span class="avat">MEMBERS OF THE</span>
						<span class="avat"> VILLA ASSOCIATION</span>
					</p>
					
					<a style="max-width: 150px; float: right" href="/villa-scams/ibiza-villa-rental-company-safe-legal/"><img class="avatheader" src="https://ibizavillas2000.com/wp-content/uploads/2022/01/Ibiza-Villas-2000-AVAT-Certification.png" alt="AVAT" title="AVAT"></a>
				
				</div>


				
				<!--<div class="small-12 medium-6 large-6 columns" style="padding-right: 60px; height: 140px;">
		
		
					<p class="right" style="color:#fff!important;">
					
						<a style="color: #fff!important;" href="tel:00442037001364"><strong>UK:</strong> <span>0044 203 700 1364</span></a>
					
						<br />
					
						<a style="color: #fff!important;" href="tel:0034666934060"><strong>Ibiza:</strong> <span>0034 666 934 060</span></a>
					
					</p>
					
					<p id="double" class="right" style="padding-right:30px!important;">
						
						<span class="avat double">MEMBERS OF THE</span>
						<span class="avat double"> VILLA ASSOCIATION</span>
					</p>
					
					<a style="max-width: 150px; float: right" href="/villa-scams/ibiza-villa-rental-company-safe-legal/"><img class="avatheader" src="https://ibizavillas2000.com/wp-content/uploads/2022/01/Ibiza-Villas-2000-AVAT-Certification.png" alt="AVAT" title="AVAT"></a>
				
				</div>

				<div href="https://ibizavillas2000.com/balance-my-holiday/" id="balanceh" class="small-12 medium-4 large-4 columns balanceh" style="background-image: url(https://ibizavillas2000.com/wp-content/uploads/2022/05/ibizavillas2000-balanceyourholida-.jpg);cursor: pointer;background-size: cover;
    margin-right: -20px;border-left:6px solid #7BD299 ; display: ;background: ; height: 140px;"><a style="color:#fff;" href="https://ibizavillas2000.com/balance-my-holiday/">
		
		
				
						<p class="right" style="    padding-top: 40px;padding-right:40px!important;">
						
						<span style="    text-shadow: 1px 4px 4px rgba(0,0,0,0.48);line-height: 20px;font-size:16px;" class="avat">MAKE YOUR HOLIDAY LAST</span>
						<span style="text-shadow: 1px 4px 4px rgba(0,0,0,0.48);font-size:16px;" class="avat">FOR (AT LEAST) 99 YEARS</span>
					<span style="display:;text-shadow: 1px 4px 4px rgba(0,0,0,0.48);font-size:12px;" class="avat">CLICK HERE TO FIND OUT HOW</span>
					</p>
					
					
		
				</a>
				</div>-->



     	</div>


	</div>

	
	
	<div style="" class="top-bar-nomads small-12 medium-12 large-12">
	
		<nav class="top-bar" data-topbar role="navigation" data-options="mobile_show_parent_link: false;">
	
				
				<a class="site-logo-como3" href="<?php echo site_url(); ?>" title="<?php bloginfo('name'); ?>"></a>
	   

			   	<ul style="background: #37A09B;
    height: 4rem;" class="title-area">
			    	<li style="display:none;" class="name"></li>
			        <li style="display:none;" class="toggle-topbar menu-icon"><a href="#"><span>Menu;</span></a></li>
			        <li style="display:none;" class="searchToggle search-toggle"><a href="#"><i class="icon-search"></i></a></li>
			    </ul><!-- /.title-area -->

	    

	    		<section class="top-bar-section">
			    	
			       		 <!-- Navigation -->
						<?php $args = array(
						'container' => false,                           // remove nav container
				        'container_class' => '',           		// class of container
				        'menu' => '',                      	        // menu name
				        'menu_class' => 'right',         	// adding custom nav class
				        'theme_location' => 'primary',                // where it's located in the theme
				        'before' => '',                                 // before each link <a>
				        'after' => '',                                  // after each link </a>
				        'link_before' => '',                            // before each link text
				        'link_after' => '',                             // after each link text
				        'depth' => 3,
				    	'fallback_cb' => false,
						);
					?>
					<?php wp_nav_menu( $args ); ?>
					
					
			
	    	</section>
		
		</nav><!-- /.top-bar -->

</div>

</div>
