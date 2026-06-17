<?php

namespace SURF\Hooks;

use WP_Post;

/**
 * Class PostHooks
 * @package SURF\Hooks
 */
class PostHooks
{

	/**
	 * Register Post hooks
	 */
	public static function register(): void
	{
		add_filter( 'nav_menu_css_class', [ static::class, 'addMenuClasses' ], 10, 2 );
	}

	/**
	 * Adds the "current-menu-item" class to the menu item that corresponds to the "Posts page" (blog page),
	 * when viewing a single post or the posts archive.
	 * @param array $classes
	 * @param WP_Post $item
	 * @return array
	 */
	public static function addMenuClasses( array $classes, WP_Post $item ): array
	{
		$item_object = property_exists( $item, 'object' ) ? $item->object : null;
		if ( $item_object !== 'page' || get_post()?->post_type !== 'post' ) {
			return $classes;
		}

		$item_object_id = property_exists( $item, 'object_id' ) ? (int) $item->object_id : null;
		$page           = (int) get_option( 'page_for_posts' );
		if ( !empty( $item_object_id ) && $item_object_id !== $page ) {
			return $classes;
		}

		if ( in_array( 'current-menu-item', $classes ) ) {
			return $classes;
		}

		$classes[] = 'current-menu-item';

		return $classes;
	}

}
