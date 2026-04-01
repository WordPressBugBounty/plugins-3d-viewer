<?php



namespace BP3D\Template;

if (!defined('ABSPATH')) {
    exit;
}

use BP3D\Helper\Utils;

/**
 * Model Viewer HTML template renderer.
 *
 * Generates the frontend HTML for displaying 3D models using either
 * the Google Model Viewer (GLB/GLTF) or Online 3D Viewer (other formats).
 */
class ModelViewer
{
    /**
     * Render the model viewer HTML.
     *
     * @param  array<string, mixed> $data  Model viewer configuration data
     * @return string Rendered HTML
     */
    public static function html(array $data): string
    {
        self::enqueueFiles();
        ob_start();

        $align_class = self::get($data, 'align', '');
        $woo_class = !empty($data['woo']) ? ' woocommerce' : '';
        $is_elementor = self::get($data, 'elementor', false) ? 'elementor' : '';
        $additional_id = self::get($data['additional'] ?? [], 'ID', '');
        $additional_class = self::get($data['additional'] ?? [], 'Class', '');

?>
        <div id="<?php echo esc_attr($data['uniqueId']); ?>" class="b3dviewer align<?php echo esc_attr($align_class . $woo_class); ?>">
            <div id="<?php echo esc_attr($additional_id); ?>" class="bp_model_parent <?php echo esc_attr($additional_class); ?> b3dviewer-wrapper <?php echo esc_attr($is_elementor); ?>">
                <style><?php echo esc_html($data['stylesheet'] ?? ''); ?></style>
                <?php
        $source = self::resolveModelSource($data);
        $poster = self::resolveModelPoster($data);
        $ext = self::getFileExtension($source);

        if (in_array($ext, ['glb', 'gltf'], true)) {
            self::renderModelViewer($data, $source, $poster);
        }
        else {
            self::renderO3DViewer($data, $source);
        }
?>
            </div>
        </div>
        <?php

        return ob_get_clean() ?: '';
    }

    /**
     * Render the Google Model Viewer element.
     *
     * @param  array<string, mixed> $data    Configuration data
     * @param  string               $source  Model source URL
     * @param  string               $poster  Poster image URL
     */
    private static function renderModelViewer(array $data, string $source, string $poster): void
    {
        $attributes = self::buildAttributeString($data);
        $camera_orbit = $data['rotateAlongX'] . 'deg ' . $data['rotateAlongY'] . 'deg 105%';
        $progress_class = !empty($data['progressBar']) ? '' : 'hide_progressbar';

?>
        <model-viewer
            data-js-focus-visible
            data-decoder="<?php echo esc_attr(self::get($data['model'] ?? [], 'decoder', 'none')); ?>"
            <?php echo esc_attr($attributes); ?>
            poster="<?php echo esc_url($poster); ?>"
            src="<?php echo esc_url($source); ?>"
            alt="<?php esc_html_e('A 3D model', 'model-viewer'); ?>"
            <?php if (!empty($data['rotate'])): ?>
                camera-orbit="<?php echo esc_attr($camera_orbit); ?>"
            <?php
        endif; ?>
            class="<?php echo esc_attr($progress_class); ?>"
        >
            <?php if (!empty($data['fullscreen'])): ?>
                <?php require __DIR__ . '/../Shortcode/fullscreen_buttons.php'; ?>
            <?php
        endif; ?>

            <?php if (!empty($data['variant'])): ?>
                <div class="variantWrapper select">
                    <?php esc_html_e('Variant', 'model-viewer'); ?>: <select id="variant"></select>
                </div>
            <?php
        endif; ?>

            <?php if (!empty($data['animation'])): ?>
                <div class="animationWrapper select">
                    <?php esc_html_e('Animations', 'model-viewer'); ?>: <select id="animations"></select>
                </div>
            <?php
        endif; ?>

            <?php if (!empty($data['loadingPercentage'])): ?>
                <div class="percentageWrapper">
                    <div class="overlay"></div>
                    <span class="percentage">0%</span>
                </div>
            <?php
        endif; ?>

            <?php if (!empty($data['multiple']) && !empty($data['models'])): ?>
                <div class="slider">
                    <div class="slides">
                        <?php foreach ($data['models'] as $key => $model): ?>
                            <?php if ($model): ?>
                                <button
                                    class="slide <?php echo esc_attr($key === 0 ? 'selected' : ''); ?>"
                                    data-source="<?php echo esc_url($model['modelUrl']); ?>"
                                    data-poster="<?php echo esc_url(self::get($model, 'poster', '')); ?>"
                                >
                                    <img src="<?php echo esc_url(self::get($model, 'poster', '')); ?>" />
                                </button>
                            <?php
                endif; ?>
                        <?php
            endforeach; ?>
                    </div>
                </div>
            <?php
        endif; ?>
        </model-viewer>
        <?php
    }

    /**
     * Render the Online 3D Viewer element for non-GLB/GLTF files.
     *
     * @param  array<string, mixed> $data    Configuration data
     * @param  string               $source  Model source URL
     */
    private static function renderO3DViewer(array $data, string $source): void
    {
        $bg_color = implode(',', Utils::hexToRGB($data['styles']['bgColor'] ?? '#ffffff'));
?>
        <div
            class="online_3d_viewer"
            style="width: <?php echo esc_attr($data['styles']['width']); ?>; height: <?php echo esc_attr($data['styles']['height']); ?>;"
            backgroundcolor="<?php echo esc_attr($bg_color); ?>"
            model="<?php echo esc_url($source); ?>"
            environmentmap="<?php echo esc_url(BP3D_DIR); ?>public/images/envmaps/fishermans_bastion/negz.jpg,<?php echo esc_url(BP3D_DIR); ?>public/images/envmaps/fishermans_bastion/negx.jpg,<?php echo esc_url(BP3D_DIR); ?>public/images/envmaps/fishermans_bastion/negy.jpg,<?php echo esc_url(BP3D_DIR); ?>public/images/envmaps/fishermans_bastion/posx.jpg,<?php echo esc_url(BP3D_DIR); ?>public/images/envmaps/fishermans_bastion/posy.jpg,<?php echo esc_url(BP3D_DIR); ?>public/images/envmaps/fishermans_bastion/posz.jpg"
        >
        </div>
        <?php if (!empty($data['fullscreen'])): ?>
            <?php require __DIR__ . '/../Shortcode/fullscreen_buttons.php'; ?>
        <?php
        endif; ?>
        <?php
    }

    /**
     * Build the model-viewer HTML attribute string from configuration.
     */
    private static function buildAttributeString(array $data): string
    {
        $parts = ['exposure=' . ($data['exposure'] ?? 1)];

        if (!empty($data['mouseControl'])) {
            $parts[] = 'camera-controls';
        }

        if (!empty($data['autoRotate'])) {
            $parts[] = 'auto-rotate';
        }

        if (!empty($data['lazyLoad'])) {
            $parts[] = 'loading=lazy';
        }

        if (!empty($data['shadow'])) {
            $parts[] = 'shadow-intensity=1 shadow-softness=1';
        }

        if (!empty($data['autoplay'])) {
            $parts[] = 'autoplay';
        }

        if (empty($data['multiple']) && !empty($data['selectedAnimation'])) {
            $anim = $data['selectedAnimation'];
            $parts[] = "data-animation={$anim} animation-name={$anim}";
        }

        return implode(' ', $parts);
    }

    /**
     * Resolve the model source URL from the data array.
     */
    private static function resolveModelSource(array $data): string
    {
        if (!empty($data['multiple']) && !empty($data['models'][0]['modelUrl'])) {
            return $data['models'][0]['modelUrl'];
        }

        return self::get($data['model'] ?? [], 'modelUrl', '');
    }

    /**
     * Resolve the poster image URL from the data array.
     */
    private static function resolveModelPoster(array $data): string
    {
        if (!empty($data['multiple']) && isset($data['models'][0]['poster'])) {
            return $data['models'][0]['poster'];
        }

        return self::get($data['model'] ?? [], 'poster', '');
    }

    /**
     * Get the file extension from a URL string.
     */
    private static function getFileExtension(string $url): string
    {
        $parts = explode('.', $url);
        return strtolower($parts[count($parts) - 1] ?? '');
    }

    /**
     * Enqueue essential frontend scripts and styles.
     */
    private static function enqueueFiles(): void
    {
        wp_enqueue_script('bp3d-public');
        wp_enqueue_style('bp3d-public');
    }

    /**
     * Safely get a value from an array.
     *
     * @param  array<string, mixed> $array
     * @param  string               $index
     * @param  mixed                $default
     * @return mixed
     */
    private static function get(array $array, string $index, mixed $default = false): mixed
    {
        return $array[$index] ?? $default;
    }
}
