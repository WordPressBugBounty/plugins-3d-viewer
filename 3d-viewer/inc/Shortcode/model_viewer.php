<model-viewer class="model" 
    id="bp_model_id_<?php echo esc_attr($id); ?>" 
    <?php echo esc_attr($model_autoplay); ?> 
    ar shadow-intensity="<?php echo esc_attr($model_Shadow); ?>" 
    <?php echo esc_attr($model_preload); ?> 
    poster="<?php echo esc_url($model_poster); ?>" 
    src="<?php echo esc_url($model_source); ?>" 
    alt="<?php echo esc_attr($alt); ?>" 
    <?php echo esc_attr($camera_control); ?> 
    <?php echo $camera_orbit; ?> 
    <?php echo esc_attr($zooming_3d); ?> 
    loading="<?php  echo esc_attr($loading); ?>" 
    <?php echo esc_attr($auto_rotate); ?> 
    <?php echo esc_attr($rotation_speed); ?>  
    <?php echo esc_attr($rotation_delay); ?> 
<?php 
if(is_array($attribute)){
    foreach($attribute as $key => $value){ 
        echo "$key='$value'";
    }
} ?>
exposure="<?php echo esc_attr($exposure) ?>"
>
<?php
    if($modeview_3d['bp_3d_progressbar'] !== '1') { ?>
        <style>
            model-viewer<?php echo '#bp_model_id_'.esc_attr($id); ?>::part(default-progress-bar) {
                display:none;
            }
            model-viewer::part(prompt) {
                display:none;
            }
        </style>
<?php } ?>
</model-viewer>

<!-- Model Cycle Script -->
<?php if ( ($modeview_3d['bp_3d_model_type'] === 'mcycle') ){ ?>
    <script>
        const modelArr = <?php echo json_encode($models_src); ?>;
        const models = [];
        for(let a = 0; a < modelArr.length; a++) {

            const { model_link } = modelArr[a];
            if( model_link ) {
                models.push(modelArr[a].model_link);
            }
        }
        const toggleModel  = document.querySelector('#bp_model_id_<?php echo esc_attr($id); ?>');
        let b = 0;
        setInterval(() => {
            toggleModel.setAttribute('src', `${models[b++ % models.length]}`)
        }, <?php echo esc_attr($models_anim); ?>);

    </script>
<?php } ?>
<!-- End of model Script -->

<!-- Model Cycle Poster Script -->
<?php if ($modeview_3d['bp_3d_poster_type'] === 'cycle'){ ?>
    <script>
        const arr = <?php echo json_encode($poster_images); ?>;
        const posters = [];
        for(var i = 0; i < arr.length; i++) {
            posters.push(arr[i].poster_img);
        }
        const togglePoster = document.querySelector('#bp_model_id_<?php echo esc_attr($id); ?>');
        let j = 0;
        setInterval(() => togglePoster.setAttribute('poster', `${posters[++j % posters.length]}`), 2000);
    </script>
<?php } ?>

