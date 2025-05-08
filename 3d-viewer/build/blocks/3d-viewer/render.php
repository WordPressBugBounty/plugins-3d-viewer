<?php

$id = wp_unique_id('3d-viewer-');

if ($attributes['currentViewer'] == 'modelViewer') {
    wp_enqueue_script('bp3d-model-viewer');
} else if ($attributes['currentViewer'] == 'O3DViewer') {
    wp_enqueue_script('bp3d-o3dviewer');
}

?>
<div
    id="<?php echo esc_attr($id) ?>"
    data-attributes="<?php echo esc_attr(wp_json_encode($attributes)) ?>"
    <?php echo get_block_wrapper_attributes(); ?>>
</div>