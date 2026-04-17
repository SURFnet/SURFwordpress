<?php

namespace SURF\Providers;

use SURF\Application;

/**
 * Class ViewServiceProvider
 * @package SURF\Providers
 */
class ViewServiceProvider extends \Illuminate\View\ViewServiceProvider
{

	/**
	 * @var Application
	 */
	protected $app;

	/**
	 * @return void
	 */
	public function boot(): void
	{
		// Clear all compiled views after a deployment or after clearing cache
		if ( function_exists( 'add_action' ) ) {
			add_action( 'surf_clear_cache', [ $this, 'clearCompiledViews' ] );
			add_action( 'surf_deploy', [ $this, 'clearCompiledViews' ] );
		}
	}

	/**
	 * @return void
	 */
	public function clearCompiledViews(): void
	{
		$pattern = surfConfig( 'view.compiled' );
		$files   = glob( $pattern . '/*.php' );

		foreach ( $files as $file ) {
			unlink( $file );
		}
	}

}
