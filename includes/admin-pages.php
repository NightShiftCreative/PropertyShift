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

        <h2 class="nav-tab-wrapper">
		    <a href="?page=rype-real-estate-settings" class="nav-tab nav-tab-active"><?php esc_html_e('Settings', 'rype-real-estate'); ?></a>
		    <a href="?page=rype-real-estate-add-ons" class="nav-tab"><?php esc_html_e('Add-Ons', 'rype-real-estate'); ?></a>
		    <a href="?page=rype-real-estate-help" class="nav-tab"><?php esc_html_e('Help', 'rype-real-estate'); ?></a>
		</h2>

		<form method="post" action="options.php">
            <?php settings_fields( 'rype-real-estate-settings-group' ); ?>
            <?php do_settings_sections( 'rype-real-estate-settings-group' ); ?>
        </form>
    </div>

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