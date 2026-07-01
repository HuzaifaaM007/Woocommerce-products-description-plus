<?php

if (!defined('ABSPATH')) {
    exit;
}

class WCPDP_Assets
{

    function wcpdp_enqueue_assets()
    {

        wp_enqueue_script(
            'wcpdp-scripts',
            WC_PRODUCTS_DESCRIPTION_PLUS_URL . 'assets/scripts/wcpdp.js',
            array(),
            WC_PRODUCTS_DESCRIPTION_PLUS_VERSION,
            true
        );

        wp_enqueue_style(
            'wcpdp-styles',
            WC_PRODUCTS_DESCRIPTION_PLUS_URL . 'assets/styles/wcpdp.css',
            array(),
            WC_PRODUCTS_DESCRIPTION_PLUS_VERSION,
            'all'
        );
    }
}
