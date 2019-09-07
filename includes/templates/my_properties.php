<?php
    //GET GLOBAL SETTINGS
    global $post;
    $admin_obj = new PropertyShift_Admin();
    $icon_set = esc_attr(get_option('ns_core_icon_set', 'fa'));
    if(function_exists('ns_core_load_theme_options')) { $icon_set = ns_core_load_theme_options('ns_core_icon_set'); }
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $current_user = wp_get_current_user();
    $author = $current_user->user_login;
    $members_submit_property_page = $admin_obj->load_settings(false, 'ps_members_submit_property_page');

    //Get agent properties
    $agent_obj = new PropertyShift_Agents();
    $synced_agent = $agent_obj->get_synced_agent_id($current_user->ID);
    $agent_properties = $agent_obj->get_agent_properties($synced_agent, 12, true, array('pending', 'publish'));

    //GET CUSTOM ARGS
    if(isset($template_args)) {
        $custom_args = $template_args['custom_args'];
        $custom_pagination = $template_args['custom_pagination'];
    }
?>

<!-- start user my properties -->
<div class="user-dashboard">
    <?php if(is_user_logged_in()) { ?>

    	<table class="user-dashboard-table my-properties-table">
    		<tr class="user-dashboard-table-header my-properties-header">
                <td class="user-dashboard-table-img my-property-img"><?php esc_html_e('Image', 'propertyshift'); ?></td>
                <td class="my-property-title"><?php esc_html_e('Title', 'propertyshift'); ?></td>
                <td class="my-property-type"><?php esc_html_e('Type', 'propertyshift'); ?></td>
                <td class="my-property-status"><?php esc_html_e('Status', 'propertyshift'); ?></td>
                <td class="my-property-date"><?php esc_html_e('Date', 'propertyshift'); ?></td>
                <td class="user-dashboard-table-actions my-property-actions"><?php esc_html_e('Actions', 'propertyshift'); ?></td>
            </tr>

            <?php
            $my_properties_listing_args = $agent_properties['args'];
            //$my_properties_listing_args['post_status'] = array('pending', 'publish');

            //OVERWRITE QUERY WITH CUSTOM ARGS
            if(isset($custom_args)) {
                foreach($my_properties_listing_args as $key=>$value) {
                    if(array_key_exists($key, $custom_args)) { 
                        if(!empty($custom_args[$key])) { $my_properties_listing_args[$key] = $custom_args[$key]; }
                    } 
                }
                foreach($custom_args as $key=>$value) {
                    if(!array_key_exists($key, $my_properties_listing_args)) { 
                        if(!empty($custom_args[$key])) { $my_properties_listing_args[$key] = $custom_args[$key]; }
                    } 
                }
            }

            $property_listing_query = new WP_Query( $my_properties_listing_args );
            if ( $property_listing_query->have_posts() ) : while ( $property_listing_query->have_posts() ) : $property_listing_query->the_post(); ?>
                
                <?php
                //Get property type
                $properties_obj = new PropertyShift_Properties();
                $property_type = $properties_obj->get_tax($post->ID, 'property_type');
                ?>

                <tr class="my-properties-entry">
                    <td class="user-dashboard-table-img my-property-img">
                        <a href="<?php the_permalink(); ?>">
                            <?php if ( has_post_thumbnail() ) { the_post_thumbnail('thumbnail'); } else { echo '<img src="'.PROPERTYSHIFT_DIR.'/images/property-img-default.gif" alt="" />'; } ?>
                        </a>
                    </td>
                    <td class="my-property-title"><a href="<?php the_permalink(); ?>"><h4><?php the_title(); ?></h4></a></td>
                    <td class="my-property-type"><?php if(!empty($property_type)) { echo wp_kses_post($property_type); } else { echo '--'; } ?></td>
                    <td class="my-property-status"><?php echo get_post_status(); ?></td>
                    <td class="my-property-date"><?php the_time('F jS, Y') ?></td>
                    <td class="user-dashboard-table-actions my-property-actions">
                        <span><a href="<?php echo esc_url($members_submit_property_page); ?>?edit_property=<?php echo get_the_ID(); ?>"><?php echo ns_core_get_icon($icon_set, 'pencil-alt', 'pencil', 'pencil'); ?> <?php esc_html_e('EDIT', 'propertyshift'); ?></a></span>
                        <?php if($post->post_author == $current_user->ID) { ?>
                            <span>
                                <a onclick="return confirm('Are you sure you want to delete this property?')" href="<?php echo get_delete_post_link( $post->ID ) ?>"><?php echo ns_core_get_icon($icon_set, 'trash'); ?> <?php esc_html_e('REMOVE', 'propertyshift'); ?></a>
                            </span>
                        <?php } ?>
                        <span><a href="<?php the_permalink(); ?>" target="_blank"><?php echo ns_core_get_icon($icon_set, 'eye', 'eye', 'preview'); ?> <?php esc_html_e('VIEW', 'propertyshift'); ?></a></span>
                    </td>
                </tr>
        <?php endwhile; ?>
            </table>
            <?php wp_reset_postdata();
            $big = 999999999; // need an unlikely integer

            $args = array(
                'base'         => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format'       => '/page/%#%',
                'total'        => $property_listing_query->max_num_pages,
                'current'      => max( 1, get_query_var('paged') ),
                'show_all'     => False,
                'end_size'     => 1,
                'mid_size'     => 2,
                'prev_next'    => True,
                'prev_text'    => esc_html__('&raquo; Previous', 'propertyshift'),
                'next_text'    => esc_html__('Next &raquo;', 'propertyshift'),
                'type'         => 'plain',
                'add_args'     => False,
                'add_fragment' => '',
                'before_page_number' => '',
                'after_page_number' => ''
            );
            
            //DETERMINE IF PAGINATION IS NEEDED
            if(isset($custom_pagination)) { 
                if ($custom_pagination === false || $custom_pagination === 'false') { $custom_pagination = false; } else { $custom_pagination = true; }
                $show_pagination = $custom_pagination; 
            } else { 
                $show_pagination = true; 
            }

            if($show_pagination === true) { ?>
                <div class="page-list"><?php echo paginate_links( $args ); ?> </div>
            <?php } ?>

        <?php else: ?>
            </table>
            <p><?php esc_html_e('You have not posted any properties.', 'propertyshift'); ?></p>
            <?php wp_reset_postdata(); ?>
        <?php endif; ?>

    <?php } else {
        ns_basics_template_loader('alert_not_logged_in.php', null, false);
    } ?>

</div>
<!-- end user my properties -->