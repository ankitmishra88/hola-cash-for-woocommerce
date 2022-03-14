<?php
/**
 * Plugin Name:       Hola Cash For WooCommerce
 * Plugin URI:        https://www.hola.cash/
 * Description:       Handle the basics with this plugin.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Ankit Mishra
 * Author URI:        https://ankitmishra88.github.io
 * Text Domain:       hola-cash-wc
 * Domain Path:       /languages
 */

 defined('ABSPATH')||die('No Script Kiddies Please');

 // Let's define our plugin constants here

 defined('HOLA_WC_FILE')||define('HOLA_WC_FILE', __FILE__);

 defined('HOLA_WC_DIR')||define('HOLA_WC_DIR',untrailingslashit(dirname(HOLA_WC_FILE)));

 defined('HOLA_WC_URL')||define('HOLA_WC_URL',plugin_dir_url(HOLA_WC_FILE));

 require_once(HOLA_WC_DIR.'/includes/class.hola_cash.php');

 global $hola_wc;

 
 if(class_exists('\HOLA_WC\HOLA_CASH')){
    $hola_wc=new \HOLA_WC\HOLA_CASH();
 }

 // get the already created instance of hola_cash
 function hola_cash_wc(){
     return $GLOBALS['hola_wc'];
 }

?>