<?php

namespace SURF\Hooks;

use SURF\Helpers\AttachmentHelper;

/**
 * Class MediaHooks
 * @package SURF\Hooks
 */
class MediaHooks
{
    /**
     * Register media hooks
     */
    public static function register(): void
    {
        if (!AttachmentHelper::isAllowedToUpload()) {
            return;
        }

        static::registerActions();
        static::registerFilters();
    }

    /**
     * add_action() or remove_action() calls
     */
    public static function registerActions(): void
    {
        add_action('admin_head', [static::class, 'addMimesToPlupload']);
    }

    /**
     * add_filter() calls
     */
    public static function registerFilters(): void
    {
        add_filter('sanitize_file_name', [static::class, 'removeUnderscore'], 10, 2);

        add_filter('mime_types', [static::class, 'addMimeTypes']);
        add_filter('rest_allowed_mime_types', [static::class, 'addMimeTypes']);
        add_filter('upload_mimes', [static::class, 'addMimeTypes']);

        add_filter('wp_check_filetype_and_ext', [static::class, 'checkMimeTypes'], 10, 5);

        // Library in Grid view
        add_filter('plupload_init', [static::class, 'updatePluploadSettings']);
        add_filter('wp_plupload_default_settings', [static::class, 'updatePluploadSettings']);
    }

    /**
     * @return void
     */
    public static function addMimesToPlupload(): void
    {
        $allowed = AttachmentHelper::listAllowedExtraMimes();
        if (empty($allowed)) {
            return;
        }

        $extensions = implode(',', array_keys($allowed));
        ?>
        <script type="text/javascript">
            jQuery(function ($) {
                if (typeof wp !== 'undefined' && wp.Uploader && wp.Uploader.defaults) {
                    wp.Uploader.defaults.filters = wp.Uploader.defaults.filters || {};
                    wp.Uploader.defaults.filters.mime_types = wp.Uploader.defaults.filters.mime_types || [];
                    wp.Uploader.defaults.filters.mime_types.push({
                        title: '<?= esc_js(_x('SURF Allowed Files', 'admin', 'wp-surf-theme')); ?>',
                        extensions: '<?= esc_js($extensions); ?>'
                    });
                }
            });
        </script>
        <?php
    }

    /**
     * @param string $filename
     * @param $filename_raw
     * @return string
     */
    public static function removeUnderscore(string $filename, $filename_raw): string
    {
        // Only remove the underscore when WP produced a "file_.ext"-style name.
        // Do NOT touch multi-extension names like "file.php_.ext".
        $parts = explode('.', $filename);
        if (count($parts) !== 2) {
            return $filename;
        }

        [$base, $ext] = $parts;
        if ($base !== '' && str_ends_with($base, '_')) {
            return substr($base, 0, -1) . '.' . $ext;
        }
        return $filename;
    }

    /**
     * @param array $mimes
     * @return array
     */
    public static function addMimeTypes(array $mimes): array
    {
        $allowed = AttachmentHelper::listAllowedExtraMimes();
        if (empty($allowed)) {
            return $mimes;
        }

        return array_merge($mimes, $allowed);
    }

    /**
     * @param array $info
     * @param $tmp_file
     * @param string $filename
     * @param $mimes
     * @param $real_mime
     * @return array
     */
    public static function checkMimeTypes(array $info, $tmp_file, string $filename, $mimes, $real_mime = null): array
    {
        if (!empty($info['ext']) && !empty($info['type'])) {
            return $info;
        }

        $allowed = AttachmentHelper::listAllowedExtraMimes();
        if (empty($allowed)) {
            return $info;
        }

        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (isset($allowed[$file_ext])) {
            $expected_type = $allowed[$file_ext];
            if (!empty($real_mime) && $real_mime !== $expected_type) {
                return $info;
            }

            $info['ext']             = $file_ext;
            $info['type']            = $expected_type;
            $info['proper_filename'] = $filename;
        }

        return $info;
    }

    /**
     * @param array $settings
     * @return array
     */
    public static function updatePluploadSettings(array $settings): array
    {
        $allowed = AttachmentHelper::listAllowedExtraMimes();
        if (empty($allowed)) {
            return $settings;
        }

        $settings['filters'] = $settings['filters'] ?? [];
        $settings['filters']['mime_types'] = $settings['filters']['mime_types'] ?? [];

        $labels = AttachmentHelper::listExtraMimesForSelect();
        foreach (array_keys($allowed) as $file_ext) {
            $settings['filters']['mime_types'][] = [
                'title'      => $labels[$file_ext] ?? $file_ext,
                'extensions' => $file_ext,
            ];
        }

        return $settings;
    }

}
