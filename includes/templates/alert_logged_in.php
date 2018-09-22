<?php $current_user = wp_get_current_user(); ?>
<div class="alert-box success">
    <p><?php esc_html_e( 'You are logged in as', 'rypecore' ); ?> <b><a href="<?php echo get_edit_user_link(); ?>"><?php echo esc_attr($current_user->user_login); ?></a></b></p>
    <a href="<?php echo wp_logout_url(get_permalink()); ?>"><?php esc_html_e( 'Logout?', 'rypecore' ); ?></a>
</div>