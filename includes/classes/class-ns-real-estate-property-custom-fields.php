<?php
// Exit if accessed directly
if (!defined( 'ABSPATH')) { exit; }

/**
 *	NS_Real_Estate_Property_Custom_Fields class
 *
 */
class NS_Real_Estate_Property_Custom_Fields {

	/************************************************************************/
	// Initialize
	/************************************************************************/

	/**
	 *	Constructor
	 */
	public function __construct() {
		add_filter('ns_basics_admin_field_types', array( $this, 'add_custom_fields_type' ));
		add_filter('ns_real_estate_settings_init_filter', array( $this, 'add_settings' ));
		add_action('ns_real_estate_after_property_settings', array( $this, 'output_field_builder'));
	}

	/************************************************************************/
	// Admin
	/************************************************************************/

	/**
	 *	Add Settings
	 *
	 * @param array $settings_init
	 */
	public function add_settings($settings_init) {
		$settings_init['ns_property_custom_fields'] = array('value' => array(), 'esc' => false);
		return $settings_init;
	}

	/**
	 *	Register the field type
	 *
	 * @param array $field_types
	 */
	public function add_custom_fields_type($field_types) {
		$field_types['custom_fields'] = array($this, 'build_admin_field_custom_fields');
		return $field_types;
	}

	/**
	 *	Build the field type
	 *
	 * @param array $field
	 */
	public function build_admin_field_custom_fields($field) { ?>
		<div class="sortable-list custom-fields-container">
			<?php 
			$custom_fields = $field['value'];
			if(!empty($custom_fields)) {
				$count = 0;
				foreach($custom_fields as $custom_field) { ?>

					<table class="custom-field-item sortable-item"> 
						<tr>
							<td>
								<label><strong><?php esc_html_e('Field Name:', 'ns-real-estate'); ?></strong></label>
								<input type="text" class="custom-field-name-input" name="<?php echo $field['name']; ?>[<?php echo $count; ?>][name]" value="<?php echo $custom_field['name']; ?>" />
                                <input type="hidden" class="custom-field-id" name="<?php echo $field['name']; ?>[<?php echo $count; ?>][id]" value="<?php echo $custom_field['id']; ?>" readonly />
								<div class="edit-custom-field-form hide-soft">
									<?php
									$admin_obj = new NS_Real_Estate_Admin();
									$custom_field_type_field = array(
										'title' => esc_html__('Field Type', 'ns-real-estate'),
										'name' => 'ns_property_custom_fields['.$count.'][type]',
										'type' => 'select',
										'value' => $custom_field['type'],
										'options' => array(esc_html__('Text Input', 'ns-real-estate') => 'text', esc_html__('Number Input', 'ns-real-estate') => 'num', esc_html__('Select Dropdown', 'ns-real-estate') => 'select'),
									);
									$admin_obj->build_admin_field($custom_field_type_field);
									?>
								</div>
							</td>
							<td class="custom-field-action edit-custom-field"><div class="sortable-item-action"><i class="fa fa-cog"></i> <?php echo esc_html_e('Edit', 'ns-real-estate'); ?></div></td>
                            <td class="custom-field-action delete-custom-field"><div class="sortable-item-action"><i class="fa fa-trash"></i> <?php echo esc_html_e('Remove', 'ns-real-estate'); ?></div></td>
						</tr>
					</table>

				<?php $count++; }
			} ?>
		</div>

		<div class="new-custom-field">
            <div class="new-custom-field-form hide-soft">
                <input type="text" style="display:block;" class="add-custom-field-value" placeholder="<?php esc_html_e('Field Name', 'ns-real-estate'); ?>" />
                <span class="admin-button add-custom-field"><?php esc_html_e('Add Field', 'ns-real-estate'); ?></span>
                <span class="button button-secondary cancel-custom-field"><i class="fa fa-times"></i> <?php esc_html_e('Cancel', 'ns-real-estate'); ?></span>
            </div>
            <span class="admin-button new-custom-field-toggle"><i class="fa fa-plus"></i> <?php esc_html_e('Create New Field', 'ns-real-estate'); ?></span>
        </div>
	<?php }

	/**
	 *	Output the field
	 */
	public function output_field_builder() { ?>
		
		<div class="ns-accordion" id="accordion-custom-fields" data-name="custom-fields">
	        <div class="ns-accordion-header"><i class="fa fa-chevron-right"></i> <?php echo esc_html_e('Property Custom Fields', 'ns-real-estate'); ?></div>
	        <div class="ns-accordion-content">

	        	<?php
	        	$admin_obj = new NS_Real_Estate_Admin();
	        	$settings_init = $admin_obj->load_settings();
				$settings = $admin_obj->get_settings($settings_init);

	            $property_custom_fields = array(
                	'title' => esc_html__('Property Custom Fields', 'ns-real-estate'),
                	'name' => 'ns_property_custom_fields',
                	'value' => $settings['ns_property_custom_fields'],
                	'type' => 'custom_fields',
                	'class' => 'admin-module-custom-fields',
                );
                $admin_obj->build_admin_field($property_custom_fields);
	            ?>

	        </div>
	    </div>

	<?php }

}

?>