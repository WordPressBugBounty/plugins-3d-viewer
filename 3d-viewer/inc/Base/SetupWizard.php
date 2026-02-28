<?php
namespace BP3D\Base;

if ( ! defined( 'ABSPATH' ) ) exit;

class SetupWizard {

    public function register() {
        add_action('admin_menu', [$this, 'adminMenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }


    public function adminMenu()
    {
        add_submenu_page('hide', 'Setup Wizard', 'Setup Wizard', 'manage_options', 'bp3d-setup-wizard', array($this, 'setup_wizard'));
    }

    public function setup_wizard()
    {
        $already_completed = get_option('bp3d_setup_wizard_completed', false);
        if ($already_completed) {
            wp_safe_redirect(admin_url('edit.php?post_type=bp3d-model-viewer'));
            exit;
        }
        update_option('bp3d_setup_wizard_completed', true);
?>
        <div class="bp3d_wrapper text-center" id="bp3d-setup-wizard" data-nonce="<?php echo esc_attr(wp_create_nonce('bp3d_security_key')); ?>">
           <span class="loader"></span>
        </div>
<?php
    }


    public function enqueue_scripts($hook)
    {
        if ($hook === 'admin_page_bp3d-setup-wizard') {
            wp_enqueue_script('bp3d-setup-wizard', BP3D_DIR . 'build/setup-wizard/index.js', array('react', 'react-dom', 'wp-util'), BP3D_VERSION, true);
            wp_enqueue_style('bp3d-setup-wizard', BP3D_DIR . 'build/setup-wizard/index.css', array(), BP3D_VERSION, 'all');

            wp_localize_script('bp3d-setup-wizard', 'bp3d_setup_wizard', [
                'nonce' => wp_create_nonce('bp3d_security_key'),
                'dir' => BP3D_DIR,
            ]);
        }
    }

    
}