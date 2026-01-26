<!-- dont show this area if there is no property id -->
<?php if(get_field('property_id')) { ?>


<script>

$( document ).ready(function() {
   
    $( "#datepicker-checkin, #datepicker-checkout" ).datepicker({
      dateFormat: "mm-dd-yy"
    });

});
</script>


  <div class="search-properties availability-apartments">

    <div class="small-12 columns">
    <h3>Check Availability &amp; Book Online</h3>
    <p>Search for this apartments availability and book securely online.</p>
    </div>

    <div class="facet-row">

      <form id="property-search-form" name="property-search-form" method="get" action="https://rudeapartments.imbookingsecure.com/makebooking/?redir=1&">

      <!-- dont display the keyid - grab this from the apartment property with php -->
        <input type="hidden" name="keyid" value="<?php the_field('property_id') ?>">

        <div class="small-6 columns">

          <div data-type="dropdown" class="facetwp-facet facetwp-type-dropdown">

            <select name="adults" placeholder="Adults" class="property-search-form-type facetwp-dropdown" required>
              <option value="" disabled selected>Adults</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8</option>
              <option value="9">9</option>
              <option value="10">10</option>
              <option value="11">11</option>
              <option value="12">12</option>
              <option value="13">13</option>
              <option value="14">14</option>
              <option value="15">15</option>
              <option value="16">16</option>
            </select>

            <i class="facetwp-dropdown-arrow icon-arrow-down"></i>
          </div>

        </div>

        <div class="small-6 columns">

          <div data-type="dropdown" class="facetwp-facet facetwp-type-dropdown">

            <select name="children" class="property-search-form-type facetwp-dropdown" required>
              <option value="" disabled selected>Children</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8</option>
              <option value="9">9</option>
              <option value="10">10</option>
              <option value="11">11</option>
              <option value="12">12</option>
              <option value="13">13</option>
              <option value="14">14</option>
              <option value="15">15</option>
              <option value="16">16</option>
            </select>

            <i class="facetwp-dropdown-arrow icon-arrow-down"></i>
          </div>

        </div>

        <div class="small-6 columns">

          <div class="facetwp-facet facetwp-type-dropdown">

            <input name="checkin" placeholder="Checkin" type="text" id="datepicker-checkin" required>
          
          </div>

        </div>

        <div class="small-6 columns">

          <div class="facetwp-facet facetwp-type-dropdown">

            <input name="checkout" placeholder="Checkout" type="text" id="datepicker-checkout" required>
          
          </div>

        </div>

        <div class="small-12 columns">
          <button class="search-fwp" type="submit">Check / Book Online</button>
        </div>

      </form>

    </div>

  </div>


<?php } ?>
<!-- end if -->
