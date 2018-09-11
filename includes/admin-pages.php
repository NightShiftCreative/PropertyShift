<?php

add_action('admin_menu', 'rype_real_estate_plugin_menu');
function rype_real_estate_plugin_menu() {
    add_menu_page('Rype Real Estate', 'Rype Real Estate', 'administrator', 'rype-real-estate-settings', 'rype_real_estate_settings_page', 'dashicons-admin-home');
    add_submenu_page('rype-real-estate-settings', 'Add-Ons', 'Add-Ons', 'administrator', 'rype-real-estate-add-ons', 'rype_real_estate_add_ons_page');
    add_submenu_page('rype-real-estate-settings', 'Help', 'Help', 'administrator', 'rype-real-estate-help', 'rype_real_estate_help_page');
    add_action( 'admin_init', 'rype_real_estate_register_options' );
}

/*-----------------------------------------------------------------------------------*/
/*  REGISTER SETTINGS
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_register_options() {
    register_setting( 'rype-real-estate-settings-group', 'properties_page');
}

/*-----------------------------------------------------------------------------------*/
/*  OUTPUT SETTINGS PAGE
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_settings_page() { ?>

    <div class="wrap">
        <h1><?php esc_html_e('Rype Real Estate', 'rype-real-estate'); ?></h1>

        <div class="rype-settings">

            <div class="rype-settings-menu-bar rype-settings-header">
                <div class="rype-settings-nav">
                    <ul>
                        <li class="active"><a href="?page=rype-real-estate-settings"><?php esc_html_e('Settings', 'rype-real-estate'); ?></a></li>
                        <li><a href="?page=rype-real-estate-add-ons"><?php esc_html_e('Add-Ons', 'rype-real-estate'); ?></a></li>
                        <li><a href="?page=rype-real-estate-help"><?php esc_html_e('Help', 'rype-real-estate'); ?></a></li>
                    </ul>
                </div>
                <div class="rype-settings-actions"><?php submit_button(esc_html__('Save Changes', 'rype-real-estate'), 'admin-button', 'submit', false); ?></div>
                <div class="clear"></div>
            </div>

            <div class="rype-settings-content">
                <h3><?php esc_html_e('Settings', 'rype-real-estate'); ?></h3>  
                <form method="post" action="options.php">
                    <?php settings_fields( 'rype-real-estate-settings-group' ); ?>
                    <?php do_settings_sections( 'rype-real-estate-settings-group' ); ?>
                </form> 
            </div>

            <div class="rype-settings-menu-bar rype-settings-footer">
                <?php
                $plugin_data = get_plugin_data( __FILE__ );
                $plugin_version = $plugin_data['Version']; 
                ?>
                <div class="rype-settings-version left"><?php esc_html_e('Version', 'rype-real-estate'); ?> <?php echo $plugin_version; ?> | <?php esc_html_e('Made by', 'rype-real-estate'); ?> <a href="http://rypecreative.com/" target="_blank">Rype Creative</a> | <a href="http://rypecreative.com/contact/#theme-support" target="_blank"><?php esc_html_e('Get Support', 'rype-real-estate'); ?></a></div>
                <div class="rype-settings-actions"><?php submit_button(esc_html__('Save Changes', 'rype-real-estate'), 'admin-button', 'submit', false); ?></div>
                <div class="clear"></div>
            </div>

        </div><!-- end rype settings -->
    </div><!-- end wrap -->

<?php } 


/*-----------------------------------------------------------------------------------*/
/*  OUTPUT ADD-ONS PAGE
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_add_ons_page() { ?>
	<div class="wrap">
        <h1><?php esc_html_e('Rype Real Estate', 'rype-real-estate'); ?></h1>

        <h2 class="nav-tab-wrapper">
		    <a href="?page=rype-real-estate-settings" class="nav-tab"><?php esc_html_e('Settings', 'rype-real-estate'); ?></a>
		    <a href="?page=rype-real-estate-add-ons" class="nav-tab nav-tab-active"><?php esc_html_e('Add-Ons', 'rype-real-estate'); ?></a>
		    <a href="?page=rype-real-estate-help" class="nav-tab"><?php esc_html_e('Help', 'rype-real-estate'); ?></a>
		</h2>

		<form method="post" action="options.php">
            <?php settings_fields( 'rype-real-estate-add-ons-group' ); ?>
            <?php do_settings_sections( 'rype-real-estate-add-ons-group' ); ?>
        </form>
    </div>
<?php }

/*-----------------------------------------------------------------------------------*/
/*  OUTPUT HELP PAGE
/*-----------------------------------------------------------------------------------*/
function rype_real_estate_help_page() { ?>
	<div class="wrap">
        <h1><?php esc_html_e('Rype Real Estate', 'rype-real-estate'); ?></h1>

        <h2 class="nav-tab-wrapper">
		    <a href="?page=rype-real-estate-settings" class="nav-tab"><?php esc_html_e('Settings', 'rype-real-estate'); ?></a>
		    <a href="?page=rype-real-estate-add-ons" class="nav-tab"><?php esc_html_e('Add-Ons', 'rype-real-estate'); ?></a>
		    <a href="?page=rype-real-estate-help" class="nav-tab nav-tab-active"><?php esc_html_e('Help', 'rype-real-estate'); ?></a>
		</h2>
    </div>
<?php }


?>