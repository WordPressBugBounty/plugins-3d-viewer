<?php
if (!defined('ABSPATH'))
    exit;

$id = wp_unique_id('bp3d-viewer-');

if ($attributes['currentViewer'] == 'modelViewer') {
    wp_enqueue_script('bp3d-model-viewer');
}
else if ($attributes['currentViewer'] == 'O3DViewer') {
    wp_enqueue_script('bp3d-o3dviewer');
}

$attributes = apply_filters('bp3d_gutenberg_model_attribute', $attributes);

?>

<div id="<?php echo esc_attr($id)?>" data-attributes="<?php echo esc_attr(wp_json_encode($attributes))?>"
    class="wp-block-b3dviewer-modelviewer">
    <div class="bp3d_backup_view" style="display: none;height:350px;">
        <model-viewer camera-controls src="<?php echo esc_url($attributes['model']['modelUrl'] ?? '')?>"
            style="height: 350px;"></model-viewer>
    </div>
    <script>
        setTimeout(() => {
            let backupModels = document.querySelectorAll('.bp3d_backup_view');
            if (backupModels.length > 0) {
                backupModels.forEach(element => {
                    if (element) {
                        element.style.display = 'block';
                        setTimeout(() => {
                            let adminMessages = document.querySelectorAll('.bp3d_admin_message');
                            if (adminMessages.length > 0) {
                                adminMessages.forEach(adminMessage => {
                                    if (adminMessage) {
                                        adminMessage.style.display = 'block';
                                    }
                                });
                            }
                        }, 5000);
                    }
                });
            }
        }, 5000);
    </script>
</div>