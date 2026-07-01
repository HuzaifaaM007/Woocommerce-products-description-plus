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
        $this->wcpdp_loader = new WCPDP_Loader();
    }

    function define_admin_hooks()
    {

        $wcpdp_admin = new WCPDP_Admin();
    }

    function define_wp_hooks()
    {
        $wcpdp_assets = new WCPDP_Assets();
    }


    function run()
    {
        $this->wcpdp_loader->run();
    }
}
