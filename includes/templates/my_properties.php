<?php
    $icon_set = esc_attr(get_option('ns_core_icon_set', 'fa'));
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $current_user = wp_get_current_user();
    $author = $current_user->user_login;
    $members_submit_property_page = get_option('rypecore_members_submit_property_page');
?>

<!-- start user my properties -->
<div class="user-dashboard">
    <?php if(is_user_logged_in()) { ?>

    	<table class="user-dashboard-table my-properties-table">
    		<tr class="user-dashboard-table-header my-properties-header">
                <td class="user-dashboard-table-img my-property-img"><?php esc_html_e('Image', 'rype-real-estate'); ?></td>
                <td class="my-property-title"><?php esc_html_e('Title', 'rype-real-estate'); ?></td>
                <td class="my-property-type"><?php esc_html_e('Type', 'rype-real-estate'); ?></td>
                <td class="my-property-status"><?php esc_html_e('Status', 'rype-real-estate'); ?></td>
                <td class="my-property-date"><?php esc_html_e('Date Created', 'rype-real-estate'); ?></td>
                <td class="user-dashboard-table-actions my-property-actions"><?php esc_html_e('Actions', 'rype-real-estate'); ?></td>
            </tr>

            <?php
            $property_listing_args = array(
                'post_type' => 'rype-property',
                'posts_per_page' => 12,
                'paged' => $paged,
                'author_name' => $author,
                'post_status' => array( 'pending', 'publish' )
            );

            $property_listing_query = new WP_Query( $property_listing_args );

            if ( $property_listing_query->have_posts() ) : while ( $property_listing_query->have_posts() ) : $property_listing_query->the_post(); ?>
                
                <?php
                //Get property type
                if(function_exists('rype_real_estate_get_property_type')) { $property_type = rype_real_estate_get_property_type($post->ID); } else { $property_type = ''; }
                ?>

                <tr class="my-properties-entry">
                    <td class="user-dashboard-table-img my-property-img">
                        <a href="<?php the_permalink(); ?>">
                            <?php if ( has_post_thumbnail() ) { the_post_thumbnail('thumbnail'); } else { echo '<img src="'.plugins_url( '/rype-real-estate/images/property-img-default.gif' ).'" alt="" />'; } ?>
                        </a>
                    </td>
                    <td class="my-property-title"><a href="<?php the_permalink(); ?>"><h4><?php the_title(); ?></h4></a></td>
                    <td class="my-property-type"><?php if(!empty($property_type)) { echo wp_kses_post($property_type); } else { echo '--'; } ?></td>
                    <td class="my-property-status"><?php echo get_post_status(); ?></td>
                    <td class="my-property-date"><?php the_time('F jS, Y') ?></td>
                    <td class="user-dashboard-table-actions my-property-actions">
                        <span><a href="<?php echo esc_url($members_submit_property_page); ?>?edit_property=<?php echo get_the_ID(); ?>"><?php echo ns_core_get_icon($icon_set, 'pencil'); ?> <?php esc_html_e('EDIT', 'rype-real-estate'); ?></a></span>
                        <span>
                            <?php 
                                if ($post->post_author == $current_user->ID) { ?>
                                    <a onclick="return confirm('Are you sure you want to delete this property?')" href="<?php echo get_delete_post_link( $post->ID ) ?>"><?php echo ns_core_get_icon($icon_set, 'trash'); ?> <?php esc_html_e('REMOVE', 'rype-real-estate'); ?></a>
                            <?php } ?>
                        </span>
                        <span><a href="<?php the_permalink(); ?>" target="_blank"><?php echo ns_core_get_icon($icon_set, 'eye', 'eye', 'preview'); ?> <?php esc_html_e('VIEW', 'rype-real-estate'); ?></a></span>
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
                'prev_text'    => esc_html__('&raquo; Previous', 'rype-real-estate'),
                'next_text'    => esc_html__('Next &raquo;', 'rype-real-estate'),
                'type'         => 'plain',
                'add_args'     => False,
                'add_fragment' => '',
                'before_page_number' => '',
                'after_page_number' => ''
            );
            ?>
            <div class="page-list"><?php echo paginate_links( $args ); ?> </div>
        <?php else: ?>
            </table>
            <p><?php esc_html_e('You have not posted any properties.', 'rype-real-estate'); ?></p>
            <?php wp_reset_postdata(); ?>
        <?php endif; ?>

    <?php } else {
        ns_basics_template_loader('alert_not_logged_in.php', null, false);
    } ?>

</div>
<!-- end user my properties -->