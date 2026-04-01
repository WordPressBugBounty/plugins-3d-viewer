<?php
namespace BP3D;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin initialization and service container.
 *
 * Handles bootstrapping all plugin services, loading premium files,
 * and managing the singleton lifecycle.
 */
class Init
{
    private static ?self$instance = null;

    private function __construct()
    {
        add_action('woocommerce_after_register_post_type', [$this, 'load_woocommerce_files']);
    }

    public static function instance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get the list of core service classes to register.
     *
     * @return array<int, class-string>
     */
    public static function get_services(): array
    {
        return [
            Base\EnqueueAssets::class ,
            Base\Import::class ,
            Base\SetupWizard::class ,
            Base\AdminNotice::class ,
            Base\Ajax::class ,
            Shortcode\Shortcode::class ,
            Base\ExtendMimeType::class ,
            Field\Viewer::class ,
            Field\Settings::class ,
            Woocommerce\SingleProduct::class ,
            Woocommerce\ProductsPro::class ,
            Helper\Utils::class ,
            Helper\Block::class ,
            Addons\Controls\Controls::class ,
            Addons\AddonsPro::class ,
            Addons\Blocks::class ,
            Template\ModelViewer::class ,
        ];
    }

    /**
     * Get WooCommerce-specific service classes.
     *
     * @return array<int, class-string>
     */
    public static function get_woocommerce_services(): array
    {
        return [
            Woocommerce\ProductMeta::class ,
        ];
    }

    /**
     * Register custom post types.
     */
    public static function register_post_type(): void
    {
        self::instantiate('BP3D\\Base\\PostTypeModelViewer')->register();

        if (\bp3dv_fs()->can_use_premium_code()) {
            self::instantiate('BP3D\\Base\\PostTypePreset')->register();
        }
    }

    /**
     * Initialize all registered services.
     */
    public static function init(): void
    {
        foreach (self::get_services() as $class) {
            $resolved_class = self::require_file($class);

            if ($resolved_class === false) {
                continue;
            }

            $service = self::instantiate($resolved_class);

            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    /**
     * Load WooCommerce-dependent service files.
     */
    public function load_woocommerce_files(): void
    {
        foreach (self::get_woocommerce_services() as $class) {
            $resolved_class = self::require_file($class);

            if ($resolved_class === false) {
                continue;
            }

            $service = self::instantiate($resolved_class);

            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    /**
     * Resolve and require the file for a given class.
     *
     * Checks for a premium "Pro" version first if applicable.
     *
     * @param  class-string  $class
     * @return class-string|false
     */
    public static function require_file(string $class): string|false
    {
        $file = str_replace('\\', '/', $class);
        $pro_file = BP3D_PATH . str_replace('BP3D', 'inc', $file . 'Pro') . '.php';
        $free_file = BP3D_PATH . str_replace('BP3D', 'inc', $file) . '.php';

        if (
        file_exists($pro_file)
        && \bp3dv_fs()->is__premium_only()
        && \bp3dv_fs()->can_use_premium_code()
        ) {
            require_once $pro_file;
            return $class . 'Pro';
        }

        if (file_exists($free_file)) {
            require_once $free_file;
            return $class;
        }

        return false;
    }

    /**
     * Instantiate a class if it exists.
     *
     * @param  class-string  $class
     * @return object
     */
    private static function instantiate(string $class): object
    {
        if (class_exists($class)) {
            return new $class();
        }

        return new \stdClass();
    }
}
