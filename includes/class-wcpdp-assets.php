<?php

if (!defined('ABSPATH')) {
    exit;
}

class WCPDP_Assets
{

    function wcpdp_enqueue_admin_assets()
    {
        wp_enqueue_script(
            'wcpdp-admin-scripts',
            WC_PRODUCTS_DESCRIPTION_PLUS_URL . 'includes/admin/assets/scripts/wcpdp_admin.js',
            array(),
            WC_PRODUCTS_DESCRIPTION_PLUS_VERSION,
            true
        );

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

        wp_enqueue_style(
            'wcpdp-popup',
            WC_PRODUCTS_DESCRIPTION_PLUS_URL . 'assets/styles/wcpdp-popup.css',
            [],
            WC_PRODUCTS_DESCRIPTION_PLUS_VERSION,
            'all'
        );

        wp_localize_script(
            'wcpdp-scripts',
            'WCPDP_ai_nonce',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wcpdp_gen_desc_nonce')
            ),
        );
    }

    function wcpdp_enqueue_assets() {}
}
