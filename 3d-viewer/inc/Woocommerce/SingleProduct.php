<?php

namespace BP3D\Woocommerce;
use BP3D\Helper\Utils;


class SingleProduct{

    public $theme_name;

    public function register(){
        $this->theme_name = wp_get_theme()->name;
        add_action('woocommerce_loaded', [$this, 'woocommerce_loaded']);
        add_action('bp3d_product_model_before', [$this, 'model']);
        add_action('bp3d_product_model_after', [$this, 'model']);
        add_action('wp_footer', [$this, 'wp_footer']);

    }


    public function wp_footer(){
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
        $class = Utils::getThemeClass($this->theme_name);
        
        // set common selector as custom selector if does not set to the product settings page and force is enabled
        if($force_to_change_position && !$custom_selector){
            $custom_selector = Utils::getCustomSelector($this->theme_name);
        }
        
        // retrieve custom selector if does not set to the product settings panel
        if(!$custom_selector){
            $custom_selector = $settings['product_gallery_selector'] ?? '.woocommerce-product-gallery'; // common selector '.woocommerce-product-gallery'
        }


        // load css and js to load model if user want to override woocommerce system 
        if($viewer_position === 'custom_selector' || $force_to_change_position || $is_not_compatible){
            $finalData = $this->getProductAttributes($modelData); ?>

            <div 
                data-theme="<?php echo esc_attr(wp_get_theme()->get('Name')) ?>"
                data-selector='<?php echo esc_attr($custom_selector) ?>'
                data-position='<?php echo esc_attr($viewer_position) ?>'
                data-unique-class="<?php echo esc_attr($class) ?>" 
                class="modelViewerBlock wooCustomSelector" 
                data-attributes='<?php echo esc_attr(wp_json_encode($finalData)); ?>'
            >
            </div>

            <?php
             wp_enqueue_script('bp3d-front-end');
             wp_enqueue_style('bp3d-custom-style');
             wp_enqueue_style('bp3d-public');
        }

    }

    public function woocommerce_loaded(){
        $settings = get_option( '_bp3d_settings_' );
        if(isset($settings['3d_woo_switcher']) && $settings['3d_woo_switcher'] !== '0'){
            remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
            add_action('woocommerce_before_single_product_summary',[$this, 'bp3d_product_models'], 20);
        }

    }

    public function bp3d_product_models(){
        if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
            return;
        }

        // Meta data of 3D Viewer
        $modeview_3d = get_post_meta( get_the_ID(), '_bp3d_product_', true );
        // $modelData = get_post_meta( get_the_ID(), '_bp3d_product_', true );
        $viewer_position = isset($modeview_3d['viewer_position']) ? $modeview_3d['viewer_position'] : 'top';
        $models = $modeview_3d['bp3d_models'] ?? [];
        $force_to_change_position =  isset($modeview_3d['force_to_change_position']) ? $modeview_3d['force_to_change_position'] : false;
        $class = Utils::getThemeClass($this->theme_name);

        if(in_array($this->theme_name, Utils::getNotCompatibleThemes())){
            if($viewer_position === 'replace'){
             $custom_selector = Utils::getCustomSelector($this->theme_name);
             ?>
                 <style>
                     <?php echo esc_html($custom_selector) ?> > *:not(.modelViewerBlock ){
                         display: none;
                     }
                 </style>
             <?php
            }
 
             return;
        }

      
        if((isset($modeview_3d['bp3d_models']) && !is_array($modeview_3d['bp3d_models'])) || $viewer_position === 'none' || $viewer_position === 'custom_selector' || sizeof($models) < 1 || $force_to_change_position === '1'){
            add_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 30);
            return;
        }

        global $product;
        wp_enqueue_style('bp3d-custom-style');
        // wp_enqueue_script('bp3d-slick');
        wp_enqueue_script('bp3d-public');

        
        $columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
        $post_thumbnail_id = $product->get_image_id();
        $wrapper_classes   = apply_filters(
            'woocommerce_single_product_image_gallery_classes',
            array(
                'woocommerce-product-gallery',
                'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
                'woocommerce-product-gallery--columns-' . absint( $columns ),
                'images',
            )
        );
        
        ?>
        
        <div class="product-modal-wrap <?php echo esc_attr($class) ?> position_<?php echo esc_attr($viewer_position) ?>">
            <div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>">
                <!-- Custom hook for 3d-viewer -->
                <?php  
                if($viewer_position === 'top') {
                    do_action( 'bp3d_product_model_before' ); ?>
                        <style>
                            .woocommerce div.product div.images .woocommerce-product-gallery__trigger {
                                position: absolute;
                                top: 385px;
                            }
                        </style>
                    <?php		
                }
        
                if($viewer_position === 'replace') {
                    add_filter( 'woocommerce_single_product_image_thumbnail_html',function($content){
                        return '';
                    }, 10, 2 );
                    do_action( 'bp3d_product_model_before' ); 	
                }
                ?>
        
                <figure class="woocommerce-product-gallery__wrapper">
                    <?php
        
                    if ( $post_thumbnail_id ) {
                        $html = \wc_get_gallery_image_html( $post_thumbnail_id, true );
                    } else {
                        $html  = '<div class="woocommerce-product-gallery__image--placeholder">';
                        $html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
                        $html .= '</div>';
                    }

                    echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
                    do_action( 'woocommerce_product_thumbnails' );
                    ?>
                </figure>
            </div>
            <?php  
                if( $viewer_position === 'bottom') {
                    do_action( 'bp3d_product_model_after' ); 
                }
            ?>
        
        </div> <!-- End of Product modal wrap --> 
        <?php
    }

    /**
     * Model
     */

    public function model(){
        global $product;
        $modelData = get_post_meta( $product->get_id(), '_bp3d_product_', true );
        $finalData = $this->getProductAttributes($modelData); 
        $class = Utils::getThemeClass($this->theme_name);
        ?>
            
        <div class="modelViewerBlock wooCustomSelector" data-unique-class="<?php echo esc_attr($class) ?>" data-attributes='<?php echo esc_attr(wp_json_encode($finalData)); ?>'></div>

        <?php
            wp_enqueue_script('bp3d-front-end');
            wp_enqueue_style('bp3d-custom-style');
            wp_enqueue_style('bp3d-public');

    }

    public function getProductAttributes($modelData){
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
            "isPagination" => $this->isset($modelData, 'show_thumbs', 0) === '1',
            "isNavigation" => $this->isset($modelData, 'show_arrows', 0) === '1',
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
            "selectedAnimation" => ""
        ];

        return $finalData;
    }

    public function isset($array, $key, $default){
        if(isset($array[$key])){
            return $array[$key];
        }
        return $default;
    }
}

