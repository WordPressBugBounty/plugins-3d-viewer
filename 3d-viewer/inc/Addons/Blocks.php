<?php



namespace BP3D\Addons;

if (!defined('ABSPATH')) {
    exit;
}

use BP3D\Helper\Utils;

/**
 * Gutenberg blocks handler.
 *
 * Registers and manages Gutenberg blocks for the 3D Viewer plugin,
 * including asset enqueuing, AJAX handlers, and block-specific
 * script localization.
 */
class Blocks
{
    /**
     * Register WordPress hooks for Gutenberg blocks.
     */
    public function register(): void
    {
        add_action('init', [$this, 'init'], 0);
        add_action('rest_api_init', [$this, 'rest_api_init']);
        add_action('init', [$this, 'registerBlockAssets'], 0);
        add_action('enqueue_block_editor_assets', [$this, 'enqueueEditorAssets']);
        add_action('wp_ajax_bp3dviewer_create_page', [$this, 'createPage']);

        // pipe checker
        add_action('wp_ajax_nopriv_bp3d_pipe_checker', [$this, 'bp3d_pipe_checker']);
        add_action('wp_ajax_bp3d_pipe_checker', [$this, 'bp3d_pipe_checker']);
    }

    /**
     * Register block-related scripts and styles on init.
     */
    public function registerBlockAssets(): void
    {
        // Frontend styles
        wp_register_style(
            'bp3d-frontend',
            BP3D_DIR . 'build/frontend.css',
        [],
            BP3D_VERSION,
            'all'
        );

        wp_register_style(
            'bp3d-custom-style',
            BP3D_DIR . 'public/css/custom-style.css',
        [],
            BP3D_VERSION,
            'all'
        );

        // Frontend script
        wp_register_script(
            'bp3d-public',
            BP3D_DIR . 'build/frontend.js',
        ['react', 'react-dom'],
            BP3D_VERSION,
            true
        );

        $settings = Utils::getSettings('_bp3d_settings_', []);

        wp_localize_script('bp3d-public', 'bp3dBlock', [
            'modelViewerSrc' => BP3D_DIR . 'public/js/model-viewer.latest.min.js',
            'o3dviewerSrc' => BP3D_DIR . 'public/js/o3dv.min.js',
            'selectors' => [
                'gallery' => $this->get_default_selector($settings('gallery', $settings('product_gallery_selector')), '.woocommerce-product-gallery'),
                'gallery_item' => $this->get_default_selector($settings('gallery_item'), '.woocommerce-product-gallery__image'),
                'gallery_item_active' => $this->get_default_selector($settings('gallery_item_active'), '.woocommerce-product-gallery__image.flex-active-slide'),
                'gallery_thumbnail_item' => $this->get_default_selector($settings('gallery_thumbnail_item'), '.flex-control-thumbs li'),
                'gallery_trigger' => $this->get_default_selector($settings('gallery_trigger'), '.woocommerce-product-gallery__trigger'),
            ]
        ]);
    }

    /**
     * Register block types from build directory.
     */
    public function init(): void
    {
        register_block_type(BP3D_PATH . 'build/blocks/3d-viewer');
        register_block_type(BP3D_PATH . 'build/blocks/preset');
    }

    /**
     * Register REST route for pipe check.
     */
    public function rest_api_init(): void
    {
        register_rest_route('bp3d/v1', '/pipe-check', [
            'methods' => 'GET',
            'callback' => [$this, 'pipeCheckCallback'],
            'permission_callback' => function () {
            return current_user_can('edit_posts');
        },
        ]);
    }

    /**
     * Handle pipe check REST API callback.
     *
     * @return \WP_REST_Response
     */
    public function pipeCheckCallback(): \WP_REST_Response
    {
        return new \WP_REST_Response(['status' => 'ok'], 200);
    }

    /**
     * Enqueue block editor assets with localized data.
     */
    public function enqueueEditorAssets(): void
    {
        $presets_raw = get_posts([
            'post_type' => 'bp3d-preset',
            'posts_per_page' => -1,
        ]);

        $presets = [];
        foreach ($presets_raw as $preset) {
            $block_content = \BP3D\Helper\Block::getBlock($preset->ID);
            $presets[] = [
                'id' => $preset->ID,
                'title' => $preset->post_title,
                'attributes' => $block_content['attrs'] ?? [],
            ];
        }

        wp_localize_script('b3dviewer-modelviewer-editor-script', 'bp3dBlock', [
            'nonce' => wp_create_nonce('apbCreatePage'),
            'isPremium' => bp3dv_fs()->can_use_premium_code(),
            'ajax_url' => admin_url('admin-ajax.php'),
            'siteUrl' => site_url(),
            'assetsUrl' => BP3D_DIR . '/public',
            'presets' => $presets,
            'editUrl' => admin_url('post.php?post='),
            'visual_editor' => admin_url('admin.php?page=3d-viewer-visual-editor'),
            '_wpnonce' => wp_create_nonce('wp_ajax'),
            'ajaxURL' => admin_url('admin-ajax.php')
        ]);

        wp_localize_script('b3dviewer-preset-editor-script', 'bp3dPreset', [
            'isPremium' => \bp3dv_fs()->is__premium_only() && \bp3dv_fs()->can_use_premium_code(),
            '_nonce' => wp_create_nonce('wp_ajax_pb3d_preset'),
        ]);
    }

    /**
     * Handle AJAX request to create a new page with 3D viewer shortcode.
     */
    public function createPage(): void
    {
        $nonce = sanitize_text_field(wp_unslash($_POST['nonce'] ?? ''));

        if (!wp_verify_nonce($nonce, 'apbCreatePage')) {
            wp_send_json_error('Invalid nonce');
        }

        $title = sanitize_text_field(wp_unslash($_POST['title'] ?? ''));
        $shortcode = sanitize_text_field(wp_unslash($_POST['shortcode'] ?? ''));

        $new_post_id = wp_insert_post([
            'post_title' => $title,
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => $shortcode,
        ]);

        if (is_wp_error($new_post_id)) {
            wp_send_json_error('Failed to create page');
        }

        $page_url = get_permalink($new_post_id);
        wp_send_json_success(['page_url' => $page_url]);
    }

    public function bp3d_pipe_checker()
    {
        $nonce = sanitize_text_field(isset($_POST['_wpnonce']) ? wp_unslash($_POST['_wpnonce']) : '');

        if (!wp_verify_nonce($nonce, 'wp_ajax')) {
            wp_send_json_error($nonce);
        }

        wp_send_json_success([
            'isPipe' => \bp3dv_fs()->is__premium_only() && \bp3dv_fs()->can_use_premium_code(),
        ]);
    }

    public function get_default_selector($selector, $default)
    {
        if ($selector) {
            return $selector;
        }
        return $default;
    }
}