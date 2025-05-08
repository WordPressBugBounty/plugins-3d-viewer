<?php

use BP3D\Helper\Utils;

?>


<div class="online_3d_viewer"
    style="width: <?php echo esc_attr($width) ?>; height: <?php echo esc_attr($height) ?>;"
    backgroundcolor="<?php echo esc_attr(implode(',', Utils::hexToRGB($modeview_3d['bp_model_bg']))) ?>"
    model="<?php echo esc_url($src) ?>"
    environmentmap="<?php echo esc_url(BP3D_DIR) ?>public/images/envmaps/fishermans_bastion/negz.jpg,<?php echo esc_url(BP3D_DIR) ?>public/images/envmaps/fishermans_bastion/negx.jpg,<?php echo esc_url(BP3D_DIR) ?>public/images/envmaps/fishermans_bastion/negy.jpg,<?php echo esc_url(BP3D_DIR) ?>public/images/envmaps/fishermans_bastion/posx.jpg,<?php echo esc_url(BP3D_DIR) ?>public/images/envmaps/fishermans_bastion/posy.jpg,<?php echo esc_url(BP3D_DIR) ?>public/images/envmaps/fishermans_bastion/posz.jpg" 
    >

</div>
<?php 
if( $modeview_3d['bp_3d_fullscreen'] === '1'){ 
        require_once(__DIR__.'/fullscreen_buttons.php');
} ?>