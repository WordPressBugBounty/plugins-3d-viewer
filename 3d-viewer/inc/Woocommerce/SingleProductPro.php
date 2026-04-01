<?php



namespace BP3D\Woocommerce;

if (!defined('ABSPATH')) {
    exit;
}

use BP3D\Helper\Utils;

/**
 * WooCommerce single product integration (Pro).
 *
 * Extends the free SingleProduct class with popup/modal support
 * for displaying 3D models in custom popups triggered by selectors.
 */
class SingleProductPro extends SingleProduct
{
    public string $theme_name = '';

    /**
     * Register WooCommerce integration hooks (extends parent).
     */
    public function register(): void
    {
        $this->theme_name = wp_get_theme()->name;

        add_action('wp', [$this, 'onWoocommerceLoaded']);
        add_action('wp_footer', [$this, 'handleIncompatibleTheme']);
        add_action('wp_footer', [$this, 'renderPopupModels']);
        add_action('bp3d_product_model_before', [$this, 'renderModel']);
        add_action('bp3d_product_model_after', [$this, 'renderModel']);
        add_action('woocommerce_product_thumbnails', [$this, 'renderGalleryThumbnail'], 40);
    }

    /**
     * Render popup 3D model modals in the footer.
     *
     * Creates modal overlays for each popup model configured on the product,
     * triggered by the corresponding CSS selector.
     */
    public function renderPopupModels(): void
    {
        global $product;

        $woo_enabled = get_option('_bp3d_settings_', ['3d_woo_switcher' => false])['3d_woo_switcher'];

        if (!$woo_enabled || !is_object($product) || !is_single()) {
            return;
        }

        if (!method_exists($product, 'get_id')) {
            return;
        }

        $modelData = get_post_meta($product->get_id(), '_bp3d_product_', true);
        $meta = Utils::getPostMeta($product->get_id(), '_bp3d_product_');

        $popupModels = (isset($modelData['bp3d_popup_models']) && is_array($modelData['bp3d_popup_models']))
            ? $modelData['bp3d_popup_models']
            : [];

        foreach ($popupModels as $model) {
            $this->renderSinglePopup($model, $meta);
        }
    }

    /**
     * Render a single popup modal for a 3D model.
     *
     * @param array<string, mixed> $model  Popup model configuration
     * @param \Closure             $meta   Meta accessor closure
     */
    private function renderSinglePopup(array $model, \Closure $meta): void
    {
        wp_enqueue_script('bp3d-public');
        wp_enqueue_style('bp3d-custom-style');
        wp_enqueue_style('bp3d-frontend');

        $viewer = $model['popupCurrentViewer'] ?? 'modelViewer';

        if ($viewer === 'O3DViewer') {
            wp_enqueue_script('bp3d-o3dviewer');
        }
        else {
            wp_enqueue_script('bp3d-model-viewer');
        }

        $finalData = Product::getProductAttributes($meta('all'));

        $finalData = array_merge($finalData, [
            'loading' => 'lazy',
            'placement' => 'popup',
            'uniqueId' => 'model' . uniqid(),
            'loadingPercentage' => true,
            'currentViewer' => $viewer,
            'isPagination' => false,
            'isNavigation' => false,
            'preload' => 'auto',
            'mouseControl' => true,
            'multiple' => true,
            'model' => ['modelUrl' => '', 'poster' => ''],
            'styles' => [
                'width' => '100%',
                'height' => '350px',
                'bgColor' => $meta('bp_model_bg', 'transparent'),
            ],
            'O3DVSettings' => [
                'isFullscreen' => true,
                'isNavigation' => $meta('show_arrows', false, true),
                'mouseControl' => true,
                'zoom' => $meta('bp_3d_zooming', true, true),
                'isPagination' => $meta('show_thumbs', false, true),
            ],
            'models' => [[
                    'modelUrl' => $model['model_src'] ?? '',
                    'poster' => $model['poster_src'] ?? '',
                    'skyboxImage' => $model['skybox_image_src'] ?? '',
                    'environmentImage' => $model['environment_image_src'] ?? '',
                    'arEnabled' => isset($model['enable_ar']) && $model['enable_ar'] === '1',
                    'modelISOSrc' => $model['model_iso_src'] ?? '',
                ]],
        ]);

        $target = esc_attr($model['target'] ?? '');
        $selector = esc_attr($model['selector'] ?? '');
        $json = esc_attr(wp_json_encode($finalData));
?>
        <div class="bp3dv-model-main" id="<?php echo $target; ?>" data-selector="<?php echo $selector; ?>">
            <div class="bp3dv-model-inner">
                <div class="close-btn">&times;</div>
                <div class="bp3dv-model-wrap">
                    <div class="pop-up-content-wrap">
                        <div class="modelViewerBlock wooCustomSelector wp-block-b3dviewer-modelviewer" data-type="popup" data-attributes='<?php echo $json; ?>'>Loading...</div>
                    </div>
                </div>
            </div>
            <div class="bg-overlay"></div>
        </div>
        <?php
    }
}
