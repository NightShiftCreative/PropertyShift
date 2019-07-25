<?php

//Get global settings
$properties_page = esc_attr(get_option('ns_properties_page'));

//Get template args
$property_filter_id = $template_args['id'];
$shortcode_filter = $template_args['shortcode_filter'];
$widget_filter = $template_args['widget_filter'];

//Get filter details
$values = get_post_custom( $property_filter_id );
$filter_position = isset( $values['ns_property_filter_position'] ) ? esc_attr( $values['ns_property_filter_position'][0] ) : 'middle';
$filter_layout = isset( $values['ns_property_filter_layout'] ) ? esc_attr( $values['ns_property_filter_layout'][0] ) : 'minimal';
$display_filter_tabs = isset( $values['ns_property_filter_display_tabs'] ) ? esc_attr( $values['ns_property_filter_display_tabs'][0] ) : 'false';
if(isset($values['ns_property_filter_items'])) {
	$filter_fields = $values['ns_property_filter_items'];
	$filter_fields = unserialize($filter_fields[0]);
} else {
	$filter_fields = ns_real_estate_load_default_property_filter_items();
}
$price_range_min = isset( $values['ns_property_filter_price_min'] ) ? esc_attr( $values['ns_property_filter_price_min'][0] ) : 0;
$price_range_max = isset( $values['ns_property_filter_price_max'] ) ? esc_attr( $values['ns_property_filter_price_max'][0] ) : 1000000;
$price_range_min_start = isset( $values['ns_property_filter_price_min_start'] ) ? esc_attr( $values['ns_property_filter_price_min_start'][0] ) : 200000;
$price_range_max_start = isset( $values['ns_property_filter_price_max_start'] ) ? esc_attr( $values['ns_property_filter_price_max_start'][0] ) : 600000;
$submit_text = isset( $values['ns_property_filter_submit_text'] ) ? esc_attr( $values['ns_property_filter_submit_text'][0] ) : esc_html__('Find Properties', 'ns-real-estate');
$custom_fields = get_option('ns_property_custom_fields');

//Get all current filters from URL
$currentFilters = array();
foreach($_GET as $key=>$value) { 
    if(!empty($value)) { $currentFilters[$key] = $value; }
}

//Get property status terms
$property_statuses = get_terms('property_status'); 

//If filter came from widget or shortcode, remove position
if(isset($widget_filter)) { $filter_layout = ''; }
if(isset($widget_filter) || isset($shortcode_filter)) { $filter_position = ''; }

//Calculate filter module class
$filter_num = 1;
foreach($filter_fields as $field) { if($field['active'] == 'true') { $filter_num++; }}

$filter_module_class = 'filter-'.$property_filter_id.' filter-count-'.$filter_num.' ';
if($filter_layout == 'boxed') { $filter_module_class .= 'filter-boxed ';  }
if($filter_layout == 'vertical') { $filter_module_class .= 'filter-boxed filter-vertical ';  }
if($filter_position == 'above') { 
	$filter_module_class .= 'filter-above-banner '; 
} else if($filter_position == 'middle') { 
	$filter_module_class .= 'filter-inside-banner '; 
} else if($filter_position == 'below')  {
	$filter_module_class .= 'filter-below-banner ';  
}

//Calculate filter item class
$filter_class = '';
if($filter_layout == 'vertical') { $filter_num = 1; }

if($filter_num == 1) {
    $filter_class = 'filter-item-1';
} else if($filter_num == 2) {
    $filter_class = 'filter-item-2';
} else if($filter_num == 3) {
    $filter_class = 'filter-item-3';
} else if($filter_num == 4) {
    $filter_class = 'filter-item-4';
} else if($filter_num == 5) {
    $filter_class = 'filter-item-5';
} else if($filter_num == 6) {
    $filter_class = 'filter-item-6';
} else if($filter_num == 7) {
    $filter_class = 'filter-item-7';
} else if($filter_num >= 8) {
    $filter_class = 'filter-item-8';
}

//Calculate container
$container = 'false';
if($filter_position == 'above' || $filter_position == 'below') { $container = 'true'; }

//Output Filter
if (!empty($filter_fields)) { ?>

	<div class="filter <?php echo $filter_module_class; ?>">
	<div <?php if($container == 'true') { echo 'class="container"'; } ?>>

		<div class="tabs" id="tabs-property-filter">
			<div class="filter-header <?php if($display_filter_tabs != 'true') { echo 'show-none'; } ?>">
	            <?php
	            if ( !empty( $property_statuses ) && !is_wp_error( $property_statuses ) ){
	                echo "<ul>"; ?>
	                <li><a href="#tabs-1"><?php esc_html_e( 'All', 'ns-real-estate' ); ?></a></li>
	                <?php $count = 0; ?>
	                <?php foreach ( $property_statuses as $property_status ) { ?>
	                    <?php $count++; ?>
	                    <li><a href="#tabs-<?php echo esc_attr($count) + 1; ?>"><?php echo esc_attr($property_status->name); ?></a></li>
	                <?php } 
	                echo "</ul>";
	            } else {
	                echo '<ul><li><a href="#tabs-1">'. esc_html__('All', 'ns-real-estate') .'</a></li></ul>';
	            } ?>
	        </div><!-- end filter header -->

			<div id="tabs-1" class="ui-tabs-hide">
				<form method="get" action="<?php echo esc_url($properties_page); ?>">
					<?php 
					$label_count = 0;
					foreach($filter_fields as $value) { 
						if(isset($value['name'])) { $name = $value['name']; }
                        if(isset($value['label'])) { $label = $value['label']; }
                        if(isset($value['placeholder'])) { $placeholder = $value['placeholder']; }
                        if(isset($value['placeholder_second'])) { $placeholder_second = $value['placeholder_second']; } else { $placeholder_second = null; }
                        if(isset($value['slug'])) { $slug = $value['slug']; }
                        if(isset($value['active']) && $value['active'] == 'true') { $active = 'true'; } else { $active = 'false'; }
                        if(isset($value['custom']) && $value['custom'] == 'true') { $custom = 'true'; } else { $custom = 'false'; } 

                        if($active == 'true') { ?>
                        	<div class="form-block filter-item <?php echo esc_attr($filter_class); ?>">

                        		<?php if(!empty($label)) {
                        			$label_count++;
		                            echo '<label>';
		                            if($custom == 'true') { echo esc_attr($name); } else { echo esc_attr($label); }
		                            echo '</label>';
		                        } ?>

		                        <?php if($slug == 'property_type') { ?>
		                            <select name="propertyType" class="form-dropdown">
		                                <option value=""><?php echo $placeholder; ?></option>
		                                <?php
		                                    $property_types = get_terms('property_type'); 
		                                    if ( !empty( $property_types ) && !is_wp_error( $property_types ) ) { ?>
		                                        <?php foreach ( $property_types as $property_type ) { ?>
		                                            <option value="<?php echo esc_attr($property_type->slug); ?>" <?php if($currentFilters['propertyType'] == $property_type->slug) { echo 'selected'; } ?>><?php echo esc_attr($property_type->name); ?></option>
		                                        <?php } ?>
		                                <?php } ?>
		                            </select>
		                        <?php } ?>

		                        <?php if($slug == 'property_status') { ?>
		                            <select name="propertyStatus" class="form-dropdown property-status-dropdown">
		                                <option value=""><?php echo $placeholder; ?></option>
		                                <?php
		                                    if ( !empty( $property_statuses ) && !is_wp_error( $property_statuses ) ) { ?>
		                                        <?php foreach ( $property_statuses as $property_status_select ) { ?>
		                                            <option value="<?php echo esc_attr($property_status_select->slug); ?>" <?php if($currentFilters['propertyStatus'] == $property_status_select->slug) { echo 'selected'; } ?>><?php echo esc_attr($property_status_select->name); ?></option>
		                                        <?php } ?>
		                                <?php } ?>
		                            </select>
		                        <?php } ?>

		                        <?php if($slug == 'property_location') { ?>
		                            <select name="propertyLocation" class="form-dropdown">
		                                <option value=""><?php echo $placeholder; ?></option>
		                                <?php
		                                $property_locations = get_terms('property_location', array( 'hide_empty' => false, 'parent' => 0 )); 
		                                if ( !empty( $property_locations ) && !is_wp_error( $property_locations ) ) { ?>
		                                    <?php foreach ( $property_locations as $property_location ) { ?>
		                                        <option value="<?php echo esc_attr($property_location->slug); ?>" <?php if($currentFilters['propertyLocation'] == $property_location->slug) { echo 'selected'; } ?>><?php echo esc_attr($property_location->name); ?></option>
		                                        <?php 
		                                            $term_children = get_term_children($property_location->term_id, 'property_location'); 
		                                            if(!empty($term_children)) {
		                                                echo '<optgroup label="'.$property_location->name.'">';
		                                                foreach ( $term_children as $child ) {
		                                                    $term = get_term_by( 'id', $child, 'property_location' ); ?>
		                                                    <option value="<?php echo $term->slug; ?>" <?php if($currentFilters['propertyLocation'] == $term->slug) { echo 'selected'; } ?>><?php echo $term->name; ?></option>
		                                                <?php }
		                                                echo '</optgroup>';
		                                            }
		                                        ?>
		                                    <?php } ?>
		                                <?php } ?>
		                            </select>
		                        <?php } ?>

		                        <?php if($slug == 'price') { ?>
		                        	<?php
		                                if(!empty($currentFilters['priceMin'])) {
		                                    $currentFilterPriceMin = preg_replace("/[^0-9]/","", $currentFilters['priceMin']);
		                                    $price_range_min_start = $currentFilterPriceMin;
		                                }
		                                if(!empty($currentFilters['priceMax'])) {
		                                    $currentFilterPriceMax = preg_replace("/[^0-9]/","", $currentFilters['priceMax']);
		                                    $price_range_max_start = $currentFilterPriceMax;
		                                }
		                            ?>
		                            <div class="price-slider-container">
			                            <div class="price-slider" data-count="1" data-min="<?php echo $price_range_min; ?>" data-max="<?php echo $price_range_max; ?>" data-min-start="<?php echo $price_range_min_start; ?>" data-max-start="<?php echo $price_range_max_start; ?>" ></div>
			                            <span class="price-slider-label price-min-label left"></span>
			                            <span class="price-slider-label price-max-label right"></span>
			                            <div class="clear"></div>
			                            <input name="priceMin" type="hidden" class="price-min-input" />
			                            <input name="priceMax" type="hidden" class="price-max-input" />
			                        </div>
		                        <?php } ?>

		                        <?php if($slug == 'beds') { ?>
		                            <select name="beds" class="form-dropdown">
		                                <option value=""><?php echo $placeholder; ?></option>
		                                <option value="1" <?php if($currentFilters['beds'] == '1') { echo 'selected'; } ?>>1</option>
                                		<option value="2" <?php if($currentFilters['beds'] == '2') { echo 'selected'; } ?>>2</option>
                                		<option value="3" <?php if($currentFilters['beds'] == '3') { echo 'selected'; } ?>>3</option>
                                		<option value="4" <?php if($currentFilters['beds'] == '4') { echo 'selected'; } ?>>4</option>
                                		<option value="5" <?php if($currentFilters['beds'] == '5') { echo 'selected'; } ?>>5</option>
                                		<option value="6" <?php if($currentFilters['beds'] == '6') { echo 'selected'; } ?>>6</option>
                                		<option value="7" <?php if($currentFilters['beds'] == '7') { echo 'selected'; } ?>>7</option>
                                		<option value="8" <?php if($currentFilters['beds'] == '8') { echo 'selected'; } ?>>8</option>
                                		<option value="9" <?php if($currentFilters['beds'] == '9') { echo 'selected'; } ?>>9</option>
                                		<option value="10" <?php if($currentFilters['beds'] == '10') { echo 'selected'; } ?>>10</option>
		                            </select>
		                        <?php } ?>

		                        <?php if($slug == 'baths') { ?>
		                            <select name="baths" class="form-dropdown">
		                                <option value=""><?php echo $placeholder; ?></option>
		                                <option value="1" <?php if($currentFilters['baths'] == '1') { echo 'selected'; } ?>>1</option>
                                		<option value="2" <?php if($currentFilters['baths'] == '2') { echo 'selected'; } ?>>2</option>
                                		<option value="3" <?php if($currentFilters['baths'] == '3') { echo 'selected'; } ?>>3</option>
                                		<option value="4" <?php if($currentFilters['baths'] == '4') { echo 'selected'; } ?>>4</option>
                                		<option value="5" <?php if($currentFilters['baths'] == '5') { echo 'selected'; } ?>>5</option>
                                		<option value="6" <?php if($currentFilters['baths'] == '6') { echo 'selected'; } ?>>6</option>
                                		<option value="7" <?php if($currentFilters['baths'] == '7') { echo 'selected'; } ?>>7</option>
                                		<option value="8" <?php if($currentFilters['baths'] == '8') { echo 'selected'; } ?>>8</option>
                                		<option value="9" <?php if($currentFilters['baths'] == '9') { echo 'selected'; } ?>>9</option>
                                		<option value="10" <?php if($currentFilters['baths'] == '10') { echo 'selected'; } ?>>10</option>
		                            </select>
		                        <?php } ?>

		                        <?php if($slug == 'area') { ?>
		                            <input type="number" name="areaMin" class="area-filter area-filter-min" placeholder="<?php echo $placeholder; ?>" value="<?php echo $currentFilters['areaMin']; ?>" />
		                            <input type="number" name="areaMax" class="area-filter area-filter-max" placeholder="<?php echo $placeholder_second; ?>" value="<?php echo $currentFilters['areaMax']; ?>" />
		                            <div class="clear"></div>
		                        <?php } ?>

		                        <?php 
		                        $custom_fields = get_option('ns_property_custom_fields');
		                        if($custom == 'true' && !empty($custom_fields)) { ?>
		                            <?php
		                            foreach($custom_fields as $field) {
		                                $custom_field_key = strtolower(str_replace(' ', '_', $field['name']));
		                                if($field['id'] == $slug) {
		                                    if($field['type'] == 'select') {  ?>
		                                        <select name="<?php echo $custom_field_key; ?>">
		                                            <option value=""><?php esc_html_e( 'Select an option...', 'ns-real-estate' ); ?></option>
		                                            <?php
		                                                $field_select_options = $field['select_options'];
		                                                foreach($field_select_options as $option) { ?>
		                                                    <option value="<?php echo $option; ?>" <?php if($currentFilters[$custom_field_key] == $option) { echo 'selected'; } ?>><?php echo $option; ?></option>
		                                                <?php }
		                                            ?>
		                                        </select>
		                                    <?php } else { ?>
		                                        <input type="<?php if($field['type'] == 'num') { echo 'number'; } else { echo 'text'; } ?>" name="<?php echo $custom_field_key; ?>" value="<?php echo $currentFilters[$custom_field_key]; ?>" />
		                                    <?php }
		                                }
		                            } ?>
		                        <?php } ?>

                        	</div>
                        <?php }
					} ?>

					<div class="filter-item filter-item-submit <?php if($label_count > 0) { echo 'has-label'; } ?> <?php echo esc_attr($filter_class); ?>">
		                <input type="hidden" name="advancedSearch" value="true" />
		                <input type="submit" class="button" value="<?php echo esc_attr($submit_text); ?>" />
		            </div>
		            <div class="clear"></div>

				</form>
			</div><!-- end tab1 -->

			<!-- start filter content -->
	        <?php $filterCount = 0; ?>
	        <?php foreach ( $property_statuses as $property_status ) { ?>
	            <?php $filterCount++ ?>
	            <div id="tabs-<?php echo esc_attr($filterCount) + 1; ?>" class="ui-tabs-hide">
	                <form method="get" action="<?php echo esc_url($properties_page); ?>">

	                <?php 
	                    foreach($filter_fields as $value) {
	                        if(isset($value['name'])) { $name = $value['name']; }
	                        if(isset($value['label'])) { $label = $value['label']; }
	                        if(isset($value['placeholder'])) { $placeholder = $value['placeholder']; }
	                        if(isset($value['slug'])) { $slug = $value['slug']; }
	                        if(isset($value['active']) && $value['active'] == 'true') { $active = 'true'; } else { $active = 'false'; }
	                        if(isset($value['custom']) && $value['custom'] == 'true') { $custom = 'true'; } else { $custom = 'false'; }

	                        if($active == 'true') { ?>
	                        <div class="form-block filter-item <?php echo esc_attr($filter_class); ?>">
	                            
	                            <?php if(!empty($label)) {
	                                echo '<label>';
	                                if($custom == 'true') { echo esc_attr($name); } else { echo esc_attr($label); }
	                                echo '</label>';
	                            } ?>

	                            <?php if($slug == 'property_type') { ?>
	                                <select name="propertyType" class="form-dropdown">
	                                    <option value=""><?php echo $placeholder; ?></option>
	                                    <?php
	                                        $property_types = get_terms('property_type'); 
	                                        if ( !empty( $property_types ) && !is_wp_error( $property_types ) ) { ?>
	                                            <?php foreach ( $property_types as $property_type ) { ?>
	                                                <option value="<?php echo esc_attr($property_type->slug); ?>"><?php echo esc_attr($property_type->name); ?></option>
	                                            <?php } ?>
	                                    <?php } ?>
	                                </select>
	                            <?php } ?>

	                            <?php if($slug == 'property_status') { ?>
	                                <select name="propertyStatus" class="form-dropdown property-status-dropdown">
	                                    <option value=""><?php echo $placeholder; ?></option>
	                                    <?php
	                                        if ( !empty( $property_statuses ) && !is_wp_error( $property_statuses ) ) { ?>
	                                            <?php foreach ( $property_statuses as $property_status_select ) { ?>
	                                                <option value="<?php echo esc_attr($property_status_select->slug); ?>"><?php echo esc_attr($property_status_select->name); ?></option>
	                                            <?php } ?>
	                                    <?php } ?>
	                                </select>
	                            <?php } ?>

	                            <?php if($slug == 'property_location') { ?>
	                                <select name="propertyLocation" class="form-dropdown">
	                                    <option value=""><?php echo $placeholder; ?></option>
	                                    <?php
	                                    $property_locations = get_terms('property_location', array( 'hide_empty' => false, 'parent' => 0 )); 
	                                    if ( !empty( $property_locations ) && !is_wp_error( $property_locations ) ) { ?>
	                                        <?php foreach ( $property_locations as $property_location ) { ?>
	                                            <option value="<?php echo esc_attr($property_location->slug); ?>"><?php echo esc_attr($property_location->name); ?></option>
	                                            <?php 
	                                                $term_children = get_term_children($property_location->term_id, 'property_location'); 
	                                                if(!empty($term_children)) {
	                                                    echo '<optgroup label="'.$property_location->name.'">';
	                                                    foreach ( $term_children as $child ) {
	                                                        $term = get_term_by( 'id', $child, 'property_location' );
	                                                        echo '<option value="'.$term->slug.'">'.$term->name.'</option>';
	                                                    }
	                                                    echo '</optgroup>';
	                                                }
	                                            ?>
	                                        <?php } ?>
	                                    <?php } ?>
	                                </select>
	                            <?php } ?>

	                            <?php if($slug == 'price') { ?>
	                            	<?php
	                                    $term_data = get_option('taxonomy_'.$property_status->term_id);
	                                    if (isset($term_data['price_range_min'])) { $term_price_range_min = $term_data['price_range_min']; } else { $term_price_range_min = ''; } 
	                                    if (isset($term_data['price_range_max'])) { $term_price_range_max = $term_data['price_range_max']; } else { $term_price_range_max = ''; }
	                                    if (isset($term_data['price_range_min_start'])) { $term_price_range_min_start = $term_data['price_range_min_start']; } else { $term_price_range_min_start = ''; }
	                                    if (isset($term_data['price_range_max_start'])) { $term_price_range_max_start = $term_data['price_range_max_start']; } else { $term_price_range_max_start = ''; }
	                                ?>
	                                <div class="price-slider-container">
		                                <div class="price-slider" data-count="<?php echo esc_attr($filterCount) + 1; ?>" data-min="<?php echo $price_range_min; ?>" data-max="<?php echo $price_range_max; ?>" data-min-start="<?php echo $price_range_min_start; ?>" data-max-start="<?php echo $price_range_max_start; ?>" ></div>
		                                <span class="price-slider-label price-min-label  left"></span>
		                                <span class="price-slider-label price-max-label right"></span>
		                                <div class="clear"></div>
		                                <input name="priceMin" type="hidden" class="price-min-input" />
		                                <input name="priceMax" type="hidden" class="price-max-input" />
		                                <input name="termPriceMin" type="hidden" value="<?php echo $term_price_range_min; ?>" class="term-price-min" />
	                                	<input name="termPriceMax" type="hidden" value="<?php echo $term_price_range_max; ?>" class="term-price-max" />
	                                	<input name="termPriceMinStart" type="hidden" value="<?php echo $term_price_range_min_start; ?>" class="term-price-min-start" />
	                                	<input name="termPriceMaxStart" type="hidden" value="<?php echo $term_price_range_max_start; ?>" class="term-price-max-start" />
		                           	</div>
	                            <?php } ?>

	                            <?php if($slug == 'beds') { ?>
	                                <select name="beds" class="form-dropdown">
	                                    <option value=""><?php echo $placeholder; ?></option>
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
	                                </select>
	                            <?php } ?>

	                            <?php if($slug == 'baths') { ?>
	                                <select name="baths" class="form-dropdown">
	                                    <option value=""><?php echo $placeholder; ?></option>
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
	                                </select>
	                            <?php } ?>

	                            <?php if($slug == 'area') { ?>
	                                <input type="number" name="areaMin" class="area-filter area-filter-min" placeholder="<?php echo $placeholder; ?>" />
	                                <input type="number" name="areaMax" class="area-filter area-filter-max" placeholder="<?php echo $placeholder_second; ?>" />
	                                <div class="clear"></div>
	                            <?php } ?>

	                            <?php if($custom == 'true') { ?>
	                                <?php 
	                                $custom_fields = get_option('ns_property_custom_fields');
	                                foreach($custom_fields as $field) {
	                                    $custom_field_key = strtolower(str_replace(' ', '_', $field['name']));
	                                    if($field['id'] == $slug) {
	                                        if($field['type'] == 'select') {  ?>
	                                            <select name="<?php echo $custom_field_key; ?>">
	                                                <option value=""><?php esc_html_e( 'Select an option...', 'ns-real-estate' ); ?></option>
	                                                <?php
	                                                    $field_select_options = $field['select_options'];
	                                                    foreach($field_select_options as $option) {
	                                                        echo '<option value="'.$option.'">'.$option.'</option>';
	                                                    }
	                                                ?>
	                                            </select>
	                                        <?php } else { ?>
	                                            <input type="<?php if($field['type'] == 'num') { echo 'number'; } else { echo 'text'; } ?>" name="<?php echo $custom_field_key; ?>" />
	                                        <?php }
	                                    }
	                                } ?>
	                            <?php } ?>

	                        </div>
	                        <?php } ?>

	                <?php } ?>

	                <div class="filter-item filter-item-submit <?php if($label_count > 0) { echo 'has-label'; } ?> <?php echo esc_attr($filter_class); ?>">
	                    <input type="hidden" name="propertyStatus" value="<?php echo esc_attr($property_status->slug); ?>" />
	                    <input type="hidden" name="advancedSearch" value="true" />
	                    <input type="submit" class="button" value="<?php echo esc_attr($submit_text); ?>" />
	                </div>
	                <div class="clear"></div>
	                
	            </form>
	            </div>
	        <?php } ?>
	        <div class="clear"></div>

		</div><!-- end tabs -->

	</div><!-- end container -->
	</div><!-- end filter -->

<?php } ?>