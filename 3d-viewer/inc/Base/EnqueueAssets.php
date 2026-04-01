<?php


namespace BP3D\Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Asset enqueue handler.
 *
 * Registers and enqueues all frontend and backend scripts/styles
 * for the 3D Viewer plugin.
 */
class EnqueueAssets
{
    /**
     * Register WordPress hooks for asset enqueuing.
     */
    public function register(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueBackendFiles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueFrontEndFiles']);
        add_filter('script_loader_tag', [$this, 'addModuleTypeAttribute'], 10, 3);
        add_action('wp_footer', [$this, 'renderCustomCss']);
    }

    /**
     * Add type="module" attribute to the model-viewer script tag.
     */
    public function addModuleTypeAttribute(string $tag, string $handle, string $src): string
    {
        if ($handle !== 'bp3d-model-viewer') {
            return $tag;
        }

        return '<script type="module" id="' . $handle . '-js" src="' . esc_url($src) . '"></script>';
    }

    /**
     * Enqueue frontend scripts with localized data.
     */
    public function enqueueFrontEndFiles(): void
    {
        wp_register_script('bp3d-model-viewer', BP3D_DIR . 'public/js/model-viewer.latest.min.js', [], BP3D_VERSION, true);
        wp_register_script('bp3d-o3dviewer', BP3D_DIR . 'public/js/o3dv.min.js', [], BP3D_VERSION, true);

        wp_localize_script('bp3d-public', 'assetsUrl', [
            'siteUrl' => site_url(),
            'assetsUrl' => BP3D_DIR . '/public',
        ]);
    }

    /**
     * Register and enqueue backend admin scripts and styles.
     */
    public function enqueueBackendFiles(string $hook_suffix): void
    {
        global $post;

        $post_type = $post->post_type
            ?? (isset($_GET['post_type']) ? sanitize_text_field(wp_unslash($_GET['post_type'])) : null);

        // Admin script & styles
        wp_register_script('bp3d-admin-script', BP3D_DIR . 'build/admin.js', ['jquery'], BP3D_VERSION, true);
        wp_register_style('bp3d-admin-style', BP3D_DIR . 'admin/css/admin-style.css', [], BP3D_VERSION);
        wp_register_style('bp3d-readonly-style', BP3D_DIR . 'admin/css/readonly.css', [], BP3D_VERSION);

        if (in_array($post_type, ['bp3d-model-viewer', 'product'], true)) {
            wp_enqueue_style('bp3d-admin-style');
            wp_enqueue_style('bp3d-readonly-style');
            wp_enqueue_script('bp3d-admin-script');
        }

        // 3D viewer libraries
        wp_register_script('bp3d-model-viewer', BP3D_DIR . 'public/js/model-viewer.latest.min.js', [], BP3D_VERSION, true);
        wp_register_script('bp3d-o3dviewer', BP3D_DIR . 'public/js/o3dv.min.js', [], BP3D_VERSION, true);

        // Visual editor
        wp_register_script('bp3d-visual-editor', BP3D_DIR . 'build/visual-editor/index.js', [
            'b3dviewer-modelviewer-editor-script',
            'wp-block-library',
            'wp-editor',
            'wp-i18n',
            'wp-api',
            'wp-util',
            'lodash',
            'wp-data',
            'wp-core-data',
            'wp-api-request',
            'wp-tinymce',
            'bp3d-model-viewer',
            'bp3d-o3dviewer',
            'wp-components',
        ], BP3D_VERSION, true);

        wp_register_style('bp3d-visual-editor', BP3D_DIR . 'build/visual-editor/index.css', [
            'b3dviewer-modelviewer-editor-style',
            'wp-components',
            'wp-block-library',
            'wp-block-editor',
            'wp-edit-blocks',
            'wp-format-library',
        ], BP3D_VERSION, 'all');
    }

    /**
     * Output custom CSS from plugin settings in the footer.
     */
    public function renderCustomCss(): void
    {
        $settings = \BP3D\Helper\Utils::getSettings('_bp3d_settings_', []);
?>
        <style>
            <?php echo esc_html($settings('custom_css')); ?>
        </style>
        <?php
    }
}
