<?php global $current_user, $wp_roles; ?>    

<!-- start user dashboard -->
<div class="user-dashboard">
	<?php if(is_user_logged_in()) { 
		if(function_exists('ns_real_estate_property_submit_form')) { echo ns_real_estate_property_submit_form(); }
	} else {
        ns_basics_template_loader('alert_not_logged_in.php', null, false);
    } ?>
</div><!-- end user dashboard -->