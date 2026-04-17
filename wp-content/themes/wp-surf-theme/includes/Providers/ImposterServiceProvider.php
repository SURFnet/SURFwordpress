<?php

namespace SURF\Providers;

use SURF\Core\ClassLoader;
use SURF\Core\Contracts\ServiceProvider;
use SURF\Core\Imposters\BaseImposter;

/**
 * Class ImposterServiceProvider
 * @package SURF\Providers
 */
class ImposterServiceProvider extends ServiceProvider
{

	/**
	 * @return void
	 */
	public function register(): void {}

	/**
	 * @return void
	 */
	public function boot(): void
	{
		$imposters = ( new ClassLoader() )->loadDirectories(
			array_map( fn( $path ) => $this->app->path( $path ), surfConfig( 'app.paths.imposters' ) )
		);
		foreach ( $imposters as $imposter ) {
			if ( !class_exists( $imposter ) ) {
				continue;
			}

			if ( is_a( $imposter, BaseImposter::class, true ) ) {
				$instance = new $imposter();
				$instance->setupImposter();
			}
		}
	}

}
