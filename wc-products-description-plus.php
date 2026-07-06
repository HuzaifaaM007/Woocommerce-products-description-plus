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

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}


define('WC_PRODUCTS_DESCRIPTION_PLUS_PATH', plugin_dir_path(__FILE__));
define('WC_PRODUCTS_DESCRIPTION_PLUS_URL', plugin_dir_url(__FILE__));
define('WC_PRODUCTS_DESCRIPTION_PLUS_VERSION', '1.0.0');

require_once WC_PRODUCTS_DESCRIPTION_PLUS_PATH . 'includes/class-wcpdp-bootstrap.php';

register_activation_hook(__FILE__, 'wcpdp_plugin_activator');

function wcpdp_is_wc_activated()
{
    return class_exists('woocommerce');
}

function wcpdp_show_admin_notice_and_deactivate(callable $notice_callback, bool $autodeactivate = true)
{

    if (!is_callable($notice_callback)) {
        return;
    }

    add_action('admin_notices', function () use ($notice_callback, $autodeactivate) {
        $notice_callback();
        if ($autodeactivate) {
            deactivate_plugins(plugin_basename(__FILE__));
            if (isset($_GET['activate'])) {
                unset($_GET['activate']);
            }
        }
    });
}

function wcpdp_wc_missing_notice()
{
    printf(
        '<div class="notice notice-error"><span class="notice-title">%1$s</span><p>%2$s</p></div>',
        wp_kses(
            sprintf(
                __(
                    'WooCommerce Prodcut Description plus requires WooCommerce to be installed and activated. <a href="%s"> Install WooCommerce</a>.',
                    'wc-products-description-plus'
                ),
                esc_url(network_admin_url('plugin-install.php?tab=plugin-information&plugin=woocommerce'))
            ),
            [
                'a' => [
                    'href' =>   [],
                ],
            ]
        )
    );
}


function wcpdp_plugin_activator()
{

    if (!wcpdp_is_wc_activated()) {
        wcpdp_show_admin_notice_and_deactivate('wcpdp_wc_missing_notice');
        return;
    }
}

function wcpdp_run_plugin()
{

    if (!wcpdp_is_wc_activated()) {
        wcpdp_show_admin_notice_and_deactivate('wcpdp_wc_missing_notice');
        return;
    }

    $bootstrap = new WCPDP_Bootstrap();
    $bootstrap->run();
}

add_action('plugins_loaded', 'wcpdp_run_plugin');
