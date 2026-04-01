<?php



namespace BP3D\Addons;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 3D Model Viewer Elementor widget.
 *
 * Provides a full-featured Elementor widget for embedding 3D models
 * with controls for viewer type, rotation, shadows, animations,
 * exposure, dimensions, backgrounds, and more.
 */
class ModelViewer extends \Elementor\Widget_Base
{
    /**
     * Get widget name.
     */
    public function get_name(): string
    {
        return '3dModelViewer';
    }

    /**
     * Get widget title.
     */
    public function get_title(): string
    {
        return esc_html__('Model Viewer', 'model-viewer');
    }

    /**
     * Get widget icon.
     */
    public function get_icon(): string
    {
        return 'eicon-preview-medium';
    }

    /**
     * Get widget categories.
     *
     * @return array<int, string>
     */
    public function get_categories(): array
    {
        return ['general'];
    }

    /**
     * Get widget keywords.
     *
     * @return array<int, string>
     */
    public function get_keywords(): array
    {
        return ['3d embed', '3d viewer', 'model viewer'];
    }

    /**
     * Get widget script dependencies.
     *
     * @return array<int, string>
     */
    public function get_script_depends(): array
    {
        return ['bp3d-public'];
    }

    /**
     * Get widget style dependencies.
     *
     * @return array<int, string>
     */
    public function get_style_depends(): array
    {
        return ['bp3d-frontend'];
    }

    /**
     * Register widget controls.
     */
    protected function register_controls(): void
    {
        $this->registerContentControls();
        $this->registerStyleControls();
    }

    /**
     * Register Content tab controls.
     */
    private function registerContentControls(): void
    {
        $this->start_controls_section('embedder', [
            'label' => esc_html__('Model Viewer', 'model-viewer'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        // Viewer type
        $this->add_control('currentViewer', [
            'label' => esc_html__('Viewer', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'modelViewer',
            'options' => [
                'modelViewer' => __('Lite', 'model-viewer'),
                'O3DViewer' => __('Advanced', 'model-viewer'),
            ],
        ]);

        // Multiple models toggle
        $this->add_control('multiple', [
            'label' => esc_html__('Use Multiple Model?', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => false,
        ]);

        // Single model controls
        $this->add_control('modelUrl', [
            'label' => esc_html__('Select Model', 'model-viewer'),
            'type' => 'b-select-file',
            'separator' => 'before',
            'placeholder' => esc_html__('Paste Model URL', 'model-viewer'),
            'condition' => ['multiple!' => 'yes'],
        ]);

        $this->add_control('useDecoder', [
            'label' => esc_html__('Use Decoder', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'none',
            'options' => [
                'none' => esc_html__('None', 'model-viewer'),
                'draco' => esc_html__('Draco', 'model-viewer'),
            ],
            'condition' => ['multiple!' => 'yes', 'currentViewer' => 'modelViewer'],
        ]);

        $this->add_control('bin_file', [
            'label' => esc_html__('Upload bin file', 'model-viewer'),
            'type' => 'b-select-file',
            'separator' => 'before',
            'placeholder' => esc_html__('Paste bin file URL', 'model-viewer'),
            'condition' => ['decoder' => 'draco', 'multiple!' => 'yes', 'currentViewer' => 'modelViewer'],
        ]);

        $this->add_control('poster', [
            'label' => esc_html__('Select Poster', 'model-viewer'),
            'type' => 'b-select-file',
            'separator' => 'after',
            'placeholder' => esc_html__('Paste Poster URL', 'model-viewer'),
            'condition' => ['multiple!' => 'yes', 'currentViewer' => 'modelViewer'],
        ]);

        // Multiple model repeater
        $repeater = new \Elementor\Repeater();

        $repeater->add_control('modelUrl', [
            'label' => esc_html__('Select Model', 'model-viewer'),
            'type' => 'b-select-file',
            'separator' => 'before',
            'placeholder' => esc_html__('Paste Model URL', 'model-viewer'),
        ]);

        $repeater->add_control('useDecoder', [
            'label' => esc_html__('Use Decoder', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'none',
            'options' => [
                'none' => esc_html__('None', 'model-viewer'),
                'draco' => esc_html__('Draco', 'model-viewer'),
            ],
            'condition' => ['currentViewer' => 'modelViewer'],
        ]);

        $repeater->add_control('bin_file', [
            'label' => esc_html__('Upload bin file', 'model-viewer'),
            'type' => 'b-select-file',
            'separator' => 'before',
            'placeholder' => esc_html__('Paste bin file URL', 'model-viewer'),
            'condition' => ['decoder' => 'draco', 'currentViewer' => 'modelViewer'],
        ]);

        $repeater->add_control('poster', [
            'label' => esc_html__('Select Poster', 'model-viewer'),
            'type' => 'b-select-file',
            'separator' => 'after',
            'placeholder' => esc_html__('Paste Poster URL', 'model-viewer'),
            'condition' => ['currentViewer' => 'modelViewer'],
        ]);

        $this->add_control('models', [
            'label' => esc_html__('Models', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'condition' => ['multiple' => 'yes'],
            'default' => [['modelUrl' => '', 'poster' => '']],
        ]);

        // Divider
        $this->add_control('hr', [
            'type' => \Elementor\Controls_Manager::DIVIDER,
        ]);

        // Custom angle controls
        $this->add_control('rotate', [
            'label' => esc_html__('Rotate', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'condition' => ['currentViewer' => 'modelViewer'],
        ]);

        $this->add_control('rotateAlongX', [
            'label' => esc_html__('Rotate Along X (degree)', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 0, 'max' => 360, 'step' => 1]],
            'condition' => ['multiple!' => 'yes', 'rotate' => 'yes', 'currentViewer' => 'modelViewer'],
        ]);

        $this->add_control('rotateAlongY', [
            'label' => esc_html__('Rotate Along Y (degree)', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 0, 'max' => 360, 'step' => 1]],
            'default' => ['unit' => 'px', 'size' => '75'],
            'condition' => ['multiple!' => 'yes', 'rotate' => 'yes', 'currentViewer' => 'modelViewer'],
        ]);

        $this->add_control('hr_after_angle', [
            'type' => \Elementor\Controls_Manager::DIVIDER,
            'condition' => ['multiple!' => 'yes', 'rotate' => 'yes'],
        ]);

        // Feature toggles
        $this->add_control('fullscreen', [
            'label' => esc_html__('Fullscreen Button', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('mouseControls', [
            'label' => esc_html__('Mouse Control', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Enable', 'model-viewer'),
            'label_off' => esc_html__('Disable', 'model-viewer'),
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('lazy_load', [
            'label' => esc_html__('Lazy Load', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Enable', 'model-viewer'),
            'label_off' => esc_html__('Disable', 'model-viewer'),
            'return_value' => 'yes',
            'default' => false,
            'condition' => ['currentViewer' => 'modelViewer'],
        ]);

        $this->add_control('shadow', [
            'label' => esc_html__('Enable Shadow', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Enable', 'model-viewer'),
            'label_off' => esc_html__('Disable', 'model-viewer'),
            'return_value' => 'yes',
            'default' => false,
            'condition' => ['currentViewer' => 'modelViewer'],
        ]);

        $this->add_control('autoplay', [
            'label' => esc_html__('Autoplay (if animated)', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => false,
            'condition' => ['currentViewer' => 'modelViewer'],
        ]);

        $this->add_control('variant', [
            'label' => esc_html__('Enable Variant Selector', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => false,
            'condition' => ['currentViewer' => 'modelViewer'],
        ]);

        $this->add_control('enableAnimationSelector', [
            'label' => esc_html__('Enable Animation Selector', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => false,
            'condition' => ['currentViewer' => 'modelViewer'],
        ]);

        $this->add_control('loadingPercentage', [
            'label' => esc_html__('Show Loading Percentage', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => false,
            'condition' => ['currentViewer' => 'modelViewer'],
        ]);

        $this->add_control('progressBar', [
            'label' => esc_html__('Show Progress Bar', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
            'condition' => ['currentViewer' => 'modelViewer'],
        ]);

        $this->add_control('exposure', [
            'label' => esc_html__('Exposure', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => ['px' => ['min' => 0.1, 'max' => 10, 'step' => 0.1]],
            'default' => ['unit' => 'px', 'size' => 1],
            'condition' => ['currentViewer' => 'modelViewer'],
        ]);

        $this->end_controls_section();
    }

    /**
     * Register Style tab controls.
     */
    private function registerStyleControls(): void
    {
        $this->start_controls_section('model', [
            'label' => esc_html__('Model', 'model-viewer'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        // Width
        $this->add_control('width', [
            'label' => esc_html__('Width', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'vw'],
            'range' => [
                'px' => ['min' => 0, 'max' => 1000, 'step' => 5],
                '%' => ['min' => 20, 'max' => 100],
                'vw' => ['min' => 5, 'max' => 100],
            ],
            'default' => ['unit' => '%', 'size' => 100],
            'selectors' => [
                '{{WRAPPER}} .b3dviewer model-viewer' => 'width: {{SIZE}}{{UNIT}};margin:0 auto;max-width:100%;',
            ],
        ]);

        // Height
        $this->add_control('height', [
            'label' => esc_html__('Height', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px', 'vh'],
            'range' => [
                'px' => ['min' => 200, 'max' => 1000, 'step' => 5],
                'vh' => ['min' => 5, 'max' => 100],
            ],
            'default' => ['unit' => 'px', 'size' => 500],
            'selectors' => [
                '{{WRAPPER}} .b3dviewer model-viewer' => 'height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .b3dviewer model-viewer #lazy-load-poster img' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        // Background color
        $this->add_control('backgroundColor', [
            'label' => esc_html__('Background Color', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .b3dviewer model-viewer' => 'background: {{VALUE}}',
            ],
        ]);

        // Background image
        $this->add_control('backgroundImage', [
            'label' => esc_html__('Choose Background Image', 'model-viewer'),
            'type' => \Elementor\Controls_Manager::MEDIA,
            'dynamic' => ['active' => true],
            'selectors' => [
                '{{WRAPPER}} .b3dviewer model-viewer' => 'background-image: url({{URL}});background-repeat: no-repeat; background-size: cover',
            ],
            'condition' => ['currentViewer' => 'modelViewer'],
        ]);

        $this->end_controls_section();
    }

    /**
     * Create a settings accessor closure.
     *
     * @return \Closure(string, mixed=, bool=, string|null=): mixed
     */
    public function bp3d_get_settings(): \Closure
    {
        $settings = $this->get_settings_for_display();

        return function (string $key, mixed $default = false, bool $is_boolean = false, ?string $key2 = null) use ($settings): mixed {
            if (isset($settings[$key], $settings[$key][$key2])) {
                return $is_boolean ? ($settings[$key][$key2] === 'yes') : $settings[$key][$key2];
            }

            if (isset($settings[$key])) {
                return $is_boolean ? ($settings[$key] === 'yes') : $settings[$key];
            }

            return $default;
        };
    }

    /**
     * Render the widget output.
     */
    protected function render(): void
    {
        $settings = $this->get_settings_for_display();
        $get_settings = $this->bp3d_get_settings();

        $finalData = [
            'align' => 'center',
            'uniqueId' => 'b3dviewer' . uniqid(),
            'multiple' => ($settings['multiple'] ?? '') === 'yes',
            'O3DVSettings' => [
                'isFullscreen' => true,
                'isPagination' => false,
                'isNavigation' => false,
                'camera' => null,
                'mouseControl' => true,
            ],
            'model' => [
                'modelUrl' => $settings['modelUrl'] ?? '',
                'poster' => $settings['poster'] ?? '',
                'useDecoder' => $settings['useDecoder'] ?? 'none',
            ],
            'currentViewer' => $settings['currentViewer'] ?? 'modelViewer',
            'models' => $get_settings('models', []),
            'lazyLoad' => ($settings['lazy_load'] ?? '') === 'yes',
            'autoplay' => (bool)($settings['autoplay'] ?? false),
            'shadow' => ($settings['shadow'] ?? '') === 'yes',
            'autoRotate' => true,
            'zoom' => true,
            'isPagination' => false,
            'isNavigation' => false,
            'preload' => 'auto',
            'rotationPerSecond' => '30',
            'mouseControl' => ($settings['mouseControls'] ?? '') === 'yes',
            'fullscreen' => ($settings['fullscreen'] ?? '') === 'yes',
            'variant' => (bool)($settings['variant'] ?? false),
            'loadingPercentage' => (bool)($settings['loadingPercentage'] ?? false),
            'progressBar' => ($settings['progressBar'] ?? '') === 'yes',
            'rotate' => ($settings['rotate'] ?? '') === 'yes',
            'rotateAlongX' => $settings['rotateAlongX']['size'] ?? 0,
            'rotateAlongY' => $settings['rotateAlongY']['size'] ?? 75,
            'exposure' => $settings['exposure']['size'] ?? 1,
            'styles' => [
                'width' => '100%',
                'height' => $get_settings('height', '500', false, 'size') . $get_settings('height', 'px', false, 'unit'),
                'bgColor' => $settings['backgroundColor'] ?? 'transparent',
                'bgImage' => $settings['backgroundImage']['url'] ?? '',
                'progressBarColor' => '#666',
            ],
            'stylesheet' => null,
            'additional' => ['ID' => '', 'Class' => '', 'CSS' => ''],
            'animation' => (bool)($settings['enableAnimationSelector'] ?? false),
            'selectedAnimation' => '',
        ];

        if ($finalData['currentViewer'] === 'O3DViewer') {
            wp_enqueue_script('bp3d-o3dviewer');
        }
        else {
            wp_enqueue_script('bp3d-model-viewer');
        }
?>

        <div class="modelViewerBlock elementor" data-attributes='<?php echo esc_attr(wp_json_encode($finalData)); ?>'></div>

        <?php
        if (is_admin()) {
            wp_enqueue_script('bp3d-o3dviewer');
        }
    }
}
