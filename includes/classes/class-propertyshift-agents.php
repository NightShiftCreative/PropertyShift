<?php
// Exit if accessed directly
if (!defined( 'ABSPATH')) { exit; }

/**
 *	PropertyShift_Agents class
 *
 */
class PropertyShift_Agents {

	/************************************************************************/
	// Initialize
	/************************************************************************/

	/**
	 *	Constructor
	 */
	public function __construct() {
		// Load admin object & settings
		$this->admin_obj = new PropertyShift_Admin();
        $this->global_settings = $this->admin_obj->load_settings();
	}

	/**
	 *	Init
	 */
	public function init() {
		$this->add_image_sizes();
		add_action('init', array( $this, 'rewrite_rules' ));
		add_action('template_redirect', array($this, 'paginate_agent_single'), 0);
		add_action('init', array( $this, 'add_custom_post_type' ));
		add_action('add_meta_boxes', array( $this, 'register_meta_box'));
		add_action('save_post', array( $this, 'save_meta_box'));
		add_filter('ns_basics_page_settings_post_types', array( $this, 'add_page_settings_meta_box'), 10, 3 );
	}

	/**
	 *	Add Image Sizes
	 */
	public function add_image_sizes() {
		add_image_size( 'agent-thumbnail', 800, 600, array( 'center', 'center' ) );
	}

	/**
	 *	Rewrite Rules
	 */
	public function rewrite_rules() {
		add_rewrite_rule('^agents/page/([0-9]+)','index.php?pagename=agents&paged=$matches[1]', 'top');
	}

	/**
	 *	Allow pagination on agent single page
	 */
	public function paginate_agent_single() {
		if ( is_singular( 'ps-agent' ) ) {
	        global $wp_query;
	        $page = ( int ) $wp_query->get( 'page' );
	        if ( $page > 1 ) {
	            // convert 'page' to 'paged'
	            $query->set( 'page', 1 );
	            $query->set( 'paged', $page );
	        }
	        remove_action( 'template_redirect', 'redirect_canonical' );
	    }
	}

	/************************************************************************/
	// Agents Custom Post Type
	/************************************************************************/

	/**
	 *	Add custom post type
	 */
	public function add_custom_post_type() {
		$agents_slug = $this->global_settings['ps_agent_detail_slug'];
	    register_post_type( 'ps-agent',
	        array(
	            'labels' => array(
	                'name' => __( 'Agents', 'propertyshift' ),
	                'singular_name' => __( 'Agent', 'propertyshift' ),
	                'add_new_item' => __( 'Add New Agent', 'propertyshift' ),
	                'search_items' => __( 'Search Agents', 'propertyshift' ),
	                'edit_item' => __( 'Edit Agent', 'propertyshift' ),
	            ),
	        'public' => true,
	        'show_in_menu' => true,
	        'menu_icon' => 'dashicons-businessman',
	        'has_archive' => false,
	        'supports' => array('title', 'page_attributes'),
	        'rewrite' => array('slug' => $agents_slug),
	        )
	    );
	}

	/**
	 *	Load agent settings
	 *
	 * @param int $post_id
	 */
	public function load_agent_settings($post_id, $return_defaults = false) {

		$users = get_users();
		$user_sync_options = array('Select a user...' => '');
		foreach($users as $user) { $user_sync_options[$user->display_name] = $user->ID; }

		$agent_settings_init = array(
			'user_sync' => array(
				'group' => 'general',
				'title' => esc_html__('Synced User', 'propertyshift'),
				'name' => 'ps_agent_user_sync',
				'description' => esc_html__('All agent details are managed from the user level. Sync this agent with a user to inherit their info. Click Update to reflect changes.', 'propertyshift'),
				'type' => 'select',
				'options' => $user_sync_options,
				'order' => 0,
			),
			'job_title' => array(
				'group' => 'general',
				'title' => esc_html__('Job Title', 'propertyshift'),
				'name' => 'ps_agent_title',
				'description' => esc_html__('Provide the agents job title.', 'propertyshift'),
				'type' => 'text',
				'order' => 1,
			),
			'email' => array(
				'group' => 'general',
				'title' => esc_html__('Email', 'propertyshift'),
				'name' => 'ps_agent_email',
				'description' => esc_html__('Provide the agents email address. This address will be used for the agent contact form.', 'propertyshift'),
				'type' => 'text',
				'order' => 2,
			),
			'mobile_phone' => array(
				'group' => 'general',
				'title' => esc_html__('Mobile Phone', 'propertyshift'),
				'name' => 'ps_agent_mobile_phone',
				'description' => esc_html__('Provide the agents mobile phone number.', 'propertyshift'),
				'type' => 'text',
				'order' => 3,
			),
			'office_phone' => array(
				'group' => 'general',
				'title' => esc_html__('Office Phone', 'propertyshift'),
				'name' => 'ps_agent_office_phone',
				'description' => esc_html__('Provide the agents office phone number.', 'propertyshift'),
				'type' => 'text',
				'order' => 4,
			),
			'description' => array(
				'group' => 'description',
				'name' => 'ps_agent_description',
				'type' => 'editor',
				'order' => 5,
				'class' => 'full-width no-padding',
			),
			'facebook' => array(
				'group' => 'social',
				'title' => esc_html__('Facebook', 'propertyshift'),
				'name' => 'ps_agent_fb',
				'description' => esc_html__('Provide a url for the agents Facebook profile.', 'propertyshift'),
				'type' => 'text',
				'order' => 6,
			),
			'twitter' => array(
				'group' => 'social',
				'title' => esc_html__('Twitter', 'propertyshift'),
				'name' => 'ps_agent_twitter',
				'description' => esc_html__('Provide a url for the agents Twitter profile.', 'propertyshift'),
				'type' => 'text',
				'order' => 7,
			),
			'google' => array(
				'group' => 'social',
				'title' => esc_html__('Google Plus', 'propertyshift'),
				'name' => 'ps_agent_google',
				'description' => esc_html__('Provide a url for the agents Google Plus profile.', 'propertyshift'),
				'type' => 'text',
				'order' => 8,
			),
			'linkedin' => array(
				'group' => 'social',
				'title' => esc_html__('Linkedin', 'propertyshift'),
				'name' => 'ps_agent_linkedin',
				'description' => esc_html__('Provide a url for the agents Linkedin profile.', 'propertyshift'),
				'type' => 'text',
				'order' => 9,
			),
			'youtube' => array(
				'group' => 'social',
				'title' => esc_html__('Youtube', 'propertyshift'),
				'name' => 'ps_agent_youtube',
				'description' => esc_html__('Provide a url for the agents Youtube profile.', 'propertyshift'),
				'type' => 'text',
				'order' => 10,
			),
			'instagram' => array(
				'group' => 'social',
				'title' => esc_html__('Instagram', 'propertyshift'),
				'name' => 'ps_agent_instagram',
				'description' => esc_html__('Provide a url for the agents Instagram profile.', 'propertyshift'),
				'type' => 'text',
				'order' => 11,
			),
			'contact_form_source' => array(
				'group' => 'contact',
				'title' => esc_html__('Agent Contact Form Source', 'propertyshift'),
				'name' => 'ps_agent_form_source',
				'type' => 'radio_image',
				'value' => 'default',
				'options' => array(
					esc_html__('Default Theme Form', 'propertyshift') => array('value' => 'default'),
					esc_html__('Contact Form 7', 'propertyshift') => array('value' => 'contact-form-7'),
				),
				'order' => 12,
				'class' => 'full-width',
				'children' => array(
					'contact_form_7_id' => array(
						'title' => esc_html__('Contact From 7 ID', 'propertyshift'),
						'description' => esc_html__('Provide the ID of the contact form you would like displayed', 'propertyshift'),
						'name' => 'ps_agent_form_id',
						'type' => 'number',
						'parent_val' => 'contact-form-7',
					),
				),
			),
		);
		$agent_settings_init = apply_filters( 'propertyshift_agent_settings_init_filter', $agent_settings_init, $post_id);
		uasort($agent_settings_init, 'ns_basics_sort_by_order');

		// Return default settings
		if($return_defaults == true) {
			
			return $agent_settings_init;
		
		// Return saved settings
		} else {
			$agent_settings = $this->admin_obj->get_meta_box_values($post_id, $agent_settings_init);
			return $agent_settings;
		}
	}

	/**
	 *	Register meta box
	 */
	public function register_meta_box() {
		add_meta_box( 'agent-details-meta-box', 'Agent Details', array($this, 'output_meta_box'), 'ps-agent', 'normal', 'high' );
	}

	/**
	 *	Output meta box interface
	 */
	public function output_meta_box($post) {

		$agent_settings = $this->load_agent_settings($post->ID); 
		wp_nonce_field( 'ps_agent_details_meta_box_nonce', 'ps_agent_details_meta_box_nonce' ); ?>

		<div class="ns-tabs meta-box-form meta-box-form-agent">
			<ul class="ns-tabs-nav">
	            <li><a href="#general"><i class="fa fa-user"></i> <span class="tab-text"><?php esc_html_e('Agent Details', 'propertyshift'); ?></span></a></li>
	            <li><a href="#properties"><i class="fa fa-home"></i> <span class="tab-text"><?php esc_html_e('Properties', 'propertyshift'); ?></span></a></li>
	            <?php do_action('propertyshift_after_agent_detail_tabs'); ?>
	        </ul>

	        <div class="ns-tabs-content">
        	<div class="tab-loader"><img src="<?php echo esc_url(home_url('/')); ?>wp-admin/images/spinner.gif" alt="" /> <?php esc_html_e('Loading...', 'propertyshift'); ?></div>

        	<!--*************************************************-->
	        <!-- GENERAL INFO -->
	        <!--*************************************************-->
	        <div id="general" class="tab-content">

	            <?php $this->admin_obj->build_admin_field($agent_settings['user_sync']); ?>

            	<?php 
            	$user_sync_id = $agent_settings['user_sync']['value'];
            	if(!empty($user_sync_id)) { ?>
            		<h3><?php esc_html_e('General Info', 'propertyshift'); ?></h3>
            		<?php 
            		$user_data = get_userdata($user_sync_id);
            		echo 'Username: <strong>'.$user_data->user_login.'</strong><br/>';
            		echo 'Display Name: <strong>'.$user_data->display_name.'</strong><br/>';
            		echo 'First Name: <strong>'.$user_data->first_name.'</strong><br/>';
            		echo 'Last Name: <strong>'.$user_data->last_name.'</strong><br/>';
            		echo 'Email: <strong>'.$user_data->user_email.'</strong><br/>'; ?>

            		<h3><?php esc_html_e('Social Profiles', 'propertyshift'); ?></h3>

            	<?php } ?>
	        </div>

	        <!--*************************************************-->
	        <!-- AGENT PROPERTIES -->
	        <!--*************************************************-->
	        <div id="properties" class="tab-content">
	            <h3><?php esc_html_e('Agent Properties', 'propertyshift'); ?></h3>
	            <?php
	            $agent_properties = $this->get_agent_properties(get_the_id(), 20, true);
            	$agent_properties_query = $agent_properties['properties']; ?>
            	<p><?php echo $agent_properties['count']; ?> <?php esc_html_e('total properties found', 'propertyshift'); ?></p>
	        	<table class="admin-table">
	                <tr>
	                    <th><?php esc_html_e('Property ID', 'propertyshift'); ?></th>
	                    <th><?php esc_html_e('Title', 'propertyshift'); ?></th>
	                    <th><?php esc_html_e('Status', 'propertyshift'); ?></th>
	                    <th><?php esc_html_e('Date Published', 'propertyshift'); ?></th>
	                    <th><?php esc_html_e('Actions', 'propertyshift'); ?></th>
	                </tr>
	                <?php if ($agent_properties_query->have_posts() ) : while ($agent_properties_query->have_posts() ) : $agent_properties_query->the_post();
	                    echo '<tr>';
	                    echo '<td>'.get_the_id().'</td>';
	                    echo '<td>'.get_the_title().'</td>';
	                    echo '<td>'.get_post_status().'</td>';
	                    echo '<td>'.get_the_date().'</td>';
	                    echo '<td><a href="'.get_the_permalink().'" target="_blank">View</a> | <a href="'.admin_url().'post.php?post='.get_the_id().'&action=edit">Edit</a></td>';
	                    echo '</tr>';
	                endwhile;
	                    wp_reset_postdata();
	                    $paged = isset($_GET['paged']) ? $_GET['paged'] : 1;
	                    $pagination_args = array(
	                        'base'         => '%_%#properties',
	                        'format'       => '?paged=%#%',
	                        'total'        => $agent_properties_query->max_num_pages,
	                        'current'      => max( 1, $paged ),
	                        'show_all'     => true,
	                        'prev_next'    => True,
	                        'prev_text'    => esc_html__('&raquo; Previous', 'propertyshift'),
	                        'next_text'    => esc_html__('Next &raquo;', 'propertyshift'),
	                    ); 
	                    echo '<tr class="admin-table-pagination"><td colspan="5">'.paginate_links($pagination_args).'</td></tr>';
	                else:
	                    echo '<tr><td colspan="5">'.esc_html__('This agent has no assigned properties.', 'propertyshift').'</td></tr>';
	                endif; ?>
	            </table>
	        </div>

	        <?php do_action('propertyshift_after_agent_details_tab_content', $agent_settings); ?>

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
        if( !isset( $_POST['ps_agent_details_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['ps_agent_details_meta_box_nonce'], 'ps_agent_details_meta_box_nonce' ) ) return;

        // if our current user can't edit this post, bail
        if( !current_user_can( 'edit_post', $post_id ) ) return;

        // allow certain attributes
        $allowed = array('a' => array('href' => array()));

        // Load settings and save
        $agent_settings = $this->load_agent_settings($post_id);
        $this->admin_obj->save_meta_box($post_id, $agent_settings, $allowed);
	}

	/************************************************************************/
	// Agent Utilities
	/************************************************************************/

	/**
	 *	Get agent properties
	 *
	 * @param int $agent_id
	 * @param int $posts_per_page
	 * @param boolean $pagination
	 */
	public function get_agent_properties($agent_id, $posts_per_page = null, $pagination = false) {
		$agent_properties = array(); 

	    $meta_query = array();
	    $meta_query['relation'] = 'AND';
	    $meta_query[] = array('key' => 'ps_agent_display', 'value' => 'agent');
	    if(is_array($agent_id)) {
	        $meta_query[] = array('key' => 'ps_agent_select', 'value' => $agent_id, 'compare' => 'IN');
	    } else {
	        $meta_query[] = array('key' => 'ps_agent_select', 'value' => $agent_id);
	    }
	    
	    $args = array(
	        'post_type' => 'ps-property',
	        'meta_query' => $meta_query,
	    );

	    if(!empty($posts_per_page)) { $args['posts_per_page'] = $posts_per_page; }
	    if($pagination == true) { 
	        $paged = isset($_GET['paged']) ? $_GET['paged'] : 1;
	        $args['paged'] = $paged; 
	    }

	    $agent_properties['args'] = $args;
	    $agent_properties['properties'] = new WP_Query($args);
	    $agent_properties['count'] =  $agent_properties['properties']->found_posts;
	    return $agent_properties;
	}

	/************************************************************************/
	// Agent Detail Methods
	/************************************************************************/

	/**
	 *	Load agent detail items
	 */
	public static function load_agent_detail_items() {
		$agent_detail_items_init = array(
	        0 => array(
	            'name' => esc_html__('Overview', 'propertyshift'),
	            'label' => esc_html__('Overview', 'propertyshift'),
	            'slug' => 'overview',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        1 => array(
	            'name' => esc_html__('Description', 'propertyshift'),
	            'label' => esc_html__('Description', 'propertyshift'),
	            'slug' => 'description',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        2 => array(
	            'name' => esc_html__('Contact', 'propertyshift'),
	            'label' => esc_html__('Contact', 'propertyshift'),
	            'slug' => 'contact',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	        3 => array(
	            'name' => esc_html__('Properties', 'propertyshift'),
	            'label' => esc_html__('Properties', 'propertyshift'),
	            'slug' => 'properties',
	            'active' => 'true',
	            'sidebar' => 'false',
	        ),
	    );

		$agent_detail_items_init = apply_filters( 'propertyshift_agent_detail_items_init_filter', $agent_detail_items_init);
	    return $agent_detail_items_init;
	}

	/************************************************************************/
	// Agent Page Settings Methods
	/************************************************************************/

	/**
	 *	Add page settings meta box
	 *
	 * @param array $post_types
	 */
	public function add_page_settings_meta_box($post_types) {
		$post_types[] = 'ps-agent';
    	return $post_types;
	}

}
?>