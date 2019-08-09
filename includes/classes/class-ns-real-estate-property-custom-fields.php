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

		// Actions and filters
		add_filter('ns_basics_admin_field_types', array( $this, 'add_custom_fields_type' ));
		add_filter('ns_real_estate_settings_init_filter', array( $this, 'add_settings' ));
		add_action('ns_real_estate_after_property_settings', array( $this, 'output_field_builder'));
		add_action('wp_ajax_ns_real_estate_delete_custom_field', array( $this, 'delete_custom_field' ));
		add_filter('ns_real_estate_property_submit_fields_init_filter', array( $this, 'add_property_submit_fields' ));
		add_filter('ns_real_estate_property_settings_init_filter', array( $this, 'add_property_settings_fields'), 10, 2);		
		add_action('ns_basics_save_meta_box_ns-property', array( $this, 'save_property_settings_fields' ));
		add_filter('ns_real_estate_filter_settings_init_filter', array($this, 'filter_custom_fields_init'));
		add_action('ns_real_estate_after_sortable_fields_ns_property_filter_items', array($this, 'add_filter_custom_fields'));
		add_action('ns_real_estate_property_details_widget', array($this, 'add_property_detail_widget_custom_fields'));
		add_action('ns_real_estate_after_property_submit_general', array($this, 'add_property_submit_form_custom_fields'));
		add_action('ns_real_estate_save_property_submit', array($this, 'save_property_submit_form_custom_fields'));

		// Get global settings
		$this->admin_obj = new NS_Real_Estate_Admin();
        $this->global_settings = $this->admin_obj->load_settings();
	}

	/************************************************************************/
	// Global Settings
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
									$custom_field_type_field = array(
										'title' => esc_html__('Field Type', 'ns-real-estate'),
										'name' => $field['name'].'['.$count.'][type]',
										'type' => 'select',
										'class' => 'custom-field-type-select',
										'value' => $custom_field['type'],
										'options' => array(esc_html__('Text Input', 'ns-real-estate') => 'text', esc_html__('Number Input', 'ns-real-estate') => 'number', esc_html__('Select Dropdown', 'ns-real-estate') => 'select'),
									);
									$this->admin_obj->build_admin_field($custom_field_type_field);
									?>

									<table class="admin-module admin-module-select-options <?php if($custom_field['type'] != 'select') { echo 'hide-soft'; } ?>">
                                        <tr>
                                            <td class="admin-module-label"><label><?php esc_html_e('Select Options:', 'ns-real-estate'); ?></label></td>
                                            <td class="admin-module-field">
                                                <div class="custom-field-select-options-container">
                                                    <?php 
                                                    if(isset($custom_field['select_options'])) { $selectOptions = $custom_field['select_options']; } else { $selectOptions =  ''; }
                                                    if(!empty($selectOptions)) {
                                                        foreach($selectOptions as $option) {
                                                            echo '<p><input type="text" name="'.$field['name'].'['.$count.'][select_options][]" value="'.$option.'" /><span class="delete-custom-field-select"><i class="fa fa-times"></i></span></p>';
                                                        }
                                                    } ?>
                                                    <div class="button add-custom-field-select"><?php esc_html_e('Add Select Option', 'ns-real-estate'); ?></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>

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
	            $property_custom_fields = array(
                	'title' => esc_html__('Property Custom Fields', 'ns-real-estate'),
                	'name' => 'ns_property_custom_fields',
                	'value' => $this->global_settings['ns_property_custom_fields'],
                	'type' => 'custom_fields',
                	'class' => 'admin-module-custom-fields',
                );
                $this->admin_obj->build_admin_field($property_custom_fields);
	            ?>

	        </div>
	    </div>

	<?php }

	/**
	 *	Delete custom field
	 */
	public function delete_custom_field() {
		$key = isset($_POST['key']) ? $_POST['key'] : '';
    	delete_post_meta_by_key('ns_property_custom_field_'.$key);
    	die();
	}


	/************************************************************************/
	// Property Submit Custom Fields
	/************************************************************************/

	/**
	 *	Add Custom Fields to property submit
	 *
	 * @param array $property_submit_fields_init
	 */
	public function add_property_submit_fields($property_submit_fields_init) {
		$custom_fields = get_option('ns_property_custom_fields');
		if(!empty($custom_fields)) {
			foreach($custom_fields as $custom_field) {
				$property_submit_fields_init[$custom_field['id']] = array('value' => $custom_field['name']);
			}
		}
		return $property_submit_fields_init;
	}


	/************************************************************************/
	// Property Settings Custom Fields
	/************************************************************************/

	/**
	 *	Add Custom Fields to property settings
	 *
	 * @param array $property_settings_init
	 */
	public function add_property_settings_fields($property_settings_init, $post_id) {
		$custom_fields = get_option('ns_property_custom_fields');
		if(!empty($custom_fields)) { 
			$count = 0;
			foreach($custom_fields as $custom_field) {

				$select_options = null;
				if($custom_field['type'] == 'select') {
					$select_options = array();
					$select_options[esc_html__('Select an option...', 'ns-real-estate')] = '';
					foreach($custom_field['select_options'] as $option) {
						$select_options[$option] = $option;
					}
				}

				$property_settings_init[$custom_field['id']] = array(
					'group' => 'general',
					'title' => $custom_field['name'],
					'name' => 'ns_property_custom_fields['.$count.'][value]',
					'type' => $custom_field['type'],
					'value' => get_post_meta($post_id, 'ns_property_custom_field_'.$custom_field['id'], true),
					'postfix' => '<input type="hidden" name="ns_property_custom_fields['.$count.'][key]" value="ns_property_custom_field_'.$custom_field['id'].'" />',
					'order' => 15,
					'options' => $select_options,
				);
				$count++; ?>
			<?php }
		}
		return $property_settings_init;
	}

	/**
	 *	Save property settings custom fields
	 *
	 * @param int $post_id
	 */
	public function save_property_settings_fields($post_id) {
		if (isset( $_POST['ns_property_custom_fields'] )) {
	        $property_custom_fields = $_POST['ns_property_custom_fields'];
	        foreach($property_custom_fields as $custom_field) {
	            update_post_meta( $post_id, $custom_field['key'], $custom_field['value'] );
	        }
	    }
	}	


	/************************************************************************/
	// Property Filter Custom Fields
	/************************************************************************/
	
	/**
	 *	Initialize filter custom fields
	 *
	 * @param array $field
	 */
	public function filter_custom_fields_init($filter_settings_init) {
		$filter_settings_init['fields']['custom_fields'] = get_option('ns_property_custom_fields');
		return $filter_settings_init;
	}

	/**
	 *	Add custom field to filter items
	 *
	 * @param array $field
	 */
	public function add_filter_custom_fields($field) {
		$custom_fields = get_option('ns_property_custom_fields'); ?>
	
		<table class="admin-module no-border no-padding-bottom">
            <tr>
                <td class="admin-module-label">
                    <label><?php esc_html_e('Add Custom Field to Filter', 'ns-real-estate'); ?></label>
                    <span class="admin-module-note"><a href="<?php echo admin_url('admin.php?page=ns-real-estate-settings#properties&custom-fields'); ?>" target="_blank"><i class="fa fa-cog"></i> <?php esc_html_e('Manage custom fields', 'ns-real-estate'); ?></a></span>
                </td>
                <td class="admin-module-field">
                    <?php 
                    if(!empty($custom_fields)) { 
                        echo '<select class="select-filter-custom-field">';
                        echo '<option value="">Select a field...</option>';
                        foreach($custom_fields as $key=>$custom_field) {
                            if(!is_array($custom_field)) { 
                                $custom_field = array( 
                                    'id' => strtolower(str_replace(' ', '_', $custom_field)),
                                    'name' => $custom_field, 
                                    'type' => 'text',
                                ); 
                            }
                            echo '<option value="'.$custom_field['id'].'">'.$custom_field['name'].'</option>';
                        }
                        echo '</select>'; ?>
                        <div class="add-filter-custom-field button button-secondary"><?php esc_html_e('Insert Field', 'ns-real-estate'); ?></div>
                    <?php } else { ?> 
                        <span class="admin-module-note"><?php esc_html_e('No custom fields have been created.', 'ns-real-estate'); ?></span>
                    <?php } ?>
                </td>
            </tr>
        </table>

	<?php }
	

	/************************************************************************/
	// Front-end Template Hooks
	/************************************************************************/

	/**
	 *	Add custom field to filter items
	 *
	 * @param int $postID
	 */
	public function add_property_detail_widget_custom_fields($postID) {
		$custom_fields = get_option('ns_property_custom_fields');
		if(!empty($custom_fields)) {                 
            foreach ($custom_fields as $custom_field) { 
                $fieldValue = get_post_meta($postID, 'ns_property_custom_field_'.$custom_field['id'], true);  
                if(!empty($fieldValue)) { ?>
                    <div class="property-detail-item"><?php echo $custom_field['name']; ?>: <span><?php echo $fieldValue; ?></span></div>
                <?php }
            }
        }
	}

	/**
	 *	Add custom fields to property submit form
	 *
	 * @param int $postID
	 */
	public function add_property_submit_form_custom_fields($postID) {
		
		$admin_obj = new NS_Real_Estate_Admin();
		$members_submit_property_fields = $admin_obj->load_settings(false, 'ns_members_submit_property_fields', false);
		$custom_fields = get_option('ns_property_custom_fields');

        if(!empty($custom_fields)) { ?>
            <div class="submit-property-section form-block-property-custom-fields">
            <h3><?php esc_html_e('Additional Details', 'ns-real-estate'); ?></h3>
            <?php $count = 0;
            echo '<div class="row">';                    
            foreach ($custom_fields as $custom_field) { 
                if(ns_basics_in_array_key($custom_field['id'], $members_submit_property_fields)) {
                    $custom_field_key = strtolower(str_replace(' ', '_', $custom_field['name'])); 
                    if(isset($postID) && !empty($postID)) { $fieldValue = get_post_meta($postID, 'ns_property_custom_field_'.$custom_field['id'], true); }  ?>
                    <div class="col-lg-4 col-md-4 custom-field-item custom-field-<?php echo $custom_field_key; ?>">
                        <div class="form-block border">
                            <label title="<?php echo $custom_field['name']; ?>"><?php echo $custom_field['name']; ?>:</label> 
                            <?php if(isset($custom_field['type']) && $custom_field['type'] == 'select') { ?>
                                <select name="ns_property_custom_fields[<?php echo $count; ?>][value]">
                                    <option value=""><?php esc_html_e('Select an option...', 'ns-real-estate'); ?></option>
                                    <?php 
                                    if(isset($custom_field['select_options'])) { $selectOptions = $custom_field['select_options']; } else { $selectOptions =  ''; }
                                    if(!empty($selectOptions)) {
                                        foreach($selectOptions as $option) { ?>
                                            <option value="<?php echo $option; ?>" <?php if(isset($fieldValue) && $fieldValue == $option) { echo 'selected'; } else { if($_POST['ns_property_custom_fields'][$count]['value'] == $option) { echo 'selected'; } } ?>><?php echo $option; ?></option>
                                        <?php }
                                    } ?>
                                </select>
                            <?php } else { ?>
                                <input type="<?php if(isset($custom_field['type']) && $custom_field['type'] == 'num') { echo 'number'; } else { echo 'text'; } ?>" class="border" name="ns_property_custom_fields[<?php echo $count; ?>][value]" value="<?php if(isset($fieldValue)) { echo $fieldValue; } else { echo esc_attr($_POST['ns_property_custom_fields'][$count]['value']); } ?>" />
                            <?php } ?>
                            <input type="hidden" name="ns_property_custom_fields[<?php echo $count; ?>][key]" value="ns_property_custom_field_<?php echo $custom_field['id']; ?>" />
                        </div>
                    </div>
                    <?php $count++; ?>
                <?php } ?>
            <?php }
            echo '</div>';
            echo '</div>';
        }
	}

	/**
	 *	Save property submit form custom fields
	 *
	 * @param int $postID
	 */
	public function save_property_submit_form_custom_fields($postID) {
		if (isset( $_POST['ns_property_custom_fields'] )) {
		    $property_custom_fields = $_POST['ns_property_custom_fields'];
		    foreach($property_custom_fields as $custom_field) {
		        update_post_meta( $postID, $custom_field['key'], $custom_field['value'] );
		    }
		}
	}

}
?>