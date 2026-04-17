<?php

namespace SURF\Admin;

/**
 * Class Sidebars
 * @package SURF\Admin
 */
class Sidebars
{

	/**
	 * Register sidebars
	 * @return void
	 */
	public static function init(): void
	{
		add_action( 'widgets_init', function ()
		{
			static::register( [
				'id'          => 'footer',
				'name'        => _x( 'Footer', 'admin', 'wp-surf-theme' ),
				'description' => _x( 'Footer', 'admin', 'wp-surf-theme' ),
			] );
		} );
	}

	/**
	 * Register a sidebar
	 * @param array $sidebar
	 * @return void
	 */
	public static function register( array $sidebar ): void
	{
		register_sidebar( $sidebar );
	}

}
