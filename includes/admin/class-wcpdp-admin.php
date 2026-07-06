<?php

if (!defined('ABSPATH')) {
    exit;
}

class WCPDP_Admin
{

    /**
     * filter : woocommerce_settings_tabs_array
     */
    public function wcpdp_add_admin_settings_tab($settings_tabs)
    {
        $settings_tabs['wcpdp_settings'] = __('wc products description plus', 'wc-products-description-plus');
        return $settings_tabs;
    }

    public function wcpdp_get_settings()
    {
        error_log('get settings');
        $settings = array(
            array(
                'title' => __('woocommerce products descrition plus', 'wc-products-description-plus'),
                'type' => 'title',
                'id' => 'wcpdp_settings_title'
            ),
            array(
                'title' => __('Enable AI', 'wc-products-description-plus'),
                'type' => 'checkbox',
                'id' => 'wcpdp_enable_ai',
                'desc' => __('Enable AI description for products ', 'wc-products-description-plus'),
                'default' => 'no',
                'desc_tip' => true
            ),
            array(
                'title' => __('Select AI Model', 'wc-products-description-plus'),
                'type' => 'select',
                'id' => 'wcpdp_ai_model',
                'desc' => __('Select the model you are using for description generation', 'wc-products-description-plus'),
                'options' => array(
                    'gemini' => 'Gemini',
                    'Groqai' => 'GroqAI',
                    'openrouter' => 'OpenRouter'
                ),
                'default' => 'gemini',
                'desc_tip' => true
            ),
            array(
                'title' => __('API KEY', 'wc-products-description-plus'),
                'type' => 'text',
                'id' => 'wcpdp_api_key',
                'desc' => __('enter the api key for the model', 'wc-products-description-plus'),
                'default' => '',
                'desc_tip' => true
            ),
            array(
                'type' => 'sectionend',
                'id' => 'wcpdp_settings_end'
            ),
        );
        return $settings;
    }

    /**
     * action:  woocommerce_settings_tabs_wcpdp_settings
     */
    public function wcpdp_settings_tab()
    {
        error_log('settings tab');
        woocommerce_admin_fields($this->wcpdp_get_settings());
    }

    /**
     * action: woocommerce_update_options_wcpdp_settings
     */
    public function wcpdp_update_settings()
    {
        error_log('update settings ');
        woocommerce_update_options($this->wcpdp_get_settings());
    }

    /**
     * action:post_submitbox_start
     * another action as the first one is breaking the UI : post_submitbox_misc_actions
     */
    public function wcpdp_add_custom_button_publish_box()
    {
        // error_log('post button');
        global $post;

        if (!$post || $post->post_type !== 'product') {
            return;
        }
        // error_log('post button 2');
        // echo '<button type="button" id="wcpdp_ai_btn" class="button button-large" style="width=100%; margin-bottom=10px;">
        //         Generate Description
        //         </button>';

        echo '<div class="misc-pub-section" style="clear:both; display:flex align-items:center; justify-content:center; margin-bottom:10px; overflow:hidden;">
            <button type="button" id="wcpdp_ai_btn" class="button button-large" style="width:100%; ">
                Generate Description
            </button>
          </div>';
    }
}
