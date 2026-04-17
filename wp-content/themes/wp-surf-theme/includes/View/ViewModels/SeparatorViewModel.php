<?php

namespace SURF\View\ViewModels;

/**
 * Class SeparatorViewModel
 * @package SURF\View\ViewModels
 */
class SeparatorViewModel
{

	/**
	 * @return bool
	 */
	public static function hasGlobalSeparator(): bool
	{
		return !empty( get_option( 'options_surf_theme_global_separator_image' ) );
	}

	/**
	 * @return array
	 */
	public static function getGlobalSeparatorImage(): array
	{
		$id    = get_option( 'options_surf_theme_global_separator_image' );
		$image = wp_get_attachment_image_src( $id, 'full' );

		if ( !$image ) {
			return [];
		}

		return [
			'url'    => $image[0],
			'width'  => $image[1],
			'height' => $image[2],
		];
	}

	/**
	 * @return array
	 */
	public static function getGlobalSeparatorMargins(): array
	{
		return [
			'sm-top'    => get_option( 'options_surf_theme_global_separator_margins_mobile_top' ) ?? 0,
			'sm-bottom' => get_option( 'options_surf_theme_global_separator_margins_mobile_bottom' ) ?? 0,
			'md-top'    => get_option( 'options_surf_theme_global_separator_margins_tablet_top' ) ?? 0,
			'md-bottom' => get_option( 'options_surf_theme_global_separator_margins_tablet_bottom' ) ?? 0,
			'lg-top'    => get_option( 'options_surf_theme_global_separator_margins_desktop_top' ) ?? 0,
			'lg-bottom' => get_option( 'options_surf_theme_global_separator_margins_desktop_bottom' ) ?? 0,
		];
	}

	/**
	 * @return false|mixed
	 */
	public static function hasGlobalSeparatorNoMargin()
	{
		return get_option( 'options_surf_theme_global_separator_no_margin' ) ?? false;
	}

	/**
	 * @return bool
	 */
	public static function hasMenuSeparator(): bool
	{
		return !empty( get_option( 'options_surf_theme_menu_separator_image' ) );
	}

	/**
	 * @return array
	 */
	public static function getMenuSeparatorImage(): array
	{
		$id    = get_option( 'options_surf_theme_menu_separator_image' );
		$image = wp_get_attachment_image_src( $id, 'full' );

		if ( !$image ) {
			return [];
		}

		return [
			'url'    => $image[0],
			'width'  => $image[1],
			'height' => $image[2],
		];
	}

	/**
	 * @return array
	 */
	public static function getMenuSeparatorMargins(): array
	{
		return [
			'sm-top'    => get_option( 'options_surf_theme_menu_separator_margins_mobile_top' ) ?? 0,
			'sm-bottom' => get_option( 'options_surf_theme_menu_separator_margins_mobile_bottom' ) ?? 0,
			'md-top'    => get_option( 'options_surf_theme_menu_separator_margins_tablet_top' ) ?? 0,
			'md-bottom' => get_option( 'options_surf_theme_menu_separator_margins_tablet_bottom' ) ?? 0,
			'lg-top'    => get_option( 'options_surf_theme_menu_separator_margins_desktop_top' ) ?? 0,
			'lg-bottom' => get_option( 'options_surf_theme_menu_separator_margins_desktop_bottom' ) ?? 0,
		];
	}

}
