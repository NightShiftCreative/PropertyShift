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
            activateButton.closest('.admin-module-field').append('<div class="admin-module-note license-disabled-message">Enter a value above and then click <strong>Save changes</strong> to activate license key.</div>');
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
            shortcodeOutput = "[ps_list_properties show_posts='"+showPosts+"' show_header='"+showHeader+"' show_pagination='"+showPagination+"' layout='"+layout+"' property_status='"+propertyStatus+"' property_type='"+propertyType+"' property_location='"+propertyLocation+"' featured='"+featured+"'][/ps_list_properties]";
        }

        //set list property taxonomy shortcode
        if(shortcode == 'real-estate-list-property-tax') {
        	var tax = $('.shortcode-selector-options .list-property-tax-type').val();
        	var taxShowPosts = $('.shortcode-selector-options .list-property-tax-show-posts').val();
        	var taxLayout = $('.shortcode-selector-options .list-property-tax-layout').val();
            var taxOrderBy = $('.shortcode-selector-options .list-property-tax-orderby').val();
        	var taxOrder = $('.shortcode-selector-options .list-property-tax-order').val();
            var taxHideEmpty = $('.shortcode-selector-options .list-property-tax-hide-empty').val();
        	shortcodeOutput = "[ps_list_property_tax tax='"+tax+"' show_posts='"+taxShowPosts+"' layout='"+taxLayout+"' orderby='"+taxOrderBy+"' order='"+taxOrder+"' hide_empty='"+taxHideEmpty+"'][/ps_list_property_tax]";
        }

        //set submit property shortcode
        if(shortcode == 'real-estate-submit-property') {
            shortcodeOutput = "[ps_submit_property]";
        }

        //set my properties shortcode
        if(shortcode == 'real-estate-my-properties') {
            shortcodeOutput = "[ps_my_properties]";
        }

        //set property filter shortcode
        if(shortcode == 'real-estate-filter') {
            var propertyFilter = $('.shortcode-selector-options .property-filter-select').val();
            shortcodeOutput = "[ps_property_filter id='"+propertyFilter+"'][/ps_property_filter]";
        }

        //set list agents shortcode
        if(shortcode == 'real-estate-list-agents') {
            var showAgentPosts = $('.shortcode-selector-options .list-agents-show-posts').val();
            if ($('.shortcode-selector-options .list-agents-show-pagination').is(':checked')) { var showAgentPagination = 'true'; } else { var showAgentPagination = 'false'; }
            shortcodeOutput = "[ps_list_agents show_posts='"+showAgentPosts+"' show_pagination='"+showAgentPagination+"'][/ps_list_agents]";
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
                <div class="ns-accordion-header"><i class="fa fa-chevron-right"></i> <span class="repeater-title-mirror floor-plan-title-mirror">'+ propertyshift_local_script.new_floor_plan +'</span> <span class="action delete delete-floor-plan"><i class="fa fa-trash"></i> '+ propertyshift_local_script.delete_text +'</span></div> \
    			<div class="ns-accordion-content floor-plan-item"> \
    				<div class="floor-plan-left"> \
    					<label>'+ propertyshift_local_script.floor_plan_title +' </label> <input class="repeater-title floor-plan-title" type="text" name="ps_property_floor_plans['+count+'][title]" placeholder="'+ propertyshift_local_script.new_floor_plan +'" /><br/> \
    					<label>'+ propertyshift_local_script.floor_plan_size +' </label> <input type="text" name="ps_property_floor_plans['+count+'][size]" /><br/> \
    					<label>'+ propertyshift_local_script.floor_plan_rooms +' </label> <input type="number" name="ps_property_floor_plans['+count+'][rooms]" /><br/> \
    					<label>'+ propertyshift_local_script.floor_plan_bathrooms +' </label> <input type="number" name="ps_property_floor_plans['+count+'][baths]" /><br/> \
    				</div> \
                    <div class="floor-plan-right"> \
                        <label>'+ propertyshift_local_script.floor_plan_description +' </label> \
    				    <textarea name="ps_property_floor_plans['+count+'][description]"></textarea> \
    				    <div class="floor-plan-img"> \
                            <label>'+ propertyshift_local_script.floor_plan_img +' </label> \
                            <input type="text" name="ps_property_floor_plans['+count+'][img]" /> \
                            <input id="_btn" class="ns_upload_image_button" type="button" value="'+ propertyshift_local_script.upload_img +'" /> \
                            <span class="button-secondary remove">'+ propertyshift_local_script.remove_text +'</span> \
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

});