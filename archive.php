<?php get_header(); ?>


<!-- might need a little rethink about this structure of the main column and sidebar... -->


<div class="site-content">

  <div class="row">
	<?php get_template_part( 'templates/loop', 'grid' ); ?>

  </div><!--/.row -->

</div>


<?php get_footer(); ?>