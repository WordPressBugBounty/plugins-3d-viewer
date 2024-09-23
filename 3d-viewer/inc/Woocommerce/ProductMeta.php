<?php
namespace BP3D\Woocommerce;

class ProductMeta{

    public function register(){
        $settings = get_option( '_bp3d_settings_', ['3d_woo_switcher' => ''] );
        if($settings['3d_woo_switcher'] == '0'){
            return;
        }

        $prefix = '_bp3d_product_';
        
        \CSF::createMetabox( $prefix, array(
            'title'        => esc_html__('3D Viewer Settings', 'model-viewer'),
            'post_type'    =>  'product',
            'show_restore' => true,
        ));

        \CSF::createSection( $prefix, array(
            'fields' => array(
              array(
                'id' => 'meta_heading',
                'type' => 'content',
                'title' => 'Support',
                'content' => 'Please leave a message if you encounter any issues on the product page. <a href="https://bplugins.com/support" target="_blank"><b>Support Center</b></a><br /> <cite Style="color:#2271b1; font-weight: bold ">The premium version also supports the following formats: obj, stl, 3dm, 3ds, 3mf, amf, bim, brep, dae, fbx, fcstd, gltf, ifc, iges, step, off, ply, and wrl.</cite>'
              ),
              array(
                'id'     => 'bp3d_models',
                'type'   => 'group',
                // 'type'   => 'repeater',
                'title'  => esc_html__('Product 3D Models', 'model-viewer'),
                'desc'  => 'Click on + icon to add 3d files, if you add multiple 3d files, we will show them as a slider <cite Style="color:#2271b1; font-weight: bold ">Multiple Files Support Only For Pro Version</cite>',
                'button_title' => __('Add New Model', 'model-viewer'),
                'max'   => 1,
                'fields' => array(
                  array(
                    'id'           => 'model_src',
                    'type'         => 'upload',
                    'title'        => esc_html__('3D Source', 'model-viewer'),
                    'subtitle'     => esc_html__('Upload Model Or Input Valid Model url', 'model-viewer'),
                    'desc'         => esc_html__('Upload / Paste Model url. Supported file type: glb, glTF', 'model-viewer'),
                    'placeholder'  => esc_html__('You Can Paste here Model url', 'model-viewer'),
                  ),
                  array(
                    'id'           => 'poster_src',
                    'type'         => 'upload',
                    'title'        => __('3D Poster', 'model-viewer'),
                    'subtitle'     => __('Upload Poster Or Input Valid poster/image url', 'model-viewer'),
                    'placeholder'  => 'You Can Paste here Poster/image url',
                    'desc'         => __('This image will display until the model is either loaded or fails to load.', 'model-viewer'),
                  )
                ),
              ),
          
              // Model Positioning Option
              array(
                'id'         => 'viewer_position',
                'type'       => 'radio',
                'title'      => esc_html__('3D Viewer Position', 'model-viewer'),
                'options'    => array(
                  'none' => esc_html__('None', 'model-viewer'),
                  'top' => esc_html__('Top of the product image', 'model-viewer'),
                  'bottom' => esc_html__('Bottom of the product image','model-viewer'),
                  'replace' => esc_html__('Replace Product Image with 3D', 'model-viewer')
                ),
                'default'    => 'top',
                'desc' => __("Select the position of the viewer", 'model-viewer')
              ),
              array(
                'id' => 'force_to_change_position',
                'type' => 'switcher',
                'title' => __("Force To Change Position", "model-viewer"),
                'text_on'  => 'Yes',
                'text_off' => 'No',
                'default' => false
              ),

              array(
                'id'        => 'bp_model_angle',
                'type'      => 'switcher',
                'title'     => 'Custom Angle',
                'subtitle'  => esc_html__('Specified Custom Angle of Model in Initial Load.', 'model-viewer'),
                'desc'      => esc_html__('Enable or Disable Custom Angle Option.', 'model-viewer'),
                'text_on'   => esc_html__('Yes', 'model-viewer'),
                'text_off'  => esc_html__('NO', 'model-viewer'),
                'text_width'  => 60,
                'default'   => false,
                'class'    => 'bp3d-readonly'
              ),
              array(
                'id'    => 'angle_property',
                'type'  => 'spacing',
                'title' => esc_html__('Custom Angle Values', 'model-viewer'),
                'subtitle'=> esc_html__('Set The Custom values for Model. Default Values are ("X=0deg Y=75deg Z=105%")', 'model-viewer'),
                'desc'    => esc_html__('Set Your Desire Values. (X= Horizontal Position, Y= Vertical Position, Z= Zoom Level/Position) ', 'model-viewer'),
                'default'  => array(
                  'top'    => '0',
                  'right'  => '75',
                  'bottom' => '105',
                ),
                'left'   => false,
                'show_units' => false,
                'top_icon'    => 'Deg',
                'right_icon'  => 'Deg',
                'bottom_icon' => '%',
                'dependency' => array( 'bp_model_angle', '==', '1' ),
              ),
          
            ) // End fields
        ) );

    }
}