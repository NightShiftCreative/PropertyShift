jQuery(document).ready(function($) {

    /********************************************/
    /* LICENSE INPUT CHANGE */
    /********************************************/
    $(".license-key-input").on("change paste keyup", function() {
        var parent = $(this).closest('.ns-license-key');
        var activateButton = parent.find('.activate-license-button');
        activateButton.addClass('disabled');
        activateButton.attr("disabled", true);
        if(parent.find('.license-disabled-message').length == 0) {
            activateButton.closest('.admin-module-field').append('<div class="admin-module-note license-disabled-message">Save changes to activate license key.</div>');
        }
    });

	/********************************************/
	/* REAL ESTATE SHORTCODE SELECTOR */
	/********************************************/
	function nsRealEstateInsertShortcode(shortcode) {
        var shortcodeOutput = '';
        var visualEditor = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();

        //set list properties shortcode
        if(shortcode == 'real-estate-list-properties') {
            var showPosts = $('.shortcode-selector-options .list-properties-show-posts').val();
            if ($('.shortcode-selector-options .list-properties-show-header').is(':checked')) { var showHeader = 'true'; } else { var showHeader = 'false'; }
            if ($('.shortcode-selector-options .list-properties-show-pagination').is(':checked')) { var showPagination = 'true'; } else { var showPagination = 'false'; }
            var layout = $('.shortcode-selector-options .list-properties-layout').val();
            var propertyStatus = $('.shortcode-selector-options .list-properties-status').val();
            var propertyType = $('.shortcode-selector-options .list-properties-type').val();
            var propertyLocation = $('.shortcode-selector-options .list-properties-location').val();
            if ($('.shortcode-selector-options .list-properties-featured').is(':checked')) { var featured = 'true'; } else { var featured = 'false'; }
            shortcodeOutput = "[ns_list_properties show_posts='"+showPosts+"' show_header='"+showHeader+"' show_pagination='"+showPagination+"' layout='"+layout+"' property_status='"+propertyStatus+"' property_type='"+propertyType+"' property_location='"+propertyLocation+"' featured='"+featured+"'][/ns_list_properties]";
        }

        //set list property taxonomy shortcode
        if(shortcode == 'real-estate-list-property-tax') {
        	var tax = $('.shortcode-selector-options .list-property-tax-type').val();
        	var taxShowPosts = $('.shortcode-selector-options .list-property-tax-show-posts').val();
        	var taxLayout = $('.shortcode-selector-options .list-property-tax-layout').val();
            var taxOrderBy = $('.shortcode-selector-options .list-property-tax-orderby').val();
        	var taxOrder = $('.shortcode-selector-options .list-property-tax-order').val();
            var taxHideEmpty = $('.shortcode-selector-options .list-property-tax-hide-empty').val();
        	shortcodeOutput = "[ns_list_property_tax tax='"+tax+"' show_posts='"+taxShowPosts+"' layout='"+taxLayout+"' orderby='"+taxOrderBy+"' order='"+taxOrder+"' hide_empty='"+taxHideEmpty+"'][/ns_list_property_tax]";
        }

        //set submit property shortcode
        if(shortcode == 'real-estate-submit-property') {
            shortcodeOutput = "[ns_submit_property]";
        }

        //set my properties shortcode
        if(shortcode == 'real-estate-my-properties') {
            shortcodeOutput = "[ns_my_properties]";
        }

        //set property filter shortcode
        if(shortcode == 'real-estate-filter') {
            var propertyFilter = $('.shortcode-selector-options .property-filter-select').val();
            shortcodeOutput = "[ns_property_filter id='"+propertyFilter+"'][/ns_property_filter]";
        }

        //set list agents shortcode
        if(shortcode == 'real-estate-list-agents') {
            var showAgentPosts = $('.shortcode-selector-options .list-agents-show-posts').val();
            if ($('.shortcode-selector-options .list-agents-show-pagination').is(':checked')) { var showAgentPagination = 'true'; } else { var showAgentPagination = 'false'; }
            shortcodeOutput = "[ns_list_agents show_posts='"+showAgentPosts+"' show_pagination='"+showAgentPagination+"'][/ns_list_agents]";
        }

        //insert shortcode
        if(visualEditor) {
            tinyMCE.activeEditor.selection.setContent(shortcodeOutput);
        } else {
            QTags.insertContent(shortcodeOutput);
        }
        self.parent.tb_remove();
    }

    $('.shortcode-selector-options').on('click', '.insert-shortcode-real-estate', function() { 
        var shortcode = $('.shortcode-selector-options .admin-module.active').attr('id');
        nsRealEstateInsertShortcode(shortcode);
    });

	/********************************************/
	/* REPEATERS (FLOOR PLANS, OPEN HOUSES, ETC.) */
	/********************************************/
	$('.repeater-container').on('click', '.add-repeater', function() {
	
        var count = $(this).closest('.repeater-container').find('.repeater-items > .ns-accordion').length;

		var repeaterItem = '\
            <div class="ns-accordion"> \
                <div class="ns-accordion-header"><i class="fa fa-chevron-right"></i> <span class="repeater-title-mirror floor-plan-title-mirror">'+ ns_real_estate_local_script.new_floor_plan +'</span> <span class="action delete delete-floor-plan"><i class="fa fa-trash"></i> '+ ns_real_estate_local_script.delete_text +'</span></div> \
    			<div class="ns-accordion-content floor-plan-item"> \
    				<div class="floor-plan-left"> \
    					<label>'+ ns_real_estate_local_script.floor_plan_title +' </label> <input class="repeater-title floor-plan-title" type="text" name="ns_property_floor_plans['+count+'][title]" placeholder="'+ ns_real_estate_local_script.new_floor_plan +'" /><br/> \
    					<label>'+ ns_real_estate_local_script.floor_plan_size +' </label> <input type="text" name="ns_property_floor_plans['+count+'][size]" /><br/> \
    					<label>'+ ns_real_estate_local_script.floor_plan_rooms +' </label> <input type="number" name="ns_property_floor_plans['+count+'][rooms]" /><br/> \
    					<label>'+ ns_real_estate_local_script.floor_plan_bathrooms +' </label> <input type="number" name="ns_property_floor_plans['+count+'][baths]" /><br/> \
    				</div> \
                    <div class="floor-plan-right"> \
                        <label>'+ ns_real_estate_local_script.floor_plan_description +' </label> \
    				    <textarea name="ns_property_floor_plans['+count+'][description]"></textarea> \
    				    <div class="floor-plan-img"> \
                            <label>'+ ns_real_estate_local_script.floor_plan_img +' </label> \
                            <input type="text" name="ns_property_floor_plans['+count+'][img]" /> \
                            <input id="_btn" class="ns_upload_image_button" type="button" value="'+ ns_real_estate_local_script.upload_img +'" /> \
                            <span class="button-secondary remove">'+ ns_real_estate_local_script.remove_text +'</span> \
                        </div> \
                    </div> \
                    <div class="clear"></div> \
    			</div> \
            </div> \
		';
	
        $(this).closest('.repeater-container').find('.repeater-items').append(repeaterItem);
        $(this).closest('.repeater-container').find('.no-floor-plan').hide();
    });
	
	$('.repeater-container').on('keypress keyup blur', '.repeater-title', function() {
		var mirrorTitle = $(this).parent().parent().prev().find('.repeater-title-mirror');
		if(mirrorTitle.html() == '') {
			mirrorTitle.html('Untitled');
		} else {
			mirrorTitle.html($(this).val());
		}
	});
	
	$('.repeater-container').on("click", ".delete", function() {
        $(this).parent().next().remove();
		$(this).parent().remove();
    });

    /********************************************/
	/* AGENT FORM ID */
	/********************************************/
	var agentFormSource = $('#agent_form_source_contact_7');
	agentFormSource.on('click', function() {
	   $('.admin-module-agent-form-id').slideDown('fast');
	});
	$('#agent_form_source').on('click', function() {
	   $('.admin-module-agent-form-id').slideUp('fast');
	});

    /********************************************/
    /* PROPERTY CUSTOM FIELDS */
    /********************************************/
    function customFieldExists(fieldValue) {
        var existingFields = [];
        $('.admin-module-custom-fields .custom-fields-container .custom-field-item').each(function(index) {
            var existingFieldValue = $(this).find('.custom-field-name-input').val();
            existingFields.push(existingFieldValue);
        });
        if($.inArray(fieldValue, existingFields) !== -1) { 
            return true;
        } else {
            return false;
        }
    }

    $('.admin-module-custom-fields').on('click', '.new-custom-field-toggle', function() {
        $(this).parent().find('.add-custom-field-value').val('');
        $(this).parent().find('.new-custom-field-form').slideDown('fast');
        $(this).hide();
    });

    //add new custom field
    $('.admin-module-custom-fields').on('click', '.add-custom-field', function() {

        var count = $('.admin-module-custom-fields .custom-fields-container .custom-field-item').length;
        var fieldValue = $(this).parent().find('.add-custom-field-value').val();
        var fieldID = Math.round(new Date().getTime() + (Math.random() * 100));

        if(customFieldExists(fieldValue)) { 
            alert(ns_real_estate_local_script.custom_field_dup_error); 
        } else { 
            var customFieldItem = '\
                <table class="custom-field-item sortable-item"> \
                    <tr> \
                        <td> \
                            <label>'+ns_real_estate_local_script.value_text+'</label> \
                            <input type="text" class="custom-field-name-input" name="ns_property_custom_fields['+count+'][name]" value="'+fieldValue+'" /> \
                            <input type="hidden" class="custom-field-id" name="ns_property_custom_fields['+count+'][id]" value="'+fieldID+'" readonly /> \
                            <div class="edit-custom-field-form hide-soft"> \
                                <table class="admin-module custom-field-type-select"> \
                                    <tr> \
                                    <td class="admin-module-label"><label>'+ns_real_estate_local_script.field_type_text+'</label></td> \
                                    <td class="admin-module-field"> \
                                        <select name="ns_property_custom_fields['+count+'][type]"> \
                                            <option value="text">'+ns_real_estate_local_script.text_input_text+'</option> \
                                            <option value="number">'+ns_real_estate_local_script.num_input_text+'</option> \
                                            <option value="select">'+ns_real_estate_local_script.select_text+'</option> \
                                        </select> \
                                    </td> \
                                    </tr> \
                                </table> \
                                <table class="admin-module admin-module-select-options hide-soft"> \
                                    <tr> \
                                    <td class="admin-module-label"><label>'+ns_real_estate_local_script.select_options_text+'</label></td> \
                                    <td class="admin-module-field"> \
                                        <div class="custom-field-select-options-container"> \
                                        <div class="button add-custom-field-select">'+ns_real_estate_local_script.select_options_add+'</div> \
                                        </div> \
                                    </td> \
                                    </tr> \
                                </table> \
                            </div> \
                        </td> \
                        <td class="custom-field-action edit-custom-field"><div class="sortable-item-action"><i class="fa fa-cog"></i> '+ns_real_estate_local_script.edit_text+'</div></td> \
                        <td class="custom-field-action delete-custom-field"><div class="sortable-item-action"><i class="fa fa-trash"></i> '+ns_real_estate_local_script.remove_text+'</div></td> \
                    </tr> \
                </table> \
            ';

            $(this).parent().parent().parent().find('.custom-fields-container').append(customFieldItem);
            $(this).parent().hide();
            $(this).parent().parent().parent().find('.custom-fields-container .admin-module-note.no-fields').hide();
            $(this).parent().parent().find('.new-custom-field-toggle').show();
        }

    });

    $('.admin-module-custom-fields').on('click', '.cancel-custom-field', function() {
        $(this).parent().hide();
        $(this).parent().parent().find('.new-custom-field-toggle').show();
    });

    $('.admin-module-custom-fields').on("click", ".edit-custom-field", function() {
        $(this).parent().find('.edit-custom-field-form').slideToggle('fast');
    });

    //If field name changes, update in filter fields
    $('.admin-module-custom-fields').on('focusin', '.custom-field-name-input', function(){
        $(this).data('val', $(this).val());
    }).on('change','.custom-field-name-input', function(){
        var originalFieldValue = $(this).data('val');
        var fieldValue = $(this).val();
        var existingFields = [];
        $('.admin-module-custom-fields-theme-options .custom-fields-container .custom-field-item').not($(this).closest('.custom-field-item')).each(function(index) {
            var existingFieldValue = $(this).find('.custom-field-name-input').val();
            existingFields.push(existingFieldValue);
        });
        if($.inArray(fieldValue, existingFields) !== -1) { 
            alert(ns_real_estate_local_script.custom_field_dup_error);
            $(this).val(originalFieldValue);
        } else {
            var fieldID = $(this).parent().find('.custom-field-id').val();
            $('.admin-module-filter-fields .filter-fields-list ').find('.custom-filter-field-'+fieldID+' .custom-filter-field-name').val($(this).val());
            $('.admin-module-filter-fields .filter-fields-list ').find('.custom-filter-field-'+fieldID+' .custom-filter-field-label').html($(this).val());
            $('.admin-module-filter-fields .select-filter-custom-field option[value="'+fieldID+'"]').html($(this).val());
        }
    });

    //delete custom field
    $('.admin-module-custom-fields').on("click", ".delete-custom-field", function() {
        var confirmMessage = ns_real_estate_local_script.delete_custom_field_confirm;
        if (confirm(confirmMessage) == true) {
            $(this).parent().parent().parent().remove();
            var customFieldID = $(this).parent().find('.custom-field-id').val();
            $('.filter-fields-list').find('.custom-filter-field-'+customFieldID).remove();
            $.ajax({
                url: 'admin-ajax.php',
                type: 'POST',
                data: {
                    action : 'ns_real_estate_delete_custom_field',
                    key: customFieldID
                },
                success: function(data){
                    //success goes here
                }
            });
        }
    });

    //custom field select options
    $('.admin-module-custom-fields').on("click", ".add-custom-field-select", function() {
        var count = $(this).closest('.custom-field-item').index('.custom-field-item');
        var selectOption = '<p><input type="text" name="ns_property_custom_fields['+count+'][select_options][]" placeholder="'+ns_real_estate_local_script.option_name_text+'" /><span class="delete-custom-field-select"><i class="fa fa-times"></i></span></p>';
        $(this).closest('.custom-field-select-options-container').append(selectOption);
    });

    $('.admin-module-custom-fields').on("click", ".delete-custom-field-select", function() {
        $(this).parent().remove();
    });

    $('.admin-module-custom-fields').on('change', '.custom-field-type-select select', function() {
        if ($(this).val() === 'select') {
            $(this).closest('.edit-custom-field-form').find('.admin-module-select-options').removeClass('hide-soft');
        } else {
            $(this).closest('.edit-custom-field-form').find('.admin-module-select-options').addClass('hide-soft');
        }
    });

    /********************************************/
    /* FILTER CUSTOM FIELDS */
    /********************************************/
    //insert custom field to filter
    $('.admin-module-filter-fields').on("click", ".add-filter-custom-field", function() {
        var customFilterFieldID = $(this).parent().find('.select-filter-custom-field').val();
        var customFilterFieldName = $(this).parent().find('.select-filter-custom-field option:selected').text();
        var count = $('.admin-module-filter-fields').find('.filter-fields-list li').length;
        var existingFilterFields = [];
        $('.admin-module-filter-fields .filter-fields-list .custom-filter-field').each(function(index) {
            var existingFilterFieldValue = $(this).find('.custom-filter-field-name').val();
            existingFilterFields.push(existingFilterFieldValue);
        });

        if($.inArray(customFilterFieldName, existingFilterFields) !== -1) { 
            alert(ns_real_estate_local_script.custom_field_dup_error); 
        } else {
            var customFilterField = '\
                <li class="sortable-item custom-filter-field custom-filter-field-'+customFilterFieldID+'"> \
                    <div class="sortable-item-header"> \
                        <div class="sort-arrows"><i class="fa fa-bars"></i></div> \
                        <span class="sortable-item-action remove right"><i class="fa fa-times"></i> '+ns_real_estate_local_script.remove_text+'</span> \
                        <span class="sortable-item-title custom-filter-field-label">'+customFilterFieldName+'</span> \
                        <span class="admin-module-note">(Custom Field)</span> \
                        <div class="clear"></div> \
                        <input type="hidden" name="ns_property_filter_items['+count+'][active]" value="true" /> \
                        <input type="hidden" name="ns_property_filter_items['+count+'][name]" value="'+customFilterFieldName+'" class="custom-filter-field-name" /> \
                        <input type="hidden" name="ns_property_filter_items['+count+'][slug]" value="'+customFilterFieldID+'" class="custom-filter-field-slug" /> \
                        <input type="hidden" name="ns_property_filter_items['+count+'][custom]" value="true" /> \
                    </div> \
                </li> \
            ';
            if(customFilterFieldName != '' && customFilterFieldID != '') { 
                $(this).closest('.admin-module-filter-fields').find('.filter-fields-list').append(customFilterField);
            }
        }
    });

    //remove custom field from filter
    $('.admin-module-filter-fields').on("click", ".sortable-item-action.remove ", function() {
        $(this).parent().remove();
    });

});