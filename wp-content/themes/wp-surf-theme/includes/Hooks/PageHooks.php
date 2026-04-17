<?php

namespace SURF\Hooks;

use SURF\Helpers\Helper;
use WP_Post;

/**
 * Class PageHooks
 * @package SURF\Hooks
 */
class PageHooks
{

	/**
	 * Register Page hooks
	 */
	public static function register(): void
	{
		add_filter( 'nav_menu_css_class', [ static::class, 'addMenuClasses' ], 10, 2 );
	}

	/**
	 * Adds the "current-menu-item" class to the menu item that corresponds to the current page
	 * @param array $classes
	 * @param WP_Post $item
	 * @return array
	 */
	public static function addMenuClasses( array $classes, WP_Post $item ): array
	{
		$item_object = property_exists( $item, 'object' ) ? $item->object : null;
		if ( $item_object !== 'page' || get_post()?->post_type !== 'page' ) {
			return $classes;
		}

		$item_object_id = property_exists( $item, 'object_id' ) ? $item->object_id : null;
		$page           = get_post( $item_object_id );
		if ( empty( $page ) ) {
			return $classes;
		}

		// Prevent "Home" from being marked as current if the current page is not the front page.
		$front_id = get_option( 'page_on_front' );
		if ( $page->ID === $front_id && !is_home() ) {
			return $classes;
		}

		$page_url = get_permalink( $page->ID );
		if ( empty( $page_url ) ) {
			return $classes;
		}

		$current_url = Helper::getCurrentUrl();
		if ( !str_contains( $current_url, $page_url ) || in_array( 'current-menu-item', $classes ) ) {
			return $classes;
		}

		$classes[] = 'current-menu-item';

		return array_unique( $classes );
	}

}
