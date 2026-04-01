<?php



namespace BP3D\Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Setup wizard handler.
 *
 * Manages the first-run setup wizard page, including routing,
 * script enqueuing, and one-time completion tracking.
 */
class SetupWizard
{
    /**
     * Register admin hooks for the setup wizard.
     */
    public function register(): void
    {
        add_action('admin_menu', [$this, 'registerMenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
    }

    /**
     * Register a hidden admin submenu page for the wizard.
     */
    public function registerMenu(): void
    {
        add_submenu_page(
            'hide',
            'Setup Wizard',
            'Setup Wizard',
            'manage_options',
            'bp3d-setup-wizard',
        [$this, 'renderWizardPage']
        );
    }

    /**
     * Render the setup wizard page.
     *
     * Redirects to the main plugin page if the wizard has already been completed.
     */
    public function renderWizardPage(): void
    {
        $already_completed = get_option('bp3d_setup_wizard_completed', false);

        if ($already_completed) {
            wp_safe_redirect(admin_url('edit.php?post_type=bp3d-model-viewer'));
            exit;
        }

        update_option('bp3d_setup_wizard_completed', true);
        $nonce = wp_create_nonce('bp3d_security_key');
?>
        <div class="bp3d_wrapper text-center" id="bp3d-setup-wizard" data-nonce="<?php echo esc_attr($nonce); ?>">
            <span class="loader"></span>
        </div>
        <?php
    }

    /**
     * Enqueue wizard-specific scripts and styles.
     */
    public function enqueueScripts(string $hook): void
    {
        if ($hook !== 'admin_page_bp3d-setup-wizard') {
            return;
        }

        wp_enqueue_script(
            'bp3d-setup-wizard',
            BP3D_DIR . 'build/setup-wizard/index.js',
        ['react', 'react-dom', 'wp-util'],
            BP3D_VERSION,
            true
        );

        wp_enqueue_style(
            'bp3d-setup-wizard',
            BP3D_DIR . 'build/setup-wizard/index.css',
        [],
            BP3D_VERSION,
            'all'
        );

        wp_localize_script('bp3d-setup-wizard', 'bp3d_setup_wizard', [
            'nonce' => wp_create_nonce('bp3d_security_key'),
            'dir' => BP3D_DIR,
        ]);
    }
}