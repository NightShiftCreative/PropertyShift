jQuery(document).ready(function($) {

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
    $('.admin-module').on("click", ".add-filter-custom-field", function() {
        var customFilterFieldID = $(this).parent().find('.select-filter-custom-field').val();
        var customFilterFieldName = $(this).parent().find('.select-filter-custom-field option:selected').text();
        var count = $('.admin-module-ns_property_filter_items').find('.sortable-list li').length;
        var existingFilterFields = [];
        $('.admin-module-ns_property_filter_items .sortable-list .custom-filter-field').each(function(index) {
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
                $(this).closest('.admin-module-ns_property_filter_items').find('.sortable-list').append(customFilterField);
            }
        }
    });

    //remove custom field from filter
    $('.admin-module-ns_property_filter_items').on("click", ".sortable-item-action.remove ", function() {
        $(this).parent().remove();
    });

});