<?php
/**
 * List Agents Widget Class
 */
class ns_real_estate_list_agents_widget extends WP_Widget {

    /** constructor */
    function __construct() {

        $widget_options = array(
          'classname'=>'list-agents-widget',
          'description'=> esc_html__('Display a list of agents.', 'propertyshift'),
          'panels_groups' => array('propertyshift')
        );
		parent::__construct('ns_real_estate_list_agents_widget', esc_html__('(Nightshift) List Agents', 'propertyshift'), $widget_options);
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );
        global $wpdb;
		global $post;

        $icon_set = get_option('ns_core_icon_set', 'fa');
        if(function_exists('ns_core_load_theme_options')) { $icon_set = ns_core_load_theme_options('ns_core_icon_set'); }

        $title = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
        $num = isset( $instance['num'] ) ? $instance['num'] : '';
        $filter = isset( $instance['filter'] ) ? $instance['filter'] : '';

        ?>
              <?php echo wp_kses_post($before_widget); ?>
                  <?php if ( $title )
                        echo wp_kses_post($before_title . $title . $after_title);

                        /*****************************************/
                        //START AGENT COUNT QUERY
                        /*****************************************/
                        $agent_counts = array();
                        $agent_listing_args = array(
                            'post_type' => 'ps-agent',
                            'showposts' => -1,
                        );

                        $agent_listing_query = new WP_Query( $agent_listing_args );
                        if ( $agent_listing_query->have_posts() ) : while ( $agent_listing_query->have_posts() ) : $agent_listing_query->the_post();

                            $values = get_post_custom( $post->ID );
                            $agent_title = isset( $values['ps_agent_title'] ) ? esc_attr( $values['ps_agent_title'][0] ) : '';
                            $agent_email = isset( $values['ps_agent_email'] ) ? esc_attr( $values['ps_agent_email'][0] ) : '';

                            //property post count
                            $args = array(
                                'post_type' => 'ps-property',
                                'meta_key' => 'ps_agent_select',
                                'meta_value' => get_the_ID()
                            );

                            $meta_posts = get_posts( $args );
                            $meta_post_count = count( $meta_posts );
                            $agent_counts[] = array("id" => get_the_ID(), "count" => $meta_post_count, "permalink" => get_the_permalink(), "img" => get_the_post_thumbnail(get_the_id(), 'thumbnail'), "name" => get_the_title(), "title" => $agent_title, "email" => $agent_email);
                            unset( $meta_posts);

                        endwhile; 
                        wp_reset_postdata();
                        else:
                        endif; 

                        if($filter == 'num_properties') {
                            function ns_real_estate_sortByCount($a, $b) { return $b['count'] - $a['count']; }
                            usort($agent_counts, 'ns_real_estate_sortByCount');
                        } 

                        $count = 1;
                        if(count($agent_counts) > 0) {
                        foreach($agent_counts as $agent) { ?>  
                            <div class="list-property list-agent">
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                        <div class="property-img">
                                            <?php if (!empty($agent['img'])) {  ?>
                                                <a href="<?php echo esc_url($agent['permalink']); ?>" class="property-img-link"><?php echo wp_kses_post($agent['img']); ?></a>
                                            <?php } else { ?>
                                                <a href="<?php echo esc_url($agent['permalink']); ?>" class="property-img-link"><img src="<?php echo PROPERTYSHIFT_DIR.'/images/agent-img-default.gif'; ?>" alt="" /></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                        <h5 title="<?php the_title(); ?>"><a href="<?php echo esc_url($agent['permalink']); ?>"><?php echo esc_attr($agent['name']); ?></a></h5>
                                        <?php if(!empty($agent['title'])) { ?><p><?php echo ns_core_get_icon($icon_set, 'tag'); ?><?php echo esc_attr($agent['title']); ?></p><?php } ?>
                                        <?php if(!empty($agent['email'])) { ?><p><?php echo ns_core_get_icon($icon_set, 'envelope', 'envelope', 'mail'); ?><?php echo esc_attr($agent['email']); ?></p><?php } ?>
                                        <p><a href="<?php echo esc_url($agent['permalink']); ?>"><i class="fa fa-angle-right icon"></i><?php esc_html_e('View Details', 'propertyshift'); ?></a></p>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            if($count >= $num) { break; }
                            $count++; 
                            }
                        } else {
                            esc_html_e('Sorry, no agents were found.', 'propertyshift');
                        }
                        ?>

              <?php echo wp_kses_post($after_widget); ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['num'] = strip_tags($new_instance['num']);
        $instance['filter'] = strip_tags($new_instance['filter']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {  

        $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'num' => 3 ) );
        $title = esc_attr($instance['title']);
        $num = esc_attr($instance['num']);
        if(isset($instance['filter'])) { $filter = esc_attr($instance['filter']); } else { $filter = null; }

        ?>

        <p>
           <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'propertyshift'); ?></label>
           <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <p>
          <label for="<?php echo esc_attr($this->get_field_id('num')); ?>"><?php esc_html_e('Number of Agents:', 'propertyshift'); ?></label>
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('num')); ?>" name="<?php echo esc_attr($this->get_field_name('num')); ?>" type="number" value="<?php echo esc_attr($num); ?>" />
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('filter')); ?>"><?php esc_html_e('Filter By:', 'propertyshift'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('filter')); ?>" name="<?php echo esc_attr($this->get_field_name('filter')); ?>">
                <option value="recent" <?php if($filter == 'recent') { echo 'selected'; } ?>><?php esc_html_e('Most Recent', 'propertyshift'); ?></option>
                <option value="num_properties" <?php if($filter == 'num_properties') { echo 'selected'; } ?>><?php esc_html_e('Number of Assigned Properties', 'propertyshift'); ?></option>
            </select>
        </p>

        <?php
    }

} // class utopian_recent_posts
add_action('widgets_init', create_function('', 'return register_widget("ns_real_estate_list_agents_widget");'));

?>