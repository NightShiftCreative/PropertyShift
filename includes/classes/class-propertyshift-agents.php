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
	
		//user fields
		add_action( 'show_user_profile', array($this, 'create_agent_user_fields'));
        add_action( 'edit_user_profile', array($this, 'create_agent_user_fields'));
        add_action( 'personal_options_update', array($this, 'save_agent_user_fields'));
        add_action( 'edit_user_profile_update', array($this, 'save_agent_user_fields'));
        add_action( 'ns_basics_edit_profile_fields', array($this, 'create_agent_user_fields'));
        add_action( 'ns_basics_edit_profile_save', array($this, 'save_agent_user_fields'));
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
	        'show_in_menu' => false,
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
		);
		$agent_settings_init = apply_filters( 'propertyshift_agent_settings_init_filter', $agent_settings_init, $post_id);
		uasort($agent_settings_init, 'ns_basics_sort_by_order');

		// Return default settings
		if($return_defaults == true) {
			
			return $agent_settings_init;
		
		// Return saved settings
		} else {
			$agent_settings = $this->admin_obj->get_meta_box_values($post_id, $agent_settings_init);
			
			$agent_user_sync_id = $agent_settings['user_sync']['value'];
			if(!empty($agent_user_sync_id)) {
		        $user_data = get_userdata($agent_user_sync_id);
		        
		        $agent_settings['avatar'] = array('title' => 'Avatar ID', 'value' => get_user_meta($agent_user_sync_id, 'avatar', true)); 
		        if(!empty($agent_settings['avatar']['value'])) { 
		        	$agent_listing_crop = $this->global_settings['ps_agent_listing_crop'];
		        	if($agent_listing_crop == 'true') { $avatar_size = 'agent-thumbnail'; } else { $avatar_size = 'full';  }
		        	$agent_settings['avatar_url'] = array('title' => 'Avatar URL', 'value' => wp_get_attachment_image_url($agent_settings['avatar']['value'], $avatar_size)); 
		        }
		        
		        $agent_settings['username'] = array('title' => 'Username', 'value' => $user_data->user_login);
		        $agent_settings['display_name'] = array('title' => 'Display Name', 'value' => $user_data->display_name);
		        $agent_settings['email'] = array('title' => 'Email', 'value' => $user_data->user_email);
		    	$agent_settings['first_name'] = array('title' => 'First Name', 'value' => $user_data->first_name);
		    	$agent_settings['last_name'] = array('title' => 'Last Name', 'value' => $user_data->last_name);
		    	$agent_settings['website'] = array('title' => 'Website', 'value' => $user_data->user_url);
		    	$agent_settings['job_title'] = array('title' => 'Job Title', 'value' => get_user_meta($agent_user_sync_id, 'ps_agent_job_title', true));
		    	$agent_settings['mobile_phone'] = array('title' => 'Mobile Phone', 'value' => get_user_meta($agent_user_sync_id, 'ps_agent_mobile_phone', true));
		    	$agent_settings['office_phone'] = array('title' => 'Office Phone', 'value' => get_user_meta($agent_user_sync_id, 'ps_agent_office_phone', true));
		    	$agent_settings['description'] = array('title' => 'Description', 'value' => $user_data->description);
		    	$agent_settings['facebook'] = array('title' => 'Facebook', 'value' => get_user_meta($agent_user_sync_id, 'ps_agent_facebook', true));
		    	$agent_settings['twitter'] = array('title' => 'Twitter', 'value' => get_user_meta($agent_user_sync_id, 'ps_agent_twitter', true));
		    	$agent_settings['google'] = array('title' => 'Google Plus', 'value' => get_user_meta($agent_user_sync_id, 'ps_agent_google', true));
		    	$agent_settings['linkedin'] = array('title' => 'Linkedin', 'value' => get_user_meta($agent_user_sync_id, 'ps_agent_linkedin', true));
		    	$agent_settings['youtube'] = array('title' => 'Youtube', 'value' => get_user_meta($agent_user_sync_id, 'ps_agent_youtube', true));
		    	$agent_settings['instagram'] = array('title' => 'Instagram', 'value' => get_user_meta($agent_user_sync_id, 'ps_agent_instagram', true));
		    	$agent_settings['contact_form_source'] = array('title' => 'Contact Form Source', 'value' => get_user_meta($agent_user_sync_id, 'ps_agent_contact', true));
		    	$agent_settings['contact_form_7_id'] = array('title' => 'Contact Form 7 ID', 'value' => get_user_meta($agent_user_sync_id, 'ps_agent_contact_form_7', true));
		    }

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
            		if(!empty($agent_settings['avatar_url']['value'])) { echo '<div><img width="100" src="'.$agent_settings['avatar_url']['value'].'" alt="" /></div>'; }
            		if(!empty($agent_settings['username']['value'])) { echo 'Username: <strong>'.$agent_settings['username']['value'].'</strong><br/>'; }
            		if(!empty($agent_settings['display_name']['value'])) { echo 'Display Name: <strong>'.$agent_settings['display_name']['value'].'</strong><br/>'; }
            		if(!empty($agent_settings['first_name']['value'])) { echo 'First Name: <strong>'.$agent_settings['first_name']['value'].'</strong><br/>'; }
            		if(!empty($agent_settings['last_name']['value'])) { echo 'Last Name: <strong>'.$agent_settings['last_name']['value'].'</strong><br/>'; }
            		if(!empty($agent_settings['email']['value'])) { echo 'Email: <strong>'.$agent_settings['email']['value'].'</strong><br/>'; }
            		if(!empty($agent_settings['website']['value'])) { echo 'Website: <strong><a href="'.$agent_settings['website']['value'].'" target="_blank">'.$agent_settings['website']['value'].'</a></strong><br/>'; }
            		if(!empty($agent_settings['description']['value'])) { echo 'Bio: '.$agent_settings['description']['value'].'<br/>'; } ?>

            		<h3><?php esc_html_e('Social Profiles', 'propertyshift'); ?></h3>
            		<?php if(!empty($agent_settings['facebook']['value'])) { echo 'Facebook: <strong>'.$agent_settings['facebook']['value'].'</strong><br/>'; } ?>
            		<?php if(!empty($agent_settings['twitter']['value'])) { echo 'Twitter: <strong>'.$agent_settings['twitter']['value'].'</strong><br/>'; } ?>

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
	// Agent User Fields
	/************************************************************************/

	/**
     *  Create Agent User Fields
     */
    public function create_agent_user_fields($user) { ?>
    	<div class="form-section">
	        <h3><?php _e("Agent Information", "propertyshift"); ?></h3>
	        
	        <?php if(is_admin()) { ?>
	        <table class="form-table">
	        <tr>
	            <th><label><?php esc_html_e('Agent Profile ID', 'propertyshift'); ?></label></th>
	            <td>
	                <input type="number" name="ps_agent_id" value="<?php echo esc_attr( get_the_author_meta( 'ps_agent_id', $user->ID ) ); ?>" class="regular-text" /><br/>
	                <span class="description"><?php esc_html_e("Provide the agents id.", 'propertyshift'); ?></span>
	            </td>
	        </tr>
	        </table>
	    	<?php } ?>

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

    /**
     *  Save Agent User Fields
     */
    public function save_agent_user_fields($user_id) {
        if(!current_user_can( 'edit_user', $user_id )) { return false; }
        if(isset($_POST['ps_agent_id'])) { update_user_meta( $user_id, 'ps_agent_id', $_POST['ps_agent_id'] ); }
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