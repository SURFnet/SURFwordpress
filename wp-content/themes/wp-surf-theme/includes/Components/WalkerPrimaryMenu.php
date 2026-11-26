<?php

namespace SURF\Components;

use Walker_Nav_Menu;

/**
 * Class WalkerPrimaryMenu
 * @package SURF\Components
 */
class WalkerPrimaryMenu extends Walker_Nav_Menu
{

	/**
	 * @param string $label The name of the parent menu item
	 * @return bool|string
	 */
	public static function getMenuItemButton( string $label = '' ): bool|string
	{
		ob_start();
		echo surfView( 'components.pages.top-menu-sub-toggle', [ 'label' => $label ] );

		return ob_get_clean();
	}

	/**
	 * Add button to menu
	 */
	function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 )
	{
		parent::start_el( $output, $item, $depth, $args );

		if ( $depth === 0 && in_array(
				'menu-item-has-children',
				is_array( $item->classes ) ? $item->classes : [ $item->classes ]
			) ) {
			$output .= static::getMenuItemButton( wp_strip_all_tags( (string) $item->title ) );
		}
	}

	/**
	 * @param $output
	 * @param $item
	 * @param $depth
	 * @param $args
	 * @return void
	 */
	function end_el( &$output, $item, $depth = 0, $args = null )
	{
		parent::end_el( $output, $item, $depth, $args );
	}

	/**
	 * Add elements to menu so we can create rounded outside borders
	 */
	function start_lvl( &$output, $depth = 0, $args = [] )
	{
		$indent = str_repeat( "\t", $depth );
		$output .= "\n$indent<ul class='sub-menu'><li class='top-border-left'></li><li class='top-border-right'></li>\n";
	}

}
