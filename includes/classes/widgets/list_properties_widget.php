<?php
/**
 * List Properties Widget Class
 */
class ns_real_estate_list_properties_widget extends WP_Widget {

    /** constructor */
    function __construct() {

        $widget_options = array(
          'classname'=>'list-properties-widget',
          'description'=> esc_html__('Display a list of properties.', 'propertyshift'),
          'panels_groups' => array('propertyshift')
        );
		parent::__construct('ns_real_estate_list_properties_widget', esc_html__('(Nightshift) List Properties', 'propertyshift'), $widget_options);
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );
        global $wpdb;

        $title = isset( $instance['title'] ) ? apply_filters('widget_title', $instance['title']) : '';
        $num = isset( $instance['num'] ) ? strip_tags($instance['num']) : 3;
        $layout = 'sidebar';
        $property_status = isset( $instance['property_status'] ) ? strip_tags($instance['property_status']) : '';
        $property_location = isset( $instance['property_location'] ) ? strip_tags($instance['property_location']) : '';
        $property_type = isset( $instance['property_type'] ) ? strip_tags($instance['property_type']) : '';
        $filter = isset( $instance['filter'] ) ? strip_tags($instance['filter']) : 'recent';
        ?>
            <?php echo wp_kses_post($before_widget); ?>
                <?php if ( $title )
                    echo wp_kses_post($before_title . $title . $after_title);

                        $meta_query_featured = array();
                        if ($filter == 'featured') {
                            $meta_query_featured[] = array(
                                'key' => 'ps_property_featured',
                                'value'   => 'true'
                            );
                        }

                        $args = array(
                            'post_type' => 'ps-property',
                            'showposts' => $num,
                            'property_status' => $property_status,
                            'property_location' => $property_location,
                            'property_type' => $property_type,
                            'meta_query' => $meta_query_featured,
                        ); 

                        if($layout == 'sidebar') {
                            $property_listing_query = new WP_Query( $args );

                            if ( $property_listing_query->have_posts() ) : while ( $property_listing_query->have_posts() ) : $property_listing_query->the_post(); ?>

                                <?php
                                // Load admin object & settings
                                $this->admin_obj = new PropertyShift_Admin();
                                $property_listing_crop = $this->admin_obj->load_settings(false, 'ns_property_listing_crop');

                                // Load property settings
                                $properties_obj = new PropertyShift_Properties();
                                $property_settings = $properties_obj->load_property_settings(get_the_id());
                                $price = $property_settings['price']['value'];
                                $price_postfix = $property_settings['price_postfix']['value'];
                                ?>

                                <div class="list-property">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                            <div class="property-img">
                                                <?php if ( has_post_thumbnail() ) {  ?>
                                                    <a href="<?php the_permalink(); ?>" class="property-img-link">
                                                        <?php if($property_listing_crop == 'true') { the_post_thumbnail('property-thumbnail'); } else { the_post_thumbnail('full'); } ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <a href="<?php the_permalink(); ?>" class="property-img-link"><img src="<?php echo PROPERTYSHIFT_DIR.'/images/property-img-default.gif'; ?>" alt="" /></a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                            <h5 title="<?php the_title(); ?>"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                                            <?php if(!empty($price)) { ?><p><strong><?php echo $properties_obj->get_formatted_price($price); ?></strong> <?php if(!empty($price_postfix)) { ?><span class="price-postfix"><?php echo esc_attr($price_postfix); ?></span><?php } ?></p><?php } ?>
                                        </div>
                                    </div>
                                </div>

                            <?php endwhile; wp_reset_postdata();
                            else: ?>
                                <p><?php esc_html_e('Sorry, no properties were found.', 'propertyshift'); ?> <?php if(is_user_logged_in() && current_user_can('administrator')) { echo '<i><b><a target="_blank" href="'. esc_url(home_url('/')) .'wp-admin/post-new.php?post_type=properties">Click here</a> to add a new property.</b></i>'; } ?></p>
                            <?php endif;
                        }

                echo wp_kses_post($after_widget); ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['num'] = strip_tags($new_instance['num']);
        $instance['property_status'] = strip_tags($new_instance['property_status']);
        $instance['property_location'] = strip_tags($new_instance['property_location']);
        $instance['property_type'] = strip_tags($new_instance['property_type']);
        $instance['filter'] = strip_tags($new_instance['filter']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {  

        $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'num' => 3, 'show_header' => null, 'show_pagination' => null, 'layout' => null, 'property_status' => null, 'property_location' => null, 'property_type' => null, 'filter' => null ) );
        $title = esc_attr($instance['title']);
        $num = esc_attr($instance['num']);
        $property_status = esc_attr($instance['property_status']);
        $property_location = esc_attr($instance['property_location']);
        $property_type = esc_attr($instance['property_type']);
        $filter = esc_attr($instance['filter']);
        ?>

        <p>
           <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'propertyshift'); ?></label>
           <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <p>
          <label for="<?php echo esc_attr($this->get_field_id('num')); ?>"><?php esc_html_e('Number of Properties:', 'propertyshift'); ?></label>
          <input class="widefat" id="<?php echo esc_attr($this->get_field_id('num')); ?>" name="<?php echo esc_attr($this->get_field_name('num')); ?>" type="number" value="<?php echo esc_attr($num); ?>" />
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('property_status')); ?>"><?php esc_html_e('Property Status:', 'propertyshift'); ?></label>
            <select name="<?php echo esc_attr($this->get_field_name('property_status')); ?>">
                <option value=""><?php esc_html_e( 'All', 'propertyshift' ); ?></option>
                <?php
                    $property_statuses = get_terms('property_status'); 
                    if ( !empty( $property_statuses ) && !is_wp_error( $property_statuses ) ) { ?>
                        <?php foreach ( $property_statuses as $property_status_select ) { ?>
                            <option value="<?php echo esc_attr($property_status_select->name); ?>" <?php if($property_status == $property_status_select->name) { echo 'selected'; } ?>><?php echo esc_attr($property_status_select->name); ?></option>
                        <?php } ?>
                <?php } ?>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('property_location')); ?>"><?php esc_html_e('Property Location:', 'propertyshift'); ?></label>
            <select name="<?php echo esc_attr($this->get_field_name('property_location')); ?>">
                <option value=""><?php esc_html_e( 'All', 'propertyshift' ); ?></option>
                <?php
                $property_locations = get_terms('property_location'); 
                if ( !empty( $property_locations ) && !is_wp_error( $property_locations ) ) { ?>
                    <?php foreach ( $property_locations as $property_location_select ) { ?>
                        <option value="<?php echo esc_attr($property_location_select->name); ?>" <?php if($property_location == $property_location_select->name) { echo 'selected'; } ?>><?php echo esc_attr($property_location_select->name); ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('property_type')); ?>"><?php esc_html_e('Property Type:', 'propertyshift'); ?></label>
            <select name="<?php echo esc_attr($this->get_field_name('property_type')); ?>">
                <option value=""><?php esc_html_e( 'All', 'propertyshift' ); ?></option>
                <?php
                    $property_types = get_terms('property_type'); 
                    if ( !empty( $property_types ) && !is_wp_error( $property_types ) ) { ?>
                        <?php foreach ( $property_types as $property_type_select ) { ?>
                            <option value="<?php echo esc_attr($property_type_select->name); ?>" <?php if($property_type == $property_type_select->name) { echo 'selected'; } ?>><?php echo esc_attr($property_type_select->name); ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('filter')); ?>"><?php esc_html_e('Filter By:', 'propertyshift'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('filter')); ?>" name="<?php echo esc_attr($this->get_field_name('filter')); ?>">
                <option value="recent" <?php if($filter == 'recent') { echo 'selected'; } ?>><?php esc_html_e('Most Recent', 'propertyshift'); ?></option>
                <option value="featured" <?php if($filter == 'featured') { echo 'selected'; } ?>><?php esc_html_e('Featured', 'propertyshift'); ?></option>
            </select>
        </p>

        <?php
    }

} // class utopian_recent_posts
add_action('widgets_init', create_function('', 'return register_widget("ns_real_estate_list_properties_widget");'));

?>