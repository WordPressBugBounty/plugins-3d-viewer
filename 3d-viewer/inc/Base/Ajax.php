<?php



namespace BP3D\Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX handler for setup wizard.
 */
class Ajax
{
    /**
     * Register AJAX actions.
     */
    public function register(): void
    {
        add_action('wp_ajax_bp3d_save_setup', [$this, 'saveSetup']);
    }

    /**
     * Handle the setup wizard save AJAX request.
     *
     * Validates nonce and permissions, then updates the Gutenberg
     * enabled setting based on the selected shortcode generator type.
     */
    public function saveSetup(): void
    {
        $nonce = sanitize_text_field(wp_unslash($_POST['_wpnonce'] ?? ''));

        if (!wp_verify_nonce($nonce, 'bp3d_security_key') || !current_user_can('manage_options')) {
            wp_send_json_error('Invalid nonce');
        }

        $value = sanitize_text_field(wp_unslash($_POST['value'] ?? ''));

        $settings = get_option('_bp3d_settings_', []);

        if (is_array($settings)) {
            $settings['gutenberg_enabled'] = $value === 'gutenberg-block' ? '1' : '0';
        }

        update_option('_bp3d_settings_', $settings);

        wp_send_json_success();
    }
}
