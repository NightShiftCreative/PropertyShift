<?php global $current_user, $wp_roles; ?>    

<!-- start user dashboard -->
<div class="user-dashboard">
	<?php if(is_user_logged_in()) { 
		if(function_exists('rype_real_estate_property_submit_form')) { echo rype_real_estate_property_submit_form(); }
	} else {
        rype_basics_template_loader('alert_not_logged_in.php', null, false);
    } ?>
</div><!-- end user dashboard -->