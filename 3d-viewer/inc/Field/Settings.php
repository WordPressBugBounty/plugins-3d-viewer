<?php
namespace BP3D\Field;

class Settings {

    protected $prefix = '_bp3d_settings_';
    public function register(){
        $this->preset();
        $this->woocommerce();
        $this->shortcode();
    }

    public function preset(){
        \CSF::createOptions( $this->prefix, array(
            'menu_title'  => 'Settings',
            'menu_slug'   => '3dviewer-settings',
            'menu_type'   => 'submenu',
            'menu_parent' => 'edit.php?post_type=bp3d-model-viewer',
            'theme'       => 'light',
            'framework_title' => esc_html__('3D Viewer Settings', 'model-viewer'),
            'menu_position' => 10,
            'footer'      => false,
            'footer_credit'  => '3D Viewer',
            'footer_text' => '',
          
        ) );

        \CSF::createSection( $this->prefix, array(
            'title'  => esc_html__('Preset', 'model-viewer'),
            'class'    => 'bp3d-readonly',
            'fields' => array(
              array(
                'id'           => 'bpp_3d_width',
                'type'         => 'dimensions',
                'title'        => esc_html__('Width', 'model-viewer'),
                'desc'         => esc_html__('3D Viewer Width', 'model-viewer'),
                'default'  => array(
                  'width'  => '100',
                  'unit'   => '%',
                ),
                'height'   => false,
              ),
              array(
                'id'      => 'bpp_3d_height',
                'type'    => 'dimensions',
                'title'   => esc_html__('Height', 'model-viewer'),
                'desc'    => esc_html__('3D Viewer height', 'model-viewer'),
                'units'   => ['px', 'em', 'pt'],
                'default'  => array(
                  'height' => '320',
                  'unit'   => 'px',
                ),
                'width'   => false,
              ),
              array(
                'id'           => 'bpp_model_bg',
                'type'         => 'color',
                'title'        => esc_html__('Background Color', 'model-viewer'),
                'subtitle'        => esc_html__('Set Background Color For 3d Model.If You don\'t need just leave blank. Default : \'transparent color\'', 'model-viewer'),
                'desc'         => esc_html__('Choose Your Background Color For Model.', 'model-viewer'),
                'default'      => 'transparent'
              ),
              array(
                'id'       => 'bpp_3d_autoplay',
                'type'     => 'switcher',
                'title'    => esc_html__('Autoplay', 'model-viewer'),
                'subtitle' => esc_html__('Enable or Disable AutoPlay', 'model-viewer'),
                'desc'     => esc_html__('Autoplay Feature is for Autoplay Supported Model.', 'model-viewer'),
                'text_on'  => esc_html__('Yes', 'model-viewer'),
                'text_off' => esc_html__('No', 'model-viewer'),
                'default'  => false,
              ),
              array(
                'id'       => '3dp_shadow_intensity',
                'type'     => 'spinner',
                'title'    => esc_html__('Shadow Intensity', 'model-viewer'),
                'subtitle' => esc_html__('Shadow Intensity for Model', 'model-viewer'),
                'desc'     => esc_html__('Use Shadow Intensity Limit for Model. "1" for Default.', 'model-viewer'),
                'default' => '1',
                'class' => 'bp3d-readonly'
              ),
          
              array(
                'id'        => 'bpp_3d_preloader',
                'type'      => 'switcher',
                'title'     => esc_html__('Preload', 'model-viewer'),
                'subtitle'  => esc_html__('Preload with poster and show model on interaction', 'model-viewer'),
                'desc'      => esc_html__('Choose "Yes" if you want to use preload with poster image.', 'model-viewer'),
                'text_on'   => esc_html__('Yes', 'model-viewer'),
                'text_off'  => esc_html__('NO', 'model-viewer'),
                'text_width'  => 60,
                'default'   => false,
              ),
              array(
                'id'       => 'bpp_camera_control',
                'type'     => 'switcher',
                'title'    => esc_html__('Moving Controls', 'model-viewer'),
                'desc'     => esc_html__('Use The Moving controls to enable user interaction', 'model-viewer'),
                'text_on'  => esc_html__('Yes', 'model-viewer'),
                'text_off' => esc_html__('No', 'model-viewer'),
                'default' => true,
          
              ),
              array(
                'id'        => 'bpp_3d_zooming',
                'type'      => 'switcher',
                'title'     => 'Enable Zoom',
                'subtitle'  => esc_html__('Enable or Disable Zoom Behaviour', 'model-viewer'),
                'desc'      => esc_html__('If you wish to disable zooming behaviour please choose No.', 'model-viewer'),
                'text_on'   => esc_html__('Yes', 'model-viewer'),
                'text_off'  => esc_html__('No', 'model-viewer'),
                'text_width'  => 60,
                'default'   => true,
              ),
              array(
                'id'        => 'bpp_3d_progressbar',
                'type'      => 'switcher',
                'title'     => esc_html__('Progressbar', 'model-viewer'),
                'subtitle'  => esc_html__('Enable or Disable Progressbar', 'model-viewer'),
                'desc'      => esc_html__('If you wish to disable Progressbar please choose No.', 'model-viewer'),
                'text_on'   => esc_html__('Yes', 'model-viewer'),
                'text_off'  => esc_html__('No', 'model-viewer'),
                'text_width'  => 60,
                'default'   => true,
              ),
              array(
                'id'         => 'bpp_3d_loading',
                'type'       => 'radio',
                'title'      => esc_html__('Loading Type', 'model-viewer'),
                'subtitle'   => esc_html__('Choose Loading type, default:  \'Auto\' ', 'model-viewer'),
                'options'    => array(
                  'auto'  => esc_html__('Auto', 'model-viewer'),
                  'lazy'  => esc_html__('Lazy', 'model-viewer'),
                  'eager' => esc_html__('Eager', 'model-viewer'),
                ),
                'default' => 'auto',
              ),
          
              array(
                'id'       => 'bpp_3d_rotate',
                'type'     => 'switcher',
                'title'    => esc_html__('Auto Rotate', 'model-viewer'),
                'subtitle' => esc_html__('Enable or Disable Auto Rotation', 'model-viewer'),
                'desc'     => esc_html__('Enables the auto-rotation of the model.', 'model-viewer'),
                'text_on'  => esc_html__('Yes', 'model-viewer'),
                'text_off' => esc_html__('No', 'model-viewer'),
                'default'  => true,
          
              ),
              array(
                'id'       => '3dp_rotate_speed',
                'type'     => 'spinner',
                'title'    => esc_html__('Auto Rotate Speed', 'model-viewer'),
                'subtitle' => esc_html__('Auto Rotation Speed Per Seconds', 'model-viewer'),
                'desc'     => esc_html__('Use Negative Number for Reverse Action. "30" for Default Behaviour.', 'model-viewer'),
                'min'         => 0,
                'max'         => 180,
                'default' => 30,
                'dependency' => array( 'bp_3d_rotate', '==', true ),
              ),
              array(
                'id'       => '3dp_rotate_delay',
                'type'     => 'number',
                'title'    => esc_html__('Auto Rotation Delay', 'model-viewer'),
                'subtitle' => esc_html__('After a period of time auto rotation will start', 'model-viewer'),
                'desc'     => esc_html__('Sets the delay before auto-rotation begins. The format of the value is a number in milliseconds.(1000ms = 1s)', 'model-viewer'),
                'default' => 3000,
                'dependency' => array( 'bp_3d_rotate', '==', true ),
              ),
              array(
                'id'       => 'bpp_3d_fullscreen',
                'type'     => 'switcher',
                'title'    => esc_html__('Fullscreen', 'model-viewer'),
                'subtitle' => esc_html__('Enable or Disable Fullscreen Mode', 'model-viewer'),
                'desc'     => esc_html__('Default: "Yes / Enable"', 'model-viewer'),
                'text_on'  => esc_html__('Yes', 'model-viewer'),
                'text_off' => esc_html__('No', 'model-viewer'),
                'default'  => true,
              ),
            ) // End fields
          
          
          ) );
    }

    public function woocommerce(){
      \CSF::createSection( $this->prefix, array(
        'title'  => esc_html__('Woocommerce Settings', 'model-viewer'),
        'fields' => array(
          // 3D Model Options
          array(
            'id'       => '3d_woo_switcher',
            'type'      => 'switcher',
            'title'    => esc_html__('Woocommerce', 'model-viewer'),
            'subtitle' => esc_html__('Enable / Disable Woocommerce Feature for 3D Viewer.', 'model-viewer'),
            'desc'     => esc_html__('Enable / Disable. Default is Enable.', 'model-viewer'),
            'default' => true,
          ),
          array(
            'id'       => 'is_not_compatible',
            'type'      => 'switcher',
            'title'    => __('3D Viewer is not Compatible with this Theme', 'model-viewer'),
            'desc'     => __('Enable if 3D Viewer is not compatible with this theme', 'model-viewer'),
            'default' => false,
          ),
          array(
            'id'       => 'product_gallery_selector',
            'type'      => 'text',
            'title'    => __('Product Gallery Class or ID or Valid CSS Selector', 'model-viewer'),
            'desc'     => __('Write here the product gallery class or id or any valid CSS selector', 'model-viewer'),
            'default' => '.woocommerce-product-gallery',
          ),
          array(
            'id'       => '3d_shadow_intensity',
            'type'     => 'spinner',
            'title'    => esc_html__('Shadow Intensity', 'model-viewer'),
            'subtitle' => esc_html__('Shadow Intensity for Model', 'model-viewer'),
            'desc'     => esc_html__('Use Shadow Intensity Limit for Model. "1" for Default.', 'model-viewer'),
            'default' => '1',
            'class'    => 'bp3d-readonly'
          ),
          array(
            'id'       => 'bp_camera_control',
            'type'     => 'switcher',
            'title'    => esc_html__('Moving Controls', 'model-viewer'),
            'desc'     => esc_html__('Use The Moving controls to enable user interaction', 'model-viewer'),
            'text_on'  => esc_html__('Yes', 'model-viewer'),
            'text_off' => esc_html__('No', 'model-viewer'),
            'default' => true,
          ),
          array(
            'id'        => 'bp_3d_zooming',
            'type'      => 'switcher',
            'title'     => esc_html__('Enable Zoom', 'model-viewer'),
            'subtitle'  => esc_html__('Enable or Disable Zoom Behaviour', 'model-viewer'),
            'desc'      => esc_html__('If you wish to disable zooming behaviour please choose No.', 'model-viewer'),
            'text_on'   => esc_html__('Yes', 'model-viewer'),
            'text_off'  => esc_html__('No', 'model-viewer'),
            'text_width'  => 60,
            'default'   => true,
          ),
          array(
            'id'        => 'bp_3d_progressbar',
            'type'      => 'switcher',
            'title'     => esc_html__('Progressbar', 'model-viewer'),
            'subtitle'  => esc_html__('Enable or Disable Progressbar', 'model-viewer'),
            'desc'      => esc_html__('If you wish to disable Progressbar please choose No.', 'model-viewer'),
            'text_on'   => esc_html__('Yes', 'model-viewer'),
            'text_off'  => esc_html__('No', 'model-viewer'),
            'text_width'  => 60,
            'default'   => true,
            'class'    => 'bp3d-readonly'
          ),
          array(
            'id'         => 'bp_3d_loading',
            'type'       => 'radio',
            'title'      => esc_html__('Loading Type', 'model-viewer'),
            'subtitle'   => esc_html__('Choose Loading type, default:  \'Auto\' ', 'model-viewer'),
            'options'    => array(
              'auto'  => esc_html__('Auto', 'model-viewer'),
              'lazy'  => esc_html__('Lazy', 'model-viewer'),
              'eager' => esc_html__('Eager', 'model-viewer'),
            ),
            'default'    => 'auto',
          ),
          array(
            'id'       => 'bp_3d_rotate',
            'type'     => 'switcher',
            'title'    => esc_html__('Auto Rotate', 'model-viewer'),
            'subtitle' => esc_html__('Enable or Disable Auto Rotation', 'model-viewer'),
            'desc'     => esc_html__('Enables the auto-rotation of the model.', 'model-viewer'),
            'text_on'  => esc_html__('Yes', 'model-viewer'),
            'text_off' => esc_html__('No', 'model-viewer'),
            'default'  => true,
            'class'    => 'bp3d-readonly'
          ),
          array(
            'id'       => '3d_rotate_speed',
            'type'     => 'spinner',
            'title'    => esc_html__('Auto Rotate Speed', 'model-viewer'),
            'subtitle' => esc_html__('Auto Rotation Speed Per Seconds', 'model-viewer'),
            'desc'     => esc_html__('Use Negative Number for Reverse Action. "30" for Default Behaviour.', 'model-viewer'),
            'min'         => 0,
            'max'         => 180,
            'default' => 30,
            'dependency' => array( 'bp_3d_rotate', '==', true ),
            'class'    => 'bp3d-readonly'
          ),
          array(
            'id'       => '3d_rotate_delay',
            'type'     => 'number',
            'title'    => esc_html__('Auto Rotation Delay', 'model-viewer'),
            'subtitle' => esc_html__('After a period of time auto rotation will start', 'model-viewer'),
            'desc'     => esc_html__('Sets the delay before auto-rotation begins. The format of the value is a number in milliseconds.(1000ms = 1s)', 'model-viewer'),
            'default' => 3000,
            'dependency' => array( 'bp_3d_rotate', '==', true ),
            'class'    => 'bp3d-readonly'
          ),
          array(
            'id'       => 'bp_3d_autoplay',
            'type'     => 'switcher',
            'title'    => esc_html__('Autoplay', 'model-viewer'),
            'subtitle' => esc_html__('Enable or Disable AutoPlay', 'model-viewer'),
            'desc'     => esc_html__('Autoplay Feature is for Autoplay Supported Model.', 'model-viewer'),
            'text_on'  => esc_html__('Yes', 'model-viewer'),
            'text_off' => esc_html__('No', 'model-viewer'),
            'default'  => false,
            'class'    => 'bp3d-readonly'
          ),
          array(
            'id'       => 'bp_3d_fullscreen',
            'type'     => 'switcher',
            'title'    => esc_html__('Fullscreen', 'model-viewer'),
            'subtitle' => esc_html__('Enable or Disable Fullscreen Mode', 'model-viewer'),
           'desc'     => esc_html__('Default: "Yes / Enable"', 'model-viewer'),
            'text_on'  => esc_html__('Yes', 'model-viewer'),
            'text_off' => esc_html__('No', 'model-viewer'),
            'default'  => true,
            'class'    => 'bp3d-readonly'
          ),
        ) // End fields
      ) );
    }

    public function shortcode(){
      \CSF::createSection( $this->prefix, array(
        'title'  => esc_html__('Shortcode Generator', 'model-viewer'),
        'fields' => array(
          // 3D Model Options
          array(
            'id'       => 'gutenberg_enabled',
            'type'      => 'switcher',
            'title'    => esc_html__('Enable Gutenberg', 'model-viewer'),
            'subtitle' => esc_html__('Enable / Disable Gutenberg Shortcode Generator.', 'model-viewer'),
            'default' => false,
          ),
        ) // End fields
      ) );
    }
}