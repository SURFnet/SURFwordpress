<?php

namespace SURF\View\ViewModels;

/**
 * Class AssetsViewModel
 * Gets the fancy header setting for the Assets page
 * @package SURF\View\ViewModels
 */
class AssetsViewModel
{

	/**
	 * @return bool
	 */
	public static function hasFancyHeader(): bool
	{
		return (bool) ( get_option( 'options_surf-assets-header-active' ) ?? 0 );
	}

	/**
	 * @return string
	 */
	public static function getHeaderTitle(): string
	{
		return (string) ( get_option( 'options_surf-assets-header-title' ) ?? '' );
	}

	/**
	 * @return string
	 */
	public static function getHeaderDescription(): string
	{
		return (string) ( get_option( 'options_surf-assets-header-description' ) ?? '' );
	}

	/**
	 * @return int
	 */
	public static function getHeaderImage(): int
	{
		return (int) ( get_option( 'options_surf-assets-header-image' ) ?? 0 );
	}

	/**
	 * @return string
	 */
	public static function getHeaderContentPosition(): string
	{
		return (string) ( get_option( 'options_surf-assets-header-content-position' ) ?? 'top' );
	}

	/**
	 * @return string
	 */
	public static function getHeaderTopFade(): string
	{
		return (string) ( get_option( 'options_surf-assets-header-top-fade' ) ?? '0' );
	}

	/**
	 * @return string
	 */
	public static function getHeaderBottomFade(): string
	{
		return (string) ( get_option( 'options_surf-assets-header-bottom-fade' ) ?? '1' );
	}

}
