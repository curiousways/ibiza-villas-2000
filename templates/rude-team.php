    <?php
/**
 * Template Name: Rude Team
**/

get_header(); ?>

<div class="site-content">

	<div class="row">
		<div class="medium-8 columns">
			
			<h1><?php the_title(); ?></h1>
			
			<?php the_content(); ?>

			
				<?php

					// check if the repeater field has rows of data
					if( have_rows('rude_team') ): $i = 0;

					 	// loop through the rows of data
					    while ( have_rows('rude_team') ) : the_row(); $i++;

							$attachment_id = get_sub_field('employee_picture');
							$size_thumb = "medium"; // (thumbnail, medium, large, full or custom size)
							$image_thumb = wp_get_attachment_image_src( $attachment_id, $size_thumb );

							$text = get_sub_field('employee_blurb');

							echo '<div class="rude-team-member ';
							if (($i % 2)==0) { echo 'even'; } else {  echo 'odd'; }
							echo '"><div class="row">';
							echo '<div class="medium-4 columns"><img src="' . $image_thumb[0] . '" alt="Ibiza Villas 2000 Team Member"></div>';
					        echo '<div class="medium-8 columns">' . $text . '</div>';
					        echo '</div></div>';

					    endwhile;

					else :

					    // no rows found

					endif;

				?>
			

			


		</div>
		<div class="medium-4 columns">
			<?php get_sidebar('primary-sidebar'); ?>
		</div>
	</div>

</div>


<?php get_footer(); ?>




