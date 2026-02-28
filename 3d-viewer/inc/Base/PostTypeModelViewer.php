<?php

namespace BP3D\Base;

if ( ! defined( 'ABSPATH' ) ) exit;

class PostTypeModelViewer
{

    protected $post_type = 'bp3d-model-viewer';
    protected $import_ver = '1.0.0';

    public function register()
    {
        add_action('init', [$this, 'registerPostType'], 0);

        if (is_admin()) {
            add_action('manage_' . $this->post_type . '_posts_custom_column', [$this, 'addShortcodeColumn'], 10, 2);
            add_action('manage_' . $this->post_type . '_posts_columns', [$this, 'addShortcodeColumnContent'], 10, 2);
            add_filter('post_updated_messages', [$this, 'changeUpdateMessage']);
            add_action('admin_head-post.php', [$this, 'bp3d_hide_publishing_actions']);
            add_action('admin_head-post-new.php', [$this, 'bp3d_hide_publishing_actions']);
            add_filter('gettext', [$this, 'bp3d_change_publish_button'], 10, 2);
            add_action('edit_form_after_title', [$this, 'bp3d_shortcode_area']);
            add_filter('post_row_actions', [$this, 'bp3d_remove_row_actions'], 10, 2);
            add_action('admin_init', [$this, 'set_meta_data']);


            // force gutenberg here
            add_action('use_block_editor_for_post', [$this, 'use_block_editor_for_post'], 999, 2);
            add_filter('filter_block_editor_meta_boxes', [$this, 'remove_metabox']);

            // add_action('add_meta_boxes', [$this, 'shortcode_area_metabox']);

            // duplicate audio player
            add_filter('post_row_actions', [$this, 'add_duplicate_post_link'], 10, 2);
            add_action('admin_action_bp3d_duplicate_post_as_draft', [$this, 'duplicate_post_action']);
        }
    }

    // set isGutenberg = false for old previously created shortcode
    function set_meta_data()
    {
        if (get_option('model_viewer_import_ver', '0') < $this->import_ver) {
            $modelViewer = new \WP_Query(array(
                'post_type' => $this->post_type,
                'post_status' => 'any',
                'posts_per_page' => -1
            ));

            while ($modelViewer->have_posts()) {
                $modelViewer->the_post();
                $id = get_the_ID();
                if (!get_post_meta($id, 'isGutenberg', true)) {
                    update_post_meta($id, 'isGutenberg', false);
                }
            };
            update_option('model_viewer_import_ver', $this->import_ver);
        }
    }

    public function use_block_editor_for_post($use, $post)
    {
        $option =  get_option('_bp3d_settings_', []);
        $gutenberg = $option['gutenberg_enabled'] ?? false;
        $isGutenberg = (bool) get_post_meta($post->ID, 'isGutenberg', true);

        if ($this->post_type === $post->post_type) {
            if ($gutenberg && $post->post_status === 'auto-draft') {
                update_post_meta($post->ID, 'isGutenberg', true);
                return true;
            } else if ($isGutenberg) {
                return true;
            } else {
                remove_post_type_support($this->post_type, 'editor');
                return false;
            }
        }

        return $use;
    }

    public function changeUpdateMessage($messages)
    {
        $messages[$this->post_type][1] = __('Model Updated', 'model-viewer');
        return $messages;
    }

    public function addShortcodeColumnContent($defaults)
    {
        unset($defaults['date']);
        $defaults['shortcode'] = 'ShortCode';
        $defaults['date'] = 'Date';
        return $defaults;
    }

    public function addShortcodeColumn($column_name, $post_ID)
    {
        if ($column_name === 'shortcode') {
            echo "<div class='b3dviewer_front_shortcode'><input value='[3d_viewer id=" . esc_attr($post_ID) . "]' ><span class='htooltip'>" . esc_html__("Copy To Clipboard", "model-viewer") . "</span><svg class='bp3d_shortcode_copy_icon' data-clipboard-text='[3d_viewer id=" . esc_attr($post_ID) . "]' width='22px' height='22px' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'> <path d='M8 4V16C8 17.1046 8.89543 18 10 18L18 18C19.1046 18 20 17.1046 20 16V7.24162C20 6.7034 19.7831 6.18789 19.3982 5.81161L16.0829 2.56999C15.7092 2.2046 15.2074 2 14.6847 2H10C8.89543 2 8 2.89543 8 4Z' stroke='#000000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/> <path d='M16 18V20C16 21.1046 15.1046 22 14 22H6C4.89543 22 4 21.1046 4 20V9C4 7.89543 4.89543 7 6 7H8' stroke='#000000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/> </svg></div>";
        }
    }

    public function registerPostType()
    {
        register_post_type(
            $this->post_type,
            array(
                'labels' => array(
                    'name'           => __('3D Viewer', 'model-viewer'),
                    'menu_name'      => __('3D Viewer', 'model-viewer'),
                    'name_admin_bar' => __('3D Viewer', 'model-viewer'),
                    'add_new'        => __('Add New', 'model-viewer'),
                    'add_new_item'   => __(' &#8627; Add New', 'model-viewer'),
                    'new_item'       => __('New 3D Viewer ', 'model-viewer'),
                    'edit_item'      => __('Edit 3D Viewer ', 'model-viewer'),
                    'search_items'   => __('Search Viewers ', 'model-viewer'),
                    'view_item'      => __('View 3D Viewer ', 'model-viewer'),
                    'all_items'      => __('All 3D Viewers', 'model-viewer'),
                    'not_found'      => __('Sorry, we couldn\'t find the Feed you are looking for.', 'model-viewer'),
                ),
                'description'     => __('3D Viewer Options.', 'model-viewer'),
                'public'          => false,
                'show_ui'         => true,
                // 'show_in_menu'    => '3d-viewer',
                'menu_icon'       => 'dashicons-format-image',
                'query_var'       => true,
                'rewrite'         => array('slug' => 'model-viewer'),
                'capability_type' => 'post',
                'has_archive'     => false,
                'hierarchical'    => false,
                'menu_position'   => 15,
                'supports'        => array('title', 'editor'),
                'show_in_rest'    => true,
                'template'        => [
                    ['b3dviewer/modelviewer']
                ],
                'template_lock' => 'all',
            )
        );
    }

    // HIDE everything in PUBLISH metabox except Move to Trash & PUBLISH button
    public function bp3d_hide_publishing_actions()
    {
        global  $post;
        if ($post->post_type == $this->post_type) {
            echo  ' <style type="text/css">
                    #misc-publishing-actions,
                    #minor-publishing-actions{
                        display:none;
                    } </style> ';
        }
    }

    public function bp3d_change_publish_button($translation, $text)
    {
        if ($this->post_type == get_post_type()) {
            if ($text == 'Publish') {
                return 'Save';
            }
        }
        return $translation;
    }

    // Hide & Disabled View, Quick Edit and Preview Button
    public function bp3d_remove_row_actions($idtions)
    {
        global  $post;
        if ($post->post_type == 'bp3d-model-viewer') {
            unset($idtions['view']);
            unset($idtions['inline hide-if-no-js']);
        }
        return $idtions;
    }

    function remove_metabox($metaboxs)
    {
        global $post;
        $screen = get_current_screen();

        if ($screen->post_type === $this->post_type) {
            return false;
        }
        return $metaboxs;
    }

 

    function bp3d_shortcode_area(){

        if($this->post_type != get_post_type()){
            return;
        }
        global $post;
        $id = $post->ID;

        $shortcode = "[3d_viewer id='" . esc_attr($id) . "']";
        ?>
        <div class="bp3d_shortcode_area_after_title">
            <label><?php esc_html_e('Copy and paste this shortcode into your posts, pages and widget', 'model-viewer'); ?></label>
           <div class="shortcode_area">
             <button class="button button-bplugins button-large bp3d_shortcode_copy_btn" data-clipboard-text="<?php echo esc_attr($shortcode) ?>"><?php echo esc_html($shortcode); ?></button>
             <svg class='bp3d_shortcode_copy_icon' data-clipboard-text='[3d_viewer id="<?php echo esc_attr($post->ID) ?>"]' width='22px' height='22px' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'> <path d='M8 4V16C8 17.1046 8.89543 18 10 18L18 18C19.1046 18 20 17.1046 20 16V7.24162C20 6.7034 19.7831 6.18789 19.3982 5.81161L16.0829 2.56999C15.7092 2.2046 15.2074 2 14.6847 2H10C8.89543 2 8 2.89543 8 4Z' stroke='#000000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/> <path d='M16 18V20C16 21.1046 15.1046 22 14 22H6C4.89543 22 4 21.1046 4 20V9C4 7.89543 4.89543 7 6 7H8' stroke='#000000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/> </svg>
           </div>
        </div>
        <?php
    }

    

    // Add a "Duplicate" link to the action links for a custom post type
    function add_duplicate_post_link($actions, $post)
    {
        if (current_user_can('edit_posts') && $post->post_type == $this->post_type) {
            $actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=bp3d_duplicate_post_as_draft&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce') . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
        }
        return $actions;
    }

    // Handle the duplication process when the "Duplicate" link is clicked
    function duplicate_post_action()
    {
        if(!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['duplicate_nonce'] ?? '')), basename(__FILE__))) {
            wp_die('No post to duplicate has been supplied!');
        }

        global $wpdb;
        $post_type = $this->post_type;
        $post_id = (isset($_GET['post']) ? absint($_GET['post']) : absint($_POST['post'] ?? 0));
        $action = sanitize_text_field(wp_unslash($_REQUEST['action'] ?? ''));

        if (!$post_id || $action != 'bp3d_duplicate_post_as_draft') {
            wp_die('No post to duplicate has been supplied!');
        }

        /*
        * get the original post id
        */
        $post = get_post($post_id);

        /*
        * if you don't want current user to be the new post author,
        * then change next couple of lines to this: $new_post_author = $post->post_author;
        */
        $current_user = wp_get_current_user();
        $new_post_author = $current_user->ID;

        /*
        * if post data exists, create the post duplicate
        */
        if (isset($post) && $post != null) {

            /*
            * new post data array
            */
            $args = array(
                'comment_status' => $post->comment_status,
                'ping_status'    => $post->ping_status,
                'post_author'    => $new_post_author,
                'post_content'   => $post->post_content,
                'post_excerpt'   => $post->post_excerpt,
                'post_name'      => $post->post_name,
                'post_parent'    => $post->post_parent,
                'post_password'  => $post->post_password,
                'post_status'    => 'draft',
                'post_title'     => $post->post_title . ' Copy',
                'post_type'      => $post->post_type,
                'to_ping'        => $post->to_ping,
                'menu_order'     => $post->menu_order
            );

            /*
            * insert the post by wp_insert_post() function
            */
            $new_post_id = wp_insert_post($args);

            /*
            * get all current post terms ad set them to the new post draft
            */
            $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
            foreach ($taxonomies as $taxonomy) {
                $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
                wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
            }

            /*
            * duplicate all post meta just in two SQL queries
            */
            $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
            if (count($post_meta_infos) != 0) {
                $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
                foreach ($post_meta_infos as $meta_info) {
                    $meta_key = $meta_info->meta_key;
                    if ($meta_key == '_wp_old_slug') continue;
                    $meta_value = addslashes($meta_info->meta_value);
                    $sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
                }
                $sql_query .= implode(" UNION ALL ", $sql_query_sel);
                $wpdb->query($sql_query);
            }

            /*
            * finally, redirect to the edit post screen for the new draft
            */
            wp_safe_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
            exit;
        } else {
            wp_die('Post creation failed, could not find original post: ' . esc_html($post_id));
        }
    }
}
