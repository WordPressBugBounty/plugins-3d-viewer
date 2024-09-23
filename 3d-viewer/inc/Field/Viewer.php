<?php
namespace BP3D\Field;

class Viewer {
    
    public function register(){
        $this->create_metabox();
    }

    public function create_metabox(){
        $prefix = '_bp3dimages_';
        \CSF::createMetabox( $prefix, array(
            'title'        => esc_html__('3D Viewer Settings', 'model-viewer'),
            'post_type'    => 'bp3d-model-viewer',
            'show_restore' => true,
        ) );

        \CSF::createSection( $prefix, array(
            'fields' => array(
              // 3D Model Options
              array(
                'id'       => 'currentViewer',
                'type'     => 'button_set',
                'title'    => __('Viewer.', 'model-viewer'),
                'subtitle' => __('Choose Viewer', 'model-viewer'),
                'desc'     => __('The Lite Viewer prioritizes .glb and .glTF files with robust features, while the Advanced Viewer, supporting .obj, .3ds, .stl, .ply, .gltf, .off, .3dm, .fbx, .dae, .wrl, .3mf, amf, ifc, .brep, .step, .iges, .fcstd, .bim  file types, has a more streamlined functionality.', 'model-viewer'),
                'multiple' => false,
                'options'  => array(
                  'modelViewer'  => 'Lite',
                  'O3DViewer'   => 'Advanced',
                ),
                'default'  => 'modelViewer'
              ),
              array(
                'id'       => 'bp_3d_model_type',
                'type'     => 'button_set',
                'title'    => esc_html__('Model Type.', 'model-viewer'),
                'subtitle' => esc_html__('Choose Model Type', 'model-viewer'),
                'desc'     => esc_html__('Select Model Type, Default- Simple.', 'model-viewer'),
                'multiple' => false,
                'options'  => array(
                  'msimple'  => esc_html__('Simple', 'model-viewer'),
                  'mcycle'   => esc_html__('Cycle', 'model-viewer'),
                ),
                'default'  => array('msimple')
              ),
              array(
                'id'       => 'bp_3d_src_type',
                'type'     => 'button_set',
                'title'    => esc_html__('Model Source Type.', 'model-viewer'),
                'subtitle' => esc_html__('Choose Model Source', 'model-viewer'),
                'desc'     => esc_html__('Select Model Source, Default- Upload.', 'model-viewer'),
                'multiple' => false,
                'options'  => array(
                  'upload'  => esc_html__('Upload', 'model-viewer'),
                  'link'   => esc_html__('Link', 'model-viewer'),
                ),
                'default'  => array('upload'),
                'dependency' => array( 'bp_3d_model_type', '==', 'msimple'),
              ),
              array(
                'id'           => 'bp_3d_src',
                'type'         => 'media',
                'button_title' => esc_html__('Upload Source', 'model-viewer'),
                'title'        => esc_html__('3D Source', 'model-viewer'),
                'subtitle'     => esc_html__('Choose 3D Model', 'model-viewer'),
                'desc'         => esc_html__('Upload or Select 3d object files. Supported file type: glb, glTF', 'model-viewer'),
                'dependency' => array( 'bp_3d_model_type|bp_3d_src_type', '==|==', 'msimple|upload', 'all' ),
              ),
              array(
                'id'           => 'bp_3d_src_link',
                'type'         => 'text',
                'button_title' => esc_html__('Paste Source', 'model-viewer'),
                'title'        => esc_html__('3D Source', 'model-viewer'),
                'subtitle'     => esc_html__('Input Model Valid url', 'model-viewer'),
                'desc'         => esc_html__('Input / Paste Model url. Supported file type: glb, glTF', 'model-viewer'),
                'placeholder'  => 'Paste here Model url',
                'dependency' => array( 'bp_3d_model_type|bp_3d_src_type', '==|==', 'msimple|link', 'all' ),
                'class'    => 'bp3d-readonly'
              ),
              array(
                'id'     => 'bp_3d_models',
                'type'   => 'repeater',
                'title'        => esc_html__('3D Cycle Models', 'model-viewer'),
                'subtitle'     => esc_html__('Cycling between 3D Models', 'model-viewer'),
                'button_title' => esc_html__('Add New Model', 'model-viewer'),
                'desc'         => esc_html__('Use Multiple Model in a row.', 'model-viewer'),
                'class'    => 'bp3d-readonly',
                'fields' => array(
                  array(
                    'id'    => 'model_src',
                    'type'  => 'media',
                    'title' =>  esc_html__('Model Source', 'model-viewer'),
                    'desc'  => esc_html__('Upload or Select 3d object files. Supported file type: glb, glTF', 'model-viewer'),
                  ),
              
                ),
                'dependency' => array( 'bp_3d_model_type', '==', 'mcycle' ),
              ),
              array(
                'id'           => 'bp_3d_width',
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
                'id'           => 'bp_3d_height',
                'type'         => 'dimensions',
                'title'        => esc_html__('Height', 'model-viewer'),
                'desc'         => esc_html__('3D Viewer height', 'model-viewer'),
                'units'        => ['px', 'em', 'pt'],
                'default'  => array(
                  'height' => '320',
                  'unit'   => 'px',
                ),
                'width'   => false,
              ),
              array(
                'id'           => 'bp_model_bg',
                'type'         => 'color',
                'title'        => esc_html__('Background Color', 'model-viewer'),
                'subtitle'        => esc_html__('Set Background Color For 3d Model.If You don\'t need just leave blank. Default : \'transparent color\'', 'model-viewer'),
                'desc'         => esc_html__('Choose Your Background Color For Model.', 'model-viewer'),
                'default'      => 'transparent'
              ),
              array(
                'id'       => 'bp_camera_control',
                'type'     => 'switcher',
                'title'    => esc_html__('Moving Controls', 'model-viewer'),
                'desc'     => esc_html__('Use The Moving controls to enable user interaction', 'model-viewer'),
                'text_on'  => 'Yes',
                'text_off' => 'No',
                'default' => true,  
          
              ),
              array(
                'id'        => 'bp_3d_zooming',
                'type'      => 'switcher',
                'title'     => esc_html__('Enable Zoom', 'model-viewer'),
                'subtitle'  => esc_html__('Enable or Disable Zooming Behaviour', 'model-viewer'),
                'desc'      => esc_html__('If you wish to disable zooming behavior please choose No.', 'model-viewer'),
                'text_on'   => esc_html__('Yes', 'model-viewer'),
                'text_off'  => esc_html__('NO', 'model-viewer'),
                'text_width'  => 60,
                'default'   => true,
                'dependency' => ['currentViewer', '==', 'modelViewer']
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
                'dependency' => ['currentViewer', '==', 'modelViewer']
              ),
              array(
                'id' => 'bp_3d_align',
                'title' => esc_html__("Align", "model-viewer"),
                'type' => 'button_set',
                'options' => [
                  'start' => esc_html__('Left', 'model-viewer'),
                  'center' => esc_html__('Center', 'model-viewer'),
                  'end' => esc_html__('Right', 'model-viewer'),
                ],
                'default' => 'center',
              ),
              array(
                'id'        => 'bp_model_angle',
                'type'      => 'switcher',
                'title'     => 'Custom Angle',
                'subtitle'  => esc_html__('Specified Custom Angle of Model in Initial Load.', 'model-viewer'),
                'desc'      => esc_html__('Enable or Disable Custom Angle Option.', 'model-viewer'),
                'class'    => 'bp3d-readonly',
                'text_on'   => esc_html__('Yes',  'model-viewer'),
                'text_off'  => esc_html__('NO', 'model-viewer'),
                'text_width'  => 60,
                'default'   => false,
                'dependency' => ['currentViewer', '==', 'modelViewer']
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
                'dependency' => array( 'bp_model_angle|currentViewer', '==|==', '1|modelViewer' ),
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
                'class'    => 'bp3d-readonly',
                'dependency' => ['currentViewer', '==', 'modelViewer']
              ),
              array(
                'id'       => '3d_shadow_intensity',
                'type'     => 'spinner',
                'title'    => esc_html__('shadow Intensity', 'model-viewer'),
                'subtitle' => esc_html__('Shadow Intensity for Model', 'model-viewer'),
                'desc'     => esc_html__('Use Shadow Intensity Limit for Model. "1" for Default.', 'model-viewer'),
                'class'    => 'bp3d-readonly',
                'default' => '1',
                'dependency' => ['currentViewer', '==', 'modelViewer']
              ),
              array(
                'id'       => '3d_exposure',
                'type'     => 'spinner',
                'min' => 0.1,
                'max' => 5,
                'title'    => esc_html__('Exposure', 'model-viewer'),
                'subtitle' => esc_html__('Brightness for Model', 'model-viewer'),
                'desc'     => esc_html__('Use exposure to to increase/decrease brightness of Model. "1" for Default.', 'model-viewer'),
                'class'    => 'bp3d-readonly',
                'default' => '1',
                'dependency' => ['currentViewer', '==', 'modelViewer']
              ),
              array(
                'id'           => 'bp_model_anim_du',
                'type'         => 'text',
                'title'        => esc_html__('Cycle Animation Duration', 'model-viewer'),
                'subtitle'     => esc_html__('Animation Duration Time at Seconds : 1000ms = 1sec', 'model-viewer'),
                'desc'         => esc_html__('Input Model Animation Duration Time (default: \'5\') Seconds', 'model-viewer'),
                'class'    => 'bp3d-readonly',
                'default'   => 5000,
                'dependency' => array( 'bp_3d_model_type|currentViewer', '==|==', 'mcycle|modelViewer' ),
              ),
              // Poster Options
              array(
                'id'       => 'bp_3d_poster_type',
                'type'     => 'button_set',
                'title'    => esc_html__('Poster Type.', 'model-viewer'),
                'subtitle' => esc_html__('Choose Poster Type', 'model-viewer'),
                'desc'     => esc_html__('Select Poster Type, Default- Simple.', 'model-viewer'),
                'class'    => 'bp3d-readonly',
                'multiple' => false,
                'options'  => array(
                  'simple'  => esc_html__('simple', 'model-viewer'),
                  'cycle'   => esc_html__('Cycle', 'model-viewer'),
                ),
                'default'  => array('simple'),
              ),
              array(
                'id'           => 'bp_3d_poster',
                'type'         => 'media',
                'button_title' => esc_html__('Upload Poster', 'model-viewer'),
                'title'        => esc_html__('3D Poster Image', 'model-viewer'),
                'subtitle'     => esc_html__('Display a poster until loaded', 'model-viewer'),
                'desc'         => esc_html__('Upload or Select 3d Poster Image.  if you don\'t want to use just leave it empty', 'model-viewer'),
                'class'    => 'bp3d-readonly',
                'dependency' => array( 'bp_3d_poster_type', '==', 'simple' ),
              ),
              array(
                'id'     => 'bp_3d_posters',
                'type'   => 'repeater',
                'title'        => esc_html__('Poster Images', 'model-viewer'),
                'subtitle'     => esc_html__('Cycling between posters', 'model-viewer'),
                'button_title' => esc_html__('Add New Poster Images', 'model-viewer'),
                'desc'         => esc_html__('Use multiple images for poster image.if you don\'t want to use just leave it empty', 'model-viewer'),
                'fields' => array(
                  array(
                    'id'    => 'poster_img',
                    'type'  => 'upload',
                    'title' => 'Poster Image'
                  ),
              
                ),
                'dependency' => array( 'bp_3d_poster_type', '==', 'cycle' ),
                'class'    => 'bp3d-readonly',
              ),
              array(
                'id'        => 'bp_3d_preloader',
                'type'      => 'switcher',
                'title'     => esc_html__('Preload', 'model-viewer'),
                'subtitle'  => esc_html__('Preload with poster and show model on interaction', 'model-viewer'),
                'desc'      => esc_html__('Choose "Yes" if you want to use preload with poster image.', 'model-viewer'),
                'text_on'   => esc_html__('Yes', 'model-viewer'),
                'text_off'  => esc_html__('NO', 'model-viewer'),
                'text_width'  => 60,
                'class'    => 'bp3d-readonly',
                'default'   => false,
                'dependency' => ['currentViewer', '==', 'modelViewer']
              ),
              array(
                'id'        => 'bp_3d_progressbar',
                'type'      => 'switcher',
                'title'     => esc_html__('Progressbar', 'model-viewer'),
                'subtitle'  => esc_html__('Enable or Disable Progressbar', 'model-viewer'),
                'desc'      => esc_html__('If you wish to disable Progressbar please choose No.', 'model-viewer'),
                'text_on'   => esc_html__('Yes', 'model-viewer'),
                'text_off'  => esc_html__('NO', 'model-viewer'),
                'text_width'  => 60,
                'default'   => true,
                'class'    => 'bp3d-readonly',
                'dependency' => ['currentViewer', '==', 'modelViewer']
              ),
              array(
                'id' => 'bp_model_progress_percent',
                'type' => 'switcher',
                'title' => __("Show Progress Percent", "model-viewer"),
                'class'    => 'bp3d-readonly',
                'default' => false,
                'dependency' => ['currentViewer', '==', 'modelViewer']
              ),
              array(
                'id'       => 'bp_3d_rotate',
                'type'     => 'switcher',
                'title'    => esc_html__('Auto Rotate', 'model-viewer'),
                'subtitle' => esc_html__('Enable or Disable Auto Rotation', 'model-viewer'),
                'desc'     => esc_html__('Enables the auto-rotation of the model.', 'model-viewer'),
                'text_on'  => esc_html__('Yes', 'model-viewer'),
                'text_off' => esc_html__('No', 'model-viewer'),
                'class'    => 'bp3d-readonly',
                'default'  => false,
                'dependency' => ['currentViewer', '==', 'modelViewer']
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
                'class'    => 'bp3d-readonly',
                'dependency' => array( 'bp_3d_rotate|currentViewer', '==|==', '1|modelViewer' ),
              ),
              array(
                'id'       => '3d_rotate_delay',
                'type'     => 'number',
                'title'    => esc_html__('Auto Rotation Delay', 'model-viewer'),
                'subtitle' => esc_html__('After a period of time auto rotation will start', 'model-viewer'),
                'desc'     => esc_html__('Sets the delay before auto-rotation begins. The format of the value is a number in milliseconds.(1000ms = 1s)', 'model-viewer'),
                'default' => 3000,
                'class'    => 'bp3d-readonly',
                'dependency' => array( 'bp_3d_rotate|currentViewer', '==|==', '1|modelViewer' ),
              ),
              array(
                'id'       => 'bp_3d_fullscreen',
                'type'     => 'switcher',
                'title'    => esc_html__('Fullscreen', 'model-viewer'),
                'subtitle' => esc_html__('Enable or Disable Fullscreen Mode', 'model-viewer'),
               'desc'     => esc_html__('Default: "Yes / Enable"', 'model-viewer'),
                'text_on'  => esc_html__('Yes', 'model-viewer'),
                'text_off' => esc_html__('No', 'model-viewer'),
                'class'    => 'bp3d-readonly',
                'default'  => true,
              ),
              
            ) // End fields
          
          
          ) );
    }
}