<?php
// Exit if accessed directly
if (!defined( 'ABSPATH')) { exit; }

/**
 *	NS_Real_Estate_Shortcodes class
 *
 *  Registers and handles all shortcodes
 */
class NS_Real_Estate_Shortcodes {

	/**
	 *	Constructor
	 */
	public function __construct() {
		add_action( 'media_buttons', array( $this, 'add_shortcode_wizard'));
		add_filter("the_content", array( $this, 'content_filter'));
		add_shortcode('ns_list_properties', array( $this, 'add_shortcode_list_properties'));
	}

	/**
	 * Content filter
	 *
	 * Remove <p> and <br/> tags from shortcode content
	 */
	public function content_filter($content) {
		$block = join("|",array('ns_list_properties', 'ns_list_property_tax', 'ns_submit_property', 'ns_my_properties', 'ns_property_filter', 'ns_list_agents'));
    	$rep = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]",$content);
    	$rep = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)?/","[/$2]",$rep);
		return $rep;
	}

	/**
	 * Add shortcode wizard
	 */
	public function add_shortcode_wizard($editor_id) {
		if($editor_id == 'content') { ?>

		<?php }
	}

	/**
	 * List Properties
	 *
	 * @param array $atts
	 * @param string $content
	 */
	public function add_shortcode_list_properties($atts, $content=null) {
		$num_properties_per_page = esc_attr(get_option('ns_num_properties_per_page', 12));
	    $atts = shortcode_atts(
	        array (
	            'show_posts' => $num_properties_per_page,
	            'show_header' => false,
	            'show_pagination' => false,
	            'layout' => 'grid',
	            'cols' => null,
	            'property_status' => '',
	            'property_location' => '',
	            'property_type' => '',
	            'featured' => 'false'
	    ), $atts);

	    $meta_query_featured = array();
	    if ($atts['featured'] != 'false') {
	        $meta_query_featured[] = array(
	            'key' => 'ns_property_featured',
	            'value'   => 'true'
	        );
	    }

	    $args = array(
	        'posts_per_page' => $atts['show_posts'],
	        'property_status' => $atts['property_status'],
	        'property_location' => $atts['property_location'],
	        'property_type' => $atts['property_type'],
	        'meta_query' => $meta_query_featured,
	    );

	    ob_start();
	    if(function_exists('ns_real_estate_template_loader')) {

	        //Set template args
	        $template_args = array();
	        $template_args['custom_args'] = $args;
	        $template_args['custom_show_filter'] = $atts['show_header'];
	        $template_args['custom_layout'] = $atts['layout'];
	        $template_args['custom_pagination'] = $atts['show_pagination'];
	        $template_args['custom_cols'] = $atts['cols'];
	        $template_args['no_post_message'] = esc_html__( 'Sorry, no properties were found.', 'ns-real-estate' );

	        //Load template
	        ns_real_estate_template_loader('loop_properties.php', $template_args);
	    }
	    $output = ob_get_clean();

	    return $output;
	}

}
?>