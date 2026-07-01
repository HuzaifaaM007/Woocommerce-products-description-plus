<?php

/**
 * Plugin Name: Woocommerce products description plus
 * Plugin URI: https://woocommerceproductsdescriptionplus.com
 * Description: adds quality description to the products 
 * Version: 1.0.0
 * Author: Huzaifa Murtaza
 * Author URI: https://huzaifamurtaza.com
 * Text Domain: woocommerce-products-description-plus
 *  
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WC_PRODUCTS_DESCRIPTION_PLUS_PATH', plugin_dir_path(__FILE__));
define('WC_PRODUCTS_DESCRIPTION_PLUS_URL', plugin_dir_url(__FILE__));
define('WC_PRODUCTS_DESCRIPTION_PLUS_VERSION', '1.0.0');

require_once WC_PRODUCTS_DESCRIPTION_PLUS_PATH . 'includes/class-wcpdp-bootstrap.php';

register_activation_hook(__FILE__, 'wcpdp_plugin_activator');

function wcpdp_plugin_activator() {}
