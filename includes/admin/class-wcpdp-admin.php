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
                'title' => __('Enable Custom Prompt', 'wc-products-description-plus'),
                'type' => 'checkbox',
                'id' => 'wcpdp_enable_custom_prompt',
                'desc' => __('Enable custom prompt ', 'wc-products-description-plus'),
                'default' => 'no',
                'desc_tip' => true
            ),
            array(
                'title' => __('Custom Prompt', 'wc-products-description-plus'),
                'type' => 'textarea',
                'id' => 'wcpdp_custom_ai_prompt',
                'default' => '',
                'desc_tip' => false,
                'desc' => __('<strong>Leave empty to use the default prompt.</strong><br><br>

        This prompt is used as the instructions for the AI. Your product data is automatically appended, so you do <strong>not</strong> need to include product information yourself.<br><br>

        <strong>Important:</strong><br>
        • Do not include product data placeholders.<br>
        • Do not change the required output format.<br>
        • The AI must respond exactly as follows:<br><br>

        <code>SHORT_DESCRIPTION:</code><br>
        <code>[short description]</code><br><br>

        <code>FULL_DESCRIPTION:</code><br>
        <code>[full description]</code><br><br>', 'wc-products-description-plus'),
            ),
            array(
                'type' => 'sectionend',
                'id' => 'wcpdp_settings_end'
            ),
            array(
                'title' => __('Description settings ', 'wc-products-description-plus'),
                'type' => 'title',
                'id' => 'wcpdp_desc_settings'
            ),
            array(
                'title' => __('Generate detailed description', 'wc-products-description-plus'),
                'type' => 'checkbox',
                'id' => 'wcpdp_gen_detailed_desc',
                'desc' => __('Generate detailed description for products', 'wc-products-description-plus'),
                'default' => 'no',
                'desc_tip' => true
            ),

            array(
                'title' => __('Add Headings in description', 'wc-products-description-plus'),
                'type' => 'checkbox',
                'id' => 'wcpdp_add_headings',
                'desc' => __('Add headings in the description text ', 'wc-products-description-plus'),
                'default' => 'no',
                'desc_tip' => true
            ),
            array(
                'title' => __('Add Tables', 'wc-products-description-plus'),
                'type' => 'checkbox',
                'id' => 'wcpdp_add_tables',
                'desc' => __('Add tables in description', 'wc-products-description-plus'),
                'default' => 'no',
                'desc_tip' => true
            ),
            array(
                'title' => __('Generate description in bullet form', 'wc-products-description-plus'),
                'type' => 'checkbox',
                'id' => 'wcpdp_gen_bullets_desc',
                'desc_tip' => true,
                'desc' => __('Generate description in bullet form for the products ', 'wc-products-description-plus'),
                'default' => 'no',

            ),

            array(
                'type' => 'sectionend',
                'id' => 'wcpdp_desc_settings_end'
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
