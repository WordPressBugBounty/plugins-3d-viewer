<?php
namespace BP3D\Woocommerce;
use BP3D\Helper\Utils;


class ProductData{

    
    public static function getProductAttributes($modelData = []){
        if(!is_array($modelData) || !is_array($modelData['bp3d_models'])){
            return [];
        }
        $options = get_option('_bp3d_settings_');
        $models = [];
        foreach($modelData['bp3d_models'] as $index => $model){
            $models[] = [
                'modelUrl' => $model['model_src'],
                "useDecoder" => "none",
                'poster' => $model['poster_src'] ?? '',
                'product_variant' => $model['product_variant'] ?? ''
            ];
        }

        $finalData = [
            "align" => 'center',
            "uniqueId" => "model".get_the_ID(),
            "multiple" => true,
            "O3DVSettings" => ['currentViewer' => 'modelViewer'],
            "model" => [
                "modelUrl" => '',
                "poster" =>  ''
            ],
            "models" => $models,
            "lazyLoad" => $options['bp_3d_loading'] === 'lazy', // done
            "autoplay" => (boolean) $options['bp_3d_autoplay'], // done
            "shadow" =>  $options['3d_shadow_intensity'] != 0, //done
            "autoRotate" => $options['bp_3d_rotate'] === '1', // done
            "zoom" => $options['bp_3d_zooming'] === '1',
            "isPagination" => Utils::isset($modelData, 'show_thumbs', 0) === '1',
            "isNavigation" => Utils::isset($modelData, 'show_arrows', 0) === '1',
            "preload" => 'auto', //$options['bp_3d_preloader'] == '1' ? 'auto' : 'interaction',
            'rotationPerSecond' => $options['3d_rotate_speed'], // done
            "mouseControl" =>  $options['bp_camera_control'] == '1',
            "fullscreen" =>  $options['bp_3d_fullscreen'] == '1', // done
            "variant" => (boolean) false,
            "loadingPercentage" =>  false, //$options['bp_model_progress_percent'] == '1',
            "progressBar" =>  false, //$options['bp_3d_progressbar'] == '1',
            "rotate" =>  false, //$options['bp_model_angle'] === '1',
            "rotateAlongX" => 0, //$options['angle_property']['top'],
            "rotateAlongY" => 75, //$options['angle_property']['right'],
            "exposure" => 1, //$options['3d_exposure'],
            "styles" => [
                "width" => '100%', //$options['bp_3d_width']['width'].$options['bp_3d_width']['unit'],
                "height" => isset($options['bp_3d_height']) ? $options['bp_3d_height']['height'].$options['bp_3d_height']['unit'] : '350px',
                "bgColor" => $modelData['bp_model_bg'] ?? '', // done
                "progressBarColor" => '#666', //$options['bp_model_progressbar_color'] ?? ''
            ],
            "stylesheet" => null,
            "additional" => [
                "ID" => "",
                "Class" => "",
                "CSS" => '',//$options['css'] ?? '',
            ],
            "animation" => false,
            "woo" =>  true,
            "selectedAnimation" => "",
            'replace_model_with_thumbnail' => $modelData['replace_model_with_thumbnail'] ?? false,
        ];

        return $finalData;
    }
}