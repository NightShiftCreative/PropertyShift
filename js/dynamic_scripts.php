<?php
//Get RTL (right to left)
if(isset($_GET['rtl'])) { $rtl = $_GET['rtl']; } else { $rtl = esc_attr(get_option('ns_core_rtl')); } 

//Get currency options
$currency_options = rype_real_estate_get_curreny_options();

$dynamic_script = '';

//OUTPUT VARIABLES FOR USE IN rype-real-estate.js
$dynamic_script .= "var rtl = '{$rtl}';";
$dynamic_script .= "var currency_symbol = '{$currency_options['symbol']}';";
$dynamic_script .= "var currency_symbol_position = '{$currency_options['symbol_position']}';";
$dynamic_script .= "var currency_thousand = '{$currency_options['thousand']}';";
$dynamic_script .= "var currency_decimal = '{$currency_options['decimal']}';";
$dynamic_script .= "var currency_decimal_num = '{$currency_options['decimal_num']}';";

wp_add_inline_script( 'rype-real-estate', $dynamic_script);

?>