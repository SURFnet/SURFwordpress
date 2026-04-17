<?php

namespace SURF\Providers;

use SURF\Core\ClassLoader;
use SURF\Core\Contracts\ServiceProvider;
use SURF\Core\Shortcodes\Shortcode;

/**
 * Class ShortcodeServiceProvider
 * @package SURF\Providers
 */
class ShortcodeServiceProvider extends ServiceProvider
{

	/**
	 * @return void
	 */
	public function register()
	{
		// not needed
	}

	/**
	 * @return void
	 */
	public function boot()
	{
		$this->registerCustomShortcodes();
	}

	/**
	 * @return void
	 */
	public function registerCustomShortcodes()
	{
		$shortcodes = ( new ClassLoader() )->loadDirectories(
			array_map( fn( $path ) => $this->app->path( $path ), surfConfig( 'app.paths.shortcodes' ) )
		);

		foreach ( $shortcodes as $shortcode ) {
			if ( !class_exists( $shortcode ) ) {
				continue;
			}

			if (
				is_a( $shortcode, Shortcode::class, true )
			) {
				$instance = new $shortcode();
				$instance->register();
			}
		}
	}

}
