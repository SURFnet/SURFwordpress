<?php

namespace SURF\Hooks;

use SURF\Helpers\PostHelper;
use WP_Taxonomy;

/**
 * Class MenuHooks
 * @package SURF\Hooks
 */
class MenuHooks
{

	/**
	 * @return void
	 */
	public static function register(): void
	{
		add_action( 'admin_head', [ static::class, 'updateMenuMetaboxes' ] );

		add_filter( 'nav_menu_css_class', [ static::class, 'addMenuItemClasses' ], 10, 3 );
		add_filter( 'walker_nav_menu_start_el', [ static::class, 'addMenuItemStyles' ], 10, 4 );
	}

	/**
	 * @return void
	 */
	public static function updateMenuMetaboxes(): void
	{
		$screen  = get_current_screen();
		$page_id = 'nav-menus';
		if ( $screen?->id !== $page_id ) {
			return;
		}

		global $wp_meta_boxes;
		if ( empty( $wp_meta_boxes[ $page_id ]['side']['default'] ) ) {
			return;
		}

		foreach ( $wp_meta_boxes[ $page_id ]['side']['default'] as $key => $box ) {
			if ( !( ( $box['args'] ?? null ) instanceof WP_Taxonomy ) ) {
				continue;
			}

			$taxonomy   = $box['args']->name;
			$tax_object = get_taxonomy( $taxonomy );
			if ( empty( $tax_object ) ) {
				continue;
			}

			$first_type = $tax_object->object_type[0] ?? null;
			if ( empty( $first_type ) ) {
				continue;
			}

			$post_type = get_post_type_object( $first_type );
			if ( empty( $post_type ) ) {
				continue;
			}

			$wp_meta_boxes[ $page_id ]['side']['default'][ $key ]['title'] = $box['title'] . ' (' . $post_type->labels->name . ')';
		}
	}

	/**
	 * @param array $classes
	 * @param $item
	 * @param $args
	 * @return array
	 */
	public static function addMenuItemClasses( array $classes, $item, $args ): array
	{
		$type = PostHelper::getMetaValue( 'menu_item_setting_button_type', $item->ID );
		if ( empty( $type ) || $type === 'none' ) {
			return $classes;
		}

		$classes[] = 'navigation-menu-button' . ( $type === 'primary' ? '' : '--secondary' );

		return $classes;
	}

	/**
	 * @param string $item_output
	 * @param $item
	 * @param $depth
	 * @param $args
	 * @return null|string
	 */
	public static function addMenuItemStyles( string $item_output, $item, $depth, $args ): ?string
	{
		$style = '';
		$color = PostHelper::getMetaValue( 'menu_item_setting_button_color', $item->ID );
		if ( !empty( $color ) ) {
			$style .= "--surf-menu-button-color: {$color};";
		}

		$hover = PostHelper::getMetaValue( 'menu_item_setting_button_color_hover', $item->ID );
		if ( !empty( $hover ) ) {
			$style .= "--surf-menu-button-hover-color: {$hover};";
		}

		if ( empty( $style ) ) {
			return $item_output;
		}

		return (string) preg_replace( '/(<a\s)/', '$1style="' . esc_attr( $style ) . '" ', $item_output, 1 );
	}

}
