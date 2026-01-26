<a id="content"></a>

<?php if ( have_posts() ) : ?>
<div class="row">

	<div class="small-12 text-center columns">
		<h4>We've found <?php echo facetwp_display( 'counts' ); ?> properties that match your search</h4>

	</div>

</div><!-- /.row -->
<div class="row">
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
</div>

<?php else: ?>
<div class="row">
<div class="small-12 text-center columns">
	<div class="alert-box error"><h2><?php _e("Sorry, your search criteria didn't return any results"); ?></h2></div>
	<h4>Take a look below if any of our other properties suit your needs</h4>
</div>
		<?php get_template_part( 'templates/featured-properties' ); ?>
</div>
<?php endif; ?>


<?php wp_reset_query(); ?>