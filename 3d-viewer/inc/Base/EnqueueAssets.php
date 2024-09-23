<?php
namespace BP3D\Base;

class EnqueueAssets{

    public function register(){
        add_action('admin_enqueue_scripts', [$this, 'enqueueBackendFiles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueFrontEndFiles']);
        add_filter('script_loader_tag', [$this, 'b3dviewer_script_type_load'] , 10, 3);
    }

    
    public function b3dviewer_script_type_load($tag, $handle, $src){
        // if not your script, do nothing and return original $tag
        if ( 'bp3d-model-viewer' !== $handle ) {
            return $tag;
        }
        // change the script tag by adding type="module" and return it.
        $tag = '<script type="module" id="'.$handle.'-js" src="' . esc_url( $src ) . '"></script>';
        return $tag;
    }

    public function enqueueFrontEndFiles(){

        wp_localize_script( 'bp3d-public', 'assetsUrl', [
            'siteUrl'   => site_url(),
            'assetsUrl' => BP3D_DIR . '/public',
        ]);

    }

    public function enqueueBackendFiles($hook_suffix){
        global $post;
        $post_type = isset($post->post_type) ? $post->post_type : (isset($_GET['post_type']) ? $_GET['post_type'] : null);
        $woo_enabled = get_option('b3dviewer_enable_woocommerce', true);

        //script
        wp_register_script('bp3d-admin-script', BP3D_DIR . 'dist/admin.js', [ 'jquery' ], BP3D_VERSION, true );
        // style
        wp_register_style('bp3d-admin-style', BP3D_DIR . 'public/css/admin-style.css', [], BP3D_VERSION );
        wp_register_style('bp3d-readonly-style', BP3D_DIR . 'public/css/readonly.css',[], BP3D_VERSION );
        
        if($post_type === 'bp3d-model-viewer'){
            wp_enqueue_style( 'bp3d-admin-style' );
            wp_enqueue_style( 'bp3d-readonly-style' );
            wp_enqueue_script('bp3d-admin-script');
        }
    }
}

