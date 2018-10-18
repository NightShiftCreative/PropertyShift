<?php

/*-----------------------------------------------------------------------------------*/
/*  Global Agent Functions
/*-----------------------------------------------------------------------------------*/

//rewrite for agents page url conflict
function rype_real_estate_agents_rewrite_rule() {
    add_rewrite_rule('^agents/page/([0-9]+)','index.php?pagename=agents&paged=$matches[1]', 'top');
}
add_action('init', 'rype_real_estate_agents_rewrite_rule');

//allow pagination on agent single page
add_action( 'template_redirect', function() {
    if ( is_singular( 'rype-agent' ) ) {
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


/*-----------------------------------------------------------------------------------*/
/*  Agents Custom Post Type
/*-----------------------------------------------------------------------------------*/
add_action( 'init', 'rype_real_estate_create_agents_post_type' );
function rype_real_estate_create_agents_post_type() {
    $agents_slug = get_option('ns_agent_detail_slug', 'agents');
    register_post_type( 'rype-agent',
        array(
            'labels' => array(
                'name' => __( 'Agents', 'rype-real-estate' ),
                'singular_name' => __( 'Agent', 'rype-real-estate' ),
                'add_new_item' => __( 'Add New Agent', 'rype-real-estate' ),
                'search_items' => __( 'Search Agents', 'rype-real-estate' ),
                'edit_item' => __( 'Edit Agent', 'rype-real-estate' ),
            ),
        'public' => true,
        'show_in_menu' => true,
        'has_archive' => false,
        'supports' => array('title', 'editor', 'thumbnail', 'page_attributes'),
        'rewrite' => array('slug' => $agents_slug),
        )
    );
}

 /* Add Agent details (meta box) */ 
 function rype_real_estate_add_agent_details_meta_box() {
    add_meta_box( 'agent-details-meta-box', 'Agent Details', 'rype_real_estate_agent_details', 'rype-agent', 'normal', 'high' );
 }
add_action( 'add_meta_boxes', 'rype_real_estate_add_agent_details_meta_box' );

function rype_real_estate_agent_details($post) {
    $agent_details_values = get_post_custom( $post->ID );
    $agent_title = isset( $agent_details_values['rypecore_agent_title'] ) ? esc_attr( $agent_details_values['rypecore_agent_title'][0] ) : '';
    $agent_email = isset( $agent_details_values['rypecore_agent_email'] ) ? esc_attr( $agent_details_values['rypecore_agent_email'][0] ) : '';
    $agent_mobile_phone = isset( $agent_details_values['rypecore_agent_mobile_phone'] ) ? esc_attr( $agent_details_values['rypecore_agent_mobile_phone'][0] ) : '';
    $agent_office_phone = isset( $agent_details_values['rypecore_agent_office_phone'] ) ? esc_attr( $agent_details_values['rypecore_agent_office_phone'][0] ) : '';
    $agent_fb = isset( $agent_details_values['rypecore_agent_fb'] ) ? esc_attr( $agent_details_values['rypecore_agent_fb'][0] ) : '';
    $agent_twitter = isset( $agent_details_values['rypecore_agent_twitter'] ) ? esc_attr( $agent_details_values['rypecore_agent_twitter'][0] ) : '';
    $agent_google = isset( $agent_details_values['rypecore_agent_google'] ) ? esc_attr( $agent_details_values['rypecore_agent_google'][0] ) : '';
    $agent_linkedin = isset( $agent_details_values['rypecore_agent_linkedin'] ) ? esc_attr( $agent_details_values['rypecore_agent_linkedin'][0] ) : '';
    $agent_youtube = isset( $agent_details_values['rypecore_agent_youtube'] ) ? esc_attr( $agent_details_values['rypecore_agent_youtube'][0] ) : '';
    $agent_instagram = isset( $agent_details_values['rypecore_agent_instagram'] ) ? esc_attr( $agent_details_values['rypecore_agent_instagram'][0] ) : '';
    $agent_form_source = isset( $agent_details_values['rypecore_agent_form_source'] ) ? esc_attr( $agent_details_values['rypecore_agent_form_source'][0] ) : 'default';
    $agent_form_id = isset( $agent_details_values['rypecore_agent_form_id'] ) ? esc_attr( $agent_details_values['rypecore_agent_form_id'][0] ) : '';
    wp_nonce_field( 'rypecore_agent_details_meta_box_nonce', 'rypecore_agent_details_meta_box_nonce' );
    ?>

    <div id="tabs" class="meta-box-form meta-box-form-agent ui-tabs">

        <ul class="ui-tabs-nav">
            <li><a href="#general"><i class="fa fa-user"></i> <span class="tab-text"><?php esc_html_e('General Info', 'rype-real-estate'); ?></span></a></li>
            <li><a href="#social"><i class="fa fa-share-alt"></i> <span class="tab-text"><?php esc_html_e('Social', 'rype-real-estate'); ?></span></a></li>
            <li><a href="#contact"><i class="fa fa-envelope"></i> <span class="tab-text"><?php esc_html_e('Contact Form', 'rype-real-estate'); ?></span></a></li>
        </ul>

        <div class="tab-loader"><img src="<?php echo esc_url(home_url('/')); ?>wp-admin/images/spinner.gif" alt="" /> <?php esc_html_e('Loading...', 'rype-real-estate'); ?></div>

        <!--*************************************************-->
        <!-- GENERAL INFO -->
        <!--*************************************************-->
        <div id="general" class="tab-content">
            <h3><?php esc_html_e('General Info', 'rype-real-estate'); ?></h3>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Job Title', 'rype-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide the agents job title.', 'rype-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="text" name="rypecore_agent_title" id="agent_title" value="<?php echo $agent_title; ?>" />
                    </td>
                </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Email', 'rype-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide the agents email address. This address will be used for the agent contact form.', 'rype-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="email" name="rypecore_agent_email" id="agent_email" value="<?php echo $agent_email; ?>" />
                    </td>
                </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Mobile Phone', 'rype-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide the agents mobile phone number', 'rype-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="text" name="rypecore_agent_mobile_phone" id="agent_mobile_phone" value="<?php echo $agent_mobile_phone; ?>" />
                    </td>
                </tr>
            </table>

            <table class="admin-module no-border">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Office Phone', 'rype-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide the agents office phone number', 'rype-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="text" name="rypecore_agent_office_phone" id="agent_office_phone" value="<?php echo $agent_office_phone; ?>" />
                    </td>
                </tr>
            </table>

        </div>

        <!--*************************************************-->
        <!-- SOCIAL -->
        <!--*************************************************-->
        <div id="social" class="tab-content">
            <h3><?php esc_html_e('Social', 'rype-real-estate'); ?></h3>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Facebook', 'rype-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide a url for the agents Facebook profile', 'rype-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="text" name="rypecore_agent_fb" id="agent_fb" value="<?php echo $agent_fb; ?>" />
                    </td>
                </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Twitter', 'rype-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide a url for the agents Twitter profile', 'rype-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="text" name="rypecore_agent_twitter" id="agent_twitter" value="<?php echo $agent_twitter; ?>" />
                    </td>
                </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Google Plus', 'rype-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide a url for the agents Google Plus profile', 'rype-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="text" name="rypecore_agent_google" id="agent_google" value="<?php echo $agent_google; ?>" />
                    </td>
                </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Linkedin', 'rype-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide a url for the agents Linkedin profile', 'rype-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="text" name="rypecore_agent_linkedin" id="agent_linkedin" value="<?php echo $agent_linkedin; ?>" />
                    </td>
                </tr>
            </table>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Youtube', 'rype-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide a url for the agents Youtube profile', 'rype-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="text" name="rypecore_agent_youtube" id="agent_youtube" value="<?php echo $agent_youtube; ?>" />
                    </td>
                </tr>
            </table>

            <table class="admin-module no-border">
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Instagram', 'rype-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide a url for the agents Instagram profile', 'rype-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <input type="text" name="rypecore_agent_instagram" id="agent_instagram" value="<?php echo $agent_instagram; ?>" />
                    </td>
                </tr>
            </table>
        </div>

        <!--*************************************************-->
        <!-- CONTACT -->
        <!--*************************************************-->
        <div id="contact" class="tab-content">
            <h3><?php esc_html_e('Contact', 'rype-real-estate'); ?></h3>

            <table class="admin-module">
                <tr>
                    <td class="admin-module-label"><label><?php esc_html_e('Agent Contact Form Source:', 'rype-real-estate'); ?></label></td>
                    <td class="admin-module-field">
                        <p><input type="radio" id="agent_form_source" name="rypecore_agent_form_source" value="default" <?php if(esc_attr($agent_form_source) == 'default') { echo 'checked'; } ?> /><?php esc_html_e('Default Theme Form', 'rype-real-estate'); ?></p>
                        <p><input type="radio" id="agent_form_source_contact_7" name="rypecore_agent_form_source" value="contact-form-7" <?php if(esc_attr($agent_form_source) == 'contact-form-7') { echo 'checked'; } ?> /><?php esc_html_e('Contact Form 7', 'rype-real-estate'); ?></p>
                    </td>
                </tr>
            </table>

            <table class="admin-module admin-module-agent-form-id hide-soft no-border" <?php if($agent_form_source == 'contact-form-7') { echo 'style="display:block;"'; } ?>>
                <tr>
                    <td class="admin-module-label">
                        <label><?php esc_html_e('Contact From 7 ID', 'rype-real-estate'); ?></label>
                        <span class="admin-module-note"><?php esc_html_e('Provide the ID of the contact form you would like displayed', 'rype-real-estate'); ?></span>
                    </td>
                    <td class="admin-module-field">
                        <?php 
                        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                        if( is_plugin_active('contact-form-7/wp-contact-form-7.php') ) { ?>
                            <input type="number" min="0" name="rypecore_agent_form_id" value="<?php echo $agent_form_id; ?>" />
                        <?php } else {
                            echo '<i>You need to install and/or activate the <a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">Contact Form 7</a> plugin.</i>';
                        } ?>
                    </td>
                </tr>
            </table>
        </div><!-- end agent contact -->

        <div class="clear"></div>
    </div>

    <?php
}

/* Save agent details */
add_action( 'save_post', 'rype_real_estate_save_agent_details_meta_box' );
function rype_real_estate_save_agent_details_meta_box( $post_id )
{
    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['rypecore_agent_details_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['rypecore_agent_details_meta_box_nonce'], 'rypecore_agent_details_meta_box_nonce' ) ) return;

    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post', $post_id ) ) return;

    // save the data
    $allowed = array(
        'a' => array( // on allow a tags
            'href' => array() // and those anchors can only have href attribute
        )
    );

    // make sure data is set before saving
    if( isset( $_POST['rypecore_agent_title'] ) )
        update_post_meta( $post_id, 'rypecore_agent_title', wp_kses( $_POST['rypecore_agent_title'], $allowed ) );

    if( isset( $_POST['rypecore_agent_email'] ) )
        update_post_meta( $post_id, 'rypecore_agent_email', wp_kses( $_POST['rypecore_agent_email'], $allowed ) );

    if( isset( $_POST['rypecore_agent_mobile_phone'] ) )
        update_post_meta( $post_id, 'rypecore_agent_mobile_phone', wp_kses( $_POST['rypecore_agent_mobile_phone'], $allowed ) );

    if( isset( $_POST['rypecore_agent_office_phone'] ) )
        update_post_meta( $post_id, 'rypecore_agent_office_phone', wp_kses( $_POST['rypecore_agent_office_phone'], $allowed ) );

    if( isset( $_POST['rypecore_agent_fb'] ) )
        update_post_meta( $post_id, 'rypecore_agent_fb', wp_kses( $_POST['rypecore_agent_fb'], $allowed ) );

    if( isset( $_POST['rypecore_agent_twitter'] ) )
        update_post_meta( $post_id, 'rypecore_agent_twitter', wp_kses( $_POST['rypecore_agent_twitter'], $allowed ) );

    if( isset( $_POST['rypecore_agent_google'] ) )
        update_post_meta( $post_id, 'rypecore_agent_google', wp_kses( $_POST['rypecore_agent_google'], $allowed ) );

    if( isset( $_POST['rypecore_agent_linkedin'] ) )
        update_post_meta( $post_id, 'rypecore_agent_linkedin', wp_kses( $_POST['rypecore_agent_linkedin'], $allowed ) );

    if( isset( $_POST['rypecore_agent_youtube'] ) )
        update_post_meta( $post_id, 'rypecore_agent_youtube', wp_kses( $_POST['rypecore_agent_youtube'], $allowed ) );

    if( isset( $_POST['rypecore_agent_instagram'] ) )
        update_post_meta( $post_id, 'rypecore_agent_instagram', wp_kses( $_POST['rypecore_agent_instagram'], $allowed ) );

    if( isset( $_POST['rypecore_agent_form_source'] ) )
        update_post_meta( $post_id, 'rypecore_agent_form_source', wp_kses( $_POST['rypecore_agent_form_source'], $allowed ) );

    if( isset( $_POST['rypecore_agent_form_id'] ) )
        update_post_meta( $post_id, 'rypecore_agent_form_id', wp_kses( $_POST['rypecore_agent_form_id'], $allowed ) );

}

/*-----------------------------------------------------------------------------------*/
/*  Add Agent Listing Image Size
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_add_agent_image_size() {
    add_image_size( 'agent-thumbnail', 800, 600, array( 'center', 'center' ) );
}
add_action( 'ns_basics_theme_support', 'rype_real_estate_add_agent_image_size' );

/*-----------------------------------------------------------------------------------*/
/*  Add Page Settings Metabox to Edit Agent Page
/*-----------------------------------------------------------------------------------*/
if(function_exists('ns_basics_is_active') && ns_basics_is_active('ns_basics_page_settings')) {
    function rype_real_estate_agents_add_page_settings_metabox() {
        add_meta_box( 'page-layout-meta-box', 'Page Settings', 'ns_basics_page_layout_meta_box', array('rype-agent'), 'normal', 'low' );
    }
    add_action('init', 'rype_real_estate_agents_add_page_settings_metabox');
}

/*-----------------------------------------------------------------------------------*/
/*  Agent Contact Form
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_agent_contact_form($agent_email) {

    $site_title = get_bloginfo('name');
    $agent_form_submit_text = esc_attr(get_option('ns_agent_form_submit_text', esc_html__('Contact Agent', 'rype-real-estate')) );
    $agent_form_success = esc_attr(get_option('ns_agent_form_success', esc_html__('Thanks! Your email has been delivered!', 'rype-real-estate')));
    
    if(is_singular('rype-property')) {
        $agent_form_message_placeholder = esc_attr(get_option('ns_agent_form_message_placeholder', esc_html__('I am interested in this property and would like to know more.', 'rype-real-estate')) );
    } else {
        $agent_form_message_placeholder =  esc_html__( 'Message', 'rype-real-estate' );
    }
    
    $nameError = '';
    $emailError = '';
    $commentError = '';

    //If the form is submitted
    if(isset($_POST['submitted'])) {
      
      // require a name from user
      if(trim($_POST['agent-contact-name']) === '') {
        $nameError =  esc_html__('Forgot your name!', 'rype-real-estate'); 
        $hasError = true;
      } else {
        $agent_contact_name = trim($_POST['agent-contact-name']);
      }
      
      // need valid email
      if(trim($_POST['agent-contact-email']) === '')  {
        $emailError = esc_html__('Forgot to enter in your e-mail address.', 'rype-real-estate');
        $hasError = true;
      } else if (!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", trim($_POST['agent-contact-email']))) {
        $emailError = 'You entered an invalid email address.';
        $hasError = true;
      } else {
        $agent_contact_email = trim($_POST['agent-contact-email']);
      }
        
      // we need at least some content
      if(trim($_POST['agent-contact-message']) === '') {
        $commentError = esc_html__('You forgot to enter a message!', 'rype-real-estate');
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
                <input type="text" name="agent-contact-name" placeholder="<?php esc_html_e( 'Name', 'rype-real-estate' ); ?>*" value="<?php if(isset($agent_contact_name)){ echo $agent_contact_name; } ?>" class="border requiredField" />
            </div>

            <div>
                <?php if($emailError != '') { ?><div class="alert-box error"><?php echo $emailError;?></div> <?php } ?>
                <input type="email" name="agent-contact-email" placeholder="<?php esc_html_e( 'Email', 'rype-real-estate' ); ?>*" value="<?php if(isset($agent_contact_email)) { echo $agent_contact_email; } ?>" class="border requiredField email" />
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
                <div class="form-loader"><img src="<?php echo esc_url(home_url('/')); ?>wp-admin/images/spinner.gif" alt="" /> <?php esc_html_e( 'Loading...', 'rype-real-estate' ); ?></div>
            </div>
        </div>
    </form>
    
<?php }

?>