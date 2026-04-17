<?php

namespace SURF\Admin\Widgets;

use Illuminate\Support\Arr;
use SURF\Enums\Theme;
use WP_Widget;

/**
 * Class NavMenuWidget
 * @package SURF\Admin\Widgets
 */
class NavMenuWidget extends WP_Widget
{

	/**
	 * Class constructor
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct(
			'surf_navigation_menu',
			_x( 'Navigation Menu', 'admin', 'wp-surf-theme' ),
			[
				'description' => _x( 'Show a navigation menu.', 'admin', 'wp-surf-theme' ),
			]
		);
	}

	/**
	 * Renders the widget settings
	 * @param array $instance
	 * @return string
	 */
	public function form( $instance ): string
	{
		$widget = $this;
		$menus  = collect( wp_get_nav_menus() )->mapWithKeys( function ( $menu )
		{
			return [ $menu->slug => $menu->name ];
		} )->toArray();
		if ( array_key_exists( 'footer-third-column-menu', $menus ) && !Theme::isPoweredBy() ) {
			unset( $menus['footer-third-column-menu'] );
		}

		$menuArgs = [
			'name'    => $widget->get_field_name( 'menu' ),
			'choices' => $menus,
			'value'   => $instance['menu'] ?? '',
			'style'   => 'width: 100%;',
		];

		echo surfView(
			'admin.widgets.nav-menu',
			compact( 'widget', 'instance', 'menuArgs' )
		);

		return '';
	}

	/**
	 * Renders the widget
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ): void
	{
		$menu = $instance['menu'] ?? '';

		echo surfView(
			'widgets.nav-menu',
			compact( 'args', 'menu' )
		);
	}

}
