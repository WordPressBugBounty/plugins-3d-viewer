<style>
    <?php echo '.wrapper_'.esc_attr($id); ?> .bp_model_parent, <?php echo '#bp_model_id_'.esc_attr($id); ?> {
        width: <?php echo esc_attr($width); ?>;
        max-width: 100%;
        <?php if(bp3d_isset($modeview_3d, 'bp_3d_align', 'center') == 'end'){
            echo "margin-left: auto";
        } ?>
        <?php if(bp3d_isset($modeview_3d, 'bp_3d_align', 'center') == 'center'){
            echo "margin: auto";
        } ?>
    }
    <?php echo '.wrapper_'.esc_attr($id); ?> .bp_model_parent .model-icon {
        fill: <?php echo esc_attr(isset($modeview_3d['bp_model_icon_color']) ? $modeview_3d['bp_model_icon_color'] : 'rgba(0, 0, 0, 0.4)'); ?>;
    }
    <?php echo '#bp_model_id_'.esc_attr($id); ?> {
        height:<?php echo esc_attr($height); ?>;
        background-color: <?php echo esc_attr($modeview_3d['bp_model_bg']); ?>;
    }
    .fullscreen <?php echo "#bp_model_id_".esc_attr($id); ?>{
    height: 100%;
    width: 100%;
    }
    <?php echo esc_html(".wrapper_$id") ?>{
        justify-content: <?php echo esc_attr(isset($modeview_3d['bp_3d_align']) ? $modeview_3d['bp_3d_align'] : 'center'); ?>;
        display: flex;
    }
    model-viewer<?php echo '#bp_model_id_'.esc_attr($id); ?>::class(userInput) {
        outline:none;
    }
    model-viewer<?php echo '#bp_model_id_'.esc_attr($id); ?>::part(default-progress-bar) {
        background:<?php echo esc_attr(isset($modeview_3d['bp_model_progressbar_color']) ? $modeview_3d['bp_model_progressbar_color'] : 'rgba(0, 0, 0, 0.4)'); ?>;
    }

    model-viewer.model {
        --poster-color: transparent;
    }
    </style>