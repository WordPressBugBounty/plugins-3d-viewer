<?php



namespace BP3D\Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Preset custom post type (Pro only).
 *
 * Registers the `bp3d-preset` post type for managing reusable
 * preset configurations for the 3D Viewer.
 */
class PostTypePreset
{
    protected string $post_type = 'bp3d-preset';

    /**
     * Register all hooks for the preset post type.
     */
    public function register(): void
    {
        add_action('init', [$this, 'registerPostType'], 1);

        if (!is_admin()) {
            return;
        }

        add_filter('post_updated_messages', [$this, 'customizeUpdateMessage']);
        add_action('admin_head-post.php', [$this, 'hidePublishingActions']);
        add_action('admin_head-post-new.php', [$this, 'hidePublishingActions']);
        add_filter('gettext', [$this, 'changePublishButtonText'], 10, 2);
        add_filter('post_row_actions', [$this, 'removeRowActions'], 10, 2);
        add_filter('filter_block_editor_meta_boxes', [$this, 'removeMetabox']);
    }

    /**
     * Customize the "post updated" message.
     *
     * @param  array<string, array<int, string>> $messages
     * @return array<string, array<int, string>>
     */
    public function customizeUpdateMessage(array $messages): array
    {
        $messages[$this->post_type][1] = __('Preset Updated', 'model-viewer');
        return $messages;
    }

    /**
     * Register the Preset custom post type.
     */
    public function registerPostType(): void
    {
        register_post_type($this->post_type, [
            'labels' => [
                'name' => __('Presets', 'model-viewer'),
                'menu_name' => __('Preset', 'model-viewer'),
                'name_admin_bar' => __('Preset', 'model-viewer'),
                'add_new' => __('Add New', 'model-viewer'),
                'add_new_item' => __('Add New', 'model-viewer'),
                'new_item' => __('New Preset', 'model-viewer'),
                'edit_item' => __('Edit Preset', 'model-viewer'),
                'view_item' => __('View Preset', 'model-viewer'),
                'all_items' => __('Presets', 'model-viewer'),
                'not_found' => __("Sorry, we couldn't find the Feed you are looking for.", 'model-viewer'),
            ],
            'description' => __('Preset Options.', 'model-viewer'),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => 'edit.php?post_type=bp3d-model-viewer',
            'menu_icon' => 'dashicons-format-image',
            'query_var' => true,
            'rewrite' => ['slug' => '3d-viewer-template'],
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => 2,
            'supports' => ['title', 'editor'],
            'show_in_rest' => true,
            'template' => [['b3dviewer/preset']],
            'template_lock' => 'all',
        ]);
    }

    /**
     * Hide misc/minor publishing actions via CSS.
     */
    public function hidePublishingActions(): void
    {
        global $post;

        if ($post->post_type !== $this->post_type) {
            return;
        }

        echo '<style type="text/css">
            #misc-publishing-actions,
            #minor-publishing-actions {
                display: none;
            }
        </style>';
    }

    /**
     * Change the "Publish" button text to "Save".
     */
    public function changePublishButtonText(string $translation, string $text): string
    {
        if ($this->post_type === get_post_type() && $text === 'Publish') {
            return 'Save';
        }

        return $translation;
    }

    /**
     * Remove "View" and "Quick Edit" row actions.
     *
     * @param  array<string, string> $actions
     * @return array<string, string>
     */
    public function removeRowActions(array $actions): array
    {
        global $post;

        if ($post->post_type === $this->post_type) {
            unset($actions['view'], $actions['inline hide-if-no-js']);
        }

        return $actions;
    }

    /**
     * Remove meta boxes from the block editor.
     *
     * @param  mixed $metaboxes
     * @return mixed
     */
    public function removeMetabox(mixed $metaboxes): mixed
    {
        $screen = get_current_screen();

        if ($screen && $screen->post_type === $this->post_type) {
            return false;
        }

        return $metaboxes;
    }
}
