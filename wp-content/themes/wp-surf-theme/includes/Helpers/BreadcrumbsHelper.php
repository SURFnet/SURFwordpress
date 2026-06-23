<?php

namespace SURF\Helpers;

/**
 * Class BreadcrumbsHelper
 * @package SURF\Helpers
 */
class BreadcrumbsHelper
{

    /**
     * @return bool
     */
    public static function shouldShow(): bool
    {
        if (is_front_page() || !function_exists('yoast_breadcrumb')) {
            return false;
        }

        $hide_crumbs = get_option('options_deactivate_breadcrumbs');
        if (!$hide_crumbs || !is_array($hide_crumbs)) {
            $hide_crumbs = [];
        }

        $should_hide = in_array(get_post_type(), $hide_crumbs);
        if ($should_hide) {
            return false;
        }

        $queried_obj = get_queried_object();
        if (!property_exists($queried_obj, 'taxonomy')) {
            return true;
        }

        $taxonomy = $queried_obj->taxonomy;
        $tax_obj  = get_taxonomy($taxonomy);
        if (empty($tax_obj)) {
            return true;
        }

        if (array_intersect($tax_obj->object_type ?? [], $hide_crumbs)) {
            return false;
        }

        return true;
    }

}
