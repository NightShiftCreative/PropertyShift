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

		//basic setup
		$this->add_image_sizes();
		add_action('init', array( $this, 'rewrite_rules' ));
		add_action('admin_init', array( $this, 'add_agent_role' ));
	
		//add & save user fields
		add_action( 'show_user_profile', array($this, 'create_agent_user_fields'));
        add_action( 'edit_user_profile', array($this, 'create_agent_user_fields'));
        add_action( 'personal_options_update', array($this, 'save_agent_user_fields'));
        add_action( 'edit_user_profile_update', array($this, 'save_agent_user_fields'));
        add_action( 'ns_basics_edit_profile_fields', array($this, 'create_agent_user_fields'));
        add_action( 'ns_basics_edit_profile_save', array($this, 'save_agent_user_fields'));
        add_action( 'user_register', array($this, 'on_agent_register'));

        //front-end agent profiles
		add_filter( 'query_vars', array($this, 'agent_query_vars'));
		add_action('init', array($this, 'agent_rewrite_rule'));
		add_filter( 'request', array($this, 'agent_profile_template_redirect'));
		add_filter( 'author_link', array( $this, 'change_author_link'), 10, 2 );

        //front-end template hooks
        add_action('ns_basics_dashboard_stats', array($this, 'add_dashboard_stats'));
		add_action('ns_basics_after_dashboard', array($this, 'add_dashboard_widgets'));
	}
	

	/************************************************************************/
	// Basic Setup
	/************************************************************************/

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
	 *	Add Agent Role
	 */
	public function add_agent_role() {
		global $wp_roles;
		remove_role('ps_agent');
    	$author_role = $wp_roles->get_role('subscriber');
		add_role('ps_agent', 'PS Agent', $author_role->capabilities);

		$role = $wp_roles->get_role('ps_agent');               
	    $role->add_cap( 'edit_ps-property');
	    $role->add_cap( 'read_ps-property');
	    $role->add_cap( 'read_ps-propertys');
	    $role->add_cap( 'delete_ps-property');
	    $role->add_cap( 'delete_ps-propertys');
	    $role->add_cap( 'edit_ps-propertys');
	    $role->add_cap( 'read_private_ps-propertys');
	    $role->add_cap( 'create_ps-propertys');

	    // Allow agents to publish properties
	    $agent_property_approval = $this->global_settings['ps_members_submit_property_approval'];
	    if($agent_property_approval != 'true') {
	    	$role->add_cap( 'publish_ps-propertys');
	    }
	    
	}

	/************************************************************************/
	// Load agent settings
	/************************************************************************/

	/**
	 *	Load agent settings
	 *
	 * @param int $user_id
	 */
	public function load_agent_settings($user_id) {

		$agent_settings = array();
		$user_data = get_userdata($user_id);
		        
		$agent_settings['avatar'] = array('title' => 'Avatar ID', 'value' => get_user_meta($user_id, 'avatar', true)); 
		if(!empty($agent_settings['avatar']['value'])) { 
			$agent_listing_crop = $this->global_settings['ps_agent_listing_crop'];
			if($agent_listing_crop == 'true') { $avatar_size = 'agent-thumbnail'; } else { $avatar_size = 'full';  }
			$agent_settings['avatar_url'] = array('title' => 'Avatar URL', 'value' => wp_get_attachment_image_url($agent_settings['avatar']['value'], $avatar_size)); 
			$agent_settings['avatar_url_thumb'] = array('title' => 'Avatar Thumbnail URL', 'value' => wp_get_attachment_image_url($agent_settings['avatar']['value'], 'thumbnail'));
		}
		        
		$agent_settings['username'] = array('title' => 'Username', 'value' => $user_data->user_login);
		$agent_settings['display_name'] = array('title' => 'Display Name', 'value' => $user_data->display_name);
		$agent_settings['edit_profile_url'] = array('title' => 'Edit Profile URL', 'value' => get_edit_user_link($user_id));
		$agent_settings['email'] = array('title' => 'Email', 'value' => $user_data->user_email);
		$agent_settings['first_name'] = array('title' => 'First Name', 'value' => $user_data->first_name);
		$agent_settings['last_name'] = array('title' => 'Last Name', 'value' => $user_data->last_name);
		$agent_settings['website'] = array('title' => 'Website', 'value' => $user_data->user_url);
		$agent_settings['show_in_listings'] = array('title' => 'Show In Listings', 'value' => get_user_meta($user_id, 'ps_agent_show_in_listings', true));
		$agent_settings['job_title'] = array('title' => 'Job Title', 'value' => get_user_meta($user_id, 'ps_agent_job_title', true));
		$agent_settings['mobile_phone'] = array('title' => 'Mobile Phone', 'value' => get_user_meta($user_id, 'ps_agent_mobile_phone', true));
		$agent_settings['office_phone'] = array('title' => 'Office Phone', 'value' => get_user_meta($user_id, 'ps_agent_office_phone', true));
		$agent_settings['description'] = array('title' => 'Description', 'value' => $user_data->description);
		$agent_settings['facebook'] = array('title' => 'Facebook', 'value' => get_user_meta($user_id, 'ps_agent_facebook', true));
		$agent_settings['twitter'] = array('title' => 'Twitter', 'value' => get_user_meta($user_id, 'ps_agent_twitter', true));
		$agent_settings['google'] = array('title' => 'Google Plus', 'value' => get_user_meta($user_id, 'ps_agent_google', true));
		$agent_settings['linkedin'] = array('title' => 'Linkedin', 'value' => get_user_meta($user_id, 'ps_agent_linkedin', true));
		$agent_settings['youtube'] = array('title' => 'Youtube', 'value' => get_user_meta($user_id, 'ps_agent_youtube', true));
		$agent_settings['instagram'] = array('title' => 'Instagram', 'value' => get_user_meta($user_id, 'ps_agent_instagram', true));
		$agent_settings['contact_form_source'] = array('title' => 'Contact Form Source', 'value' => get_user_meta($user_id, 'ps_agent_contact', true));
		$agent_settings['contact_form_7_id'] = array('title' => 'Contact Form 7 ID', 'value' => get_user_meta($user_id, 'ps_agent_contact_form_7', true));

		$agent_settings = apply_filters( 'propertyshift_agent_settings_filter', $agent_settings, $user_id);

		return $agent_settings;
	}

	/************************************************************************/
	// Agent User Fields
	/************************************************************************/

	/**
     *  Create Agent User Fields
     */
    public function create_agent_user_fields($user) { 
    	if(in_array('ps_agent', $user->roles) || in_array('administrator', $user->roles)) { ?>
    	<div class="form-section">
	        <h3><?php _e("Agent Information", "propertyshift"); ?></h3>

	        <table class="form-table">
	        <tr>
	            <th><label><?php esc_html_e('Show in Agent Listings', 'propertyshift'); ?></label></th>
	            <td>
	            	<input type="radio" name="ps_agent_show_in_listings" checked <?php if (get_the_author_meta( 'ps_agent_show_in_listings', $user->ID) == 'true' ) { ?>checked="checked"<?php }?> value="true" />Yes<br/>
	            	<input type="radio" name="ps_agent_show_in_listings" <?php if (get_the_author_meta( 'ps_agent_show_in_listings', $user->ID) == 'false' ) { ?>checked="checked"<?php }?> value="false" />No<br/>
	            </td>
	        </tr>
	        </table>

	        <table class="form-table">
	        <tr>
	            <th><label><?php esc_html_e('Job Title', 'propertyshift'); ?></label></th>
	            <td>
	                <input type="text" name="ps_agent_job_title" value="<?php echo esc_attr( get_the_author_meta( 'ps_agent_job_title', $user->ID ) ); ?>" class="regular-text" /><br/>
	                <span class="description"><?php esc_html_e("Provide the agents job title. For example: Broker", 'propertyshift'); ?></span>
	            </td>
	        </tr>
	        </table>

	        <table class="form-table">
	        <tr>
	            <th><label><?php esc_html_e('Mobile Phone', 'propertyshift'); ?></label></th>
	            <td>
	                <input type="text" name="ps_agent_mobile_phone" value="<?php echo esc_attr( get_the_author_meta( 'ps_agent_mobile_phone', $user->ID ) ); ?>" class="regular-text" /><br/>
	                <span class="description"><?php esc_html_e("Provide the agents mobile phone number.", 'propertyshift'); ?></span>
	            </td>
	        </tr>
	        </table>

	        <table class="form-table">
	        <tr>
	            <th><label><?php esc_html_e('Office Phone', 'propertyshift'); ?></label></th>
	            <td>
	                <input type="text" name="ps_agent_office_phone" value="<?php echo esc_attr( get_the_author_meta( 'ps_agent_office_phone', $user->ID ) ); ?>" class="regular-text" /><br/>
	                <span class="description"><?php esc_html_e("Provide the agents office phone number.", 'propertyshift'); ?></span>
	            </td>
	        </tr>
	        </table>

	        <table class="form-table">
	        <tr>
	            <th><label><?php esc_html_e('Facebook', 'propertyshift'); ?></label></th>
	            <td>
	                <input type="text" name="ps_agent_facebook" value="<?php echo esc_attr( get_the_author_meta( 'ps_agent_facebook', $user->ID ) ); ?>" class="regular-text" /><br/>
	                <span class="description"><?php esc_html_e("Provide the agents Facebook profile URL.", 'propertyshift'); ?></span>
	            </td>
	        </tr>
	        </table>

	        <table class="form-table">
	        <tr>
	            <th><label><?php esc_html_e('Twitter', 'propertyshift'); ?></label></th>
	            <td>
	                <input type="text" name="ps_agent_twitter" value="<?php echo esc_attr( get_the_author_meta( 'ps_agent_twitter', $user->ID ) ); ?>" class="regular-text" /><br/>
	                <span class="description"><?php esc_html_e("Provide the agents Twitter profile URL.", 'propertyshift'); ?></span>
	            </td>
	        </tr>
	        </table>

	        <table class="form-table">
	        <tr>
	            <th><label><?php esc_html_e('Linkedin', 'propertyshift'); ?></label></th>
	            <td>
	                <input type="text" name="ps_agent_linkedin" value="<?php echo esc_attr( get_the_author_meta( 'ps_agent_linkedin', $user->ID ) ); ?>" class="regular-text" /><br/>
	                <span class="description"><?php esc_html_e("Provide the agents Linkedin profile URL.", 'propertyshift'); ?></span>
	            </td>
	        </tr>
	        </table>

	        <table class="form-table">
	        <tr>
	            <th><label><?php esc_html_e('Google Plus', 'propertyshift'); ?></label></th>
	            <td>
	                <input type="text" name="ps_agent_google" value="<?php echo esc_attr( get_the_author_meta( 'ps_agent_google', $user->ID ) ); ?>" class="regular-text" /><br/>
	                <span class="description"><?php esc_html_e("Provide the agents Google Plus profile URL.", 'propertyshift'); ?></span>
	            </td>
	        </tr>
	        </table>

	        <table class="form-table">
	        <tr>
	            <th><label><?php esc_html_e('Youtube', 'propertyshift'); ?></label></th>
	            <td>
	                <input type="text" name="ps_agent_youtube" value="<?php echo esc_attr( get_the_author_meta( 'ps_agent_youtube', $user->ID ) ); ?>" class="regular-text" /><br/>
	                <span class="description"><?php esc_html_e("Provide the agents Youtube profile URL.", 'propertyshift'); ?></span>
	            </td>
	        </tr>
	        </table>

	        <table class="form-table">
	        <tr>
	            <th><label><?php esc_html_e('Instagram', 'propertyshift'); ?></label></th>
	            <td>
	                <input type="text" name="ps_agent_instagram" value="<?php echo esc_attr( get_the_author_meta( 'ps_agent_instagram', $user->ID ) ); ?>" class="regular-text" /><br/>
	                <span class="description"><?php esc_html_e("Provide the agents Instagram profile URL.", 'propertyshift'); ?></span>
	            </td>
	        </tr>
	        </table>

	        <?php if(is_admin()) { ?>
	        <table class="form-table">
	        <tr>
	            <th><label><?php esc_html_e('Agent Contact Form', 'propertyshift'); ?></label></th>
	            <td>
	            	<input type="radio" name="ps_agent_contact" checked <?php if (get_the_author_meta( 'ps_agent_contact', $user->ID) == 'default' ) { ?>checked="checked"<?php }?> value="default" />Default Contact Form<br/>
	            	<input type="radio" name="ps_agent_contact" <?php if (get_the_author_meta( 'ps_agent_contact', $user->ID) == 'contact-form-7' ) { ?>checked="checked"<?php }?> value="contact-form-7" />Contact Form 7<br/>
	            	<input type="radio" name="ps_agent_contact" <?php if (get_the_author_meta( 'ps_agent_contact', $user->ID) == 'none' ) { ?>checked="checked"<?php }?> value="none" />None
	            </td>
	        </tr>
	        </table>
	        <table class="form-table">
	        <tr>
	            <th><label><?php esc_html_e('Contact Form 7 ID', 'propertyshift'); ?></label></th>
	            <td>
	                <input type="text" name="ps_agent_contact_form_7" value="<?php echo esc_attr( get_the_author_meta( 'ps_agent_contact_form_7', $user->ID ) ); ?>" class="regular-text" /><br/>
	                <span class="description"><?php esc_html_e("Provide the Contact Form 7 ID.", 'propertyshift'); ?></span>
	            </td>
	        </tr>
	        </table>
    		<?php } ?>
    	</div>
    	<?php }
    }

    /**
     *  Save Agent User Fields
     */
    public function save_agent_user_fields($user_id) {
        if(!current_user_can( 'edit_user', $user_id )) { return false; }
        if(isset($_POST['ps_agent_show_in_listings'])) {update_user_meta( $user_id, 'ps_agent_show_in_listings', $_POST['ps_agent_show_in_listings'] ); }
        if(isset($_POST['ps_agent_job_title'])) {update_user_meta( $user_id, 'ps_agent_job_title', $_POST['ps_agent_job_title'] ); }
        if(isset($_POST['ps_agent_mobile_phone'])) {update_user_meta( $user_id, 'ps_agent_mobile_phone', $_POST['ps_agent_mobile_phone'] ); }
        if(isset($_POST['ps_agent_office_phone'])) {update_user_meta( $user_id, 'ps_agent_office_phone', $_POST['ps_agent_office_phone'] ); }
        if(isset($_POST['ps_agent_facebook'])) {update_user_meta( $user_id, 'ps_agent_facebook', $_POST['ps_agent_facebook'] ); }
        if(isset($_POST['ps_agent_twitter'])) {update_user_meta( $user_id, 'ps_agent_twitter', $_POST['ps_agent_twitter'] ); }
        if(isset($_POST['ps_agent_linkedin'])) {update_user_meta( $user_id, 'ps_agent_linkedin', $_POST['ps_agent_linkedin'] ); }
        if(isset($_POST['ps_agent_google'])) {update_user_meta( $user_id, 'ps_agent_google', $_POST['ps_agent_google'] ); }
        if(isset($_POST['ps_agent_youtube'])) {update_user_meta( $user_id, 'ps_agent_youtube', $_POST['ps_agent_youtube'] ); }
        if(isset($_POST['ps_agent_instagram'])) {update_user_meta( $user_id, 'ps_agent_instagram', $_POST['ps_agent_instagram'] ); }
        if(isset($_POST['ps_agent_contact'])) {update_user_meta( $user_id, 'ps_agent_contact', $_POST['ps_agent_contact'] ); }
    	if(isset($_POST['ps_agent_contact_form_7'])) {update_user_meta( $user_id, 'ps_agent_contact_form_7', $_POST['ps_agent_contact_form_7'] ); }
    }

    /**
     *  On agent register
     */
    public function on_agent_register($user_id) {
    	if($this->global_settings['ps_members_auto_agent_profile'] == 'true') {
    		update_user_meta( $user_id, 'ps_agent_show_in_listings', 'true');
    	} else {
    		update_user_meta( $user_id, 'ps_agent_show_in_listings', 'false');
    	}
    }


	/************************************************************************/
	// Agent Utilities
	/************************************************************************/

    /**
     *  Get agents
     *
     */
    public function get_agents($empty_default = false) {
    	$agents = array();
    	if($empty_default == true) { $agents['Select an agent...'] = ''; }
    	$user_agents = get_users(array('role__in' => array('ps_agent', 'administrator')));
    	foreach($user_agents as $user) {
			$agents[$user->display_name.' ('.$user->user_login.')'] = $user->ID;
		}
		return $agents;
    }

    /**
     *  Check if user is an agent
     *
     */
    public function is_agent($user_id) {
    	$user_meta = get_userdata($user_id);
    	$user_roles = $user_meta->roles; 
    	if(in_array("ps_agent", $user_roles) || in_array("administrator", $user_roles)){
    		return true;
    	} else {
    		return false;
    	}
    }

	/**
	 *	Get agent properties
	 *
	 * @param int $user_id
	 * @param int $posts_per_page
	 * @param boolean $pagination
	 */
	public function get_agent_properties($user_id, $posts_per_page = null, $pagination = false, $post_status = array('publish')) {
		$agent_properties = array(); 
	    
	    $args = array(
	        'post_type' => 'ps-property',
	        'author' => $user_id,
	    );

	    if(!empty($posts_per_page)) { $args['posts_per_page'] = $posts_per_page; }
	    if($pagination == true) { 
	        $paged = isset($_GET['paged']) ? $_GET['paged'] : 1;
	        $args['paged'] = $paged; 
	    }

	    $args['post_status'] = $post_status;

	    $agent_properties['args'] = $args;
	    $agent_properties['properties'] = new WP_Query($args);
	    $agent_properties['count'] =  $agent_properties['properties']->found_posts;
	    return $agent_properties;
	}

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
	// Front-End Agent Profiles
	/************************************************************************/

	/**
	 *	Add query var
	 */
	public function agent_query_vars( $vars ) {
	    $vars[] = $this->global_settings['ps_agent_detail_slug'];
	    return $vars;
	}
	
	/**
	 *	Add rewrite rule
	 */
	public function agent_rewrite_rule() {
		$agent_slug = $this->global_settings['ps_agent_detail_slug'];
	    add_rewrite_tag( '%'.$agent_slug.'%', '([^&]+)' );
	    add_rewrite_rule(
	        '^'.$agent_slug.'/([^/]*)/?',
	        'index.php?'.$agent_slug.'=$matches[1]',
	        'top'
	    );
	}
	
	/**
	 *	Redirect to agent profile template
	 */
	public function agent_profile_template_redirect($query_vars) {
		$agent_slug = $this->global_settings['ps_agent_detail_slug'];
		$agent_profile_page = $this->global_settings['ps_members_profile_page'];

		//Redirect to agent page (fallback to author archive if agent page isn't set)
		if(!empty($agent_profile_page)) {
			if(isset($query_vars[$agent_slug])) { $query_vars['page_id'] = $agent_profile_page; }
		} else {
			if(isset($query_vars[$agent_slug])) { 
				$agent_id = get_user_by('slug', $query_vars[$agent_slug]);
				$agent_id = $agent_id->ID;
				$query_vars['author'] = $agent_id; 
			}
		}
	    
    	return $query_vars;
	}

	/**
	 *	Modify author link
	 */
	function change_author_link($link, $author_id) {
		$agent_slug = $this->global_settings['ps_agent_detail_slug'];
		$user_meta = get_userdata($author_id);
		$user_roles = $user_meta->roles;
		if(in_array('ps_agent', $user_roles)) {
			$link = str_replace( 'author', $agent_slug, $link );
		}
	    return $link;
	}

	/************************************************************************/
	// Front-end Template Hooks
	/************************************************************************/

	/**
	 *	Add dashboard stats
	 */
	public function add_dashboard_stats() { 
		
		$current_user = wp_get_current_user();

		//Get post likes
		$post_likes_obj = new NS_Basics_Post_Likes();
		$saved_posts = $post_likes_obj->show_user_likes_count($current_user); 

		//Get properties
		$pending_properties = $this->get_agent_properties($current_user->ID, null, false, array('pending'));
		$published_properties = $this->get_agent_properties($current_user->ID, null, false, array('publish')); ?>
		
		<div class="user-dashboard-widget stat">
			<span><?php echo $pending_properties['count']; ?></span>
			<p><?php esc_html_e( 'Pending Properties', 'propertyshift' ) ?></p>
		</div>
		<div class="user-dashboard-widget stat">
			<span><?php echo $published_properties['count']; ?></span>
			<p><?php esc_html_e( 'Published Properties', 'propertyshift' ) ?></p>
		</div>
		<div class="user-dashboard-widget stat">
			<span><?php echo $saved_posts; ?></span>
			<p><?php esc_html_e( 'Saved Posts', 'propertyshift' ) ?></p>
		</div>
	<?php }

	/**
	 *	Add dashboard widgets
	 */
	public function add_dashboard_widgets() { 
		$members_my_properties_page = $this->global_settings['ps_members_my_properties_page']; ?>
		<div class="user-dashboard-widget">
			<h4><?php esc_html_e( 'Your Recent Properties', 'propertyshift' ) ?></h4>
			<?php echo do_shortcode('[ps_my_properties show_posts=3 show_pagination="false"]'); ?>
			<?php if(!empty($members_my_properties_page)) { ?><a href="<?php echo $members_my_properties_page; ?>" class="button small">View All Properties</a><?php } ?>
		</div>
	<?php }

} ?>