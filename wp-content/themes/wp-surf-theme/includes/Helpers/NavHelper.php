<?php

namespace SURF\Helpers;

/**
 * Class NavHelper
 * @package SURF\Helpers
 */
class NavHelper
{

	/**
	 * @param string $menuId
	 * @return array
	 */
	public static function navToArray( string $menuId ): array
	{
		$items = wp_get_nav_menu_items( $menuId );
		if ( empty( $items ) ) {
			return [];
		}

		$menu = [];
		foreach ( $items as $item ) {
			if ( empty( $item->menu_item_parent ) ) {
				$menu[ $item->ID ] = [
					'title'    => $item->title,
					'url'      => $item->url,
					'target'   => $item->target ?: '_self',
					'classes'  => array_filter( $item->classes ),
					'children' => static::processNavChildren( $item->ID, $items ),
				];
			}
		}

		return $menu;
	}

	/**
	 * @param string $parentId
	 * @param array $items
	 * @return array
	 */
	protected static function processNavChildren( string $parentId, array $items ): array
	{
		$children = [];
		foreach ( $items as $item ) {
			if ( $item->menu_item_parent === $parentId ) {
				$children[ $item->ID ] = [
					'title'    => $item->title,
					'url'      => $item->url,
					'target'   => $item->target ?: '_self',
					'classes'  => array_filter( $item->classes ),
					'children' => static::processNavChildren( $item->ID, $items ),
				];
			}
		}

		return $children;
	}

}
