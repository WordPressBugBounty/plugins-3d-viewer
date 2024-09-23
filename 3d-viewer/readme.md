## Pro filter hooks

1. bp3d_woocommerce_model_attribute

Example

```
add_filter('bp3d_woocommerce_model_attribute', function($default){
    return wp_parse_args([
        'id' => 'model_custom_id'
    ], $default);
})
```

## shortcode

1. [3d_viewer_product]
   ## _params_
   ### width
    <ul>
        <li>type String</li>
    </ul>


sk_rEx;Di8@l7UPWHz9Dj9E^WJ-_CRP1

        // code for modal/popup

        $popupModels = isset($modelData['bp3d_popup_models']) ? $modelData['bp3d_popup_models'] : [];
        foreach ($popupModels as $model){
            wp_enqueue_script('bp3d-front-end');
            wp_enqueue_style('bp3d-custom-style');
            wp_enqueue_style('bp3d-public');
            $finalData = $this->getProductAttributes($modelData);
            $finalData['loading'] = 'lazy';
            $finalData['models'] = [[
                'modelUrl' => $model['model_src']
            ]];
            ?>
                <div class="bp3dv-model-main" data-selector="<?php echo esc_attr($model['selector']) ?>">
                    <div class="bp3dv-model-inner">        
                    <div class="close-btn">&times;</div>
                        <div class="bp3dv-model-wrap">
                            <div class="pop-up-content-wrap">
                            <div class="modelViewerBlock wooCustomSelector" data-attributes='<?php echo esc_attr(wp_json_encode($finalData)); ?>'></div> 
                            </div>
                        </div>  
                    </div>  
                    <div class="bg-overlay"></div>
                </div> 
                <script>

                </script>
            <?php
        }