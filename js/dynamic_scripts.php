<?php
//Get RTL (right to left)
if(isset($_GET['rtl'])) { $rtl = $_GET['rtl']; } else { $rtl = esc_attr(get_option('rypecore_rtl')); } 

//Get currency options
$currency_symbol = esc_attr(get_option('rypecore_currency_symbol', '$'));
$currency_symbol_position = esc_attr(get_option('rypecore_currency_symbol_position', 'before'));
$currency_thousand = esc_attr(get_option('rypecore_thousand_separator', ','));
$currency_decimal = esc_attr(get_option('rypecore_decimal_separator', '.'));
$currency_decimal_num = esc_attr(get_option('rypecore_num_decimal', '0'));

$dynamic_script = '';

//OUTPUT VARIABLES FOR USE IN rype-real-estate.js
$dynamic_script .= "var rtl = '{$rtl}';";
$dynamic_script .= "var currency_symbol = '{$currency_symbol}';";
$dynamic_script .= "var currency_symbol_position = '{$currency_symbol_position}';";
$dynamic_script .= "var currency_thousand = '{$currency_thousand}';";
$dynamic_script .= "var currency_decimal = '{$currency_decimal}';";
$dynamic_script .= "var currency_decimal_num = '{$currency_decimal_num}';";
$dynamic_script .= "var contact_form_success = '{$contact_form_success}';";

wp_add_inline_script( 'rype-real-estate', $dynamic_script);

?>