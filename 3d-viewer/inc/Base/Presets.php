<?php



namespace BP3D\Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Preset data accessor.
 *
 * Retrieves preset configuration data from Gutenberg block
 * content stored in preset posts.
 */
class Presets
{
    /**
     * Get preset attributes by post ID.
     *
     * Parses the post content as blocks and extracts the preset
     * attributes from the first block.
     *
     * @param  int|null $id  Preset post ID
     * @return array<string, mixed> Preset attributes or empty array
     */
    public static function getPresetById(?int $id): array
    {
        if (!$id) {
            return [];
        }

        $content = get_post_field('post_content', $id);
        $content = str_replace(']]>', ']]&gt;', $content);

        $blocks = parse_blocks($content);

        if (count($blocks) === 0) {
            return [];
        }

        return $blocks[0]['attrs']['preset'] ?? [];
    }
}
