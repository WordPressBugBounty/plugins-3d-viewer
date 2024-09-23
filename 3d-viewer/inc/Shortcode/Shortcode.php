<?php
namespace BP3D\Shortcode;

use BP3D\Helper\Utils;
use BP3D\Helper\Block;
use BP3D\Template\ModelViewer;
use BP3D\Woocommerce\ProductData;

class Shortcode{

    public function register(){
        add_shortcode('3d_viewer', [$this, 'bp3dviewer_cpt_content_func']);
        add_shortcode('3d_viewer_product', [$this, 'product_model_viewer']);
    }

    //Lets register our shortcode
    function bp3dviewer_cpt_content_func( $atts ){
        extract( shortcode_atts( array(
            'id' => '',
            'src' => '',
            'alt' => '',
            'width' => '100%',
            'height' => 'auto',
            'auto_rotate' => 'auto-rotate',
            'camera_controls' => 'camera-controls',
            'zooming_3d' => '',
            'loading' => '',
            'poster' => ''
        ), $atts ));
        
        if(!$id){
            return false;
        }

        
        $post_type = get_post_type($id);
        $isGutenberg = get_post_meta($id, 'isGutenberg', true);
        
        if(!in_array($post_type, ['bp3d-model-viewer', 'product'])){
            return false;
        }
        
        if($isGutenberg){
            $blocks =  Block::getBlock($id);
            if($blocks){
                return render_block($blocks);
            }
            return;
        }
        ob_start();

        $data = wp_parse_args(  get_post_meta( $id, '_bp3dimages_', true ), $this->get3DViewerDefaultData());

        if($data['bp_3d_src_type'] == 'upload'){
            $modelSrc = $data['bp_3d_src']['url'];
        }else {
            $modelSrc = $data['bp_3d_src_link'];
        }

        $models = [];

        if(isset($data['bp_3d_models']) && is_array($data['bp_3d_models'])){
            foreach($data['bp_3d_models'] as $index => $model){
                $models[] = [
                    'modelUrl' => $model['model_link'],
                    "useDecoder" => "none",
                ];
    
                if(isset($data['bp_3d_posters'][$index]['poster_img'])){
                    $models[$index]['poster'] = $data['bp_3d_posters'][$index]['poster_img'];
                }
            }
        }

        $poster = $data['bp_3d_poster']['url'] ?? '';

        $finalData = [
            "align" => $data['bp_3d_align'],
            "uniqueId" => "model$id",
            "currentViewer" => $data['currentViewer'],
            "multiple" => $data['bp_3d_model_type'] !== 'msimple',
            "model" => [
                "modelUrl" => $modelSrc,
                "poster" => $poster
            ],
            "O3DVSettings" =>  [
				"isFullscreen" =>  $data['bp_3d_fullscreen'] == '1',
				"isPagination" => $this->isset($data, 'show_thumbs', 0) === '1',
				"isNavigation" =>  $this->isset($data, 'show_arrows', "1") === '1',
				"camera" =>  null,
				"mouseControl" =>  $data['bp_camera_control'] == '1',
			],
            "models" => $models,
            "lazyLoad" =>  $data['bp_3d_loading'] === 'lazy', // maybe not needed
            "loading" =>  $data['bp_3d_loading'], // maybe not needed
            "autoplay" => (boolean) $data['bp_3d_autoplay'],
            "shadow" =>  $data['3d_shadow_intensity'] != 0,
            "autoRotate" => $data['bp_3d_rotate'] === '1',
            "zoom" => $data['bp_3d_zooming'] === '1',
            "isPagination" => $this->isset($data, 'show_thumbs', 0) === '1',
            "isNavigation" => $this->isset($data, 'show_arrows', "1") === '1',
            "preload" => 'auto', //$data['bp_3d_preloader'] == '1' ? 'auto' : $poster ? 'interaction' : 'auto',
            'rotationPerSecond' => $data['3d_rotate_speed'],
            "mouseControl" =>  $data['bp_camera_control'] == '1',
            "fullscreen" =>  $data['bp_3d_fullscreen'] == '1',
            "variant" =>  false,
            "loadingPercentage" =>  $data['bp_model_progress_percent'] == '1',
            "progressBar" =>  $data['bp_3d_progressbar'] == '1',
            "rotate" =>  $data['bp_model_angle'] === '1',
            "rotateAlongX" => $data['angle_property']['top'],
            "rotateAlongY" => $data['angle_property']['right'],
            "exposure" => $data['3d_exposure'],
            "styles" => [
                "width" => $data['bp_3d_width']['width'].$data['bp_3d_width']['unit'],
                "height" =>  $data['bp_3d_height']['height'].$data['bp_3d_height']['unit'],
                "bgColor" => $data['bp_model_bg'],
                "progressBarColor" => $data['bp_model_progressbar_color'] ?? ''
            ],
            "stylesheet" => null,
            "additional" => [
                "ID" => "",
                "Class" => "",
                "CSS" => $data['css'] ?? '',
            ],
            "animation" => (boolean) false,
            "woo" => (boolean) false,
            "selectedAnimation" => ""
        ];
        ?>

        <div class="modelViewerBlock" data-attributes='<?php echo esc_attr(wp_json_encode($finalData)) ?>'></div>

        <?php

        wp_enqueue_script('bp3d-front-end');
        wp_enqueue_style('bp3d-custom-style');
        wp_enqueue_style('bp3d-public');
        
        return ob_get_clean();
    }

    /**
     * shortcode for product model viewer
     */
    public function product_model_viewer($attrs){
        extract( shortcode_atts( array(
            'id' => get_the_ID(),
            'width' => '100%'
        ), $attrs ));

        $post_type = get_post_type($id);

        if(!in_array($post_type, ['product'])){
            return false;
        }
        ob_start(); 

        global $product;
        $settings = get_option('_bp3d_settings_', []); // settings data
        $woocommerce_enabled = $settings['3d_woo_switcher'] ?? false; // is woocommerce enabled or not in the settings panel
        $is_not_compatible = $settings['is_not_compatible'] ?? false;

        // exit if woocommerce not enabled or product variable is unavailable or it's not a single page(not a product page)
        if(!$woocommerce_enabled || gettype($product) !== 'object' || !is_single()){
            return;
        }
       
        // exit if get_id() method does not exists on the product object/variable
        if(!method_exists( $product, 'get_id')){
            return;
        }
        
        $modelData = get_post_meta( $product->get_id(), '_bp3d_product_', true );
        $viewer_position = isset($modelData['viewer_position']) ? $modelData['viewer_position'] : '';
        $force_to_change_position =  isset($modelData['force_to_change_position']) ? $modelData['force_to_change_position'] : false;
        $custom_selector =  $modelData['custom-selector'] ?? '';
        
        // set common selector as custom selector if does not set to the product settings page and force is enabled
        if($force_to_change_position && !$custom_selector){
            $custom_selector = Utils::getCustomSelector(wp_get_theme()->name);
        }
        
        // retrieve custom selector if does not set to the product settings panel
        if(!$custom_selector){
            $custom_selector = $settings['product_gallery_selector'] ?? '.woocommerce-product-gallery'; // common selector '.woocommerce-product-gallery'
        }


        // load css and js to load model if user want to override woocommerce system 
        if($viewer_position === 'custom_selector' || $force_to_change_position || $is_not_compatible){
            $finalData = ProductData::getProductAttributes($modelData);
            
            ?>

            <div
                class="modelViewerBlock wooCustomSelector" 
                data-attributes='<?php echo esc_attr(wp_json_encode($finalData)); ?>'
            >
            </div>

            <?php
             wp_enqueue_script('bp3d-front-end');
             wp_enqueue_style('bp3d-custom-style');
             wp_enqueue_style('bp3d-public');
        }
       

        return ob_get_clean(); 
    }

    public function get3DViewerDefaultData() {
        return  array(
            'bp_3d_model_type' => 'msimple', // done
            'bp_3d_src_type' => 'upload',  // done
            "currentViewer" => 'modelViewer',
            'bp_3d_src' => array(
                'url' => 'http://localhost/freemius/wp-content/uploads/2022/04/PEP-3D-Model_2.glb',
                'title' => ''
            ), // done
            'bp_3d_src_link' => 'i-do-not-exist.glb', // done
            'bp_3d_models' => array(
                array(
                    'model_link' => 'http://localhost/freemius/wp-content/uploads/2022/08/RobotExpressive.glb',
                ),
            ), // done
            'bp_model_anim_du' => 5000,
            'bp_3d_poster_type' => 'simple', // done
            'bp_3d_poster' => array('url' => ''), // done
            'bp_3d_posters' => '', // done
            'bp_3d_autoplay' => '', // done
            'bp_3d_preloader' => '',
            'bp_camera_control' => 1, // done
            'bp_3d_zooming' => 1,
            'bp_3d_loading' => 'auto', // done
            'bp_3d_rotate' => 1, // done
            '3d_rotate_speed' => 30, // done
            '3d_rotate_delay' => 3000,
            'bp_model_angle' => '', // done
            'angle_property' => array(
                'top' => 0,
                'right' => 75,
                'bottom' => 105,
            ), // done
            'bp_3d_fullscreen' => 1, // done
            'bp_3d_progressbar' => 1, // done
            'bp_model_progress_percent' => 0, // done
            '3d_shadow_intensity' => 1, // done
            '3d_exposure' => 1, // done
            'bp_3d_width' => array(
                'width' => 100,
                'unit' => '%',
            ), // done
            'bp_3d_height' => array(
                'height' => 500,
                'unit' => 'px',
            ), // done
            'bp_3d_align' => 'center', // done
            'bp_model_bg' => '#8224e3', // done
            'bp_model_progressbar_color' => '', // done
            'bp_model_icon_color' => '', // no need
            'css' => '',
        );
    }

    public function isset($array, $key, $default){
        if(isset($array[$key])){
            return $array[$key];
        }
        return $default;
    }
}
