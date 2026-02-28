<?php
namespace BP3D\Base;
if ( ! defined( 'ABSPATH' ) ) exit;


class AdminNotice {

    public function register(){
        add_action('admin_notices', array($this, 'upgrade_notice'));
		add_filter('admin_footer_text', [$this, 'bp3d_admin_footer']);
        add_action('admin_head', [$this, 'bp3d_admin_head']);
    }

    function upgrade_notice()
	{
		$page = get_current_screen();
        if(!$page){
            return;
        }
		$is_posters_page = $page->base == 'edit' && $page->post_type == 'bp3d-model-viewer';
		if (!bp3dv_fs()->can_use_premium_code() && ($page->base == 'bp3d-model-viewer_page_3dviewer-settings' || $is_posters_page || $page->base === 'bp3d-model-viewer_page_3d-viewer-visual-editor')) {
?>
			<style>

			</style>
			<div class="bp3d_upgrade_notice <?php echo esc_attr($is_posters_page ? 'bp3d_model_viewer' : 'settings') ?> ">
				<div class="flex">
					<svg id="svg1902" xml:space="preserve" width="36" height="36" viewBox="0 0 682.66669 682.66669" xmlns="http://www.w3.org/2000/svg"><defs id="defs1906"><clipPath clipPathUnits="userSpaceOnUse" id="clipPath1916"><path d="M 0,512 H 512 V 0 H 0 Z" id="path1914"></path></clipPath><clipPath clipPathUnits="userSpaceOnUse" id="clipPath1932"><path d="M 0,512 H 512 V 0 H 0 Z" id="path1930"></path></clipPath></defs><g id="g1908" transform="matrix(1.3333333,0,0,-1.3333333,0,682.66667)"><g id="g1910"><g id="g1912" clip-path="url(#clipPath1916)"><g id="g1918" transform="translate(492,403.4072)"><path d="M 0,0 -236,-87.407 -472,0" id="path1920" style="fill: none; stroke: rgb(20, 110, 245); stroke-width: 40; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; stroke-dasharray: none; stroke-opacity: 1;"></path></g></g></g><g id="g1922" transform="translate(256,316)"><path d="M 0,0 V -94" id="path1924" style="fill: none; stroke: rgb(20, 110, 245); stroke-width: 40; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; stroke-dasharray: none; stroke-opacity: 1;"></path></g><g id="g1926"><g id="g1928" clip-path="url(#clipPath1932)"><g id="g1934" transform="translate(492,110)"><path d="M 0,0 C 0,28.719 -23.281,52 -52,52 H -94 V -90 h 42 c 28.719,0 52,23.281 52,52 z" id="path1936" style="fill: none; stroke: rgb(20, 110, 245); stroke-width: 40; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; stroke-dasharray: none; stroke-opacity: 1;"></path></g><g id="g1938" transform="translate(212,162)"><path d="m 0,0 h 106 v -1.008 l -55,-53.21 v -0.983 c 0,0 62,-26.772 62,-48.071 C 113,-124.572 95.336,-142 74.037,-142 H 0" id="path1940" style="fill: none; stroke: rgb(20, 110, 245); stroke-width: 40; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; stroke-dasharray: none; stroke-opacity: 1;"></path></g><g id="g1942" transform="translate(256,469.3437)"><path d="m 0,0 216,-79.999 v -172.153 c 14.854,-4.437 28.423,-11.877 40,-21.618 V -52.159 L 0,42.656 -256,-52.159 v -322.247 l 171,-63.334 v 42.656 l -131,48.518 v 266.567 z" id="path1944" style="fill: rgb(20, 110, 245); fill-opacity: 1; fill-rule: nonzero; stroke: none;"></path></g></g></g></g></svg>
					<h3>3D Viewer</h3>
				</div>
				<p aria-label="3D Viewer lets you embed interactive 3D models and 360 product views on WordPress sites with support for GLB, GLTF, OBJ, STL, FBX, DAE, and BIM.">3D Viewer lets you embed interactive 3D models and 360 product views on WordPress sites with almost all 3D file formats.</p>
				<div style="display:flex;gap:5px">
					<a aria-label="Upgrade To Pro" href="<?php echo esc_url(admin_url('/edit.php?post_type=bp3d-model-viewer&page=3d-viewer#/pricing')) ?>" class="button button-primary button-bplugins" target="_blank">Upgrade To Pro </a>
					<a aria-label="Support" href="https://wordpress.org/support/plugin/3d-viewer" class="button button-primary button-bplugins" target="_blank">Support 
                        <svg enable-background="new 0 0 515.283 515.283" height="16" viewBox="0 0 515.283 515.283" width="16" xmlns="http://www.w3.org/2000/svg">
							<g>
								<g>
									<g>
										<g>
											<path d="m372.149 515.283h-286.268c-22.941 0-44.507-8.934-60.727-25.155s-25.153-37.788-25.153-60.726v-286.268c0-22.94 8.934-44.506 25.154-60.726s37.786-25.154 60.727-25.154h114.507c15.811 0 28.627 12.816 28.627 28.627s-12.816 28.627-28.627 28.627h-114.508c-7.647 0-14.835 2.978-20.241 8.384s-8.385 12.595-8.385 20.242v286.268c0 7.647 2.978 14.835 8.385 20.243 5.406 5.405 12.594 8.384 20.241 8.384h286.267c7.647 0 14.835-2.978 20.242-8.386 5.406-5.406 8.384-12.595 8.384-20.242v-114.506c0-15.811 12.817-28.626 28.628-28.626s28.628 12.816 28.628 28.626v114.507c0 22.94-8.934 44.505-25.155 60.727-16.221 16.22-37.788 25.154-60.726 25.154zm-171.76-171.762c-7.327 0-14.653-2.794-20.242-8.384-11.179-11.179-11.179-29.306 0-40.485l237.397-237.398h-102.648c-15.811 0-28.626-12.816-28.626-28.627s12.815-28.627 28.626-28.627h171.761c3.959 0 7.73.804 11.16 2.257 3.201 1.354 6.207 3.316 8.837 5.887.001.001.001.001.002.002.019.019.038.037.056.056.005.005.012.011.017.016.014.014.03.029.044.044.01.01.019.019.029.029.011.011.023.023.032.032.02.02.042.041.062.062.02.02.042.042.062.062.011.01.023.023.031.032.011.01.019.019.029.029.016.015.03.029.044.045.005.004.012.011.016.016.019.019.038.038.056.057 0 .001.001.001.002.002 2.57 2.632 4.533 5.638 5.886 8.838 1.453 3.43 2.258 7.2 2.258 11.16v171.761c0 15.811-12.817 28.627-28.628 28.627s-28.626-12.816-28.626-28.627v-102.648l-237.4 237.399c-5.585 5.59-12.911 8.383-20.237 8.383z" fill="rgba(255, 255, 255, 1)" />
										</g>
									</g>
								</g>
							</g>
						</svg></a>
				</div>
			</div>
<?php
		}
	}

	 public function bp3d_admin_footer($text)
    {
        if ('bp3d-model-viewer' == get_post_type()) {
            $url = 'https://wordpress.org/plugins/3d-viewer/reviews/?filter=5#new-post';
            $text = sprintf('If you like <strong> 3D Viewer </strong> please leave us a <a href="%s" target="_blank">Review</a>. Your Review is very important to us as it helps us to grow more. ', $url);
        }

        return $text;
    }

    function bp3d_admin_head()
    {
?>
        <style>
            .menu-icon-bp3d-model-viewer ul li:has(a[href$="3d-viewer-affiliation"]) {
                display: none;
            }
        </style>
<?php
    }

}