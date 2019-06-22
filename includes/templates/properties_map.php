<?php
	$home_default_map_zoom = esc_attr(get_option('ns_real_estate_default_map_zoom', 10));
	$home_default_map_latitude = esc_attr(get_option('ns_real_estate_default_map_latitude', 39.2904));
	$home_default_map_longitude = esc_attr(get_option('ns_real_estate_default_map_longitude', -76.5000));
    $google_maps_pin = esc_attr(get_option('ns_real_estate_google_maps_pin'));
    if(empty($google_maps_pin)) { $google_maps_pin = plugins_url( '/ns-real-estate/images/pin.png'); }
?>

<div class="module no-padding ns-properties-map">
    <div id="map-canvas"></div>
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
                var propertyPrice = '<?php if(!empty($price)) { ?><div><?php echo ns_real_estate_format_price($price); ?><?php if(!empty($price_postfix)) { ?> <span class="price-postfix"><?php echo esc_attr($price_postfix); ?></span><?php } ?></div><?php } ?>';
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