<?php
$home_default_map_zoom = esc_attr(get_option('ns_real_estate_default_map_zoom', 10));
$home_default_map_latitude = esc_attr(get_option('ns_real_estate_default_map_latitude', 39.2904));
if(empty($home_default_map_latitude)) { $home_default_map_latitude = 39.2904; }
$home_default_map_longitude = esc_attr(get_option('ns_real_estate_default_map_longitude', -76.5000));
if(empty($home_default_map_longitude)) { $home_default_map_longitude = -76.5000; }

$google_maps_pin = esc_attr(get_option('ns_real_estate_google_maps_pin'));
if(empty($google_maps_pin)) { $google_maps_pin = plugins_url( '/ns-real-estate/images/pin.png'); }
?>

<input type=button id="remove-pin" class="remove-pin right" value="<?php echo esc_html_e('Clear Location', 'ns-real-estate'); ?>">
<div class="clear"></div>
                                
<input size="25" id="pac-input" class="controls" type="text" placeholder="Search" value="">
<div id="map-canvas-one-pin"></div>

<script>         
var map;
var markers = [];
var marker = '';
                  
function initialize() {

        //var latInput = document.getElementById('property_latitude');
        //var lngInput = document.getElementById('property_longitude');
        var latInput = document.getElementsByClassName('admin-module-ns_property_latitude input');
        var lngInput = document.getElementsByClassName('admin-module-ns_property_longitude input');
                  
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
                    latInput.value = markerDefault.position.lat();
                    lngInput.value = markerDefault.position.lng();
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

            if (places.length == 0) {
                return;
            }
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
                latInput.value = marker.position.lat();
                lngInput.value = marker.position.lng();

                markers.push(marker);

                bounds.extend(place.geometry.location);

                //update marker position on drag
                google.maps.event.addListener(
                    marker,
                    'drag',
                    function() {
                        latInput.value = marker.position.lat();
                        lngInput.value = marker.position.lng();
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
        $('#property_latitude').val('');
        $('#property_longitude').val('');
        $('#pac-input').val('');
    });
});
</script>