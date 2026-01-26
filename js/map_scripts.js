(function($) {

/*
*  render_map
*
*  This function will render a Google Map onto the selected jQuery element
*
*  @type  function
*  @date  8/11/2013
*  @since 4.3.0
*
*  @param $el (jQuery element)
*  @return  n/a
*/

function render_map( $el ) {

  // var
  var $markers = $el.find('.marker');



  // vars
  var args = {
    zoom    : 4,
    minZoom : 8,
    center    : new google.maps.LatLng(0, 0),
    mapTypeId : google.maps.MapTypeId.ROADMAP,
  };

  // create map
  map = new google.maps.Map( $el[0], args);

  // add a markers reference
  map.markers = [];

  // add markers
  $markers.each(function(){

      add_marker( $(this), map );

  });

  var mcOptions = {
                  maxZoom: 15,
                  styles: [{
                  textColor: 'white',
                  textSize: 16,
                  fontWeight: 'lighter',
                  anchorText: [5, -7],
                  height: 60,
                  url: "/wp-content/themes/rudeibiza/images/rude-marker-multi.png",
                  width: 59
                  },]
                }
;

  var mc = new MarkerClusterer(map, map.markers, mcOptions);
  // center map
  center_map( map );

}

/*
*  add_marker
*
*  This function will add a marker to the selected Google Map
*
*  @type  function
*  @date  8/11/2013
*  @since 4.3.0
*
*  @param $marker (jQuery element)
*  @param map (Google Map object)
*  @return  n/a
*/

function add_marker( $marker, map ) {

  // var
  var latlng = new google.maps.LatLng( $marker.attr('data-lat'), $marker.attr('data-lng') );


// Créer une fenêtre d'information (AJOUTEZ CECI ICI)
  var infowindow = new google.maps.InfoWindow({
    content   : $marker.html()
  });


  // create marker
   var iconBase = '/wp-content/themes/rudeibiza/images';
  var marker = new google.maps.Marker({
    position  : latlng,
    map       : map,
    icon: iconBase + '/rude-marker.png'
  });


 // Ajoutez cette partie pour gérer la visibilité en fonction du niveau de zoom
  google.maps.event.addListener(map, 'zoom_changed', function() {
    if (map.getZoom() >= 13) {
      marker.setVisible(false);
    } else {
      marker.setVisible(true);
    }
  });

// Afficher la fenêtre d'information lorsque le marqueur est cliqué (AJOUTEZ CECI ICI)
  google.maps.event.addListener(marker, 'click', function() {
    infowindow.open( map, marker );
  });


// create circle
  var circle = new google.maps.Circle({
    strokeColor: '#F2C14A',
    strokeOpacity: 0.8,
    strokeWeight: 2,
    fillColor: '#F2C14A',
    fillOpacity: 0.5,
    map: map,
    center: latlng,
    radius: 500  // Ceci est en mètres
  });

 // Ajoutez cette partie pour gérer la visibilité du cercle en fonction du niveau de zoom
  google.maps.event.addListener(map, 'zoom_changed', function() {
    if (map.getZoom() >= 13) {
      circle.setVisible(true);
    } else {
      circle.setVisible(false);
    }
  });
  
 // Afficher la fenêtre d'information lorsque le cercle est cliqué (AJOUTEZ CECI ICI)
  google.maps.event.addListener(circle, 'click', function() {
    infowindow.setPosition(circle.getCenter());
    infowindow.open(map);
  });

  // add to array
  map.markers.push( marker );

  // if marker contains HTML, add it to an infoWindow
  if( $marker.html() )
  {
    // create info window
    var infowindow = new google.maps.InfoWindow({
      content   : $marker.html()
    });

    // show info window when marker is clicked
    google.maps.event.addListener(marker, 'click', function() {

      infowindow.open( map, marker );

    });

 
  }

}

/*
*  center_map
*
*  This function will center the map, showing all markers attached to this map
*
*  @type  function
*  @date  8/11/2013
*  @since 4.3.0
*
*  @param map (Google Map object)
*  @return  n/a
*/

function center_map( map ) {

  // vars
  var bounds = new google.maps.LatLngBounds();

  // loop through all markers and create bounds
  $.each( map.markers, function( i, marker ){

    var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );

    bounds.extend( latlng );

  });

  // only 1 marker?
  if( map.markers.length == 1 )
  {
    // set center of map
      map.setCenter( bounds.getCenter() );
  }
  else
  {
    // fit to bounds
    map.fitBounds( bounds );
    map.setZoom( 10 );

  }

  // re-centre map when toggle button is clicked

  $(document).on("click",".show-results-map",function(e){
      google.maps.event.trigger(map, "resize");
      map.setCenter( bounds.getCenter() );
  
    if (window.matchMedia('(min-width: 768px)').matches) {
        map.setZoom( 10 );
    } else {
        map.setZoom( 9 );
    }
  });

  $(document).on("click",".toggle-property-map",function(e){
      google.maps.event.trigger(map, "resize");
      map.setCenter( bounds.getCenter() );
      map.setZoom( 14 );
  });

}



/*
*  document ready
*
*  This function will render each map when the document is ready (page has loaded)
*
*  @type  function
*  @date  8/11/2013
*  @since 5.0.0
*
*  @param n/a
*  @return  n/a
*/

$(document).ready(function(){
  $('.acf-map').each(function(){
    render_map( $(this) );
  });
});

  // $( "#property-map" ).not( ".force-display" ).hide();


})(jQuery);

// single property pages
// show map
$(document).on("click",".toggle-property-map",function(e){
    e.preventDefault();
    $(this).addClass('active');
    $(".toggle-property-images, .toggle-property-availability").removeClass('active');
    $( "#property-map" ).show();
    $( "#property-slider, #property-availability" ).hide();
    $("html, body").animate({ scrollTop: $('h1').offset().top }, 0);    
});
// show villa manager pricing
$(document).on("click",".toggle-property-availability",function(e){
    e.preventDefault();
    $(this).addClass('active');
    $(".toggle-property-images, .toggle-property-map").removeClass('active');
    $( "#property-availability" ).show();
    $( "#property-slider, #property-map" ).hide();
    $("html, body").animate({ scrollTop: $('h1').offset().top }, 0);    
});
// show images
$(document).on("click",".toggle-property-images",function(e){
    e.preventDefault();
    $(this).addClass('active');
    $(".toggle-property-map, .toggle-property-availability").removeClass('active');
    $( "#property-slider" ).show();
    $( "#property-map, #property-availability" ).hide();
    $("html, body").animate({ scrollTop: $('h1').offset().top }, 0);
    $('.page-slider, .property-slider.landscape').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      lazyLoad: 'ondemand',
      prevArrow: '<button type="button" class="slick-prev"></button>',
      nextArrow: '<button type="button" class="slick-next"></button>',
      arrows: true,
      dots: true,
      fade: true
    });
  });










 // for the property grid results 
$(document).on("click",".show-results-map",function(e){
    e.preventDefault();
    $(this).addClass('active');
    $(".hide-results-map").removeClass('active');
    $( ".map-container" ).show();
});


$(document).on("click",".hide-results-map",function(e){
    e.preventDefault();
    $(this).addClass('active');
    $(".show-results-map").removeClass('active');
    $( ".map-container" ).hide();
});


