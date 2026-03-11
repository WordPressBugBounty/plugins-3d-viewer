<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$id = wp_unique_id('bp3d-viewer-');

if ($attributes['currentViewer'] == 'modelViewer') {
    wp_enqueue_script('bp3d-model-viewer');
} else if ($attributes['currentViewer'] == 'O3DViewer') {
    wp_enqueue_script('bp3d-o3dviewer');
}

$attributes = apply_filters('bp3d_gutenberg_model_attribute', $attributes);

?>

<div
    id="<?php echo esc_attr($id) ?>"
    data-attributes="<?php echo esc_attr(wp_json_encode($attributes)) ?>"
    class="wp-block-b3dviewer-modelviewer">
        <div class="bp3d_backup_view" style="display: none;">
            <?php if(current_user_can('manage_options')) { ?>
                <p><b>Admin message:</b> Something went wrong. the model is not working perfectly. <a target="_blank" href="https://bplugins.com/contact/">Get Support</p>
                <?php } ?>
                <model-viewer camera-controls src="<?php echo esc_url($attributes['model']['modelUrl'] ?? '') ?>" style="height: 350px;"></model-viewer>
        </div>
        <script>
            setTimeout(() => {
                let backupModels = document.querySelectorAll('.bp3d_backup_view');
                if(backupModels.length > 0){
                    backupModels.forEach(element => {
                        if(element){
                            element.style.display = 'block';
                        }
                    });
                }
            }, 5000);
        </script>
</div>