<?php
//Get RTL (right to left)
if(isset($_GET['rtl'])) { $rtl = $_GET['rtl']; } else { $rtl = esc_attr(get_option('ns_core_rtl')); } 

//Get currency options
$admin_obj = new PropertyShift_Admin();
$settings = $admin_obj->load_settings();

$dynamic_script = '';

//OUTPUT VARIABLES FOR USE IN ns-real-estate.js
$dynamic_script .= "var rtl = '{$rtl}';";
$dynamic_script .= "var currency_symbol = '{$settings['ns_real_estate_currency_symbol']}';";
$dynamic_script .= "var currency_symbol_position = '{$settings['ns_real_estate_currency_symbol_position']}';";
$dynamic_script .= "var currency_thousand = '{$settings['ns_real_estate_thousand_separator']}';";
$dynamic_script .= "var currency_decimal = '{$settings['ns_real_estate_decimal_separator']}';";
$dynamic_script .= "var currency_decimal_num = '{$settings['ns_real_estate_num_decimal']}';";

wp_add_inline_script( 'propertyshift', $dynamic_script);

?>