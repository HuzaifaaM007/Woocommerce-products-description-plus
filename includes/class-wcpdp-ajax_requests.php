<?php


if (!defined('ABSPATH')) {
    exit;
}


class WCPDP_Ajax_Request
{
    private WCPDP_AI $wcpdp_ai;



    /**
     * action:wp_ajax_wcpdp_generate_description_ajax_request
     */
    public function wcpdp_generate_description_ajax_request()
    {


        require_once WC_PRODUCTS_DESCRIPTION_PLUS_PATH . 'includes/class-wcpdp-ai.php';
        $this->wcpdp_ai = new WCPDP_AI();


        if (!current_user_can('manage_options')) {
            wp_send_json_error('permission Denied', 403);
            error_log('ajax request 1.0');

            return;
        }


        error_log('ajax request');

        check_ajax_referer('wcpdp_gen_desc_nonce', 'nonce');

        error_log('ajax request 2 ');

        $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
        $product_data = array();

        if ($product_id) {
            $product = wc_get_product($product_id);

            if (!$product) {
                wp_send_json_error('Invalid product ID', 404);
                return;
            }

            error_log('product id ');
            $product_data = array(
                'name' => $product->get_name(),
                'is_virtual' => $product->is_virtual(),
                'regular_price'  => $product->get_regular_price(),
                'sale_price'     => $product->get_sale_price(),
                'sku'            => $product->get_sku(),
                'weight'         => $product->get_weight(),
                'length'         => $product->get_length(),
                'width'          => $product->get_width(),
                'height'         => $product->get_height(),
                'categories'     => $product->get_category_ids(),
                'permalink'      => $product->get_permalink(),
                'product_type'   => $product->get_type(),
            );
        } else {
            error_log('no id');

            $product_data = array(
                'name'              => sanitize_text_field($_POST['name'] ?? ''),
                'is_virtual'        => $_POST['is_virtual'],
                'regular_price'     => wc_format_decimal($_POST['regular_price'] ?? ''),
                'sale_price'        => wc_format_decimal($_POST['sale_price'] ?? ''),
                'sku'               => sanitize_text_field($_POST['sku'] ?? ''),

                'weight'            => wc_format_decimal($_POST['weight'] ?? ''),
                'length'            => wc_format_decimal($_POST['length'] ?? ''),
                'width'             => wc_format_decimal($_POST['width'] ?? ''),
                'height'            => wc_format_decimal($_POST['height'] ?? ''),


                'product_type'      => sanitize_text_field($_POST['product_type'] ?? ''),

                'categories' => array_map(
                    'sanitize_text_field',
                    $_POST['categories'] ?? array()
                ),
            );
        }

        error_log('dimensions check: ' . print_r([
            'length' => $product_data['length'] ?? 'NOT SET',
            'width'  => $product_data['width'] ?? 'NOT SET',
            'height' => $product_data['height'] ?? 'NOT SET',
        ], true));

        $product_null_atts = $this->product_data_validator($product_data);

        error_log('null atts' . print_r($product_null_atts, true));

        if (is_array($product_null_atts) && !empty($product_null_atts)) {
            wp_send_json_error('Following attributes are missing  ' . print_r($product_null_atts));
        }

        $result = $this->wcpdp_ai->wcpdp_gen_desc($product_data);

        if ($result['success']) {
            wp_send_json_success($result['content']);
        }

        error_log('printing the products data' . print_r($product_data, true));
        wp_send_json_error($result['error']);
    }

    function product_data_validator($product_data)
    {
        $required_fields = array('name', 'regular_price',  'product_type');
        $missing_atts = array();

        if (!$product_data['is_virtual']) {
            $required_fields[] = 'weight';
            $required_fields[] = 'length';
            $required_fields[] = 'height';
            $required_fields[] = 'width';
        }

        foreach ($required_fields as $field) {
            if (empty($product_data[$field])) {
                $missing_atts[] = $field;
            }
        }

        if (!empty($missing_atts)) {
            return $missing_atts;
        }

        return true;
    }
}
