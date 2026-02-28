<?php

if (! defined('ABSPATH')) exit;

if (!class_exists('BP3DAdmin')) {
	class BP3DAdmin
	{
		function __construct()
		{
			add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
			add_action('admin_menu', [$this, 'adminMenu'], 15);
			add_action('admin_head', [$this, 'admin_head']);
		}

		function adminEnqueueScripts($hook)
		{
			if (str_contains($hook, '3d-viewer')) {
				wp_enqueue_style('bp3d-dashboard', BP3D_DIR . 'build/dashboard.css', [], BP3D_VERSION);

				wp_enqueue_script('bp3d-admin-script', BP3D_DIR . 'build/dashboard.js', ['react', 'react-dom',  'wp-components', 'wp-i18n', 'wp-api', 'wp-util', 'lodash', 'wp-media-utils', 'wp-data', 'wp-core-data', 'wp-api-request'], BP3D_VERSION, true);
				wp_localize_script('bp3d-admin-script', 'bp3dDashboard', [
					'dir' => BP3D_DIR,
				]);
			}
		}

		function adminMenu()
		{

			add_submenu_page(
				'edit.php?post_type=bp3d-model-viewer',
				__('Visual Editor', 'model-viewer'),
				__('Visual Editor', 'model-viewer'),
				'edit_posts',
				'3d-viewer-visual-editor',
				[$this, 'visualEditorPage'],
				4
			);

			add_submenu_page(
				'edit.php?post_type=bp3d-model-viewer',
				__('Demo and Help - 3D Viewer', 'model-viewer'),
				'<span style="color: #f18500;">' . __('Help & Demos', 'model-viewer') . '</span>',
				'edit_posts',
				'3d-viewer',
				[$this, 'dashboardPage'],
				9
			);
		}


		function dashboardPage()
		{ ?>
			<div
				id='bp3dAdminDashboard'
				data-info='<?php echo esc_attr(wp_json_encode([
								'version' => BP3D_VERSION,
								'isPremium' => bp3dv_fs()->can_use_premium_code(),
								'hasPro' => file_exists(BP3D_PATH . '/inc/Base/LicenseActivation.php'),
								'nonce' => wp_create_nonce('apbCreatePage'),
								'licenseActiveNonce' => wp_create_nonce('bPlLicenseActivation')
							])); ?>'></div>
		<?php }

		function upgradePage()
		{ ?>
			<div id='bp3dAdminUpgrade'>Coming soon...</div>
		<?php }



		function visualEditorPage()
		{
			wp_enqueue_script('bp3d-visual-editor');

			wp_enqueue_media();

			wp_enqueue_style('bp3d-visual-editor');
		?>
			<div class="wrap" id='bp3dAdminVisualEditor'></div>
		<?php
		}

		public function admin_head()
		{
		?>
			<style>
				.fs-submenu-item.3d-viewer.pricing.upgrade-mode {
					background: #146ef5;
					border-radius: 3px;
					color: #fff;
					display: inline-block;
					padding: 9px 20px 9px 18px;
				}
			</style>
<?php
		}
	}

	new BP3DAdmin;
}
