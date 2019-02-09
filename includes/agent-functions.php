<?php

/*-----------------------------------------------------------------------------------*/
/*  Global Agent Functions
/*-----------------------------------------------------------------------------------*/

//rewrite for agents page url conflict
function ns_real_estate_agents_rewrite_rule() {
    add_rewrite_rule('^agents/page/([0-9]+)','index.php?pagename=agents&paged=$matches[1]', 'top');
}
add_action('init', 'ns_real_estate_agents_rewrite_rule');

//allow pagination on agent single page
add_action( 'template_redirect', function() {
    if ( is_singular( 'ns-agent' ) ) {
        global $wp_query;
        $page = ( int ) $wp_query->get( 'page' );
        if ( $page > 1 ) {
            // convert 'page' to 'paged'
            $query->set( 'page', 1 );
            $query->set( 'paged', $page );
        }
        // prevent redirect
        remove_action( 'template_redirect', 'redirect_canonical' );
    }
}, 0 );

//returns agent properties
function ns_real_estate_get_agent_properties($agent_id, $posts_per_page = null, $pagination = false) {
    $agent_properties = array(); 
    $args = array(
        'post_type' => 'ns-property',
        'meta_query' => array(
            'relation' => 'AND',
            array('key' => 'ns_agent_display', 'value' => 'agent'),
            array('key' => 'ns_agent_select', 'value' => $agent_id),
        ),
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


/*-----------------------------------------------------------------------------------*/
/*  Agents Custom Post Type
/*-----------------------------------------------------------------------------------*/
add_action( 'init', 'ns_real_estate_create_agents_post_type' );
function ns_real_estate_create_agents_post_type() {
    $agents_slug = get_option('ns_agent_detail_slug', 'agents');
    register_post_type( 'ns-agent',
        array(
            'labels' => array(
                'name' => __( 'Agents', 'ns-real-estate' ),
                'singular_name' => __( 'Agent', 'ns-real-estate' ),
                'add_new_item' => __( 'Add New Agent', 'ns-real-estate' ),
                'search_items' => __( 'Search Agents', 'ns-real-estate' ),
                'edit_item' => __( 'Edit Agent', 'ns-real-estate' ),
            ),
        'public' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-groups',
        'has_archive' => false,
        'supports' => array('title', 'editor', 'thumbnail', 'page_attributes'),
        'rewrite' => array('slug' => $agents_slug),
        )
    );
}

 /* Add Agent details (meta box) */ 
 function ns_real_estate_add_agent_details_meta_box() {
    add_meta_box( 'agent-details-meta-box', 'Agent Details', 'ns_real_estate_agent_details', 'ns-agent', 'normal', 'high' );
 }
add_action( 'add_meta_boxes', 'ns_real_estate_add_agent_details_meta_box' );

function ns_real_estate_agent_details($post) {
    $agent_details_values = get_post_custom( $post->ID );
    $agent_title = isset( $agent_details_values['ns_agent_title'] ) ? esc_attr( $agent_details_values['ns_agent_title'][0] ) : '';
    $agent_email = isset( $agent_details_values['ns_agent_email'] ) ? esc_attr( $agent_details_values['ns_agent_email'][0] ) : '';
    $agent_mobile_phone = isset( $agent_details_values['ns_agent_mobile_phone'] ) ? esc_attr( $agent_details_values['ns_agent_mobile_phone'][0] ) : '';
    $agent_office_phone = isset( $agent_details_values['ns_agent_office_phone'] ) ? esc_attr( $agent_details_values['ns_agent_office_phone'][0] ) : '';
    $agent_description = isset( $agent_details_values['ns_agent_description'] ) ? $agent_details_values['ns_agent_description'][0] : '';
    $agent_fb = isset( $agent_details_values['ns_agent_fb'] ) ? esc_attr( $agent_details_values['ns_agent_fb'][0] ) : '';
    $agent_twitter = isset( $agent_details_values['ns_agent_twitter'] ) ? esc_attr( $agent_details_values['ns_agent_twitter'][0] ) : '';
    $agent_google = isset( $agent_details_values['ns_agent_google'] ) ? esc_attr( $agent_details_values['ns_agent_google'][0] ) : '';
    $agent_linkedin = isset( $agent_details_values['ns_agent_linkedin'] ) ? esc_attr( $agent_details_values['ns_agent_linkedin'][0] ) : '';
    $agent_youtube = isset( $agent_details_values['ns_agent_youtube'] ) ? esc_attr( $agent_details_values['ns_agent_youtube'][0] ) : '';
    $agent_instagram = isset( $agent_details_values['ns_agent_instagram'] ) ? esc_attr( $agent_details_values['ns_agent_instagram'][0] ) : '';
    $agent_form_source = isset( $agent_details_values['ns_agent_form_source'] ) ? esc_attr( $agent_details_values['ns_agent_form_source'][0] ) : 'default';
    $agent_form_id = isset( $agent_details_values['ns_agent_form_id'] ) ? esc_attr( $agent_details_values['ns_agent_form_id'][0] ) : '';
    wp_nonce_field( 'ns_agent_details_meta_box_nonce', 'ns_agent_details_meta_box_nonce' );
    ?>

    <div class="ns-tabs meta-box-form meta-box-form-agent">

        <ul class="ns-tabs-nav">
            <li><a href="#general"><i class="fa fa-user"></i> <span class="tab-text"><?php esc_html_e('General Info', 'ns-real-estate'); ?></span></a></li>
            <li><a href="#description"><i class="fa fa-pencil-alt"></i> <span class="tab-text"><?php esc_html_e('Description', 'ns-real-estate'); ?></span></a></li>
            <li><a href="#social"><i class="fa fa-share-alt"></i> <span class="tab-text"><?php esc_html_e('Social', 'ns-real-estate'); ?></span></a></li>
            <li><a href="#contact"><i class="fa fa-envelope"></i> <span class="tab-text"><?php esc_html_e('Contact Form', 'ns-real-estate'); ?></span></a></li>
            <li><a href="#properties"><i class="fa fa-home"></i> <span class="tab-text"><?php esc_html_e('Properties', 'ns-real-estate'); ?></span></a></li>
            <?php do_action('ns_real_estate_after_agent_detail_tabs'); ?>
        </ul>

        <div class="ns-tabs-content">
        <div class="tab-loader"><img src="<?php echo esc_url(home_url('/')); ?>wp-admin/images/spinner.gif" alt="" /> <?php esc_html_e('Loading...', 'ns-real-estate'); ?></div>

        <!--*************************************************-->
        <!-- GENERAL INFO -->
        <!--*************************************************-->
        <div id="general" class="tab-content">
            <h3><?php esc_html_e('General Info', 'ns-real-estate'); ?></h3>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Job Title', 'ns-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide the agents job title.', 'ns-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="text" name="ns_agent_title" id="agent_title" value="<?php echo $agent_title; ?>" />
                    </td>
                </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Email', 'ns-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide the agents email address. This address will be used for the agent contact form.', 'ns-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="email" name="ns_agent_email" id="agent_email" value="<?php echo $agent_email; ?>" />
                    </td>
                </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Mobile Phone', 'ns-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide the agents mobile phone number', 'ns-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="text" name="ns_agent_mobile_phone" id="agent_mobile_phone" value="<?php echo $agent_mobile_phone; ?>" />
                    </td>
                </tr>
            </table>

            <table class="admin-module no-border">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Office Phone', 'ns-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide the agents office phone number', 'ns-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="text" name="ns_agent_office_phone" id="agent_office_phone" value="<?php echo $agent_office_phone; ?>" />
                    </td>
                </tr>
            </table>

            <?php do_action('ns_real_estate_after_agent_details_general', $values); ?>

        </div>

        <!--*************************************************-->
        <!-- DESCRIPTION -->
        <!--*************************************************-->
        <div id="description" class="tab-content">
            <h3><?php echo esc_html_e('Description', 'ns-real-estate'); ?></h3>
            <?php 
            $editor_id = 'agentdescription';
            $settings = array('textarea_name' => 'ns_agent_description', 'editor_height' => 180);
            wp_editor( $agent_description, $editor_id, $settings);
            ?>
        </div>

        <!--*************************************************-->
        <!-- SOCIAL -->
        <!--*************************************************-->
        <div id="social" class="tab-content">
            <h3><?php esc_html_e('Social', 'ns-real-estate'); ?></h3>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Facebook', 'ns-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide a url for the agents Facebook profile', 'ns-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="text" name="ns_agent_fb" id="agent_fb" value="<?php echo $agent_fb; ?>" />
                    </td>
                </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Twitter', 'ns-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide a url for the agents Twitter profile', 'ns-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="text" name="ns_agent_twitter" id="agent_twitter" value="<?php echo $agent_twitter; ?>" />
                    </td>
                </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Google Plus', 'ns-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide a url for the agents Google Plus profile', 'ns-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="text" name="ns_agent_google" id="agent_google" value="<?php echo $agent_google; ?>" />
                    </td>
                </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Linkedin', 'ns-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide a url for the agents Linkedin profile', 'ns-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="text" name="ns_agent_linkedin" id="agent_linkedin" value="<?php echo $agent_linkedin; ?>" />
                    </td>
                </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Youtube', 'ns-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide a url for the agents Youtube profile', 'ns-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="text" name="ns_agent_youtube" id="agent_youtube" value="<?php echo $agent_youtube; ?>" />
                    </td>
                </tr>
            </table>

            <table class="admin-module no-border">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Instagram', 'ns-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide a url for the agents Instagram profile', 'ns-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="text" name="ns_agent_instagram" id="agent_instagram" value="<?php echo $agent_instagram; ?>" />
                    </td>
                </tr>
            </table>
        </div>

        <!--*************************************************-->
        <!-- CONTACT -->
        <!--*************************************************-->
        <div id="contact" class="tab-content">
            <h3><?php esc_html_e('Contact', 'ns-real-estate'); ?></h3>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label"><label><?php esc_html_e('Agent Contact Form Source:', 'ns-real-estate'); ?></label></td>
                    <td class="admin-module-field">
                        <p><input type="radio" id="agent_form_source" name="ns_agent_form_source" value="default" <?php if(esc_attr($agent_form_source) == 'default') { echo 'checked'; } ?> /><?php esc_html_e('Default Theme Form', 'ns-real-estate'); ?></p>
                        <p><input type="radio" id="agent_form_source_contact_7" name="ns_agent_form_source" value="contact-form-7" <?php if(esc_attr($agent_form_source) == 'contact-form-7') { echo 'checked'; } ?> /><?php esc_html_e('Contact Form 7', 'ns-real-estate'); ?></p>
                    </td>
                </tr>
            </table>

            <table class="admin-module admin-module-agent-form-id hide-soft no-border" <?php if($agent_form_source == 'contact-form-7') { echo 'style="display:block;"'; } ?>>
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Contact From 7 ID', 'ns-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide the ID of the contact form you would like displayed', 'ns-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <?php 
                        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                        if( is_plugin_active('contact-form-7/wp-contact-form-7.php') ) { ?>
                            <input type="number" min="0" name="ns_agent_form_id" value="<?php echo $agent_form_id; ?>" />
                        <?php } else {
                            echo '<i>You need to install and/or activate the <a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">Contact Form 7</a> plugin.</i>';
                        } ?>
                    </td>
                </tr>
            </table>
        </div><!-- end agent contact -->

        <!--*************************************************-->
        <!-- AGENT PROPERTIES -->
        <!--*************************************************-->
        <div id="properties" class="tab-content">
            <h3><?php esc_html_e('Agent Properties', 'ns-real-estate'); ?></h3>

            <?php
            $agent_properties = ns_real_estate_get_agent_properties(get_the_id(), 2, true);
            $agent_properties_query = $agent_properties['properties'];
            ?>

            <p><?php echo $agent_properties['count']; ?> <?php esc_html_e('total properties found', 'ns-real-estate'); ?></p>

            <table class="admin-table">
                <tr>
                    <th><?php esc_html_e('Property ID', 'ns-real-estate'); ?></th>
                    <th><?php esc_html_e('Title', 'ns-real-estate'); ?></th>
                    <th><?php esc_html_e('Status', 'ns-real-estate'); ?></th>
                    <th><?php esc_html_e('Date Published', 'ns-real-estate'); ?></th>
                    <th><?php esc_html_e('Actions', 'ns-real-estate'); ?></th>
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
                    $big = 999999999; // need an unlikely integer
                    $paged = isset($_GET['paged']) ? $_GET['paged'] : 1;
                    $pagination_args = array(
                        //'base'         => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                        'base'         => '%_%#properties',
                        'format'       => '?paged=%#%',
                        'total'        => $agent_properties_query->max_num_pages,
                        'current'      => max( 1, $paged ),
                        'show_all'     => true,
                        'prev_next'    => True,
                        'prev_text'    => esc_html__('&raquo; Previous', 'ns-real-estate'),
                        'next_text'    => esc_html__('Next &raquo;', 'ns-real-estate'),
                    ); 
                    echo '<tr><td colspan="5">'.paginate_links($pagination_args).'</td></tr>';
                else:
                    echo '<tr><td colspan="5">'.esc_html__('This agent has no assigned properties.', 'ns-real-estate').'</td></tr>';
                endif; ?>
            </table>
        </div>

        <!--*************************************************-->
        <!-- ADD-ONS -->
        <!--*************************************************-->
        <?php do_action('ns_real_estate_after_agent_details_tab_content', $values); ?>

        </div><!-- end tabs content -->
        <div class="clear"></div>
    </div>

    <?php
}

/* Save agent details */
add_action( 'save_post', 'ns_real_estate_save_agent_details_meta_box' );
function ns_real_estate_save_agent_details_meta_box( $post_id )
{
    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['ns_agent_details_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['ns_agent_details_meta_box_nonce'], 'ns_agent_details_meta_box_nonce' ) ) return;

    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post', $post_id ) ) return;

    // save the data
    $allowed = array(
        'a' => array( // on allow a tags
            'href' => array() // and those anchors can only have href attribute
        )
    );

    // make sure data is set before saving
    if( isset( $_POST['ns_agent_title'] ) )
        update_post_meta( $post_id, 'ns_agent_title', wp_kses( $_POST['ns_agent_title'], $allowed ) );

    if( isset( $_POST['ns_agent_email'] ) )
        update_post_meta( $post_id, 'ns_agent_email', wp_kses( $_POST['ns_agent_email'], $allowed ) );

    if( isset( $_POST['ns_agent_mobile_phone'] ) )
        update_post_meta( $post_id, 'ns_agent_mobile_phone', wp_kses( $_POST['ns_agent_mobile_phone'], $allowed ) );

    if( isset( $_POST['ns_agent_office_phone'] ) )
        update_post_meta( $post_id, 'ns_agent_office_phone', wp_kses( $_POST['ns_agent_office_phone'], $allowed ) );

    if( isset( $_POST['ns_agent_description'] ) )
        update_post_meta( $post_id, 'ns_agent_description', wp_kses_post($_POST['ns_agent_description']) );

    if( isset( $_POST['ns_agent_fb'] ) )
        update_post_meta( $post_id, 'ns_agent_fb', wp_kses( $_POST['ns_agent_fb'], $allowed ) );

    if( isset( $_POST['ns_agent_twitter'] ) )
        update_post_meta( $post_id, 'ns_agent_twitter', wp_kses( $_POST['ns_agent_twitter'], $allowed ) );

    if( isset( $_POST['ns_agent_google'] ) )
        update_post_meta( $post_id, 'ns_agent_google', wp_kses( $_POST['ns_agent_google'], $allowed ) );

    if( isset( $_POST['ns_agent_linkedin'] ) )
        update_post_meta( $post_id, 'ns_agent_linkedin', wp_kses( $_POST['ns_agent_linkedin'], $allowed ) );

    if( isset( $_POST['ns_agent_youtube'] ) )
        update_post_meta( $post_id, 'ns_agent_youtube', wp_kses( $_POST['ns_agent_youtube'], $allowed ) );

    if( isset( $_POST['ns_agent_instagram'] ) )
        update_post_meta( $post_id, 'ns_agent_instagram', wp_kses( $_POST['ns_agent_instagram'], $allowed ) );

    if( isset( $_POST['ns_agent_form_source'] ) )
        update_post_meta( $post_id, 'ns_agent_form_source', wp_kses( $_POST['ns_agent_form_source'], $allowed ) );

    if( isset( $_POST['ns_agent_form_id'] ) )
        update_post_meta( $post_id, 'ns_agent_form_id', wp_kses( $_POST['ns_agent_form_id'], $allowed ) );

    do_action('ns_real_estate_save_agent_details', $post_id);

}

/*-----------------------------------------------------------------------------------*/
/*  Add Agent Listing Image Size
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_add_agent_image_size() {
    add_image_size( 'agent-thumbnail', 800, 600, array( 'center', 'center' ) );
}
add_action( 'ns_core_theme_support', 'ns_real_estate_add_agent_image_size' );

/*-----------------------------------------------------------------------------------*/
/*  Add Page Settings Metabox to Edit Agent Page
/*-----------------------------------------------------------------------------------*/
if(function_exists('ns_basics_is_active') && ns_basics_is_active('ns_basics_page_settings')) {
    function ns_real_estate_agents_add_page_settings_metabox() {
        add_meta_box( 'page-layout-meta-box', 'Page Settings', 'ns_basics_page_layout_meta_box', array('ns-agent'), 'normal', 'low' );
    }
    add_action('init', 'ns_real_estate_agents_add_page_settings_metabox');
}

/*-----------------------------------------------------------------------------------*/
/*  Agent Contact Form
/*-----------------------------------------------------------------------------------*/
function ns_real_estate_agent_contact_form($agent_email) {

    $site_title = get_bloginfo('name');
    $agent_form_submit_text = esc_attr(get_option('ns_agent_form_submit_text', esc_html__('Contact Agent', 'ns-real-estate')) );
    $agent_form_success = esc_attr(get_option('ns_agent_form_success', esc_html__('Thanks! Your email has been delivered!', 'ns-real-estate')));
    
    if(is_singular('ns-property')) {
        $agent_form_message_placeholder = esc_attr(get_option('ns_agent_form_message_placeholder', esc_html__('I am interested in this property and would like to know more.', 'ns-real-estate')) );
    } else {
        $agent_form_message_placeholder =  esc_html__( 'Message', 'ns-real-estate' );
    }
    
    $nameError = '';
    $emailError = '';
    $commentError = '';

    //If the form is submitted
    if(isset($_POST['submitted'])) {
      
      // require a name from user
      if(trim($_POST['agent-contact-name']) === '') {
        $nameError =  esc_html__('Forgot your name!', 'ns-real-estate'); 
        $hasError = true;
      } else {
        $agent_contact_name = trim($_POST['agent-contact-name']);
      }
      
      // need valid email
      if(trim($_POST['agent-contact-email']) === '')  {
        $emailError = esc_html__('Forgot to enter in your e-mail address.', 'ns-real-estate');
        $hasError = true;
      } else if (!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", trim($_POST['agent-contact-email']))) {
        $emailError = 'You entered an invalid email address.';
        $hasError = true;
      } else {
        $agent_contact_email = trim($_POST['agent-contact-email']);
      }
        
      // we need at least some content
      if(trim($_POST['agent-contact-message']) === '') {
        $commentError = esc_html__('You forgot to enter a message!', 'ns-real-estate');
        $hasError = true;
      } else {
        if(function_exists('stripslashes')) {
          $agent_contact_message = stripslashes(trim($_POST['agent-contact-message']));
        } else {
          $agent_contact_message = trim($_POST['agent-contact-message']);
        }
      }
        
      // upon no failure errors let's email now!
      if(!isset($hasError)) {

        /*---------------------------------------------------------*/
        /* SET EMAIL YOUR EMAIL ADDRESS HERE                       */
        /*---------------------------------------------------------*/
        $emailTo = $agent_email;
        $subject = 'Submitted message from '.$agent_contact_name;
        $sendCopy = trim($_POST['sendCopy']);
        $formUrl = $_POST['current_url'];
        $body = "This message was sent from a contact from on: $formUrl \n\n Name: $agent_contact_name \n\nEmail: $agent_contact_email \n\nMessage: $agent_contact_message";
        $headers = 'From: ' .$site_title.' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $agent_contact_email;

        mail($emailTo, $subject, $body, $headers);
            
        // set our boolean completion value to TRUE
        $emailSent = true;
      }
    }

    ?>

    <form id="agent-contact-form" class="contact-form agent-contact-form" method="post">

        <div class="alert-box success <?php if($emailSent) { echo 'show'; } else { echo 'hide'; } ?>"><?php echo $agent_form_success; ?></div>

        <div class="contact-form-fields">
            <div>
                <?php if($nameError != '') { ?><div class="alert-box error"><?php echo $nameError;?></div> <?php } ?>
                <input type="text" name="agent-contact-name" placeholder="<?php esc_html_e( 'Name', 'ns-real-estate' ); ?>*" value="<?php if(isset($agent_contact_name)){ echo $agent_contact_name; } ?>" class="border requiredField" />
            </div>

            <div>
                <?php if($emailError != '') { ?><div class="alert-box error"><?php echo $emailError;?></div> <?php } ?>
                <input type="email" name="agent-contact-email" placeholder="<?php esc_html_e( 'Email', 'ns-real-estate' ); ?>*" value="<?php if(isset($agent_contact_email)) { echo $agent_contact_email; } ?>" class="border requiredField email" />
            </div>

            <div>
                <?php if($commentError != '') { ?><div class="alert-box error"><?php echo $commentError;?></div> <?php } ?>
                <textarea name="agent-contact-message" class="border"><?php if(isset($agent_contact_message)) { echo $agent_contact_message; } else { echo $agent_form_message_placeholder; } ?></textarea>
            </div>

            <div>
                <?php $current_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>
                <input type="hidden" name="current_url" value="<?php echo $current_url; ?>" />
                <input type="hidden" name="submitted" id="submitted" value="true" />
                <input type="submit" name="submit" value="<?php echo $agent_form_submit_text; ?>" />
                <div class="form-loader"><img src="<?php echo esc_url(home_url('/')); ?>wp-admin/images/spinner.gif" alt="" /> <?php esc_html_e( 'Loading...', 'ns-real-estate' ); ?></div>
            </div>
        </div>
    </form>
    
<?php }

?>