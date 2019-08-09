<?php
// Exit if accessed directly
if (!defined( 'ABSPATH')) { exit; }

/**
 *	NS_Real_Estate_Maps class
 *
 */
class NS_Real_Estate_Maps {

	/************************************************************************/
	// Initialize
	/************************************************************************/

	/**
	 *	Constructor
	 */
	public function __construct() {

		// Load admin object & settings
		$this->admin_obj = new NS_Real_Estate_Admin();
        $this->global_settings = $this->admin_obj->load_settings();
	}

	/************************************************************************/
	// Map Methods
	/************************************************************************/

	/**
	 *	Single property map
	 *
	 * @param int $latitude
	 * @param int $longitude
	 * @param boolean $map_only
	 */
	public function build_single_property_map($latitude, $longitude, $map_only = false) {
 
 		// Get global settings
		$home_default_map_zoom = $this->global_settings['ns_real_estate_default_map_zoom'];
		$home_default_map_latitude = $this->global_settings['ns_real_estate_default_map_latitude'];
		$home_default_map_longitude = $this->global_settings['ns_real_estate_default_map_longitude'];	 
		$google_maps_pin = $this->global_settings['ns_real_estate_google_maps_pin'];

		//Output map
		if($map_only == false) { ?>
			<div class="admin-module-note admin-map-note left"><?php esc_html_e('Enter an address in the search field below to add a marker to the map', 'ns-real-estate'); ?></div>
			<input type=button id="remove-pin" class="admin-button remove-pin right" value="<?php esc_html_e('Clear Location', 'ns-real-estate'); ?>">
			<div class="clear"></div>
			<input id="pac-input" class="controls" type="text" placeholder="Search" value="">
		<?php } ?>

		<div id="map-canvas-one-pin"></div>
		<script>
			var map;
			var markers = [];
			var marker = '';
			                  
			function initialize() {

			jQuery(document).ready(function($){

			    var latInput = $('.admin-module-ns_property_latitude input');
			    var lngInput = $('.admin-module-ns_property_longitude input');
          
			    map = new google.maps.Map(document.getElementById('map-canvas-one-pin'), {
			        mapTypeId: google.maps.MapTypeId.ROADMAP,
			        zoom: <?php echo $home_default_map_zoom; ?>
			    });
			   
			    var defaultBounds = new google.maps.LatLngBounds(
			        new google.maps.LatLng(<?php if(!empty($latitude)) { echo esc_attr($latitude); } else { echo $home_default_map_latitude; } ?>, <?php if(!empty($longitude)) { echo esc_attr($longitude); } else { echo $home_default_map_longitude; } ?>),
			        new google.maps.LatLng(<?php if(!empty($latitude)) { echo esc_attr($latitude); } else { echo $home_default_map_latitude; } ?>, <?php if(!empty($longitude)) { echo esc_attr($longitude); } else { echo $home_default_map_longitude; } ?>));
			    map.setCenter(defaultBounds.getCenter());

			    // Create the search box and link it to the UI element.
			    var input = (document.getElementById('pac-input'));
			    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

			    var searchBox = new google.maps.places.SearchBox((input));

			    //If lat/long is set, place marker
			    <?php if(!empty($latitude) && !empty($longitude)) { ?>

			        var markerDefault = new google.maps.Marker({
			            map: map,
			            icon: '<?php echo $google_maps_pin; ?>',
			            position: new google.maps.LatLng(<?php echo esc_attr($latitude); ?>, <?php echo esc_attr($longitude); ?>),
			            draggable:true
			        });

			        //update marker position on drag
			        google.maps.event.addListener(
			            markerDefault,
			            'drag',
			            function() {
			                latInput.val(markerDefault.position.lat());
			                lngInput.val(markerDefault.position.lng());
			            }
			        );

			        //remove marker
			        jQuery(document).ready(function($){
			            $('.remove-pin').click(function() {
			            $('#property_latitude').val('');
			            $('#property_longitude').val('');
			            $('#pac-input').val('');
			            markerDefault.setMap(null);
			            });
			        });

			    <?php } else { ?>
			        var markerDefault = null;
			    <?php } ?>

			    // Listen for the event fired when the user selects an item from the
			    // pick list. Retrieve the matching places for that item.
			    google.maps.event.addListener(searchBox, 'places_changed', function() {

			        markerDefault = null;

			        var places = searchBox.getPlaces();

			        if (places.length == 0) { return; }
			        for (var i = 0, marker; marker = markers[i]; i++) {
			            marker.setMap(null);
			        }

			        // For each place, get the icon, place name, and location.
			        markers = [];
			        var bounds = new google.maps.LatLngBounds();
			        for (var i = 0, place; place = places[i]; i++) {

			            var image = {
			                url: place.icon,
			                size: new google.maps.Size(71, 71),
			                origin: new google.maps.Point(0, 0),
			                anchor: new google.maps.Point(17, 34),
			                scaledSize: new google.maps.Size(25, 25)
			            };

			            // Create a marker for each place.
			            marker = new google.maps.Marker({
			                map: map,
			                icon: '<?php echo $google_maps_pin; ?>',
			                title: place.name,
			                position: place.geometry.location,
			                draggable:true
			            });

			            //update lat and lng input fields
			            latInput.val(marker.position.lat());
			            lngInput.val(marker.position.lng());

			            markers.push(marker);

			            bounds.extend(place.geometry.location);

			            //update marker position on drag
			            google.maps.event.addListener(
			                marker,
			                'drag',
			                function() {
			                    latInput.val(marker.position.lat());
			                    lngInput.val(marker.position.lng());
			                }
			            );
			        }

			        //map.fitBounds(bounds);
			        map.setCenter(bounds.getCenter());
			        map.setZoom(12);
			    });

			    // Bias the SearchBox results towards places that are within the bounds of the current map's viewport.
			    google.maps.event.addListener(map, 'bounds_changed', function() {
			        var bounds = map.getBounds();
			        searchBox.setBounds(bounds);
			    });
			  
			});                
			}

			google.maps.event.addDomListener(window, 'load', initialize);

			//refresh map when tab is clicked 
			function refreshMap() {
			    setTimeout(function(){
			        var center = map.getCenter();                  
			        google.maps.event.trigger(map, 'resize'); 
			        map.setCenter(center); 
			    }, 50);
			}  

			//remove marker
			jQuery(document).ready(function($){
			    $('.remove-pin').click(function() {
			        $('.admin-module-ns_property_latitude input').val('');
			        $('.admin-module-ns_property_longitude input').val('');
			        $('#pac-input').val('');
			    });
			});
		</script>

	<?php }

	/**
	 *	Multi properties map
	 */
	public function build_properties_map() {

		// Get global settings
		$home_default_map_zoom = $this->global_settings['ns_real_estate_default_map_zoom'];
		$home_default_map_latitude = $this->global_settings['ns_real_estate_default_map_latitude'];
		$home_default_map_longitude = $this->global_settings['ns_real_estate_default_map_longitude'];	 
		$google_maps_pin = $this->global_settings['ns_real_estate_google_maps_pin'];

		// Load properties object
		$properties_obj = new NS_Real_Estate_Properties();

		//Individual page banner settings
    	$page_id = ns_core_get_page_id();
    	$values = get_post_custom( $page_id ); ?>

    	<div class="module no-padding ns-properties-map">
    		<div id="map-canvas"></div>
    		<?php do_action('ns_real_estate_after_properties_map', $values); ?>
    		<script>
		        "use strict";

		        //intialize the map
		        function initialize() {

		            var mapOptions = {
		                zoom: <?php if(!empty($home_default_map_zoom)) { echo esc_attr($home_default_map_zoom); } else { echo '10'; } ?>,
		                scrollwheel: false,
		                center: new google.maps.LatLng(<?php if(!empty($home_default_map_latitude)) { echo esc_attr($home_default_map_latitude); } else { echo '39.29000'; } ?>, <?php if(!empty($home_default_map_longitude)) { echo esc_attr($home_default_map_longitude); } else { echo '-76.5000'; } ?>)
		            };

		            var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);


		        // MARKERS
		        /****************************************************************/
		        <?php
		        $property_listing_args = array('post_type' => 'ns-property', 'posts_per_page' => -1);
		        $property_listing_query = new WP_Query( $property_listing_args ); 
		        $map_count = 1;
		        ?>

		        <?php if ( $property_listing_query->have_posts() ) : while ( $property_listing_query->have_posts() ) : $property_listing_query->the_post(); ?>

		            <?php
		                $marker = 'marker'.$map_count;
		                $contentString = 'contentString'.$map_count;
		                $infowindow = 'infowindow'.$map_count;

		                //Get post meta data
		                $values = get_post_custom( $post->ID );
		                $latitude = isset( $values['ns_property_latitude'] ) ? esc_attr( $values['ns_property_latitude'][0] ) : '';
		                $longitude = isset( $values['ns_property_longitude'] ) ? esc_attr( $values['ns_property_longitude'][0] ) : '';
		                $price = isset( $values['ns_property_price'] ) ? esc_attr( $values['ns_property_price'][0] ) : '';
		                $price_postfix = isset( $values['ns_property_price_postfix'] ) ? esc_attr( $values['ns_property_price_postfix'][0] ) : '';
		            ?>

		            <?php if(!empty($latitude) && !empty($longitude)) { ?>
		                <?php $map_count++; ?>

		                //show marker
		                var <?php echo esc_attr($marker); ?> = new google.maps.Marker({
		                    position: new google.maps.LatLng(<?php echo esc_attr($latitude); ?>, <?php echo esc_attr($longitude); ?>),
		                    icon: '<?php echo $google_maps_pin; ?>',
		                    map: map
		                });

		                //show info box
		                var propertyPrice = '<?php if(!empty($price)) { ?><div><?php echo $properties_obj->get_formatted_price($price); ?><?php if(!empty($price_postfix)) { ?> <span class="price-postfix"><?php echo esc_attr($price_postfix); ?></span><?php } ?></div><?php } ?>';
		                var <?php echo esc_attr($contentString); ?> = '<div class="info-box"><a href="<?php the_permalink(); ?>"><?php if ( has_post_thumbnail() ) { the_post_thumbnail("thumb"); } ?><p><strong><?php the_title(); ?></strong></p></a>'+propertyPrice+'</div>';

		                var <?php echo esc_attr($infowindow); ?> = new google.maps.InfoWindow({ content: <?php echo wp_kses_post($contentString); ?> });

		                google.maps.event.addListener(<?php echo esc_attr($marker); ?>, 'click', function() {
		                    <?php echo esc_attr($infowindow); ?>.open(map,<?php echo esc_attr($marker); ?>);
		                  });

		            <?php } ?>

		        <?php endwhile; ?>
		            <?php wp_reset_postdata(); ?>
		        <?php else: ?>
		        <?php endif; ?>

		        }

		        google.maps.event.addDomListener(window, 'load', initialize);
		    </script>
    	</div>
	<?php }

}