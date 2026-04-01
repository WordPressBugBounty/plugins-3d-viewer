<?php



namespace BP3D\Woocommerce;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WooCommerce product meta box.
 *
 * Registers the 3D product settings metabox on WooCommerce product
 * edit screens using the CSF framework. Defines model source, poster,
 * AR, hotspot, and positioning options.
 */
class ProductMeta
{
    protected string $prefix = '_bp3d_product_';

    /**
     * Register the metabox.
     */
    public function register(): void
    {
        $settings = get_option('_bp3d_settings_', ['3d_woo_switcher' => '']);

        if (($settings['3d_woo_switcher'] ?? '') === '0') {
            return;
        }

        \CSF::createMetabox($this->prefix, [
            'title' => esc_html__('3D Product Settings', 'model-viewer'),
            'post_type' => 'product',
            'show_restore' => true,
        ]);

        \CSF::createSection($this->prefix, [
            'fields' => $this->getFields(),
        ]);
    }

    /**
     * Build and return the metabox field definitions.
     *
     * @return array<int, array<string, mixed>>
     */
    private function getFields(): array
    {
        return [
            // Support link header
            [
                'id' => 'meta_heading',
                'type' => 'content',
                'title' => 'Support',
                'content' => 'Please leave a message if you encounter any issues on the product page. '
                . '<a href="https://bplugins.com/support" target="_blank"><b>Support Center</b></a><br />'
                . '<cite style="color:#2271b1; font-weight: bold">The premium version also supports the following formats: obj, stl, 3dm, 3ds, 3mf, amf, bim, brep, dae, fbx, fcstd, gltf, ifc, iges, step, off, ply, and wrl.</cite>',
            ],

            // 3D Models group
            [
                'id' => 'bp3d_models',
                'type' => 'group',
                'title' => esc_html__('Product 3D Models', 'model-viewer'),
                'desc' => 'Click on + icon to add 3d files, if you add multiple 3d files, we will show them as a slider. <cite style="color:#2271b1; font-weight: bold">Multiple Files Support Only In Pro Version</cite>',
                'button_title' => __('Add New Model', 'model-viewer'),
                'max' => 1,
                'fields' => [
                    [
                        'id' => 'model_src',
                        'type' => 'upload',
                        'title' => esc_html__('3D Source', 'model-viewer'),
                        'subtitle' => esc_html__('Upload Model Or Input Valid Model url', 'model-viewer'),
                        'desc' => esc_html__('Upload / Paste Model url. Supported file type: glb, glTF', 'model-viewer'),
                        'placeholder' => esc_html__('You Can Paste here Model url', 'model-viewer'),
                    ],
                    [
                        'id' => 'poster_src',
                        'type' => 'upload',
                        'title' => __('3D Poster', 'model-viewer'),
                        'subtitle' => __('Upload Poster Or Input Valid poster/image url', 'model-viewer'),
                        'placeholder' => 'You Can Paste here Poster/image url',
                        'desc' => __('This image will display until the model is either loaded or fails to load.', 'model-viewer'),
                    ],
                    [
                        'id' => 'exposure',
                        'type' => 'slider',
                        'min' => 0.1,
                        'max' => 10,
                        'step' => 0.1,
                        'title' => __('Exposure', 'model-viewer'),
                        'subtitle' => __('Brightness for Model', 'model-viewer'),
                        'desc' => __('Use exposure to increase/decrease brightness of Model. "1" for Default.', 'model-viewer'),
                        'default' => 1,
                    ],
                    [
                        'id' => 'invalid',
                        'type' => 'upload',
                        'title' => __('Environment Image', 'model-viewer'),
                        'subtitle' => __('Upload Image Or Input Valid Image URL', 'model-viewer'),
                        'placeholder' => 'You Can Paste here Image/image URL',
                        'class' => 'bp3d-readonly',
                    ],
                    [
                        'id' => 'invalid',
                        'type' => 'upload',
                        'title' => __('Skybox Image', 'model-viewer'),
                        'subtitle' => __('Upload Image Or Input Valid image URL', 'model-viewer'),
                        'placeholder' => 'You Can Paste here image URL',
                        'class' => 'bp3d-readonly',
                    ],
                    [
                        'id' => 'invalid',
                        'type' => 'switcher',
                        'title' => __('Enable AR', 'model-viewer'),
                        'subtitle' => __('Enable AR (Augmented Reality)', 'model-viewer'),
                        'class' => 'bp3d-readonly',
                    ],
                    [
                        'id' => 'hotspots',
                        'type' => 'group',
                        'title' => __('Hotspots', 'model-viewer'),
                        'desc' => __('Add hotspots to your 3D model to add interactive annotations and points of interest.', 'model-viewer'),
                        'class' => 'bp3d-readonly',
                        'fields' => [
                            [
                                'id' => 'invalid',
                                'type' => 'text',
                                'title' => __('Hotspot Name', 'model-viewer'),
                                'desc' => __('Add a name to your hotspot. This will be displayed when the user hovers over the hotspot.', 'model-viewer'),
                                'placeholder' => 'Add hotspot name',
                                'default' => '[]',
                                'class' => 'bp3d-readonly',
                            ],
                        ],
                    ],
                    [
                        'id' => 'initial_view',
                        'type' => 'text',
                        'title' => __('Initial View', 'model-viewer'),
                        'desc' => __('Paste the Initial View JSON data copied from the Visual editor.', 'model-viewer'),
                        'placeholder' => 'Paste here Initial View JSON data',
                        'default' => '[]',
                        'class' => 'bp3d-readonly',
                    ],
                ],
            ],

            // Viewer position
            [
                'id' => 'viewer_position',
                'type' => 'radio',
                'title' => esc_html__('3D Viewer Position', 'model-viewer'),
                'desc' => __('Select the position of the viewer', 'model-viewer'),
                'options' => [
                    'none' => esc_html__('None', 'model-viewer'),
                    'top' => esc_html__('Top of the product image', 'model-viewer'),
                    'bottom' => esc_html__('Bottom of the product image', 'model-viewer'),
                    'replace' => esc_html__('Replace Product Image with 3D', 'model-viewer'),
                    'merge_with_first_image' => 'Show 3D on First Image of Woocommerce Gallery',
                ],
                'default' => 'none',
            ],

            // Display in shop listings (Pro-only)
            [
                'id' => 'invalid',
                'type' => 'switcher',
                'title' => __('Display 3D models in shop pages and product listings', 'model-viewer'),
                'desc' => __('Enable this option to display 3D models in shop pages and product listings', 'model-viewer'),
                'default' => false,
                'class' => 'bp3d-readonly',
            ],

            // Preset/Template selector
            [
                'id' => 'bp_model_template',
                'type' => 'select',
                'title' => __('Template/Preset', 'model-viewer'),
                'desc' => __('Select Template', 'model-viewer'),
                'options' => [],
                'class' => 'bp3d-readonly',
            ],

            // Hotspot style
            [
                'id' => 'invalid',
                'title' => __('Hotspot Style', 'model-viewer'),
                'type' => 'button_set',
                'options' => [
                    'style-1' => 'Style 1',
                    'style-2' => 'Style 2',
                    'style-3' => 'Style 3',
                ],
                'default' => 'style-1',
                'class' => 'bp3d-readonly',
            ],

            // Background color
            [
                'id' => 'bp_model_bg',
                'type' => 'color',
                'title' => __('Background', 'model-viewer'),
                'desc' => __('Set Background color', 'model-viewer'),
                'default' => 'transparent',
            ],

            // Popup models (Pro-only)
            [
                'id' => 'bp3d_popup_models',
                'type' => 'group',
                'title' => 'Popup 3D Models',
                'desc' => 'You can add multiple popup 3D models to your product.',
                'button_title' => __('Add New Model', 'model-viewer'),
                'class' => 'bp3d-readonly',
                'fields' => [
                    [
                        'id' => 'model_url',
                        'type' => 'text',
                        'title' => __('Model URL', 'model-viewer'),
                        'desc' => __('Paste the Model URL.', 'model-viewer'),
                        'placeholder' => 'Paste here Model URL',
                        'default' => '[]',
                        'class' => 'bp3d-readonly',
                    ],
                ],
            ],

            // Custom angle
            [
                'id' => 'bp_model_angle',
                'type' => 'switcher',
                'title' => 'Custom Angle',
                'subtitle' => esc_html__('Specified Custom Angle of Model in Initial Load.', 'model-viewer'),
                'desc' => esc_html__('Enable or Disable Custom Angle Option.', 'model-viewer'),
                'text_on' => esc_html__('Yes', 'model-viewer'),
                'text_off' => esc_html__('NO', 'model-viewer'),
                'text_width' => 60,
                'default' => false,
                'class' => 'bp3d-readonly',
            ],

            // Custom angle values
            [
                'id' => 'angle_property',
                'type' => 'spacing',
                'title' => esc_html__('Custom Angle Values', 'model-viewer'),
                'subtitle' => esc_html__('Set The Custom values for Model. Default Values are ("X=0deg Y=75deg Z=105%")', 'model-viewer'),
                'desc' => esc_html__('Set Your Desire Values. (X= Horizontal Position, Y= Vertical Position, Z= Zoom Level/Position)', 'model-viewer'),
                'default' => ['top' => '0', 'right' => '75', 'bottom' => '105'],
                'left' => false,
                'show_units' => false,
                'top_icon' => 'Deg',
                'right_icon' => 'Deg',
                'bottom_icon' => '%',
                'dependency' => ['bp_model_angle', '==', '1'],
            ],
        ];
    }
}
