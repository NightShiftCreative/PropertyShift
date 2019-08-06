<?php
	//global settings
    $icon_set = esc_attr(get_option('ns_core_icon_set', 'fa'));
    if(function_exists('ns_core_load_theme_options')) { $icon_set = ns_core_load_theme_options('ns_core_icon_set'); }
	$properties_page = esc_attr(get_option('ns_properties_page'));
    $property_listing_display_time = esc_attr(get_option('ns_property_listing_display_time', 'true'));
    $property_listing_display_favorite = esc_attr(get_option('ns_property_listing_display_favorite', 'true'));
    $property_listing_display_share = esc_attr(get_option('ns_property_listing_display_share', 'true'));
    $property_listing_crop = esc_attr(get_option('ns_property_listing_crop', 'true'));	

    //property settings
    $property_obj = new NS_Real_Estate_Properties();
	$property_settings = $property_obj->load_property_settings($post->ID);
	$featured = $property_settings['featured']['value'];
	$address = $property_settings['street_address']['value'];
	$price = $property_settings['price']['value'];
	$price_postfix = $property_settings['price_postfix']['value'];
	$area = $property_settings['area']['value'];
	$area = $property_obj->get_formatted_area($area);
	$area_postfix = $property_settings['area_postfix']['value'];
	$bedrooms = $property_settings['beds']['value'];
	$bathrooms = $property_settings['baths']['value'];
	$property_status = $property_obj->get_tax($post->ID, 'property_status');
?>

<div <?php post_class(); ?>>

	<?php do_action('ns_real_estate_before_property_card', $property_settings); ?>

	<div class="property-img">

		<?php if($featured == 'true') { ?>
			<a href="<?php if(!empty($properties_page)) { echo esc_url($properties_page).'/?featured=true'; } ?>" class="property-tag button alt featured">
				<?php esc_html_e('Featured', 'ns-real-estate'); ?>	
			</a>
		<?php } ?>

		<?php if ( has_post_thumbnail() ) {  ?>
			<a href="<?php the_permalink(); ?>" class="property-img-link">
				<div class="img-fade"></div>
                <?php if($property_listing_crop == 'true') { the_post_thumbnail('property-thumbnail'); } else { the_post_thumbnail('full'); } ?>
            </a>
		<?php } else { ?>
			<a href="<?php the_permalink(); ?>" class="property-img-link"><img src="<?php echo plugins_url( '/ns-real-estate/images/property-img-default.gif' ); ?>" alt="" /></a>
		<?php } ?>
	</div>
	
	<div class="property-content">

		<?php if(!empty($property_status)) { ?>
			<div class="property-tag button status"><?php echo wp_kses_post($property_status); ?></div>
		<?php } ?>

		<?php if($property_listing_display_time == 'true' || $property_listing_display_favorite == 'true' || $property_listing_display_share == 'true') { ?>
			<div class="property-actions">
				<?php if($property_listing_display_time == 'true') {
					$toggle = ns_core_get_icon($icon_set, 'clock', 'clock3', 'clock');
					$content = human_time_diff( get_the_time('U'), current_time('timestamp') ) . esc_html__(' ago', 'ns-real-estate'); 
					echo ns_basics_tooltip($toggle, $content); 
				} ?>
		        <?php if($property_listing_display_favorite == 'true' && function_exists('ns_basics_get_post_likes_button')) { echo ns_basics_get_post_likes_button(get_the_ID()); } ?>
		        <?php if($property_listing_display_share == 'true' && function_exists('ns_basics_get_social_share')) { echo ns_basics_get_social_share(); } ?>
			</div>
		<?php } ?>

		<?php if(!empty($price)) { ?>
			<div class="property-price">
				<?php echo $property_obj->get_formatted_price($price); ?>
				<?php if(!empty($price_postfix)) { ?><span class="price-postfix"><?php echo esc_attr($price_postfix); ?></span><?php } ?>
			</div>
		<?php } ?>

		<div class="property-title">
            <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
            <?php if(!empty($address)) { echo '<p class="property-address">'.$address.'</p>'; } ?>
        </div>

        <table class="property-details">
            <tr>
				<?php if(!empty($bedrooms)) { ?><td><?php echo ns_core_get_icon($icon_set, 'bed', 'bed', 'n/a'); ?> <?php echo esc_attr($bedrooms).' '; esc_html_e('Beds', 'ns-real-estate'); ?></td><?php } ?>
				<?php if(!empty($bathrooms)) { ?><td><?php echo ns_core_get_icon($icon_set, 'tint', 'bathtub', 'n/a'); ?> <?php echo esc_attr($bathrooms).' '; esc_html_e('Baths', 'ns-real-estate'); ?></td><?php } ?>
            	<?php if(!empty($area)) { ?><td><?php echo ns_core_get_icon($icon_set, 'expand'); ?> <?php echo esc_attr($area); ?> <?php if(!empty($area_postfix)) { echo '<span class="area-postfix">'.esc_attr($area_postfix).'</span>'; } ?></td><?php } ?>
            </tr>
        </table>
	</div>

	<?php do_action('ns_real_estate_after_property_card', $property_settings); ?>

</div>