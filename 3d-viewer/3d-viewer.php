<?php

/*
 * Plugin Name: 3D Viewer
 * Plugin URI:  https://bplugins.com/
 * Description: Easily display interactive 3D models on the web. Supported File type .glb, .gltf,obj 3ds stl ply off 3dm fbx dae wrl 3mf amf ifc brep step iges fcstd bim
 * Version: 1.5.0
 * Author: bPlugins
 * Author URI: http://bplugins.com
 * License: GPLv3
 * Text Domain: model-viewer
 * Domain Path:  /languages
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( function_exists( 'bp3dv_fs' ) ) {
    bp3dv_fs()->set_basename( false, __FILE__ );
} else {
    if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
        require_once dirname( __FILE__ ) . '/vendor/autoload.php';
    }
    function get_registered_js_files() {
        global $wp_scripts;
        $registered_js_files = array();
        // Loop through all registered scripts and collect their handles
        foreach ( $wp_scripts->registered as $handle => $script ) {
            $registered_js_files[] = $handle;
        }
        return $registered_js_files;
    }

    if ( isset( $_SERVER['HTTP_HOST'] ) && $_SERVER['HTTP_HOST'] === 'localhost' ) {
        define( 'BP3D_VERSION', time() );
    } else {
        define( 'BP3D_VERSION', '1.5.0' );
    }
    defined( 'BP3D_DIR' ) or define( 'BP3D_DIR', plugin_dir_url( __FILE__ ) );
    defined( 'BP3D_PATH' ) or define( 'BP3D_PATH', plugin_dir_path( __FILE__ ) );
    defined( 'BP3D_TEMPLATE_PATH' ) or define( 'BP3D_TEMPLATE_PATH', plugin_dir_path( __FILE__ ) . 'inc/Template/' );
    defined( 'BP3D__FILE__' ) or define( 'BP3D__FILE__', __FILE__ );
    define( 'BP3D_IMPORT_VER', '1.0.0' );
    if ( !function_exists( 'bp3dv_fs' ) ) {
        // Create a helper function for easy SDK access.
        function bp3dv_fs() {
            global $bp3dv_fs;
            if ( !isset( $bp3dv_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $bp3dv_fs = fs_dynamic_init( array(
                    'id'              => '8795',
                    'slug'            => '3d-viewer',
                    'type'            => 'plugin',
                    'public_key'      => 'pk_5e6ce3f226c86e3b975b59ed84d6a',
                    'is_premium'      => false,
                    'premium_suffix'  => 'Pro',
                    'has_addons'      => false,
                    'has_paid_plans'  => true,
                    'trial'           => array(
                        'days'               => 7,
                        'is_require_payment' => false,
                    ),
                    'has_affiliation' => 'selected',
                    'menu'            => array(
                        'contact' => false,
                        'slug'    => 'edit.php?post_type=bp3d-model-viewer',
                    ),
                    'is_live'         => true,
                ) );
            }
            return $bp3dv_fs;
        }

        // Init Freemius.
        bp3dv_fs();
        // Signal that SDK was initiated.
        do_action( 'bp3dv_fs_loaded' );
    }
    function bp3d_isset(  $array, $key, $default = false  ) {
        if ( isset( $array[$key] ) ) {
            return $array[$key];
        }
        return $default;
    }

    // External files Inclusion
    require_once 'admin/csf/codestar-framework.php';
    require_once 'admin/ads/submenu.php';
    if ( !class_exists( 'BP3D' ) ) {
        class BP3D {
            protected static $instance = null;

            public static function get_instance() {
                if ( null === self::$instance ) {
                    self::$instance = new self();
                }
                return self::$instance;
            }

            public function __construct() {
                $init_file = BP3D_PATH . 'inc/Init.php';
                require_once BP3D_PATH . '3d-viewer-block/inc/block.php';
                if ( file_exists( $init_file ) ) {
                    require_once $init_file;
                }
                if ( class_exists( 'BP3D\\Init' ) ) {
                    \BP3D\Init::instance();
                }
            }

            function plugins_loaded() {
            }

        }

        BP3D::get_instance();
    }
}