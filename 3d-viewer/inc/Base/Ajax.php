<?php

namespace BP3D\Base;

class Ajax {
    public function register(){
        add_action('wp_ajax_bp3d_save_setup', [$this, 'save_setup']);
    }

    public function save_setup(){
        if(!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'] ?? '')), 'bp3d_security_key') || !current_user_can('manage_options')){
            wp_send_json_error('Invalid nonce');
        }

        $id = sanitize_text_field(wp_unslash($_POST['id'] ?? ''));
        $value = sanitize_text_field(wp_unslash($_POST['value'] ?? ''));

        $settings = get_option('_bp3d_settings_', []);

        if(is_array($settings)){
            $settings['gutenberg_enabled'] = $value === 'gutenberg-block' ? '1' : '0';
        }

        update_option('_bp3d_settings_', $settings);

        wp_send_json_success();
        
    }
}


