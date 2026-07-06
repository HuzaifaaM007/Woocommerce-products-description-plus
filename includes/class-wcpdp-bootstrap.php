<?php

if (!defined('ABSPATH')) {
    exit;
}

class WCPDP_Bootstrap
{
    private WCPDP_Loader $wcpdp_loader;

    public function __construct()
    {
        $this->load_dependencies();
        $this->define_wp_hooks();
        $this->define_admin_hooks();
    }

    function load_dependencies()
    {
        require_once WC_PRODUCTS_DESCRIPTION_PLUS_PATH . 'includes/class-wcpdp-loader.php';
        require_once WC_PRODUCTS_DESCRIPTION_PLUS_PATH . 'includes/class-wcpdp-assets.php';
        require_once WC_PRODUCTS_DESCRIPTION_PLUS_PATH . 'includes/admin/class-wcpdp-admin.php';
        require_once WC_PRODUCTS_DESCRIPTION_PLUS_PATH . 'includes/class-wcpdp-ajax_requests.php';


        $this->wcpdp_loader = new WCPDP_Loader();
    }

    function define_admin_hooks()
    {

        error_log('admin hooks');
        $wcpdp_admin = new WCPDP_Admin();
        $this->wcpdp_loader->add_filter('woocommerce_settings_tabs_array', $wcpdp_admin, 'wcpdp_add_admin_settings_tab', 99);
        $this->wcpdp_loader->add_action('woocommerce_settings_tabs_wcpdp_settings', $wcpdp_admin, 'wcpdp_settings_tab');
        $this->wcpdp_loader->add_action('woocommerce_update_options_wcpdp_settings', $wcpdp_admin, 'wcpdp_update_settings');
        $this->wcpdp_loader->add_action('post_submitbox_misc_actions', $wcpdp_admin, 'wcpdp_add_custom_button_publish_box');

        $wcpdp_ajax_req = new WCPDP_Ajax_Request();
        $this->wcpdp_loader->add_action('wp_ajax_wcpdp_generate_description_ajax_request', $wcpdp_ajax_req, 'wcpdp_generate_description_ajax_request');

        $wcpdp_assets = new WCPDP_Assets();
        $this->wcpdp_loader->add_action('admin_enqueue_scripts', $wcpdp_assets, 'wcpdp_enqueue_assets');
    }

    function define_wp_hooks()
    {
        $wcpdp_assets = new WCPDP_Assets();
    }


    function run()
    {
        error_log('wcpdp_run_boot');
        $this->wcpdp_loader->run();
    }
}
