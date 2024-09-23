<?php
namespace BP3D\Helper;

class Utils {

    public static $theme_name = null;

    function __construct()
    {
        self::$theme_name = wp_get_theme()->name;
    }

    public static function isset($array, $key, $default = false){
        if(isset($array[$key])){
            return $array[$key];
        }
        return $default;
    }

    public static function isset2($array, $key1, $key2, $default = false){
        if(isset($array[$key1][$key2])){
            return $array[$key1][$key2];
        }
        return $default;
    }

     /**
     * convert hex to rgb color
     */
    public static function hexToRGB($hex, $alpha = false){
        $hex      = str_replace('#', '', $hex);
        $length   = strlen($hex);
        $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
        $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
        $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
        if ( $alpha ) {
            $rgb['a'] = $alpha;
        }
        return $rgb;
    }

    /**
     * @param string $theme
     * @return string css selector
     */
    public static function getCustomSelector($theme){

        $theme = str_replace(' Child', '', $theme);

        // $common_themes = ['Twenty Twenty-Four', 'Astra', 'Storely', 'OceanWP', 'Woodmart', 'Rafdt'];

        // if(in_array($theme, $common_themes)){
        //     return '.woocommerce-product-gallery';
        // }

        $selectors = [
            'Woostify' => '.product-gallery'
        ];

        return $selectors[$theme] ?? '.woocommerce-product-gallery';
    }


    /**
     * @param string $string
     * @return string css class
     */
    static function getThemeClass($string) {
        // Replace spaces with underscores
        $string = str_replace(' ', '_', $string);
        // Convert the string to lowercase
        $string = strtolower($string);
        return $string;
    }

    static function getNotCompatibleThemes(){

        $settings = get_option( '_bp3d_settings_' );

        $is_not_compatible = $settings['is_not_compatible'] ?? false;
        $themes = ['Twenty Twenty-Four', 'Twenty Twenty Three', 'Woostify', 'Raft', 'eStore'];

        if($is_not_compatible){
            return wp_parse_args([wp_get_theme()->name], $themes);
        }

        return $themes;
    }
}