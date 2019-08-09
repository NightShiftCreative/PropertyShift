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
        $this->global_settings = $this->admin_obj->load_settings();;
	}

	/**
	 *	Init
	 */
	public function init() {
		add_action('init', array( $this, 'rewrite_rules' ));
		add_action( 'ns_basics_page_settings_init_filter', array( $this, 'add_page_settings' ));
		$this->add_image_sizes();
		add_action( 'init', array( $this, 'add_custom_post_type' ));
		add_action( 'init', array( $this, 'property_type_init' ));
		add_action( 'init', array( $this, 'property_status_init' ));
		add_action( 'init', array( $this, 'property_location_init' ));
		add_action( 'init', array( $this, 'property_amenities_init' ));
		add_filter( 'manage_edit-ns-property_columns', array( $this, 'add_properties_columns' ));
		add_action( 'manage_ns-property_posts_custom_column', array( $this, 'manage_properties_columns' ), 10, 2 );
		add_action( 'add_meta_boxes', array( $this, 'register_meta_box'));
		add_action( 'save_post', array( $this, 'save_meta_box'));
		add_filter( 'ns_basics_page_settings_post_types', array( $this, 'add_page_settings_meta_box'), 10, 3 );
		add_action( 'widgets_init', array( $this, 'properties_sidebar_init'));
		add_filter( 'ns_core_after_top_bar_member_menu', array( $this, 'add_topbar_links'));

		//add property type tax fields
		add_action('property_type_edit_form_fields', array( $this, 'add_tax_fields'), 10, 2);
		add_action('edited_property_type', array( $this, 'save_tax_fields'), 10, 2);
		add_action('property_type_add_form_fields', array( $this, 'add_tax_fields'), 10, 2 );  
		add_action('created_property_type', array( $this, 'save_tax_fields'), 10, 2);

		//add property status tax fields
		add_action('property_status_edit_form_fields', array( $this, 'add_tax_fields'), 10, 2);
		add_action('edited_property_status', array( $this, 'save_tax_fields'), 10, 2);
		add_action('property_status_add_form_fields', array( $this, 'add_tax_fields'), 10, 2 );  
		add_action('created_property_status', array( $this, 'save_tax_fields'), 10, 2);

		add_action( 'property_status_edit_form_fields', array( $this, 'add_tax_price_range_field'), 10, 2);
		add_action('property_status_add_form_fields', array( $this, 'add_tax_price_range_field'), 10, 2 );

		//add property location tax fields
		add_action('property_location_edit_form_fields', array( $this, 'add_tax_fields'), 10, 2);
		add_action('edited_property_location', array( $this, 'save_tax_fields'), 10, 2);
		add_action('property_location_add_form_fields', array( $this, 'add_tax_fields'), 10, 2 );  
		add_action('created_property_location', array( $this, 'save_tax_fields'), 10, 2);

		//front-end template hooks
		add_action('ns_real_estate_property_actions', array($this, 'add_property_share'));
		add_action('ns_real_estate_property_actions', array($this, 'add_property_favoriting'));
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
		
		//get all agents
		$agents_array = array();
        $agents_array[esc_html__('Select an agent...', 'ns-real-estate')] = '';
        $agent_listing_query = get_posts(array('post_type' => 'ns-agent', 'posts_per_page' => -1));
        foreach($agent_listing_query as $agent) { $agents_array[$agent->post_title] = $agent->ID; }

        // settings
		$property_settings_init = array(
			'id' => array(
				'group' => 'general',
				'title' => esc_html__('Property Code', 'ns-real-estate'),
				'description' => esc_html__('An optional string to used to identify properties', 'ns-real-estate'),
				'name' => 'ns_property_code',
				'type' => 'text',
				'value' => $post_id,
				'order' => 0,
			),
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
				'value' => 'Sq Ft',
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
			'latitude' => array(
				'group' => 'map',
				'title' => esc_html__('Latitude', 'ns-real-estate'),
				'name' => 'ns_property_latitude',
				'type' => 'text',
				'order' => 13,
			),
			'longitude' => array(
				'group' => 'map',
				'title' => esc_html__('Longitude', 'ns-real-estate'),
				'name' => 'ns_property_longitude',
				'type' => 'text',
				'order' => 14,	
			),
			'video_url' => array(
				'group' => 'video',
				'title' => esc_html__('Video URL', 'ns-real-estate'),
				'name' => 'ns_property_video_url',
				'type' => 'text',
				'order' => 15,
			),
			'video_cover' => array(
				'group' => 'video',
				'title' => esc_html__('Video Cover Image', 'ns-real-estate'),
				'name' => 'ns_property_video_img',
				'type' => 'image_upload',
				'display_img' => true,
				'order' => 16,
			),
			'owner_display' => array(
				'group' => 'owner_info',
				'title' => esc_html__('What to display for owner information?', 'ns-real-estate'),
				'name' => 'ns_agent_display',
				'type' => 'radio_image',
				'class' => 'full-width',
				'order' => 17,
				'value' => 'none',
				'options' => array(
					esc_html__('None', 'ns-real-estate') => array('value' => 'none'),
					esc_html__('Author Info', 'ns-real-estate') => array('value' => 'author'),
					esc_html__('Agent Info', 'ns-real-estate') => array('value' => 'agent'),
					esc_html__('Custom Info', 'ns-real-estate') => array('value' => 'custom'),
				),
				'children' => array(
					'agent' => array(
						'title' => esc_html__('Select Agent', 'ns-real-estate'),
						'name' => 'ns_agent_select',
						'type' => 'select',
						'options' => $agents_array,
						'parent_val' => 'agent',
					),
					'owner_custom_name' => array(
						'title' => esc_html__('Custom Name', 'ns-real-estate'),
						'name' => 'ns_agent_custom_name',
						'type' => 'text',
						'parent_val' => 'custom',
					),
					'owner_custom_email' => array(
						'title' => esc_html__('Custom Email', 'ns-real-estate'),
						'name' => 'ns_agent_custom_email',
						'type' => 'text',
						'parent_val' => 'custom',
					),
					'owner_custom_phone' => array(
						'title' => esc_html__('Custom Phone', 'ns-real-estate'),
						'name' => 'ns_agent_custom_phone',
						'type' => 'text',
						'parent_val' => 'custom',
					),
					'owner_custom_url' => array(
						'title' => esc_html__('Custom Website', 'ns-real-estate'),
						'name' => 'ns_agent_custom_url',
						'type' => 'text',
						'parent_val' => 'custom',
					),
				),
			),
		);
		$property_settings_init = apply_filters( 'ns_real_estate_property_settings_init_filter', $property_settings_init, $post_id);
		uasort($property_settings_init, 'ns_basics_sort_by_order');

		// Return default settings
		if($return_defaults == true) {
			
			return $property_settings_init;
		
		// Return saved settings
		} else {
			$property_settings = $this->admin_obj->get_meta_box_values($post_id, $property_settings_init);
			return $property_settings;
		}
	}

	/**
	 *	Output meta box interface
	 */
	public function output_meta_box($post) {

		$property_settings = $this->load_property_settings($post->ID); 
		wp_nonce_field( 'ns_property_details_meta_box_nonce', 'ns_property_details_meta_box_nonce' ); ?>
		
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

	        <!--*************************************************-->
	        <!-- MAP -->
	        <!--*************************************************-->
	        <div id="map" class="tab-content">
	            <h3><?php echo esc_html_e('Map', 'ns-real-estate'); ?></h3>
	            <?php
	            foreach($property_settings as $setting) {
	            	if($setting['group'] == 'map') {
            			$this->admin_obj->build_admin_field($setting);
            		}
	            }
	            $maps_obj = new NS_Real_Estate_Maps();
	            $maps_obj->build_single_property_map($property_settings['latitude']['value'], $property_settings['longitude']['value']);
	            ?>
	        </div>

	        <!--*************************************************-->
	        <!-- VIDEO -->
	        <!--*************************************************-->
	        <div id="video" class="tab-content">
	            <h3><?php echo esc_html_e('Video', 'ns-real-estate'); ?></h3>
	            <?php
	            foreach($property_settings as $setting) {
	            	if($setting['group'] == 'video') {
            			$this->admin_obj->build_admin_field($setting);
            		}
	            } ?>
	        </div>

	        <!--*************************************************-->
	        <!-- OWNER INFO -->
	        <!--*************************************************-->
	        <div id="agent" class="tab-content">
	            <h3><?php echo esc_html_e('Owner Info', 'ns-real-estate'); ?></h3>
	            <?php
	            foreach($property_settings as $setting) {
	            	if($setting['group'] == 'owner_info') {
            			$this->admin_obj->build_admin_field($setting);
            		}
	            } ?>
	        </div>

	        <?php do_action('ns_real_estate_after_property_tab_content', $property_settings); ?>

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
	// Property Taxonomies
	/************************************************************************/

	/**
	 *	Register property type taxonomy
	 */
	public function property_type_init() {
		$property_type_tax_slug = $this->global_settings['ns_property_type_tax_slug'];
	    $labels = array(
	    'name'                          => __( 'Property Type', 'ns-real-estate' ),
	    'singular_name'                 => __( 'Property Type', 'ns-real-estate' ),
	    'search_items'                  => __( 'Search Property Types', 'ns-real-estate' ),
	    'popular_items'                 => __( 'Popular Property Types', 'ns-real-estate' ),
	    'all_items'                     => __( 'All Property Types', 'ns-real-estate' ),
	    'parent_item'                   => __( 'Parent Property Type', 'ns-real-estate' ),
	    'edit_item'                     => __( 'Edit Property Type', 'ns-real-estate' ),
	    'update_item'                   => __( 'Update Property Type', 'ns-real-estate' ),
	    'add_new_item'                  => __( 'Add New Property Type', 'ns-real-estate' ),
	    'new_item_name'                 => __( 'New Property Type', 'ns-real-estate' ),
	    'separate_items_with_commas'    => __( 'Separate property types with commas', 'ns-real-estate' ),
	    'add_or_remove_items'           => __( 'Add or remove property types', 'ns-real-estate' ),
	    'choose_from_most_used'         => __( 'Choose from most used property types', 'ns-real-estate' )
	    );
	    
	    register_taxonomy(
	        'property_type',
	        'ns-property',
	        array(
	            'label'         => __( 'Property Types', 'ns-real-estate' ),
	            'labels'        => $labels,
	            'hierarchical'  => true,
	            'rewrite' => array( 'slug' => $property_type_tax_slug )
	        )
	    );
	}

	/**
	 *	Register property status taxonomy
	 */
	public function property_status_init() {
		$property_status_tax_slug = $this->global_settings['ns_property_status_tax_slug'];
	    $labels = array(
	    'name'                          => __( 'Property Status', 'ns-real-estate' ),
	    'singular_name'                 => __( 'Property Status', 'ns-real-estate' ),
	    'search_items'                  => __( 'Search Property Statuses', 'ns-real-estate' ),
	    'popular_items'                 => __( 'Popular Property Statuses', 'ns-real-estate' ),
	    'all_items'                     => __( 'All Property Statuses', 'ns-real-estate' ),
	    'parent_item'                   => __( 'Parent Property Status', 'ns-real-estate' ),
	    'edit_item'                     => __( 'Edit Property Status', 'ns-real-estate' ),
	    'update_item'                   => __( 'Update Property Status', 'ns-real-estate' ),
	    'add_new_item'                  => __( 'Add New Property Status', 'ns-real-estate' ),
	    'new_item_name'                 => __( 'New Property Status', 'ns-real-estate' ),
	    'separate_items_with_commas'    => __( 'Separate property statuses with commas', 'ns-real-estate' ),
	    'add_or_remove_items'           => __( 'Add or remove property statuses', 'ns-real-estate' ),
	    'choose_from_most_used'         => __( 'Choose from most used property statuses', 'ns-real-estate' )
	    );
	    
	    register_taxonomy(
	        'property_status',
	        'ns-property',
	        array(
	            'label'         => __( 'Property Status', 'ns-real-estate' ),
	            'labels'        => $labels,
	            'hierarchical'  => true,
	            'rewrite' => array( 'slug' => $property_status_tax_slug )
	        )
	    );
	}

	/**
	 *	Register property location taxonomy
	 */
	public function property_location_init() {
		$property_location_tax_slug = $this->global_settings['ns_property_location_tax_slug'];
	    $labels = array(
	    'name'                          => __( 'Property Location', 'ns-real-estate' ),
	    'singular_name'                 => __( 'Property Location', 'ns-real-estate' ),
	    'search_items'                  => __( 'Search Property Locations', 'ns-real-estate' ),
	    'popular_items'                 => __( 'Popular Property Locations', 'ns-real-estate' ),
	    'all_items'                     => __( 'All Property Locations', 'ns-real-estate' ),
	    'parent_item'                   => __( 'Parent Property Location', 'ns-real-estate' ),
	    'edit_item'                     => __( 'Edit Property Location', 'ns-real-estate' ),
	    'update_item'                   => __( 'Update Property Location', 'ns-real-estate' ),
	    'add_new_item'                  => __( 'Add New Property Location', 'ns-real-estate' ),
	    'new_item_name'                 => __( 'New Property Location', 'ns-real-estate' ),
	    'separate_items_with_commas'    => __( 'Separate property locations with commas', 'ns-real-estate' ),
	    'add_or_remove_items'           => __( 'Add or remove property locations', 'ns-real-estate' ),
	    'choose_from_most_used'         => __( 'Choose from most used property locations', 'ns-real-estate' )
	    );
	    
	    register_taxonomy(
	        'property_location',
	        'ns-property',
	        array(
	            'label'         => __( 'Property Location', 'ns-real-estate' ),
	            'labels'        => $labels,
	            'hierarchical'  => true,
	            'rewrite' => array( 'slug' => $property_location_tax_slug )
	        )
	    );
	}

	/**
	 *	Register property amenities taxonomy
	 */
	public function property_amenities_init() {
		$property_amenities_tax_slug = $this->global_settings['ns_property_amenities_tax_slug'];
	    $labels = array(
	    'name'                          => __( 'Amenities', 'ns-real-estate' ),
	    'singular_name'                 => __( 'Amenity', 'ns-real-estate' ),
	    'search_items'                  => __( 'Search Amenities', 'ns-real-estate' ),
	    'popular_items'                 => __( 'Popular Amenities', 'ns-real-estate' ),
	    'all_items'                     => __( 'All Amenities', 'ns-real-estate' ),
	    'parent_item'                   => __( 'Parent Amenity', 'ns-real-estate' ),
	    'edit_item'                     => __( 'Edit Amenity', 'ns-real-estate' ),
	    'update_item'                   => __( 'Update Amenity', 'ns-real-estate' ),
	    'add_new_item'                  => __( 'Add New Amenity', 'ns-real-estate' ),
	    'new_item_name'                 => __( 'New Amenity', 'ns-real-estate' ),
	    'separate_items_with_commas'    => __( 'Separate amenities with commas', 'ns-real-estate' ),
	    'add_or_remove_items'           => __( 'Add or remove amenities', 'ns-real-estate' ),
	    'choose_from_most_used'         => __( 'Choose from most used amenities', 'ns-real-estate' )
	    );
	    
	    register_taxonomy(
	        'property_amenities',
	        'ns-property',
	        array(
	            'label'         => __( 'Amenities', 'ns-real-estate' ),
	            'labels'        => $labels,
	            'hierarchical'  => true,
	            'rewrite' => array( 'slug' => $property_amenities_tax_slug )
	        )
	    );
	}

	/************************************************************************/
	// Add Columns to Properties Post Type
	/************************************************************************/

	/**
	 *	Add properties columns
	 *
	 * @param array $columns
	 *
	 */
	public function add_properties_columns($columns) {
		$columns = array(
	        'cb' => '<input type="checkbox" />',
	        'title' => __( 'Property', 'ns-real-estate' ),
	        'thumbnail' => __('Image', 'ns-real-estate'),
	        'location' => __( 'Location', 'ns-real-estate' ),
	        'type' => __( 'Type', 'ns-real-estate' ),
	        'status' => __( 'Status', 'ns-real-estate' ),
	        'price'  => __( 'Price','ns-real-estate' ),
	        'author' => __('Author', 'ns-real-estate'),
	        'date' => __( 'Date', 'ns-real-estate' )
	    );
	    return $columns;
	}

	/**
	 *	Manage properties columns
	 *
	 * @param string $column
	 * @param int $post_id 
	 */
	public function manage_properties_columns($column, $post_id) {
		global $post;
		$property_settings = $this->load_property_settings($post_id); 

	    switch( $column ) {

	        case 'thumbnail' :
	            if(has_post_thumbnail()) { echo the_post_thumbnail('thumbnail'); } else { echo '--'; }
	            break;
	        case 'price' :
	            $price = $property_settings['price']['value'];
	            if(!empty($price)) { $price = $this->get_formatted_price($price); }
	            if(empty($price)) { echo '--'; } else { echo $price; }
	            break;
	        case 'location' :

	            //Get property location
	          	$property_location = $this->get_tax_location($post_id);
	            if(empty($property_location)) { echo '--'; } else { echo $property_location; }
	            break;

	        case 'type' :

	            //Get property type
	        	$property_type = $this->get_tax($post_id, 'property_type');
	            if(empty( $property_type)) { echo '--'; } else { echo $property_type; }
	            break;

	        case 'status' :

	            //Get property status
	        	$property_status = $this->get_tax($post_id, 'property_status');
	            if(empty($property_status)) { echo '--'; } else { echo $property_status; }
	            break;

	        default :
	            break;
	    }
	}

	/************************************************************************/
	// Customize Property Taxonomies Admin Page
	/************************************************************************/

	/**
	 *	Add taxonomy fields
	 *
	 * @param string $tag
	 */
	public function add_tax_fields($tag) {
		if(is_object($tag)) { $t_id = $tag->term_id; } else { $t_id = ''; }
	    $term_meta = get_option( "taxonomy_$t_id");
	    ?>
	    <tr class="form-field">
	        <th scope="row" valign="top"><label for="cat_Image_url"><?php esc_html_e('Category Image Url', 'ns-real-estate'); ?></label></th>
	        <td>
	            <div class="admin-module admin-module-tax-field admin-module-tax-img no-border">
	                <input type="text" class="property-tax-img" name="term_meta[img]" id="term_meta[img]" size="3" style="width:60%;" value="<?php echo $term_meta['img'] ? $term_meta['img'] : ''; ?>">
	                <input class="button admin-button ns_upload_image_button" type="button" value="<?php esc_html_e('Upload Image', 'ns-real-estate'); ?>" />
	                <span class="button button-secondary remove"><?php esc_html_e('Remove', 'ns-real-estate'); ?></span><br/>
	                <p class="description"><?php esc_html_e('Image for Term, use full url', 'ns-real-estate'); ?></p>
	            </div>
	        </td>
	    </tr>
	<?php }

	/**
	 *	Add taxonomy price range field
	 *
	 * @param string $tag
	 */
	public function add_tax_price_range_field($tag) {
		if(is_object($tag)) { $t_id = $tag->term_id; } else { $t_id = ''; }
	    $term_meta = get_option( "taxonomy_$t_id");
	    ?>
	    <tr class="form-field">
	        <th scope="row" valign="top">
	            <strong><?php esc_html_e('Price Range Settings', 'ns-real-estate'); ?></strong>
	            <p class="admin-module-note"><?php esc_html_e('Settings here will override the defaults configured in the plugin settings.', 'ns-real-estate'); ?></p>
	        </th>
	        <td>
	            <div class="admin-module admin-module-tax-field tax-price-range-field no-border">
	                <label for="price_range_min"><?php esc_html_e('Minimum', 'ns-real-estate'); ?></label>
	                <input type="number" class="property-tax-price-range-min" name="term_meta[price_range_min]" id="term_meta[price_range_min]" size="3" value="<?php echo $term_meta['price_range_min'] ? $term_meta['price_range_min'] : ''; ?>">
	            </div>
	            <div class="admin-module admin-module-tax-field tax-price-range-field no-border">
	                <label for="price_range_max"><?php esc_html_e('Maximum', 'ns-real-estate'); ?></label>
	                <input type="number" class="property-tax-price-range-max" name="term_meta[price_range_max]" id="term_meta[price_range_max]" size="3" value="<?php echo $term_meta['price_range_max'] ? $term_meta['price_range_max'] : ''; ?>">
	            </div>
	            <div class="admin-module admin-module-tax-field tax-price-range-field no-border">
	                <label for="price_range_min_start"><?php esc_html_e('Minimum Start', 'ns-real-estate'); ?></label>
	                <input type="number" class="property-tax-price-range-min-start" name="term_meta[price_range_min_start]" id="term_meta[price_range_min_start]" size="3" value="<?php echo $term_meta['price_range_min_start'] ? $term_meta['price_range_min_start'] : ''; ?>">
	            </div>
	            <div class="admin-module admin-module-tax-field tax-price-range-field no-border">
	                <label for="price_range_max_start"><?php esc_html_e('Maximum Start', 'ns-real-estate'); ?></label>
	                <input type="number" class="property-tax-price-range-max-start" name="term_meta[price_range_max_start]" id="term_meta[price_range_max_start]" size="3" value="<?php echo $term_meta['price_range_max_start'] ? $term_meta['price_range_max_start'] : ''; ?>">
	            </div>
	        </td>
	    </tr>
	<?php }

	/**
	 *	Save taxonomy fields
	 *
	 * @param int $term_id
	 */
	public function save_tax_fields($term_id) {
		if ( isset( $_POST['term_meta'] ) ) {
	        $t_id = $term_id;
	        $term_meta = get_option( "taxonomy_$t_id");
	        $cat_keys = array_keys($_POST['term_meta']);
	            foreach ($cat_keys as $key){
	            if (isset($_POST['term_meta'][$key])){
	                $term_meta[$key] = $_POST['term_meta'][$key];
	            }
	        }
	        //save the option array
	        update_option( "taxonomy_$t_id", $term_meta );
	    }
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

	/**
	 *	Get property taxonomy
	 *
	 * @param int $post_id
	 * @param string $tax
	 * @param string $array
	 */
	public function get_tax($post_id, $tax, $array = null, $hide_empty = true) {
		$output = '';

	    if($hide_empty == false) {
	        $tax_terms =  get_terms(['taxonomy' => $tax, 'hide_empty' => false,]);
	    } else {
	        $tax_terms = get_the_terms( $post_id, $tax);
	    }

	    if($tax_terms && ! is_wp_error($tax_terms)) : 
	        
	        //populate term links
	        $term_links = array();
	        foreach ($tax_terms as $term) {
	            if($array == 'true') {
	                $term_links[] = $term->slug;
	            } else {
	                $term_links[] = '<a href="'. esc_attr(get_term_link($term->slug, $tax)) .'">'.$term->name.'</a>' ;
	            }
	        }

	        //determine output
	        if($array == 'true') { $output = $term_links;  } else { $output = join( ", ", $term_links); }
	    
	    endif;
	    return $output;
	}

	/**
	 *	Get property location
	 *
	 * @param int $post_id
	 */
	public function get_tax_location($post_id, $output = null, $array = null) {
		$property_location = '';
	    $property_location_output = '';
	    $property_location_terms = get_the_terms( $post_id, 'property_location');
	    if ( $property_location_terms && ! is_wp_error( $property_location_terms) ) : 
	        $property_location_links = array();
	        $property_location_child_links = array();
	        foreach ( $property_location_terms as $property_location_term ) {
	            if($property_location_term->parent != 0) {
	                if($array == 'true') {
	                    $property_location_child_links[] = $property_location_term->slug;
	                } else {
	                    $property_location_child_links[] = '<a href="'. esc_attr(get_term_link($property_location_term ->slug, 'property_location')) .'">'.$property_location_term ->name.'</a>' ;
	                }
	            } else {
	                if($array == 'true') {
	                    $property_location_links[] = $property_location_term->slug;
	                } else {
	                    $property_location_links[] = '<a href="'. esc_attr(get_term_link($property_location_term ->slug, 'property_location')) .'">'.$property_location_term ->name.'</a>' ;
	                }
	            }
	        }                   
	        $property_location = join( "<span>, </span>", $property_location_links );
	        $property_location_children = join( "<span>, </span>", $property_location_child_links );
	    endif;

	    if($array == 'true') {
	        if(!empty($property_location_links)) { $property_location_output = array_merge($property_location_links, $property_location_child_links); }
	    } else {
	        if($output == 'parent') {
	            $property_location_output = $property_location;
	        } else if($output == 'children') {
	            $property_location_output = $property_location_children;
	        } else {
	            $property_location_output .= $property_location_children;
	            if(!empty($property_location_children) && !empty($property_location)) { $property_location_output .= ', '; } 
	            $property_location_output .= $property_location;
	        }
	    }
	    
	    return $property_location_output; 
	}

	/**
	 *	Retrieves the full address, including location
	 *
	 * @param int $post_id
	 *
	 */
	public function get_full_address($post_id) {
	    $property_settings = $this->load_property_settings($post_id);
	    $street_address = $property_settings['street_address']['value'];
	    $property_address = '';
	    $property_location = $this->get_tax_location($post_id);
	    if(!empty($street_address)) { $property_address .= $street_address; }
	    if(!empty($street_address) && !empty($property_location)) { $property_address .= ', '; }
	    if(!empty($property_location)) { $property_address .= $property_location; }
	    return $property_address;
	}

	/**
	 *	Get property amenities
	 *
	 * @param int $post_id
	 * @param boolean $hide_empty
	 * @param boolean $array
	 */
	public function get_tax_amenities($post_id, $hide_empty = true, $array = null) {
		$property_amenities = '';
	    $property_amenities_links = array();

	    if($hide_empty == false) {
	        $property_amenities_terms =  get_terms(['taxonomy' => 'property_amenities', 'hide_empty' => false,]);
	    } else {
	        $property_amenities_terms = get_the_terms( $post_id, 'property_amenities' );
	    }

	    if ( $property_amenities_terms && ! is_wp_error( $property_amenities_terms) ) : 
	        foreach ( $property_amenities_terms as $property_amenity_term ) {
	            if($array == 'true') {
	                $property_amenities_links[] = $property_amenity_term->slug;
	            } else {
	                if(has_term($property_amenity_term->slug, 'property_amenities', $post_id)) { $icon = '<i class="fa fa-check icon"></i>'; } else { $icon = '<i class="fa fa-times icon"></i>'; }
	                $property_amenities_links[] = '<li><a href="'. esc_attr(get_term_link($property_amenity_term->slug, 'property_amenities')) .'">'.$icon.'<span>'.$property_amenity_term->name.'</span></a></li>' ;
	            }
	        } 
	    endif;

	    if($array == 'true') { 
	        $property_amenities = $property_amenities_links;
	    } else { 
	        $property_amenities = join( '', $property_amenities_links ); 
	        if(!empty($property_amenities)) { $property_amenities = '<ul class="amenities-list clean-list">'.$property_amenities.'</ul>'; }
	    }

	    return $property_amenities;
	}

	/**
	 *	Get property walkscore
	 *
	 * @param int $post_id
	 *
	 */
	public function get_walkscore($lat, $lon, $address) {
		$address = urlencode($address);
	    $url = "http://api.walkscore.com/score?format=json&address=$address";
	    $url .= "&lat=$lat&lon=$lon&wsapikey=f6c3f50b09a7ce69d6d276015e57e996";
	    $request = wp_remote_get($url);
	    $str = wp_remote_retrieve_body($request);
	    return $str;
	}


	/************************************************************************/
	// Property Page Settings Methods
	/************************************************************************/
	
	/**
	 *	Add page settings meta box
	 *
	 * @param array $post_types
	 */
	public function add_page_settings_meta_box($post_types) {
		$post_types[] = 'ns-property';
    	return $post_types;
	}

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

		// Set default page layout
		if($_GET['post_type'] == 'ns-property') { $page_settings_init['page_layout']['value'] = 'right sidebar'; }
			
		// Set default page sidebar
		if($_GET['post_type'] == 'ns-property') { $page_settings_init['page_layout_widget_area']['value'] = 'properties_sidebar'; }

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

	/**
	 *	Process front-end property submit
	 */
	public function insert_property_post($edit_property_id = null) {
		$members_submit_property_approval = esc_attr(get_option('ns_members_submit_property_approval', 'true'));
		if($members_submit_property_approval == 'true') {$members_submit_property_approval = 'pending';} else {$members_submit_property_approval = 'publish'; }

		$output = array();
		$errors = array();

		// require a title
		if(trim($_POST['title']) === '') {
		    $errors['title'] =  esc_html__('Please enter a title!', 'ns-real-estate'); 
		} else {
		    $title = trim($_POST['title']);
		}

		// require an address
		if(trim($_POST['street_address']) === '') {
		    $errors['address'] =  esc_html__('Please enter an address!', 'ns-real-estate'); 
		} else {
		    $street_address = trim($_POST['street_address']);
		}

		// require a price
		if(trim($_POST['price']) === '') {
		    $errors['price'] =  esc_html__('Please enter a price!', 'ns-real-estate'); 
		} else {
		    $price = trim($_POST['price']);
		}

		// Get property taxonomies
		if(isset($_POST['property_location'])) { $property_location = $_POST['property_location']; }
		if(isset($_POST['property_type'])) { $property_type = $_POST['property_type']; }
		if(isset($_POST['contract_type'])) { $property_status = $_POST['contract_type']; }
		if(isset($_POST['property_amenities'])) { $property_amenities = $_POST['property_amenities']; }

		// If there are no errors
		if(empty($errors)) {

			//insert post

		} else {
			$output['success'] = '';
		}

		$output['errors'] = $errors;
		return $output;
	}

	/************************************************************************/
	// Front-end Template Hooks
	/************************************************************************/

	/**
	 *	Add topbar links
	 */
	public function add_topbar_links() {
		$icon_set = 'fa';
		if(function_exists('ns_core_load_theme_options')) { $icon_set = ns_core_load_theme_options('ns_core_icon_set'); }
		$members_my_properties_page = $this->global_settings['ns_members_my_properties_page'];
		$members_submit_property_page = $this->global_settings['ns_members_submit_property_page']; ?>
		<?php if(!empty($members_my_properties_page)) { ?><li><a href="<?php echo $members_my_properties_page; ?>"><?php echo ns_core_get_icon($icon_set, 'home'); ?><?php esc_html_e( 'My Properties', 'ns-real-estate' ); ?></a></li><?php } ?>
		<?php if(!empty($members_submit_property_page)) { ?><li><a href="<?php echo $members_submit_property_page; ?>"><?php echo ns_core_get_icon($icon_set, 'plus'); ?><?php esc_html_e( 'Submit Property', 'ns-real-estate' ); ?></a></li><?php } ?>
	<?php }

	/**
	 *	Add property sharing
	 */
	public function add_property_share() {
		$property_listing_display_share = esc_attr(get_option('ns_property_listing_display_share', 'true'));
		if(class_exists('NS_Basics_Post_Sharing') && $property_listing_display_share == 'true') {
			$post_share_obj = new NS_Basics_Post_Sharing();
			echo $post_share_obj->build_post_sharing_links();
		}
	}

	/**
	 *	Add property favoriting
	 */
	public function add_property_favoriting() {
		$property_listing_display_favorite = esc_attr(get_option('ns_property_listing_display_favorite', 'true'));
		if(class_exists('NS_Basics_Post_Likes') && $property_listing_display_favorite == 'true') {
			$post_likes_obj = new NS_Basics_Post_Likes();
			global $post;
			echo $post_likes_obj->get_post_likes_button($post->ID);
		}
	}


	/************************************************************************/
	// Register Widget Areas
	/************************************************************************/

	/**
	 *	Register properties sidebar
	 */
	public static function properties_sidebar_init() {
		register_sidebar( array(
	        'name' => esc_html__( 'Properties Sidebar', 'ns-real-estate' ),
	        'id' => 'properties_sidebar',
	        'before_widget' => '<div class="widget widget-sidebar widget-sidebar-properties %2$s">',
	        'after_widget' => '</div>',
	        'before_title' => '<h4>',
	        'after_title' => '</h4>',
	    ));
	}

}
?>