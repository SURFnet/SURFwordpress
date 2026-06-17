<?php

namespace SURF\Admin;

use SURF\Admin\Widgets\AppStoresWidget;
use SURF\Admin\Widgets\FormWidget;
use SURF\Admin\Widgets\NavMenuWidget;
use SURF\Admin\Widgets\SocialMenuWidget;
use SURF\Admin\Widgets\CustomContentWidget;
use WP_Widget;

/**
 * Class Widgets
 * @package SURF\Admin
 */
class Widgets
{

	/**
	 * Register widgets
	 * @return void
	 */
	public static function init(): void
	{
		add_action( 'widgets_init', [ static::class, 'removeWidgets' ] );
		add_action( 'widgets_init', [ static::class, 'initWidgets' ], 11 );
	}

	/**
	 * @return void
	 */
	public static function removeWidgets(): void
	{
		unregister_widget( 'GFWidget' );
		unregister_widget( 'PLL_Widget_Languages' );
		unregister_widget( 'PLL_Widget_Calendar' );
		unregister_widget( 'WP_Nav_Menu_Widget' );
		unregister_widget( 'WP_Widget_Archives' );
		unregister_widget( 'WP_Widget_Block' );
		unregister_widget( 'WP_Widget_Calendar' );
		unregister_widget( 'WP_Widget_Categories' );
		unregister_widget( 'WP_Widget_Custom_HTML' );
		unregister_widget( 'WP_Widget_Links' );
		unregister_widget( 'WP_Widget_Media' );
		unregister_widget( 'WP_Widget_Media_Audio' );
		unregister_widget( 'WP_Widget_Media_Gallery' );
		unregister_widget( 'WP_Widget_Media_Image' );
		unregister_widget( 'WP_Widget_Media_Video' );
		unregister_widget( 'WP_Widget_Meta' );
		unregister_widget( 'WP_Widget_Pages' );
		unregister_widget( 'WP_Widget_Recent_Comments' );
		unregister_widget( 'WP_Widget_Recent_Posts' );
		unregister_widget( 'WP_Widget_RSS' );
		unregister_widget( 'WP_Widget_Search' );
		unregister_widget( 'WP_Widget_Tag_Cloud' );
		unregister_widget( 'WP_Widget_Text' );
	}

	/**
	 * @return void
	 */
	public static function initWidgets(): void
	{
		static::register( new AppStoresWidget() );
		static::register( new CustomContentWidget() );
		static::register( new FormWidget() );
		static::register( new NavMenuWidget() );
		static::register( new SocialMenuWidget() );
	}

	/**
	 * Register a WP_Widget
	 * Create widget classes in includes/Widgets
	 * See: https://developer.wordpress.org/themes/functionality/widgets.
	 * @param WP_Widget $widget
	 */
	public static function register( WP_Widget $widget ): void
	{
		register_widget( $widget );
	}

}
