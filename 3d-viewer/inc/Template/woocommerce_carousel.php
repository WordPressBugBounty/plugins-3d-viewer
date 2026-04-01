<?php
/**
 * WooCommerce product carousel template.
 *
 * Renders a carousel of 3D model viewers for WooCommerce product galleries.
 * This template expects the following variables to be defined:
 *
 * @var array  $settings_opt   Plugin settings
 * @var array  $models         Array of model data (each with 'model_src')
 * @var string $id             Unique viewer ID
 * @var string $model_autoplay Autoplay attribute string
 * @var string $model_Shadow   Shadow intensity value
 * @var string $alt            Alt text for models
 * @var string $camera_controls Camera controls attribute
 * @var string $camera_orbit   Camera orbit attribute
 * @var string $zooming_3d     Zoom attribute
 * @var string $loading        Loading strategy
 * @var string $auto_rotate    Auto rotation attribute
 * @var string $rotation_speed Rotation speed attribute
 * @var string $rotation_delay Rotation delay attribute
 * @var array  $attribute      Additional model-viewer HTML attributes
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="bp3dmodel-carousel" data-fullscreen="<?php echo esc_attr($settings_opt['bp_3d_fullscreen'] ?? ''); ?>">
    <?php foreach ($models as $carousel_model) : ?>
        <div class="bp3dmodel-item">
            <div class="bp_model_gallery">
                <model-viewer
                    class="model"
                    id="bp_model_id_<?php echo esc_attr($id); ?>"
                    <?php echo esc_attr($model_autoplay); ?>
                    ar
                    shadow-intensity="<?php echo esc_attr($model_Shadow); ?>"
                    src="<?php echo esc_url($carousel_model['model_src']); ?>"
                    alt="<?php echo esc_attr($alt); ?>"
                    <?php echo esc_attr($camera_controls); ?>
                    <?php echo esc_attr($camera_orbit); ?>
                    <?php echo esc_attr($zooming_3d); ?>
                    loading="<?php echo esc_attr($loading); ?>"
                    <?php echo esc_attr($auto_rotate); ?>
                    <?php echo esc_attr($rotation_speed); ?>
                    <?php echo esc_attr($rotation_delay); ?>
                    <?php
                    if (is_array($attribute)) {
                        foreach ($attribute as $key => $value) {
                            echo esc_attr("{$key}='{$value}'");
                        }
                    }
                    ?>
                >
                    <?php if (($settings_opt['bp_3d_progressbar'] ?? '1') !== '1') : ?>
                        <style>
                            model-viewer#bp_model_id_<?php echo esc_attr($id); ?>::part(default-progress-bar) {
                                display: none;
                            }
                        </style>
                    <?php endif; ?>
                </model-viewer>
            </div>
        </div>
    <?php endforeach; ?>
</div>