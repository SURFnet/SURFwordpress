<?php

namespace SURF\Providers;

use SURF\Core\Contracts\ServiceProvider;
use SURF\Core\Vite;

/**
 * Class ViteServiceProvider
 * @package SURF\Providers
 */
class ViteServiceProvider extends ServiceProvider
{

	/**
	 * @return void
	 */
	public function register(): void
	{
		$this->app->singleton( Vite::class, Vite::class );
	}

	/**
	 * @return void
	 */
	public function boot(): void
	{
		add_action( 'wp_enqueue_scripts', function ()
		{
			vite()->enqueueClient();
		} );

		add_filter( 'script_loader_tag', function ( string $tag, string $handle, string $src )
		{
			if ( !str_starts_with( $handle, 'surf.' ) || str_contains( $handle, 'translations' ) ) {
				return $tag;
			}

			if ( preg_match( '/type=[\'"].*?[\'"]/', $tag, $matches ) ) {
				return preg_replace( '/type=[\'"].*?[\'"]/', 'type="module"', $tag );
			}

			return str_replace( '<script', '<script type="module"', $tag );
		}, 10, 3 );
	}

}
