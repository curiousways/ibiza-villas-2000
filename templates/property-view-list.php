<?php 
  $villa_post_id = get_the_ID();
  echo '<input id="villa_post_id" type="hidden" value="' . $villa_post_id . '">'; 
 ?>



<script>

$( document ).ready(function() {



  // Make an empty array to set the array
  property_list = [];

  // get the current properties ID
  villa_post_id = $("#villa_post_id").val();
  
  // set found property as false so we can check it later
  found_property = false;

  // if cookie property exists then create property_list from a json value to an array object
  if ($.cookie('property')) {
    property_list = JSON.parse($.cookie('property'));
  };

  // for each array item within property list i is the position in array and val is the value
  $.each( property_list, function( i, val ) {

  // if the value of the array matches the property ID set the found_property var to true and stop the "each" loop with return
    if (val == villa_post_id) {
      found_property = true;
      return;
    }

  });

  // if found property is true then disable the button and update it's text
  if (found_property == true) {
    $("#propertyListToggle").addClass("disabled");
    $("#propertyListToggle").text("Remove from wishlist");
  } 


  // on add property click - prevent default
  $( "#propertyListToggle" ).on( "click", function(e) {

    // if it already has the class disabled 
    if ($(this).hasClass("disabled")) {
     
      // remove last property id from the array
      // $.removeCookie('property', { path: '/' });


      // for each array item within property list i is the position in array and val is the value
      $.each( property_list, function( i, val ) {

      // if the property is within the array value[i] it'll match the postid. i is used because it matches for eg 2118 to property_post_id 2118 without quotes
        if (property_list[i] == villa_post_id) {
          property_list[i] = "";
          return;
        }

      });
      
      // update the cookie
      $.cookie('property', JSON.stringify(property_list), { expires: 30, path: '/' });


      // remove disabled class
      $(this).removeClass("disabled");
      // return text to add to list
      $(this).text("Add to wishlist");

    } else {
      // if found property is false then add the current property ID to the property_list array
      if (found_property == false) {
        property_list.push(villa_post_id);
        // add the property_list back to the cookie and turn it back into a json object... cookie is set to 30days and is global
        $.cookie('property', JSON.stringify(property_list), { expires: 30, path: '/' });
      };
      // disable the button and update its text
      $(this).addClass("disabled");
      $(this).text("Remove from wishlist");
    };

    e.preventDefault();
  });









});
</script>