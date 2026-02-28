<?php

namespace BP3D\Field;

if ( ! defined( 'ABSPATH' ) ) exit;


class Viewer
{

  protected $prefix = '_bp3dimages_';
  public function register()
  {
    add_action('init', [$this, 'init'], 0);
  }

  function init()
  {
    $this->create_metabox();
  }

  public function create_metabox()
  {
    \CSF::createMetabox($this->prefix, array(
      'title'        => __('3D Viewer Settings', 'model-viewer'),
      'post_type'    => 'bp3d-model-viewer',
      'show_restore' => true,
      'theme' => 'light'
    ));

    $this->model();
    $this->settings();
    $this->style();
  }

  public function model()
  {
    \CSF::createSection($this->prefix, array(
      'title' => __('Model', 'model-viewer'),
      'fields' => array(
        $this->upgrade_section(),
        array(
          'id'       => 'currentViewer',
          'type'     => 'button_set',
          'title'    => __('Viewer.', 'model-viewer'),
          'subtitle' => __('Choose Viewer', 'model-viewer'),
          'desc'     => __("Choose between Lite and Advanced viewer modes. Lite is optimized for GLB and GLTF files with strong performance and essential features. Advanced supports almost all 3D file types but offers a more streamlined feature set.", "model-viewer"),
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
          'title'    => __('Model Type.', 'model-viewer'),
          'subtitle' => __('Choose Model Type', 'model-viewer'),
          'desc'     => __("Enables multiple models in a single viewer, allowing users to switch between different 3D models.", "model-viewer"),
          'multiple' => false,
          'options'  => array(
            'msimple'  => __('Single', 'model-viewer'),
            'mcycle'   => __('Multiple', 'model-viewer'),
          ),
          'default'  => array('msimple')
        ),
        array(
          'id'       => 'bp_3d_src_type',
          'type'     => 'button_set',
          'title'    => __('Model Source Type.', 'model-viewer'),
          'subtitle' => __('Choose Model Source', 'model-viewer'),
          'desc'     => __('Choose between uploading a file or using a URL.', 'model-viewer'),
          'multiple' => false,
          'options'  => array(
            'upload'  => __('Upload', 'model-viewer'),
            'link'   => __('Link', 'model-viewer'),
          ),
          'default'  => array('upload'),
          'dependency' => array('bp_3d_model_type', '==', 'msimple'),
        ),
        array(
          'id'           => 'bp_3d_src',
          'type'         => 'media',
          'button_title' => __('Upload Source', 'model-viewer'),
          'title'        => __('3D Source', 'model-viewer'),
          'subtitle'     => __('Choose 3D Model', 'model-viewer'),
          'desc'         =>  __("Specifies the URL of the 3D model file to be displayed in the viewer.", "model-viewer"),
          'dependency' => array('bp_3d_model_type|bp_3d_src_type', '==|==', 'msimple|upload', 'all'),
        ),
        array(
          'id'           => 'bp_3d_src_link',
          'type'         => 'text',
          'button_title' => __('Paste Source', 'model-viewer'),
          'title'        => __('3D Source', 'model-viewer'),
          'subtitle'     => __('Input Model Valid url', 'model-viewer'),
          'desc'         => __("Specifies the URL of the 3D model file to be displayed in the viewer.", "model-viewer"),
          'placeholder'  => 'Paste here Model url',
          'dependency' => array('bp_3d_model_type|bp_3d_src_type', '==|==', 'msimple|link', 'all'),
          'class'    => 'bp3d-readonly'
        ),
        array(
          'id'     => 'readonly',
          'type'   => 'repeater',
          'title'        => __('3D Cycle Models', 'model-viewer'),
          'subtitle'     => __('Cycling between 3D Models', 'model-viewer'),
          'button_title' => __('Add New Model', 'model-viewer'),
          'desc'         => __('Use Multiple Model in a row.', 'model-viewer'),
          'class'    => 'bp3d-readonly',
          'fields' => array(
            array(
              'id'    => 'model_src',
              // 'library' => 'model',
              'type'  => 'media',
              'title' =>  __('Model Source', 'model-viewer'),
              'desc'  => __('Upload or Select 3d object files. Supported file type: glb, glTF', 'model-viewer'),
            ),

          ),
          'dependency' => array('bp_3d_model_type', '==', 'mcycle'),
        ),
        array(
              'id'           => 'readonly',
              'type'         => 'text',
              'title'        => __('Initial View', 'model-viewer'),
              'desc'     => __('Paste the Initial View JSON data copied from the Visual editor. Initial View allow you to set the initial view of your 3D model.', 'model-viewer'),
              'placeholder'  => 'Paste here Initial View JSON data',
              'class'    => 'bp3d-readonly',
              'default'  => '[]'
            ),
         array(
          'id'           => 'readonly',
          'type'         => 'upload',
          'button_title' => __('Upload', 'model-viewer'),
          'title'        => __('Environment Image', 'model-viewer'),
          'desc'         =>  __("Sets an environment image to improve lighting and reflections on the model.", "model-viewer"),
          'dependency' => array('bp_3d_model_type|currentViewer', '==|==', 'msimple|modelViewer', 'all'),
          'class'    => 'bp3d-readonly',
        ),

        array(
          'id'           => 'readonly',
          'type'         => 'upload',
          'button_title' => __('Upload', 'model-viewer'),
          'title'        => __('HDR Skybox Image', 'model-viewer'),
          'desc'         =>  __("Sets a skybox image that appears as the background and provides environmental lighting for the model.", "model-viewer"),
          'dependency' => array('bp_3d_model_type|currentViewer', '==|==', 'msimple|modelViewer', 'all'),
          'class'    => 'bp3d-readonly',
        ),
      )
    ));
  }

  public function settings()
  {
    \CSF::createSection($this->prefix, array(
      'title' => __('Settings', 'model-viewer'),
      'fields' => array(
        $this->upgrade_section(),
        array(
          'id'       => 'bp_camera_control',
          'type'     => 'switcher',
          'title'    => __('Moving Controls', 'model-viewer'),
          'desc'     =>  __("Allows users to rotate, pan, and interact with the model using a mouse or touch input.", "model-viewer"),
          'text_on'  => 'Yes',
          'text_off' => 'No',
          'default' => true,

        ),
        array(
          'id'        => 'bp_3d_zooming',
          'type'      => 'switcher',
          'title'     => __('Enable Zoom', 'model-viewer'),
          'subtitle'  => __('Enable or Disable Zooming Behaviour', 'model-viewer'),
          'desc'      =>  __("Enables zooming in and out of the 3D model using mouse scroll or touch gestures.", "model-viewer"),
          'text_on'   => __('Yes', 'model-viewer'),
          'text_off'  => __('NO', 'model-viewer'),
          'text_width'  => 60,
          'default'   => true,
          'dependency' => ['currentViewer', '==', 'modelViewer', 'all']
        ),

        array(
          'id'         => 'bp_3d_loading',
          'type'       => 'radio',
          'title'      => __('Loading Type', 'model-viewer'),
          'subtitle'   => __('Choose Loading type, default:  \'Auto\' ', 'model-viewer'),
          'options'    => array(
            'auto'  => __('Auto', 'model-viewer'),
            'lazy'  => __('Lazy', 'model-viewer'),
            'eager' => __('Eager', 'model-viewer'),
          ),
          'default'    => 'auto',
          'dependency' => ['currentViewer', '==', 'modelViewer', 'all']
        ),
        array(
          'id'        => 'bp_model_angle',
          'type'      => 'switcher',
          'title'     => 'Initial View',
          'subtitle'  => __('Specified Custom Angle of Model in Initial Load.', 'model-viewer'),
          'desc'      => __("Defines the initial camera angle and orientation of the model when the viewer first loads.", "model-viewer"),
          'class'    => 'bp3d-readonly',
          'text_on'   => __('Yes',  'model-viewer'),
          'text_off'  => __('NO', 'model-viewer'),
          'text_width'  => 60,
          'default'   => false,
          'dependency' => ['currentViewer', '==', 'modelViewer', 'all']
        ),
        array(
          'id'    => 'angle_property',
          'type'  => 'spacing',
          'title' => __('Custom Angle Values', 'model-viewer'),
          'subtitle' => __('Set The Custom values for Model. Default Values are ("X=0deg Y=75deg Z=105%")', 'model-viewer'),
          'desc'    => __('Set Your Desire Values. (X= Horizontal Position, Y= Vertical Position, Z= Zoom Level/Position) ', 'model-viewer'),
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
          'dependency' => array('bp_model_angle|currentViewer', '==|==', '1|modelViewer', 'all'),
        ),
        array(
          'id'       => 'bp_3d_autoplay',
          'type'     => 'switcher',
          'title'    => __('Autoplay', 'model-viewer'),
          'subtitle' => __('Enable or Disable AutoPlay', 'model-viewer'),
          'desc'     => __("Automatically starts model animation when the viewer loads.", "model-viewer"),
          'text_on'  => __('Yes', 'model-viewer'),
          'text_off' => __('No', 'model-viewer'),
          'default'  => false,
          'class'    => 'bp3d-readonly',
          'dependency' => ['currentViewer', '==', 'modelViewer', 'all']
        ),
        array(
          'id'       => '3d_shadow_intensity',
          'type'     => 'spinner',
          'title'    => __('shadow Intensity', 'model-viewer'),
          'subtitle' => __('Shadow Intensity for Model', 'model-viewer'),
          'desc'     => __("Controls how dark or light the model’s shadow appears. Higher values create darker, more visible shadows.", "model-viewer"),
          'class'    => 'bp3d-readonly',
          'default' => '1',
          'dependency' => ['currentViewer', '==', 'modelViewer', 'all']
        ),
        array(
          'id'       => '3d_exposure',
          'type'     => 'spinner',
          'min' => 0.1,
          'max' => 5,
          'title'    => __('Exposure', 'model-viewer'),
          'subtitle' => __('Brightness for Model', 'model-viewer'),
          'desc'     => __("Adjusts the brightness of the model scene. Higher values make the model brighter.", "model-viewer"),
          'class'    => 'bp3d-readonly',
          'default' => '1',
          'dependency' => ['currentViewer', '==', 'modelViewer', 'all']
        ),
        array(
          'id'           => 'bp_model_anim_du',
          'type'         => 'text',
          'title'        => __('Cycle Animation Duration', 'model-viewer'),
          'subtitle'     => __('Animation Duration Time at Seconds : 1000ms = 1sec', 'model-viewer'),
          'desc'         => __('Input Model Animation Duration Time (default: \'5\') Seconds', 'model-viewer'),
          'class'    => 'bp3d-readonly',
          'default'   => 5000,
          'dependency' => array('bp_3d_model_type|currentViewer', '==|==', 'mcycle|modelViewer', 'all'),
        ),
        // Poster Options
        array(
          'id'       => 'bp_3d_poster_type',
          'type'     => 'button_set',
          'title'    => __('Poster Type.', 'model-viewer'),
          'subtitle' => __('Choose Poster Type', 'model-viewer'),
          'desc'     => __('Select Poster Type, Default- Simple.', 'model-viewer'),
          'class'    => 'bp3d-readonly',
          'multiple' => false,
          'options'  => array(
            'simple'  => __('simple', 'model-viewer'),
            'cycle'   => __('Cycle', 'model-viewer'),
          ),
          'default'  => array('simple'),
        ),
        array(
          'id'           => 'bp_3d_poster',
          'type'         => 'media',
          'button_title' => __('Upload Poster', 'model-viewer'),
          'title'        => __('3D Poster Image', 'model-viewer'),
          'subtitle'     => __('Display a poster until loaded', 'model-viewer'),
          'desc'         => __('Upload or Select 3d Poster Image.  if you don\'t want to use just leave it empty', 'model-viewer'),
          'class'    => 'bp3d-readonly',
          'dependency' => array('bp_3d_poster_type', '==', 'simple', 'all'),
        ),
        array(
          'id'     => 'bp_3d_posters',
          'type'   => 'repeater',
          'title'        => __('Poster Images', 'model-viewer'),
          'subtitle'     => __('Cycling between posters', 'model-viewer'),
          'button_title' => __('Add New Poster Images', 'model-viewer'),
          'desc'         => __('Use multiple images for poster image.if you don\'t want to use just leave it empty', 'model-viewer'),
          'fields' => array(
            array(
              'id'    => 'poster_img',
              'type'  => 'upload',
              'title' => 'Poster Image'
            ),

          ),
          'dependency' => array('bp_3d_poster_type', '==', 'cycle', 'all'),
          'class'    => 'bp3d-readonly',
        ),
        array(
          'id'        => 'bp_3d_preloader',
          'type'      => 'switcher',
          'title'     => __('Preload', 'model-viewer'),
          'subtitle'  => __('Preload with poster and show model on interaction', 'model-viewer'),
          'desc'      => __("Controls how the model is preloaded. Auto lets the browser decide the best loading strategy.", "model-viewer"),
          'text_on'   => __('Yes', 'model-viewer'),
          'text_off'  => __('NO', 'model-viewer'),
          'text_width'  => 60,
          'class'    => 'bp3d-readonly',
          'default'   => false,
          'dependency' => ['currentViewer', '==', 'modelViewer', 'all']
        ),
        array(
          'id'        => 'bp_3d_progressbar',
          'type'      => 'switcher',
          'title'     => __('Progressbar', 'model-viewer'),
          'subtitle'  => __('Show/Hide Progressbar', 'model-viewer'),
          'desc'      => __("Displays a progress bar during model loading to indicate loading status.", "model-viewer"),
          'text_on'   => __('Yes', 'model-viewer'),
          'text_off'  => __('NO', 'model-viewer'),
          'text_width'  => 60,
          'default'   => true,
          // 'class'    => 'bp3d-readonly',
          'dependency' => ['currentViewer', '==', 'modelViewer', 'all']
        ),
        array(
          'id' => 'bp_model_progress_percent',
          'type' => 'switcher',
          'title' => __("Show Progress Percent", "model-viewer"),
          'desc' => __("Shows the loading percentage while the 3D model is being loaded.", "model-viewer"),
          'class'    => 'bp3d-readonly',
          'default' => false,
          'dependency' => ['currentViewer', '==', 'modelViewer', 'all']
        ),
        array(
          'id'       => 'bp_3d_rotate',
          'type'     => 'switcher',
          'title'    => __('Auto Rotate', 'model-viewer'),
          'subtitle' => __('Enable or Disable Auto Rotation', 'model-viewer'),
          'desc'     => __("Allows users to manually rotate the 3D model using mouse or touch gestures.", "model-viewer"),
          'text_on'  => __('Yes', 'model-viewer'),
          'text_off' => __('No', 'model-viewer'),
          'class'    => 'bp3d-readonly',
          'default'  => false,
          'dependency' => ['currentViewer', '==', 'modelViewer', 'all']
        ),
        array(
          'id'       => '3d_rotate_speed',
          'type'     => 'spinner',
          'title'    => __('Auto Rotate Speed', 'model-viewer'),
          'subtitle' => __('Auto Rotation Speed Per Seconds', 'model-viewer'),
          'desc'     => __("Controls how fast the model rotates automatically. Higher values mean faster rotation.", "model-viewer"),
          'min'         => 0,
          'max'         => 180,
          'default' => 30,
          'class'    => 'bp3d-readonly',
          'dependency' => array('bp_3d_rotate|currentViewer', '==|==', '1|modelViewer', 'all'),
        ),
        array(
          'id'       => '3d_rotate_delay',
          'type'     => 'number',
          'title'    => __('Auto Rotation Delay (ms)', 'model-viewer'),
          'subtitle' => __('After a period of time auto rotation will start', 'model-viewer'),
          'desc'     => __("Sets the delay time before automatic rotation starts after the model loads or after user interaction stops.", "model-viewer"),
          'default' => 3000,
          'class'    => 'bp3d-readonly',
          'dependency' => array('bp_3d_rotate|currentViewer', '==|==', '1|modelViewer', 'all'),
        ),
        array(
          'id'       => 'bp_3d_fullscreen',
          'type'     => 'switcher',
          'title'    => __('Fullscreen', 'model-viewer'),
          'subtitle' => __('Enable or Disable Fullscreen Mode', 'model-viewer'),
          'desc'     => __("Shows a fullscreen button so users can view the model in fullscreen mode.", "model-viewer"),
          'text_on'  => __('Yes', 'model-viewer'),
          'text_off' => __('No', 'model-viewer'),
          'class'    => 'bp3d-readonly',
          'default'  => true,
        ),
        array(
          'id'           => 'bp_3d_enable_ar',
          'type'         => 'switcher',
          'title'        => __('Enable AR', 'model-viewer'),
          'desc'         => __("Enables AR (Augmented Reality) so visitors can view the 3D model in their real environment on supported devices.", "model-viewer"),
          'text_on'      => 'Yes',
          'text_off'     => 'No',
          'default'      => false,
          'dependency'   => ['currentViewer', '==', 'modelViewer', 'all'],
          'class' => 'bp3d-readonly',
        ),
        array(
          'id'           => 'model_iso_src',
          'type'         => 'upload',
          // 'library'      => 'model',
          'title'        => __('3D Source for iOS (Optional)', 'model-viewer'),
          'subtitle'     => __('Upload Model Or Input Valid Model url', 'model-viewer'),
          'desc'         => __("Specifies the iOS-specific model file (.usdz) used for viewing the 3D model in AR on Apple devices.", "model-viewer"),
          'placeholder'  => 'You Can Paste here Model url',
          'class' => 'bp3d-readonly',
          'dependency' => ['currentViewer', '==', 'modelViewer', 'all']
        ),
        array(
          'id'           => 'ar_placement',
          'type'         => 'button_set',
          'title'        => __('AR Placement', 'model-viewer'),
          'desc'         => __("Defines how the model is placed in AR. Choose 'floor' to place the model on the ground or 'wall' to attach it to a vertical surface.", "model-viewer"),
          'options'  => array(
            'floor' => 'Floor',
            'wall'   => 'Wall',
          ),
          'default'  => 'floor',
          'dependency' => ['currentViewer', '==', 'modelViewer', 'all'],
          'class' => 'bp3d-readonly',
        ),
        array(
          'id'           => 'ar_mode',
          'type'         => 'button_set',
          'title'        => __('AR Mode', 'model-viewer'),
          'desc'         => __("Selects the AR viewing mode. 'Quick Look' is used for iOS devices, while other modes enable AR on supported Android devices.", "model-viewer"),
          'options'  => array(
            'webxr' => 'WebXR',
            'scene-viewer' => 'Scene Viewer',
            'quick-look'   => 'Quick Look',
          ),
          'default'  => 'webxr',
          'dependency' => ['currentViewer', '==', 'modelViewer', 'all'],
          'class' => 'bp3d-readonly',
        ),
      )
    ));
  }

  public function style()
  {
    \CSF::createSection($this->prefix, array(
      'title' => __('Style', 'model-viewer'),
      'fields' => array(
        $this->upgrade_section(),
        array(
          'id'           => 'bp_3d_width',
          'type'         => 'dimensions',
          'title'        => __('Width', 'model-viewer'),
          'desc'         => __("Sets the width of the 3D viewer. You can use values like %, px, or vw for responsive layouts.", "model-viewer"),
          'default'  => array(
            'width'  => 100,
            'unit'   => '%',
          ),
          'height'   => false,
        ),
        array(
          'id'      => 'bp_3d_height',
          'type'    => 'dimensions',
          'title'   => __('Height', 'model-viewer'),
          'desc'    => __("Sets the height of the 3D viewer. Adjust this to control how much vertical space the model occupies.", "model-viewer"),
          'units'   => ['px', 'em', 'pt'],
          'default'  => array(
            'height' => 320,
            'unit'   => 'px',
          ),
          'width'   => false,
        ),

        array(
          'id' => 'bp_3d_align',
          'title' => __("Align", "model-viewer"),
          'desc' => __("Controls the alignment of the 3D viewer within its container, such as left, center, or right.", "model-viewer"),
          'type' => 'button_set',
          'options' => [
            'start' => 'Left',
            'center' => 'Center',
            'end' => 'Right'
          ],
          'default' => 'center',
        ),
        array(
          'id'           => 'bp_model_bg',
          'type'         => 'color',
          'title'        => __('Background Color', 'model-viewer'),
          'subtitle'        => __('Set Background Color For 3d Model.If You don\'t need just leave blank. Default : \'transparent color\'', 'model-viewer'),
          'desc'         => __("Sets the background color of the 3D viewer. Use transparent or any valid CSS color value.", "model-viewer"),
          'default'      => 'transparent',
          // 'class' => 'bp3d-readonly',
        ),
        array(
          'id'           => 'bp_model_progressbar_color',
          'type'         => 'color',
          'title'        => __('Progressbar Color', 'model-viewer'),
          'subtitle'        => __('Set Progressbar Color For 3d Model.', 'model-viewer'),
          'desc'         => __("Changes the color of the loading progress bar shown while the model is loading.", "model-viewer"),
          'default'      => 'rgba(0, 0, 0, 0.4)',
          'dependency' => ['currentViewer|bp_3d_progressbar', '==|==', 'modelViewer|1', 'all'],
          'class' => 'bp3d-readonly',
        ),
        // array(
        //   'id'           => 'bp_model_icon_color',
        //   'type'         => 'color',
        //   'title'        => __('Icon Color', 'model-viewer'),
        //   'subtitle'        => __('Icon Color For 3d Model.', 'model-viewer'),
        //   'desc'         => __('Choose Icon Color For Model.', 'model-viewer'),
        //   'default'      => '#333'
        // ),
        array(
          'id'       => 'css',
          'type'     => 'code_editor',
          'title'    => 'CSS Editor (use this ID below as wrapper selector)',
          'desc'     => __("Add your own CSS to style the 3D viewer. Use this to customize spacing, colors, buttons, or other UI elements.", "model-viewer"),
          'subtitle' => ' ',
          'class'    => 'custom-css bp3d-readonly',
          'settings' => array(
            'theme'  => 'mbo',
            'mode'   => 'css',
          ),
        ),
        // array(
        //   'id'       => 'additional_id',
        //   'type'     => 'text',
        //   'title'    => 'Additional ID',
        //   'desc'     => __("Adds a custom HTML ID to the 3D viewer wrapper. Useful for targeting the viewer with CSS or JavaScript.", "model-viewer"),
        //   'subtitle' => ' ',
        //   'default'  => '',
        // ),
        // array(
        //   'id'       => 'additional_class',
        //   'type'     => 'text',
        //   'title'    => 'Additional Class',
        //   'desc'     => __("Adds custom CSS class names to the 3D viewer wrapper for advanced styling or scripting.", "model-viewer"),
        //   'subtitle' => ' ',
        //   'default'  => '',
        // ),
        
      )
    ));
  }

  function upgrade_section(){
		return array(
					'type' => 'content',
					'content' => '<div class="bp3d-metabox-upgrade-section">3D Viewer lets you embed interactive 3D models and 360 product views on WordPress sites with almost all 3D file formats. <a class="button button-bplugins" href="' . admin_url('edit.php?post_type=bp3d-model-viewer&page=3d-viewer#/pricing') . '">Upgrade to PRO </a></div>'
		);
	}
}
