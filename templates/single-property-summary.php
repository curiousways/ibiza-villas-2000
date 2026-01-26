<!-- Single Property Summary -->

      <?php 
      $property_summary = "";
      $property_summary = get_field('property_summary');
      if( !empty($property_summary ) ) : ?>
        <div class="property-summary">
        <h3><?php _e('Property Location'); ?></h3>
        <?php if(get_field('property_summary')) { the_field('property_summary'); } ?>
        </div>
      <?php endif; ?>