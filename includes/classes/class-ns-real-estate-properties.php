<?php
// Exit if accessed directly
if (!defined( 'ABSPATH')) { exit; }

/**
 *	NS_Real_Estate_Properties class
 *
 */
class NS_Real_Estate_Properties {

	/************************************************************************/
	// Initialize
	/************************************************************************/

	public function __construct() {

		// Load admin object & settings
		$this->admin_obj = new NS_Real_Estate_Admin();
        $settings_init = $this->admin_obj->load_settings();
        $this->global_settings = $this->admin_obj->get_settings($settings_init);
	}

	/**
	 *	Init
	 */
	public function init() {
		add_action('init', array( $this, 'rewrite_rules' ));
		add_action( 'ns_basics_page_settings_init_filter', array( $this, 'add_page_settings' ));
		$this->add_image_sizes();
		add_action( 'init', array( $this, 'add_custom_post_type' ));
		add_action( 'add_meta_boxes', array( $this, 'register_meta_box'));
		add_action( 'save_post', array( $this, 'save_meta_box'));
	}

	/**
	 *	Add Image Sizes
	 */
	public function add_image_sizes() {
		add_image_size( 'property-thumbnail', 800, 600, array( 'center', 'center' ) );
	}

	/**
	 *	Rewrite Rules
	 */
	public function rewrite_rules() {
		add_rewrite_rule('^properties/page/([0-9]+)','index.php?pagename=properties&paged=$matches[1]', 'top');
	}

	/************************************************************************/
	// Properties Custom Post Type
	/************************************************************************/

	/**
	 *	Add custom post type
	 */
	public function add_custom_post_type() {
		$properties_slug = $this->global_settings['ns_property_detail_slug'];
	    register_post_type( 'ns-property',
	        array(
	            'labels' => array(
	                'name' => __( 'Properties', 'ns-real-estate' ),
	                'singular_name' => __( 'Property', 'ns-real-estate' ),
	                'add_new_item' => __( 'Add New Property', 'ns-real-estate' ),
	                'search_items' => __( 'Search Properties', 'ns-real-estate' ),
	                'edit_item' => __( 'Edit Property', 'ns-real-estate' ),
	            ),
	        'public' => true,
	        'show_in_menu' => true,
	        'menu_icon' => 'dashicons-admin-home',
	        'has_archive' => false,
	        'supports' => array('title', 'author', 'editor', 'revisions', 'thumbnail', 'page_attributes'),
	        'rewrite' => array('slug' => $properties_slug),
	        )
	    );
	}

	/**
	 *	Register meta box
	 */
	public function register_meta_box() {
		add_meta_box( 'property-details-meta-box', 'Property Details', array($this, 'output_meta_box'), 'ns-property', 'normal', 'high' );
	}

	/**
	 *	Load property settings
	 *
	 * @param int $post_id
	 */
	public function load_property_settings($post_id, $return_defaults = false) {
		$property_settings_init = array(
			'featured' => array(
				'group' => 'general',
				'title' => esc_html__('Featured Property', 'ns-real-estate'),
				'name' => 'ns_property_featured',
				'type' => 'checkbox',
				'value' => 'false',
				'order' => 1,
			),
			'street_address' => array(
				'group' => 'general',
				'title' => esc_html__('Street Address', 'ns-real-estate'),
				'name' => 'ns_property_address',
				'description' => esc_html__('Provide the address for the property', 'ns-real-estate'),
				'type' => 'text',
				'order' => 2,
			),
			'price' => array(
				'group' => 'general',
				'title' => esc_html__('Price', 'ns-real-estate'),
				'name' => 'ns_property_price',
				'description' => esc_html__('Use only numbers. Do not include commas or dollar sign (ex.- 250000)', 'ns-real-estate'),
				'type' => 'number',
				'min' => 0,
				'order' => 3,
			),
			'price_postfix' => array(
				'group' => 'general',
				'title' => esc_html__('Price Postfix', 'ns-real-estate'),
				'name' => 'ns_property_price_postfix',
				'description' => esc_html__('Provide the text displayed after the price (ex.- Per Month)', 'ns-real-estate'),
				'type' => 'text',
				'order' => 4,
			),
			'beds' => array(
				'group' => 'general',
				'title' => esc_html__('Bedrooms', 'ns-real-estate'),
				'name' => 'ns_property_bedrooms',
				'description' => esc_html__('Provide the number of bedrooms', 'ns-real-estate'),
				'type' => 'number',
				'min' => 0,
				'order' => 5,
			),
			'baths' => array(
				'group' => 'general',
				'title' => esc_html__('Bathrooms', 'ns-real-estate'),
				'name' => 'ns_property_bathrooms',
				'description' => esc_html__('Provide the number of bathrooms', 'ns-real-estate'),
				'type' => 'number',
				'min' => 0,
				'step' => 0.5,
				'order' => 6,
			),
			'garages' => array(
				'group' => 'general',
				'title' => esc_html__('Garages', 'ns-real-estate'),
				'name' => 'ns_property_garages',
				'description' => esc_html__('Provide the number of garages', 'ns-real-estate'),
				'type' => 'number',
				'min' => 0,
				'order' => 7,
			),
			'area' => array(
				'group' => 'general',
				'title' => esc_html__('Area', 'ns-real-estate'),
				'name' => 'ns_property_area',
				'description' => esc_html__('Provide the area. Use only numbers and decimals, do not include commas.', 'ns-real-estate'),
				'type' => 'number',
				'min' => 0,
				'step' => 0.01,
				'order' => 8,
			),
			'area_postfix' => array(
				'group' => 'general',
				'title' => esc_html__('Area Postfix', 'ns-real-estate'),
				'name' => 'ns_property_area_postfix',
				'description' => esc_html__('Provide the text to display directly after the area (ex. - Sq Ft)', 'ns-real-estate'),
				'type' => 'text',
				'order' => 9,
			),
			'description' => array(
				'group' => 'description',
				'name' => 'ns_property_description',
				'type' => 'editor',
				'order' => 10,
				'class' => 'full-width no-padding',
			),
			'gallery' => array(
				'group' => 'gallery',
				'name' => 'ns_additional_img',
				'type' => 'gallery',
				'serialized' => true,
				'order' => 11,
				'class' => 'full-width no-padding',
			),
			'floor_plans' => array(
				'group' => 'floor_plans',
				'name' => 'ns_property_floor_plans',
				'type' => 'floor_plans',
				'serialized' => true,
				'order' => 12,
				'class' => 'full-width no-padding',
			),
		);
		$property_settings_init = apply_filters( 'ns_real_estate_property_settings_init_filter', $property_settings_init);
		uasort($property_settings_init, 'ns_basics_sort_by_order');

		// Return default settings
		if($return_defaults == true) {
			
			return $property_settings_init;
		
		// Return saved settings
		} else {
			$property_settings = $this->admin_obj->get_meta_box_values($post_id, $property_settings_init);
			$property_settings = apply_filters( 'ns_real_estate_property_settings_filter', $property_settings);
			return $property_settings;
		}
	}

	/**
	 *	Output meta box interface
	 */
	public function output_meta_box($post) { 

		$property_settings = $this->load_property_settings($post->ID); 
		wp_nonce_field( 'ns_property_details_meta_box_nonce', 'ns_property_details_meta_box_nonce' );?>
		
		<div class="ns-tabs meta-box-form meta-box-form-property-details">
			<ul class="ns-tabs-nav">
	            <li><a href="#general" title="<?php esc_html_e('General Info', 'ns-real-estate'); ?>"><i class="fa fa-home"></i> <span class="tab-text"><?php echo esc_html_e('General Info', 'ns-real-estate'); ?></span></a></li>
	            <li><a href="#description" title="<?php esc_html_e('Description', 'ns-real-estate'); ?>"><i class="fa fa-pencil-alt"></i> <span class="tab-text"><?php echo esc_html_e('Description', 'ns-real-estate'); ?></span></a></li>
	            <li><a href="#gallery" title="<?php esc_html_e('Gallery', 'ns-real-estate'); ?>"><i class="fa fa-image"></i> <span class="tab-text"><?php echo esc_html_e('Gallery', 'ns-real-estate'); ?></span></a></li>
	            <li><a href="#floor-plans" title="<?php esc_html_e('Floor Plans', 'ns-real-estate'); ?>"><i class="fa fa-th-large"></i> <span class="tab-text"><?php echo esc_html_e('Floor Plans', 'ns-real-estate'); ?></span></a></li>
	            <li><a href="#map" title="<?php esc_html_e('Map', 'ns-real-estate'); ?>" onclick="refreshMap()"><i class="fa fa-map"></i> <span class="tab-text"><?php echo esc_html_e('Map', 'ns-real-estate'); ?></span></a></li>
	            <li><a href="#video" title="<?php esc_html_e('Video', 'ns-real-estate'); ?>"><i class="fa fa-video"></i> <span class="tab-text"><?php echo esc_html_e('Video', 'ns-real-estate'); ?></span></a></li>
	            <li><a href="#agent" title="<?php esc_html_e('Owner Info', 'ns-real-estate'); ?>"><i class="fa fa-user"></i> <span class="tab-text"><?php echo esc_html_e('Owner Info', 'ns-real-estate'); ?></span></a></li>
	            <?php do_action('ns_real_estate_after_property_tabs'); ?>
	        </ul>

	        <div class="ns-tabs-content">
        	<div class="tab-loader"><img src="<?php echo esc_url(home_url('/')); ?>wp-admin/images/spinner.gif" alt="" /> <?php echo esc_html_e('Loading...', 'ns-real-estate'); ?></div>
        	
        	<!--*************************************************-->
	        <!-- GENERAL INFO -->
	        <!--*************************************************-->
	        <div id="general" class="tab-content">
	            <h3><?php echo esc_html_e('General Info', 'ns-real-estate'); ?></h3>
	            <?php
	            foreach($property_settings as $setting) {
	            	if($setting['group'] == 'general') {
            			$this->admin_obj->build_admin_field($setting);
            		}
	            } ?>
	        </div>

	        <!--*************************************************-->
	        <!-- DESCRIPTION -->
	        <!--*************************************************-->
	        <div id="description" class="tab-content">
	            <h3><?php echo esc_html_e('Description', 'ns-real-estate'); ?></h3>
	            <?php
	            foreach($property_settings as $setting) {
	            	if($setting['group'] == 'description') {
            			$this->admin_obj->build_admin_field($setting);
            		}
	            } ?>
	        </div>

	        <!--*************************************************-->
	        <!-- GALLERY -->
	        <!--*************************************************-->
	        <div id="gallery" class="tab-content">
	            <h3><?php echo esc_html_e('Gallery', 'ns-real-estate'); ?></h3>
	            <?php
	            foreach($property_settings as $setting) {
	            	if($setting['group'] == 'gallery') {
            			$this->admin_obj->build_admin_field($setting);
            		}
	            } ?>
	        </div>

	        <!--*************************************************-->
	        <!-- FLOOR PLANS -->
	        <!--*************************************************-->
	        <div id="floor-plans" class="tab-content">
	            <h3><?php echo esc_html_e('Floor Plans', 'ns-real-estate'); ?></h3>
	            <?php
	            foreach($property_settings as $setting) {
	            	if($setting['group'] == 'floor_plans') {
            			$this->admin_obj->build_admin_field($setting);
            		}
	            } ?>
	        </div>

        	</div><!-- end ns-tabs-content -->
        	<div class="clear"></div>

		</div><!-- end ns-tabs -->

	<?php }

	/**
	 * Save Meta Box
	 */
	public function save_meta_box($post_id) {
		// Bail if we're doing an auto save
        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        // if our nonce isn't there, or we can't verify it, bail
        if( !isset( $_POST['ns_property_details_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['ns_property_details_meta_box_nonce'], 'ns_property_details_meta_box_nonce' ) ) return;

        // if our current user can't edit this post, bail
        if( !current_user_can( 'edit_post', $post_id ) ) return;

        // allow certain attributes
        $allowed = array('a' => array('href' => array()));

        // Load property settings and save
        $property_settings = $this->load_property_settings($post_id);
        $this->admin_obj->save_meta_box($post_id, $property_settings, $allowed);
	}


	/************************************************************************/
	// Property Utilities
	/************************************************************************/

	/**
	 *	Count properties
	 *
	 * @param string $type
	 * @param int $user_id 
	 */
	public function count_properties($type, $user_id = null) {
		$args_total_properties = array(
            'post_type' => 'ns-property',
            'showposts' => -1,
            'author_name' => $user_id,
            'post_status' => $type 
        );

        $meta_posts = get_posts( $args_total_properties );
        $meta_post_count = count( $meta_posts );
        unset( $meta_posts);
        return $meta_post_count;
	}

	/**
	 *	Get formatted price
	 *
	 * @param string $price
	 */
	public function get_formatted_price($price) {

	    $currency_symbol = $this->global_settings['ns_real_estate_currency_symbol'];
	    $currency_symbol_position = $this->global_settings['ns_real_estate_currency_symbol_position'];
	    $currency_thousand = $this->global_settings['ns_real_estate_thousand_separator'];
	    $currency_decimal = $this->global_settings['ns_real_estate_decimal_separator'];
	    $currency_decimal_num =  $this->global_settings['ns_real_estate_num_decimal'];

	    if(!empty($price)) { $price = number_format($price, $currency_decimal_num, $currency_decimal, $currency_thousand); }
	    if($currency_symbol_position == 'before') { $price = $currency_symbol.$price; } else { $price = $price.$currency_symbol; }

	    return $price;
	}

	/**
	 *	Get formatted area
	 *
	 * @param string $area
	 */
	public function get_formatted_area($area) {
		
	    $decimal_num_area = $this->global_settings['ns_real_estate_num_decimal_area'];
	    $decimal_area = $this->global_settings['ns_real_estate_decimal_separator_area'];
	    $thousand_area =  $this->global_settings['ns_real_estate_thousand_separator_area'];

    	if(!empty($area)) { $area = number_format($area, $decimal_num_area, $decimal_area, $thousand_area); }
    	return $area;
	}


	/************************************************************************/
	// Property Page Settings Methods
	/************************************************************************/
	
	/**
	 *	Add page settings
	 *
	 * @param array $page_settings_init
	 */
	public function add_page_settings($page_settings_init) {
		
		// Add map banner options
		$page_settings_init['banner_source']['options'][esc_html__('Map Banner', 'ns-real-estate')] = array(
			'value' => 'properties_map', 
			'icon' => NS_BASICS_PLUGIN_DIR.'/images/google-maps-icon.png', 
		);

		// Add filter banner options
		$page_settings_init['property_filter_override'] = array(
			'group' => 'banner',
			'title' => esc_html__('Use Custom Property Filter Settings', 'ns-real-estate'),
			'name' => 'ns_banner_property_filter_override',
			'description' => esc_html__('The global property filter settings can be configured in NS Real Estate > Settings', 'ns-real-estate'),
			'value' => 'false',
			'type' => 'switch',
			'order' => 14,
			'children' => array(
				'property_filter_display' => array(
					'title' => esc_html__('Display Property Filter', 'ns-real-estate'),
					'name' => 'ns_banner_property_filter_display',
					'type' => 'checkbox',
					'value' => 'true',
				),
				'property_filter_id' => array(
					'title' => esc_html__('Select a Filter', 'ns-real-estate'),
					'name' => 'ns_banner_property_filter_id',
					'type' => 'select',
					'options' => array(),
				),
			),
		);

		return $page_settings_init;
	}

	/************************************************************************/
	// Property Detail Methods
	/************************************************************************/

	/**
	 *	Load property detail items
	 */
	public static function load_property_detail_items() {
		$property_detail_items_init = array(
	        0 => array(
	            'name' => esc_html__('Overview', 'ns-real-estate'),
	            'label' => esc_html__('Overview', 'ns-real-estate'),
	            'slug' => 'overview',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        1 => array(
	            'name' => esc_html__('Description', 'ns-real-estate'),
	            'label' => esc_html__('Description', 'ns-real-estate'),
	            'slug' => 'description',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        2 => array(
	            'name' => esc_html__('Gallery', 'ns-real-estate'),
	            'label' => esc_html__('Gallery', 'ns-real-estate'),
	            'slug' => 'gallery',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        3 => array(
	            'name' => esc_html__('Property Details', 'ns-real-estate'),
	            'label' => esc_html__('Property Details', 'ns-real-estate'),
	            'slug' => 'property_details',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        4 => array(
	            'name' => esc_html__('Video', 'ns-real-estate'),
	            'label' => esc_html__('Video', 'ns-real-estate'),
	            'slug' => 'video',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        5 => array(
	            'name' => esc_html__('Amenities', 'ns-real-estate'),
	            'label' => esc_html__('Amenities', 'ns-real-estate'),
	            'slug' => 'amenities',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        6 => array(
	            'name' => esc_html__('Floor Plans', 'ns-real-estate'),
	            'label' => esc_html__('Floor Plans', 'ns-real-estate'),
	            'slug' => 'floor_plans',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        7 => array(
	            'name' => esc_html__('Location', 'ns-real-estate'),
	            'label' => esc_html__('Location', 'ns-real-estate'),
	            'slug' => 'location',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        8 => array(
	            'name' => esc_html__('Walk Score', 'ns-real-estate'),
	            'label' => esc_html__('Walk Score', 'ns-real-estate'),
	            'slug' => 'walk_score',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        9 => array(
	            'name' => esc_html__('Agent Info', 'ns-real-estate'),
	            'label' => 'Agent Information',
	            'slug' => 'agent_info',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        10 => array(
	            'name' => esc_html__('Related Properties', 'ns-real-estate'),
	            'label' => 'Related Properties',
	            'slug' => 'related',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	    );

		$property_detail_items_init = apply_filters( 'ns_real_estate_property_detail_items_init_filter', $property_detail_items_init);
	    return $property_detail_items_init;
	}

	/************************************************************************/
	// Property Submit Methods
	/************************************************************************/

	/**
	 *	Load front-end property submit fields
	 */
	public static function load_property_submit_fields() {
		$property_submit_fields_init = array(
			'property_title' => array('value' => esc_html__('Property Title (required)', 'ns-real-estate'), 'attributes' => array('disabled', 'checked')),
            'price' => array('value' => esc_html__('Price (required)', 'ns-real-estate'), 'attributes' => array('disabled', 'checked')),
            'price_postfix' => array('value' => esc_html__('Price Postfix', 'ns-real-estate')),
            'street_address' => array('value' => esc_html__('Street Address (required)', 'ns-real-estate'), 'attributes' => array('disabled', 'checked')),
            'description' => array('value' => esc_html__('Description', 'ns-real-estate')),
            'beds' => array('value' => esc_html__('Beds', 'ns-real-estate')),
            'baths' => array('value' => esc_html__('Baths', 'ns-real-estate')),
            'garages' => array('value' => esc_html__('Garages', 'ns-real-estate')),
            'area' => array('value' => esc_html__('Area', 'ns-real-estate')),
            'area_postfix' => array('value' => esc_html__('Area Postfix', 'ns-real-estate')),
            'video' => array('value' => esc_html__('Video', 'ns-real-estate')),
            'property_location' => array('value' => esc_html__('Property Location', 'ns-real-estate')),
            'property_type' => array('value' => esc_html__('Property Type', 'ns-real-estate')),
            'property_status' => array('value' => esc_html__('Property Status', 'ns-real-estate')),
            'amenities' => array('value' => esc_html__('Amenities', 'ns-real-estate')),
            'floor_plans' => array('value' => esc_html__('Floor Plans', 'ns-real-estate')),
            'featured_image' => array('value' => esc_html__('Featured Image', 'ns-real-estate')),
            'gallery_images' => array('value' => esc_html__('Gallery Images', 'ns-real-estate')),
            'map' => array('value' => esc_html__('Map', 'ns-real-estate')),
            'owner_info' => array('value' => esc_html__('Owner Info', 'ns-real-estate')),
	    );
	    $property_submit_fields_init = apply_filters( 'ns_real_estate_property_submit_fields_init_filter', $property_submit_fields_init);
	    return $property_submit_fields_init;
	}

}
?>