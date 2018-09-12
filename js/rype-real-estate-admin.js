jQuery(document).ready(function($) {

	/********************************************/
	/* REAL ESTATE SHORTCODE SELECTOR */
	/********************************************/
	function rypeRealEstateInsertShortcode(shortcode) {
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
            shortcodeOutput = "[rao_list_properties show_posts='"+showPosts+"' show_header='"+showHeader+"' show_pagination='"+showPagination+"' layout='"+layout+"' property_status='"+propertyStatus+"' property_type='"+propertyType+"' property_location='"+propertyLocation+"' featured='"+featured+"'][/rao_list_properties]";
        }

        //set list property taxonomy shortcode
        if(shortcode == 'real-estate-list-property-tax') {
        	var tax = $('.shortcode-selector-options .list-property-tax-type').val();
        	var taxShowPosts = $('.shortcode-selector-options .list-property-tax-show-posts').val();
        	var taxLayout = $('.shortcode-selector-options .list-property-tax-layout').val();
            var taxOrderBy = $('.shortcode-selector-options .list-property-tax-orderby').val();
        	var taxOrder = $('.shortcode-selector-options .list-property-tax-order').val();
            var taxHideEmpty = $('.shortcode-selector-options .list-property-tax-hide-empty').val();
        	shortcodeOutput = "[rao_list_property_tax tax='"+tax+"' show_posts='"+taxShowPosts+"' layout='"+taxLayout+"' orderby='"+taxOrderBy+"' order='"+taxOrder+"' hide_empty='"+taxHideEmpty+"'][/rao_list_property_tax]";
        }

        //set list agents shortcode
        if(shortcode == 'real-estate-list-agents') {
            var showAgentPosts = $('.shortcode-selector-options .list-agents-show-posts').val();
            if ($('.shortcode-selector-options .list-agents-show-pagination').is(':checked')) { var showAgentPagination = 'true'; } else { var showAgentPagination = 'false'; }
            shortcodeOutput = "[rao_list_agents show_posts='"+showAgentPosts+"' show_pagination='"+showAgentPagination+"'][/rao_list_agents]";
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
        rypeRealEstateInsertShortcode(shortcode);
    });

	/********************************************/
	/* REPEATERS (FLOOR PLANS, OPEN HOUSES, ETC.) */
	/********************************************/
	$('.admin-module-floor-plans').on('click', '.add-floor-plan', function() {
	
        var count = $('.admin-module-floor-plans .accordion .floor-plan-item').length;

		var floorPlanItem = '\
            <h3 class="accordion-tab"><i class="fa fa-chevron-right icon"></i> <span class="repeater-title-mirror floor-plan-title-mirror">'+ rype_real_estate_local_script.new_floor_plan +'</span> <span class="delete delete-floor-plan right"><i class="fa fa-trash"></i> '+ rype_real_estate_local_script.delete_text +'</span></h3> \
			<div class="floor-plan-item"> \
				<div class="floor-plan-left"> \
					<label>'+ rype_real_estate_local_script.floor_plan_title +' </label> <input class="repeater-title floor-plan-title" type="text" name="rypecore_floor_plans['+count+'][title]" placeholder="'+ rype_real_estate_local_script.new_floor_plan +'" /><br/> \
					<label>'+ rype_real_estate_local_script.floor_plan_size +' </label> <input type="text" name="rypecore_floor_plans['+count+'][size]" /><br/> \
					<label>'+ rype_real_estate_local_script.floor_plan_rooms +' </label> <input type="number" name="rypecore_floor_plans['+count+'][rooms]" /><br/> \
					<label>'+ rype_real_estate_local_script.floor_plan_bathrooms +' </label> <input type="number" name="rypecore_floor_plans['+count+'][baths]" /><br/> \
				</div> \
                <div class="floor-plan-right"> \
                    <label>'+ rype_real_estate_local_script.floor_plan_description +' </label> \
				    <textarea name="rypecore_floor_plans['+count+'][description]"></textarea> \
				    <div class="floor-plan-img"> \
                        <label>'+ rype_real_estate_local_script.floor_plan_img +' </label> \
                        <input type="text" name="rypecore_floor_plans['+count+'][img]" /> \
                        <input id="_btn" class="rype_upload_image_button" type="button" value="'+ rype_real_estate_local_script.upload_img +'" /> \
                        <span class="button-secondary remove">'+ rype_real_estate_local_script.remove_text +'</span> \
                    </div> \
                </div> \
                <div class="clear"></div> \
			</div> \
		';
	
        $(this).parent().find('.accordion').append(floorPlanItem);
		$( ".accordion" ).accordion( "refresh" );

        $(this).closest('.admin-module-floor-plans').find('.no-floor-plan').hide();
    });
	
	$('.admin-module-repeater').on('keypress keyup blur', '.repeater-title', function() {
		var mirrorTitle = $(this).parent().parent().prev().find('.repeater-title-mirror');
		if(mirrorTitle.html() == '') {
			mirrorTitle.html('Untitled');
		} else {
			mirrorTitle.html($(this).val());
		}
	});
	
	$('.admin-module-repeater').on("click", ".delete", function() {
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

	/************************************************/
    /* PROPERTY DETAIL BOXED GALLERY OPTIONS TOGGLE */
    /************************************************/
    var propertyDetailTemplate = $('#property_detail_template_agent_contact');
    propertyDetailTemplate.on('click', function() {
       $('.admin-module-property-detail-display-gallery-agent').slideDown('fast');
    });
    $('#property_detail_template_full, #property_detail_template_classic').on('click', function() {
       $('.admin-module-property-detail-display-gallery-agent').hide();
    });

    /********************************************/
    /* PROPERTY CUSTOM FIELDS */
    /********************************************/
    function customFieldExists(fieldValue) {
        var existingFields = [];
        $('.admin-module-custom-fields-theme-options .custom-fields-container .custom-field-item').each(function(index) {
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
            alert(rype_real_estate_local_script.custom_field_dup_error); 
        } else { 
            var customFieldItem = '\
                <table class="custom-field-item sortable-item"> \
                    <tr> \
                        <td> \
                            <label>'+rype_real_estate_local_script.value_text+'</label> \
                            <input type="text" class="custom-field-name-input" name="rypecore_custom_fields['+count+'][name]" value="'+fieldValue+'" /> \
                            <input type="hidden" class="custom-field-id" name="rypecore_custom_fields['+count+'][id]" value="'+fieldID+'" readonly /> \
                            <div class="edit-custom-field-form hide-soft"> \
                                <table class="admin-module"> \
                                    <tr> \
                                    <td class="admin-module-label"><label>'+rype_real_estate_local_script.field_type_text+'</label></td> \
                                    <td class="admin-module-field"> \
                                        <select class="custom-field-type-select" name="rypecore_custom_fields['+count+'][type]"> \
                                            <option value="text">'+rype_real_estate_local_script.text_input_text+'</option> \
                                            <option value="num">'+rype_real_estate_local_script.num_input_text+'</option> \
                                            <option value="select">'+rype_real_estate_local_script.select_text+'</option> \
                                        </select> \
                                    </td> \
                                    </tr> \
                                </table> \
                                <table class="admin-module admin-module-select-options hide-soft"> \
                                    <tr> \
                                    <td class="admin-module-label"><label>'+rype_real_estate_local_script.select_options_text+'</label></td> \
                                    <td class="admin-module-field"> \
                                        <div class="custom-field-select-options-container"></div> \
                                        <div class="button add-custom-field-select">'+rype_real_estate_local_script.select_options_add+'</div> \
                                    </td> \
                                    </tr> \
                                </table> \
                                <table class="admin-module no-border"> \
                                    <tr> \
                                    <td class="admin-module-label"><label>'+rype_real_estate_local_script.front_end_text+'</label></td> \
                                    <td class="admin-module-field"><input type="checkbox" name="rypecore_custom_fields['+count+'][front_end]" checked /></td> \
                                    </tr> \
                                </table> \
                            </div> \
                        </td> \
                        <td class="custom-field-action edit-custom-field"><div class="sortable-item-action"><i class="fa fa-cog"></i> '+rype_real_estate_local_script.edit_text+'</div></td> \
                        <td class="custom-field-action delete-custom-field"><div class="sortable-item-action"><i class="fa fa-trash"></i> '+rype_real_estate_local_script.remove_text+'</div></td> \
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
            alert(rype_real_estate_local_script.custom_field_dup_error);
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
        var confirmMessage = rype_real_estate_local_script.delete_custom_field_confirm;
        if (confirm(confirmMessage) == true) {
            $(this).parent().parent().parent().remove();
            var customFieldID = $(this).parent().find('.custom-field-id').val();
            $('.filter-fields-list').find('.custom-filter-field-'+customFieldID).remove();
            $.ajax({
                url: 'admin-ajax.php',
                type: 'POST',
                data: {
                    action : 'rype_real_estate_delete_custom_field',
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
        var selectOption = '<p><input type="text" name="rypecore_custom_fields['+count+'][select_options][]" placeholder="'+rype_real_estate_local_script.option_name_text+'" /><span class="delete-custom-field-select"><i class="fa fa-times"></i></span></p>';
        $(this).parent().find('.custom-field-select-options-container').append(selectOption);
    });

    $('.admin-module-custom-fields').on("click", ".delete-custom-field-select", function() {
        $(this).parent().remove();
    });

    $('.admin-module-custom-fields').on('change', '.custom-field-type-select', function() {
        if ($(this).val() === 'select') {
            $(this).closest('.edit-custom-field-form').find('.admin-module-select-options').removeClass('hide-soft');
        } else {
            $(this).closest('.edit-custom-field-form').find('.admin-module-select-options').addClass('hide-soft');
        }
    });

    //check for hashtag in url and display tab
    $(function () {
        var hash = $.trim( window.location.hash );
        if(hash == '#custom-property-fields') {
            $('#properties-tab').trigger('click');
            //$("#accordion-custom-fields").accordion("option", "active", 0);
        }
        if (hash) $(hash).trigger('click');
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
            alert(rype_real_estate_local_script.custom_field_dup_error); 
        } else {
            var customFilterField = '\
                <li class="sortable-item custom-filter-field custom-filter-field-'+customFilterFieldID+'"> \
                    <div class="sortable-item-header"> \
                        <div class="sort-arrows"><i class="fa fa-bars"></i></div> \
                        <span class="sortable-item-action remove right"><i class="fa fa-times"></i> '+rype_real_estate_local_script.remove_text+'</span> \
                        <span class="sortable-item-title custom-filter-field-label">'+customFilterFieldName+'</span> \
                        <span class="admin-module-note">(Custom Field)</span> \
                        <div class="clear"></div> \
                        <input type="hidden" name="rypecore_property_filter_items['+count+'][active]" value="true" /> \
                        <input type="hidden" name="rypecore_property_filter_items['+count+'][name]" value="'+customFilterFieldName+'" class="custom-filter-field-name" /> \
                        <input type="hidden" name="rypecore_property_filter_items['+count+'][slug]" value="'+customFilterFieldID+'" class="custom-filter-field-slug" /> \
                        <input type="hidden" name="rypecore_property_filter_items['+count+'][custom]" value="true" /> \
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

    /********************************************/
    /* PAGE BANNER PROPERTY FILTER OPTIONS */
    /********************************************/
    $('#banner_property_filter_override').change(function(){
        if($(this).prop("checked")) {
            $('.admin-module-page-banner-property-filter-options').slideUp('fast');
        } else {
            $('.admin-module-page-banner-property-filter-options').slideDown('fast');
        }
    });

});