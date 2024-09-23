<?php
namespace BP3D\Addons;

use BP3D\Template\ModelViewer;

if(!defined('ABSPATH')) {
    return;
}

    class Blocks{
        
        public function register(){
            add_action('init', [$this, 'init']);
            add_action('wp_ajax_nopriv_bp3d_pipe_checker', [$this, 'bp3d_pipe_checker']);
            add_action('wp_ajax_bp3d_pipe_checker', [$this, 'bp3d_pipe_checker']);
        }

        public function bp3d_pipe_checker(){
            $nonce = $_GET['_wpnonce'];

            if( !wp_verify_nonce( $nonce, 'wp_ajax' )){
                echo wp_send_json([
                    'success' => false,
                ]);
                wp_die();
            }

            echo wp_send_json([
                'data' => [
                    'isPipe' => \bp3dv_fs()->is__premium_only() && \bp3dv_fs()->can_use_premium_code(),
                ]
            ]);
            wp_die();
        }

        public function init(){

            // public
            wp_register_style( 'bp3d-custom-style', BP3D_DIR . 'public/css/custom-style.css', [],  BP3D_VERSION);
            wp_register_style( 'bp3d-public', BP3D_DIR . 'dist/public.css', ['bp3d-custom-style'], BP3D_VERSION );
            wp_register_style( 'bp3d-block', BP3D_DIR . 'dist/block.css', [ 'bp3d-public'], BP3D_VERSION );

            wp_register_script('bp3d-3d-viewer', BP3D_DIR.'dist/3d-viewer.js', [], '1.0.0', true );
            wp_register_script('bp3d-o3dviewer', BP3D_DIR.'public/js/o3dviewer.js', [], BP3D_VERSION, true );
            wp_register_script('bp3d-model-viewer', BP3D_DIR.'public/js/model-viewer.min.js', [], BP3D_VERSION, true );
            wp_register_script('bp3d-public', BP3D_DIR . 'dist/public.js', [ 'react', 'react-dom', 'jquery' ], BP3D_VERSION, true );

            wp_register_script('bp3d-front-end', BP3D_DIR . 'dist/3d-viewer-frontend.js', [ 'react', 'react-dom', 'jquery'], BP3D_VERSION, true );

            wp_register_script('bp3d-block', BP3D_DIR . 'dist/block.js', ['bp3d-public', 'bp3d-o3dviewer' ], BP3D_VERSION, true );

            register_block_type(BP3D_PATH .'blocks/3d-viewer', array(
                'editor_script' => 'bp3d-block',
                'editor_style' => 'bp3d-block',
                'style' => 'bp3d-public',
                'render_callback' => function($attrs){
                    ob_start();  ?>
                    
                    <div class="modelViewerBlock" data-attributes='<?php echo esc_attr(wp_json_encode($attrs)) ?>'></div>
                    
                    <?php return ob_get_clean();
                }
            ));

            wp_localize_script('bp3d-block', 'bp3dBlock', [
                'nonce' => wp_create_nonce('wp_ajax'),
                'ajaxURL' => admin_url( 'admin-ajax.php' )
            ]);


            wp_localize_script('b3dviewer-modelviewer-view-script', 'bp3dBlock', [
                'modelViewerSrc' => BP3D_DIR.'public/js/model-viewer.min.js',
                'o3dviewerSrc' => BP3D_DIR.'public/js/o3dviewer.js'
            ]);

            wp_localize_script('bp3d-front-end', 'bp3dBlock', [
                'modelViewerSrc' => BP3D_DIR.'public/js/model-viewer.min.js',
                'o3dviewerSrc' => BP3D_DIR.'public/js/o3dviewer.js'
            ]);
            
            wp_localize_script('bp3d-public', 'bp3dBlock', [
                'modelViewerSrc' => BP3D_DIR.'public/js/model-viewer.min.js',
                'o3dviewerSrc' => BP3D_DIR.'public/js/o3dviewer.js'
            ]);
            
            wp_set_script_translations( 'b3dviewer-blocks', 'model-viewer', plugin_dir_path( __FILE__ ) . '/languages' ); // Translate
        }
        
    }

