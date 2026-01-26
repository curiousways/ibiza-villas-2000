<?php
/*
 * The grid loop
 */
?>

<a id="content"></a>

<?php if ( have_posts() ) : ?>

<ul class="property-grid">

	<?php while ( have_posts() ) : the_post(); ?>
	
		<li>

			<?php 
      			// get the inside of the grid loop to make life easier 
      			get_template_part( 'templates/loop-grid-part' ); 
      		?>

		</li>
	
	<?php endwhile; ?>

</ul>

<?php else: ?>

	<div class="alert-box error"><?php _e('Sorry, the information you requested was not found'); ?></div>

<?php endif; ?>

<div class="row">

	<div class="small-12 columns">

		<?php if (function_exists("emm_paginate")) {
		    emm_paginate();
		} ?>

	</div>

</div><!-- /.row -->