<?php

namespace SURF\Admin;

/**
 * Class Shortcodes
 * @package SURF\Admin
 */
class Shortcodes
{

	/**
	 * Register shortcodes
	 * @return void
	 */
	public static function init(): void
	{
		// Register shortcodes here
	}

	/**
	 * Register shortcode
	 * @param string $action
	 * @param callable $function
	 * @return void
	 */
	public static function register( string $action, callable $function ): void
	{
		add_shortcode( $action, $function );
	}

}
